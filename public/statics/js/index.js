/**
 * Created by lizhipeng on 2017/4/21.
 */

$(() => {
    const container = $('#images-wall');
    // 图片墙
    const imstags = $('#tags');

    const options = {
        itemSelector : '.box',
        columnWidth: 1,
        isAnimated: true
    };

    container.masonry(options);

    const regRemove = (obj) => {
        // 点击删除按钮
        obj.on('click', (event) => {
            let section = $(event.currentTarget).closest('section');
            swal({
                title: '确定要删除该图片吗？',
                text: section.attr('image-name'),
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: '确定删除',
                cancelButtonText: '取消',
                closeOnConfirm: false,
                imageUrl: section.find('img').attr('src')
            }, () => {
                $.post(container.attr('data-api') + '/destroy', {
                    ids: [
                        section.attr('image-id')
                    ]
                }, (data) => {
                    // remove clicked element
                    container.masonry('remove', section).masonry('layout');
                    // layout remaining item elements

                    //section.remove();
                    swal({
                        title: '删除成功',
                        text: '图片' + section.attr('image-name') + '以成功从服务器删除',
                        type: 'success',
                        timer: 1000
                    });
                }, 'json');
            });
        });
    };

    const regEdit = (obj) => {
        obj.on('click', (event) => {
            // 内置标签选择框，只显示当前分类的标签
            let type = $(event.currentTarget).parents('section').attr('type');
            const football_tags = $('#football-tags-label');
            const nba_tags = $('#nba-tags-label');
            // 打开图片编辑
            let tags = JSON.parse($(event.currentTarget).parents('section').attr('image-tags'));
            $('#tags-editor').tagEditor('destroy').val('').tagEditor({
                initialTags: tags,
                delimiter: ', ',
                placeholder: '输入或选择标签，输入多个标签以空格分割',
                beforeTagSave: function(field, editor, tags, tag, val) {
                    // Remove tag + tag; add tag + val
                    if (tag) {
                        // 表示修改，先删除tsgs
                        nba_tags.find('span:contains(' + tag + ')').siblings('input[type="checkbox"]').prop("checked", false);
                        football_tags.find('span:contains(' + tag + ')').siblings('input[type="checkbox"]').prop("checked", false);
                    }
                    if (val) {
                        // 添加
                        nba_tags.find('span:contains(' + val + ')').siblings('input[type="checkbox"]').prop("checked", true);
                        football_tags.find('span:contains(' + val + ')').siblings('input[type="checkbox"]').prop("checked", true);
                    }
                },
                beforeTagDelete: function(field, editor, tags, val) {
                    // Remove tag + val;
                    // 删除
                    nba_tags.find('span:contains(' + val + ')').siblings('input[type="checkbox"]').prop("checked", false);
                    football_tags.find('span:contains(' + val + ')').siblings('input[type="checkbox"]').prop("checked", false);
                }
            });
            // 加入赛事id
            $('#image-game_id').val($(event.currentTarget).parents('section').attr('game-id'));
            // 加入图片id
            $('#image-id').val($(event.currentTarget).parents('section').attr('image-id'));
            // 加入图片名称
            $('#image-name').val($(event.currentTarget).parents('section').attr('image-name'));
            // 内置标签选择框，只显示当前分类的标签
            switch (type) {
                case '篮球':
                    football_tags.css('display', 'none');
                    nba_tags.css('display', 'block');
                    // 根据文本框中的标签自动选择tags-label中的标签
                    nba_tags.find('input[type="checkbox"]').prop("checked", false);
                    tags.forEach((tag) => {
                        nba_tags.find('span:contains(' + tag + ')').siblings('input[type="checkbox"]').prop("checked", true);
                    });
                    break;
                case '足球':
                    nba_tags.css('display', 'none');
                    football_tags.css('display', 'block');
                    // 根据文本框中的标签自动将tags-label中的标签设置为选中状态
                    football_tags.find('input[type="checkbox"]').prop("checked", false);
                    tags.forEach((tag) => {
                        football_tags.find('span:contains(' + tag + ')').siblings('input[type="checkbox"]').prop("checked", true);
                    });
                    break;
                default:
            }
        });
    };

    const wall = new ImageWall(container, () => {
        // 图片墙html加载之后执行
        const imgLoad = imagesLoaded(container);
        imgLoad.on('progress', (instance, image) => {
            // 图片加载后，根据是否加载成功，标记图片样式
            if (image.isLoaded) {
                $(image.img).removeClass('loading');
            } else {
                $(image.img).addClass('broken');
            }
            $(image.img).parent('section').css('visibility', 'visible');
            container.masonry('appended', $(image.img).parent('section'));
        });
        imgLoad.on('always', () => {
            container.viewer({
                url: 'data-original'
            });
            container.viewer('update');
            regEdit($('.edit'));
            regRemove($('.remove'));
        });
        container.trigger('click');
    });

    // 刷新图片墙
    let refresh = (game_id) => {
        wall.clear();
        container.masonry('destroy');
        container.masonry(options);
        wall.loadFromGame(game_id);
        container.attr('game-id', game_id);
    };

    // 建立树形菜单
    const tree = new Tree((view) => {
        $('#tree').treeview({
            data: view,
            onNodeSelected: (event, data) => {
                if (typeof (data.game_id) !== 'undefined') {
                    refresh(data.game_id);
                }
            }
        });
        let game_id = container.attr('game-id');
        wall.loadFromGame(game_id);
        container.attr('game-id', game_id);
    });
    // 获取菜单并显示
    tree.show();

    const xhrOnProgress = function(fun) {
        xhrOnProgress.onprogress = fun; //绑定监听
        //使用闭包实现监听绑
        return function() {
            //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
            const xhr = $.ajaxSettings.xhr();
            //判断监听函数是否为函数
            if (typeof xhrOnProgress.onprogress !== 'function')
                return xhr;
            //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
            if (xhrOnProgress.onprogress && xhr.upload) {
                xhr.upload.onprogress = xhrOnProgress.onprogress;
            }
            return xhr;
        }
    };

    const image_upload = $('#images-upload');
    image_upload.siblings('button').on('click', (event) => {
        image_upload.val('').trigger('click').one('change', (event) => {
            const files = $(event.currentTarget).prop('files');
            const table = $('<table class="table table-responsive table-condensed"></table>');
            for (let key in files) {
                // 过滤非文件内容
                let file = files[key];
                if (!(file instanceof File)) {
                    continue;
                }
                // 载入文件信息及图片预览
                let img = $('<img>').attr('src', window.URL.createObjectURL(file)).attr('width', '100%');
                let td_image = $('<td>').html(img);
                let td_file = $('<td>').append($('<h5>').text(file.name), $('<p>'));
                let td_percent = $('<td>');
                let tr = $('<tr>').append(td_image, td_file, td_percent);
                table.append(tr);

                // 并发上传
                let formData = new FormData();
                formData.append('file', file);
                formData.append('game_id', container.attr('game-id'));
                $.ajax({
                    url: container.attr('data-api') + '/upload',
                    type: 'POST',
                    cache: false,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    xhr: xhrOnProgress(function(e){
                        // 获取进度
                        let percent = e.loaded / e.total;//计算百分比
                        tr.css('background', '#f0f0f0 linear-gradient(to right, #e4f1fb ' + percent * 100 + '%, rgba(0, 0, 0, 0) ' + (percent * 100 + (10 - percent * 10)) + '%)');
                        td_percent.html((percent * 100).toFixed(0) + '%');
                        td_file.find('p').text((e.loaded / 1024 / 1024).toFixed(2) + 'MB / ' + (e.total / 1024 / 1024).toFixed(2) + 'MB');
                    })
                }).done(function(res) {
                    if (res.status === 'success') {
                        let image = res.data[file.name];
                        let section = wall.dataToHtml({
                            id: image.id,
                            name: image.name,
                            thumb: image.thumb,
                            url: image.url,
                            type: image.game.type,
                            game_id: image.game_id,
                            tags: image.tags,
                        });
                        container.prepend(section);
                        section.imagesLoaded().done((instance) => {
                            // 图片加载后，根据是否加载成功，标记图片样式
                            let img = section.find('img');
                            img.removeClass('loading');
                            img.parent('section').css('visibility', 'visible');
                            container.masonry('prepended', section);
                        });
                        regRemove(section.find('.remove'));
                        regEdit(section.find('.edit'));
                        container.viewer('update');
                        console.log('上传成功');
                    } else {
                        tr.css('background', '#f2dede');
                        console.log('上传失败');
                    }
                }).fail((res) => {
                    tr.css('background', '#f2dede');
                    console.log('上传失败');
                });
            }
            let update_modal = $('#upload-modal');
            update_modal.find('.modal-body').html(table);
            update_modal.modal('show');
        });
    });

    // 生成标签
    let createTags = (obj, tags) => {
        for (let tag of tags) {
            if (tag.name === '|') {
                obj.append('<hr>');
                continue;
            }
            let label = $('<label>').attr('for', 'tag-' + tag.id);
            let checkbox = $('<input type="checkbox" id="tag-' + tag.id + '" tag-id="' + tag.id + '" class="sr-only">');
            let span = $('<span>').addClass('label label-info').text(tag.name);
            obj.append(label.append(checkbox, span));
        }
        obj.find('input[type="checkbox"]').on('change', (event) => {
            let tag = $(event.currentTarget).siblings('span').text();
            if ($(event.currentTarget).is(":checked")) {
                // 选中
                $('#tags-editor').tagEditor('addTag', tag);
            } else {
                // 未选中
                $('#tags-editor').tagEditor('removeTag', tag);
            }
        });
    };

    $.get(imstags.attr('data-api'), {
        type: '足球'
    }, (tags) => {
        createTags($('#football-tags-label'), tags);
    }, 'json');
    $.get(imstags.attr('data-api'), {
        type: '篮球'
    }, (tags) => {
        createTags($('#nba-tags-label'), tags);
    }, 'json');

    // 提交图片信息修改
    $('#update-image').on('click', () => {
        let id = $('#image-id').val();
        let name = $('#image-name').val();
        let game_id = $('#image-game_id').val();
        let tags = $('#tags-editor').tagEditor('getTags')[0].tags;
        $.post(container.attr('data-api') + '/update', {
            id: id,
            name: name,
            game_id: game_id,
            tags: tags
        }, (data) => {
            const image_id = $('#image-id').val();
            const section = $('section[image-id="' + image_id + '"]');
            section.attr('image-tags', JSON.stringify(tags));
            section.attr('image-name', name);
            section.find('.title h3').text(name);
            swal({
                title: '修改成功',
                type: 'success',
                timer: 800
            });
        }, 'json');
    });

    let createLabels = (obj, labels) => {

        for (let $label of labels) {
            let label = $('<label>').attr('for', 'label-' + $label.id);
            let checkbox = $('<input type="checkbox" id="label-' + $label.id + '" label-id="' + $label.id + '" class="sr-only">');
            let span = $('<span>').addClass('label label-info').text($label.name);
            obj.append(label.append(checkbox, span));
        }
        obj.find('input[type="checkbox"]').on('change', (event) => {
            let tag = $(event.currentTarget).siblings('span').text();
            if ($(event.currentTarget).is(":checked")) {
                // 选中
                //$('#tags-editor').tagEditor('addTag', tag);
            } else {
                // 未选中
                //$('#tags-editor').tagEditor('removeTag', tag);
            }
        });
    };

    let labels = $('#labels');
    $.get(labels.attr('data-api'), {

    }, (data) => {
        labels.show();
        createLabels(labels, data);
    }, 'json');

});

// 树形菜单
function Tree(func) {
    const container = $('#images-wall');
    this.func = func;
    this.api = $('#tree').attr('data-api');
    this.tag = '';
    this.start_time = '';
    this.end_time = '';
    this.keywords = '';
    this.tree = {};
    this.view = {};

    Date.prototype.Format = function(fmt) { //author: meizz
        const o = {
            "M+" : this.getMonth() + 1,               //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for(let k in o)
            if(new RegExp("(" + k + ")").test(fmt))
                fmt = fmt.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length));
        return fmt;
    };

    this.show = (tag, start_time, end_time, keywords) => {
        tag && (this.tag = tag);
        start_time && (this.start_time = start_time);
        end_time && (this.end_time = end_time);
        keywords && (this.end_time = keywords);
        // 拿到数据
        this.getData(() => {
            // 绘制菜单
            this.paint();
        });
    };
    // 通过Ajax获取数据，并调用dateToTree转换为树状结构
    this.getData = (func) => {
        $.get(this.api, {
            tag: this.tag,
            start_time: this.start_time,
            end_time: this.end_time,
            keywords: this.keywords
        }, (data) => {
            for (let key in data) {
                this.dateToTree(data[key]);
            }
            func();
        }, 'json');
    };
    // 将键值对格式数据转换为树形结构数据
    this.dateToTree = (data) => {
        // {date: "date_content", type: "type_content", id: 0, name: ""} --> {type_content: {date_content: {id: 0, name: ""}}}
        const type = data.type;
        const date = data.date;
        const name = data.name;
        const d = new Date(Date.parse(date));
        const year = d.Format('yyyy年');
        const month = d.Format('M月');
        typeof (this.tree[type]) === 'undefined' && (this.tree[type] = {});
        typeof (this.tree[type][year]) === 'undefined' && (this.tree[type][year] = {});
        typeof (this.tree[type][year][month]) === 'undefined' && (this.tree[type][year][month] = {});
        typeof (this.tree[type][year][month][date]) === 'undefined' && (this.tree[type][year][month][date] = {});
        this.tree[type][year][month][date][name] = data.id;
    };
    // 递归生成菜单树
    const d = new Date();
    this.year = d.Format('yyyy年');
    this.month = d.Format('M月');
    this.date = d.Format('yyyy-MM-dd');
    let lock = 0;
    let currentlock = 1;
    this.toView = (tree) => {
        const view = [];
        for (let key in tree) {
            if (typeof (tree[key]) === 'object') {
                // 继续迭代
                if (this.year === key || this.month === key || this.date === key) {
                    let state = {expanded:true};
                    if (this.month === key || this.date === key) {
                        if (this.date === key) {
                            state.selected = true;
                            if (currentlock < 4)currentlock = 4;
                        }
                        if (currentlock < 3)currentlock = 3;
                    }
                    view.push({text: key, state:state, nodes: this.toView(tree[key])});
                    if (currentlock < 2)currentlock = 2;
                } else {
                    view.push({text: key, nodes: this.toView(tree[key])});
                }
            } else {
                if (currentlock > lock) {
                    container.attr('game-id', tree[key]);
                    lock = currentlock;
                }
                view.push({text: key, game_id: tree[key]});
            }
        }
        return view;
    };
    // 绘制树形菜单
    this.paint = () => {
        const view = this.toView(this.tree);
        this.func(view);
    };
}

// 图片墙
// container:jquery对象
// func:生成html并插入页面之后调用等回调
function ImageWall(container, func) {
    this.func = func;
    this.container = container;
    this.api = container.attr('data-api');

    this.game_id = '';
    this.tag = '';
    this.start_time = '';
    this.end_time = '';
    this.keywords = '';
    // 从比赛id中加载图片
    this.loadFromGame = (game_id) => {
        this.game_id = game_id;
        this.tag = '';
        this.start_time = '';
        this.end_time = '';
        this.keywords = '';
        this.getImage();
    };
    // 通过查询条件加载图片
    this.loadFromCondition = (tag, start_time, end_time, keywords) => {
        this.game_id = '';
        this.tag = tag;
        this.start_time = start_time;
        this.end_time = end_time;
        this.keywords = keywords;
        this.getImage();
    };
    // ajax加载图片并插入到图片墙
    this.getImage = () => {
        $.get(this.api, {
            game_id: this.game_id,
            tag: this.tag,
            start_time: this.start_time,
            end_time: this.end_time,
            keywords: this.keywords
        }, (data) => {
            for (let key in data) {
                this.container.append(this.dataToHtml(data[key]));
            }
            this.func();
        }, 'json');
    };
    // 获取到的json数据转换为html数据
    this.dataToHtml = (data) => {
        // {"id": "1", "name": "test", "url": "http:\/\/your_path", "game_id": "1"} --> html
        const section = $('<section>').addClass('box col-md-3 col-sm-4 col-xs-6');
        section.attr('type', data.type);
        section.attr('game-id', data.game_id);
        section.attr('image-id', data.id);
        section.attr('image-name', data.name);
        section.attr('image-tags', JSON.stringify(data.tags.map(function (tag) {
            return tag.name;
        })));
        const title = $('<div>').addClass('title').html($('<h3>').text(data.name));
        const image = $('<img>').addClass('img-thumbnail');
        image.attr('src', data.thumb);
        image.attr('title', data.name);
        image.attr('data-original', data.url);
        const option = $('<div>').addClass('option');
        const ul = $('<ul>');
        const remove = $('<li title="删除" class="remove"><span class="glyphicon glyphicon glyphicon-trash" aria-hidden="true"></span></li>');
        const edit = $('<li title="编辑" class="edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true" data-toggle="modal" data-target="#image-editor"></span></li>');
        return section.append(title, image, option.append(ul.append(remove, edit)));
    };
    this.clear = () => {
        this.container.empty();
    }
}
