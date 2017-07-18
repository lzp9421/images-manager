/**
 * Created by lizhipeng on 2017/4/21.
 */

$(function () {


    Date.prototype.Format = function(fmt) { //author: meizz
        var o = {
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
        for(var k in o)
            if(new RegExp("(" + k + ")").test(fmt))
                fmt = fmt.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length));
        return fmt;
    };


    var container = $('#images-wall');
    // 图片墙
    var imstags = $('#tags');

    var options = {
        itemSelector : '.box',
        columnWidth: 1,
        isAnimated: true
    };

    if (location.hash === '#nba') {
        var div1 = $('#football-search').parent('div'), div2 = $('#nba-search').parent('div');
        var t = div1.prop("outerHTML");
        div1.replaceWith(div2.prop("outerHTML"));
        div2.replaceWith(t);
    }
    //container.masonry(options);

    var regRemove = function(obj) {
        // 点击删除按钮
        obj.on('click', function (event) {
            var section = $(event.currentTarget).closest('section');
            swal({
                title: '确定要删除该图片吗？',
                text: section.attr('image-name'),
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: '确定删除',
                cancelButtonText: '取消',
                closeOnConfirm: false,
                imageUrl: section.find('img').attr('src')
            }, function () {
                $.post(container.attr('data-api') + '/destroy', {
                    ids: [
                        section.attr('image-id')
                    ]
                }, function (data) {
                    if (data.status === 'success') {
                        // remove clicked element
                        //container.masonry('remove', section).masonry('layout');
                        // layout remaining item elements

                        //section.remove();
                        swal({
                            title: '删除成功',
                            text: '图片' + section.attr('image-name') + '已成功从服务器删除',
                            type: 'success',
                            timer: 1000
                        });
                    } else {
                        swal({
                        title: '删除失败' + data.data,
                        text: '图片' + section.attr('image-name') + '未能成功删除',
                        type: 'error',
                        timer: 5000
                    });
                    }
                }, 'json');
            });
        });
    };

    var setTag = function (o, t, b) {
        var tags = o.find('span:contains(' + t + ')');
        tags.each(function (i, e) {
            if ($(e).text() === t) {
                $(e).siblings('input[type="checkbox"]').prop("checked", b);
            }
        });
    };
    var regTagschenge = function (obj) {
        var football_tags = $('#football-tags-search');
        var nba_tags = $('#nba-tags-search');
        obj.tagEditor({
            initialTags: [],
            delimiter: ', ',
            placeholder: '输入或选择标签，输入多个标签以空格分割',
            forceLowercase: false,
            beforeTagSave: function (field, editor, tags, tag, val) {
                // Remove tag + tag; add tag + val
                if (tag) {
                    // 表示修改，先删除tsgs
                    setTag(nba_tags, tag, false);
                    setTag(football_tags, tag, false);
                }
                if (val) {
                    // 添加
                    setTag(nba_tags, val, true);
                    setTag(football_tags, val, true);
                }
            },
            beforeTagDelete: function (field, editor, tags, val) {
                // Remove tag + val;
                // 删除
                setTag(nba_tags, val, false);
                setTag(football_tags, val, false);
            }
        });
    };
    regTagschenge($('#search-image-mane'));


    $('#new3games').on('click', 'button', function (event) {
        var conditions = {};
        var game_id = $(this).attr('data-search-id');
        conditions.game_id = game_id;
        refresh(conditions, game_id);
    });
    var regEdit = function (obj) {
        obj.on('click', function (event) {
            // 内置标签选择框，只显示当前分类的标签
            var type = $(event.currentTarget).parents('section').attr('type');
            var football_tags = $('#football-tags-label');
            var nba_tags = $('#nba-tags-label');
            // 打开图片编辑
            var tags = JSON.parse($(event.currentTarget).parents('section').attr('image-tags'));
            $('#tags-editor').tagEditor('destroy').val('').tagEditor({
                initialTags: tags,
                delimiter: ', ',
                placeholder: '输入或选择标签，输入多个标签以空格分割',
                beforeTagSave: function (field, editor, tags, tag, val) {
                    // Remove tag + tag; add tag + val
                    if (tag) {
                        // 表示修改，先删除tsgs
                        setTag(nba_tags, tag, false);
                        setTag(football_tags, tag, false);
                    }
                    if (val) {
                        // 添加
                        setTag(nba_tags, val, true);
                        setTag(football_tags, val, true);
                    }
                },
                beforeTagDelete: function (field, editor, tags, val) {
                    // Remove tag + val;
                    // 删除
                    setTag(nba_tags, val, false);
                    setTag(football_tags, val, false);
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
                    tags.forEach(function (tag) {
                        setTag(nba_tags, tag, true);
                    });
                    break;
                case '足球':
                    nba_tags.css('display', 'none');
                    football_tags.css('display', 'block');
                    // 根据文本框中的标签自动将tags-label中的标签设置为选中状态
                    football_tags.find('input[type="checkbox"]').prop("checked", false);
                    tags.forEach(function (tag) {
                        setTag(football_tags, tag, true);
                    });
                    break;
                default:
            }
        });
    };

    var wall = new ImageWall(container, function (count) {
        // 图片墙html加载之后执行
        if (count) {
            var imgLoad = imagesLoaded(container);
            imgLoad.on('progress', function (instance, image) {
                // 图片加载后，根据是否加载成功，标记图片样式
                if (image.isLoaded) {
                    $(image.img).removeClass('loading');
                } else {
                    $(image.img).addClass('broken');
                }
                $(image.img).parent('section').css('visibility', 'visible');
                //container.masonry('appended', $(image.img).parent('section')).masonry('layout');
            });
            imgLoad.on('always', function () {
                container.viewer({
                    url: 'data-original'
                });
                container.viewer('update');
                regEdit($('.edit'));
                regRemove($('.remove'));
            });
        }
        container.trigger('click');
    });

    // 刷新图片墙
    var refresh = function (conditions, game_id) {
        //container.masonry('destroy');
        wall.clear();
        wall.loadFromGame(conditions);
        container.attr('game-id', game_id);
        //container.masonry(options);
        container.trigger('click');
    };

    var createTreeView = function (view) {
        var tree = $('#tree');
        tree.treeview({
            data: view,
            onNodeSelected: function (event, data) {
                var conditions = {};
                var game_id;
                if (typeof (data.game_id) !== 'undefined') {
                    conditions.game_id = game_id = data.game_id;
                    if(/新闻$/.test(data.text)) {
                        conditions.year = 2000;
                        conditions.type = tree.treeview('getNode', data.parentId).text;
                        conditions.game_id = undefined;
                    }
                }else if (/^20\d{2}\-[01]\d-[0-3]\d$/.test(data.text)) {
                    conditions.date = data.text;
                    var month = tree.treeview('getNode', data.parentId);
                    var year = tree.treeview('getNode', month.parentId);
                    conditions.type = tree.treeview('getNode', year.parentId).text;
                }/* else if (/^[01]?\d月$/.test(data.text)) {
                    conditions.month = parseInt(data.text);
                    let year = tree.treeview('getNode', data.parentId);
                    conditions.year = year.text;
                    conditions.type = tree.treeview('getNode', year.parentId).text;
                    console.log(conditions);
                } else if(/^20\d{2}年$/.test(data.text)) {
                    conditions.year = parseInt(data.text);
                    conditions.type = tree.treeview('getNode', data.parentId).text;
                    console.log(conditions);
                } else if (/^[足篮]球$/) {
                    conditions.type = data.text;
                    console.log(conditions);
                }*/
                refresh(conditions, game_id);
            }
        });
    };

    // 建立树形菜单
    var tree = new Tree(function (view) {
        createTreeView(view);
    });
    // 获取菜单并显示
    tree.show(function () {
        var game_id = container.attr('game-id');
        var conditions = {};
        conditions.game_id = game_id;
        refresh(conditions, game_id)
    });

    var xhrOnProgress = function (fun) {
        xhrOnProgress.onprogress = fun; //绑定监听
        //使用闭包实现监听绑
        return function () {
            //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
            var xhr = $.ajaxSettings.xhr();
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

    var image_upload = $('#images-upload');
    image_upload.siblings('button').on('click', function (event) {
        image_upload.val('').trigger('click').one('change', function (event) {
            var files = $(event.currentTarget).prop('files');
            var table = $('<table class="table table-responsive table-condensed"></table>');
            for (var key in files) {
                // 过滤非文件内容
                var file = files[key];
                if (!(file instanceof File)) {
                    continue;
                }
                $(function () {

                    // 载入文件信息及图片预览
                    var img = $('<img>').attr('src', window.URL.createObjectURL(file)).attr('width', '100%');
                    var td_image = $('<td>').html(img);
                    var td_file = $('<td>').append($('<h5>').text(file.name), $('<p>'));
                    var td_percent = $('<td>');
                    var tr = $('<tr>').append(td_image, td_file, td_percent);
                    table.append(tr);

                    // 并发上传
                    var formData = new FormData();
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
                        async: false,
                        xhr: xhrOnProgress(function (e) {
                            // 获取进度
                            var percent = e.loaded / e.total;//计算百分比
                            tr.css('background', '#f0f0f0 linear-gradient(to right, #e4f1fb ' + percent * 100 + '%, rgba(0, 0, 0, 0) ' + (percent * 100 + (10 - percent * 10)) + '%)');
                            td_percent.html((percent * 100).toFixed(0) + '%');
                            td_file.find('p').text((e.loaded / 1024 / 1024).toFixed(2) + 'MB / ' + (e.total / 1024 / 1024).toFixed(2) + 'MB');
                        })
                    }).done(function (res) {
                        if (res.status === 'success') {
                            var image = res.data[file.name];
                            var section = wall.dataToHtml({
                                id: image.id,
                                name: image.name,
                                thumb: image.thumb,
                                url: image.url,
                                type: image.game.type,
                                game_id: image.game_id,
                                tags: image.tags,
                            });
                            container.prepend(section);
                            section.imagesLoaded().done(function (instance) {
                                // 图片加载后，根据是否加载成功，标记图片样式
                                var img = section.find('img');
                                img.removeClass('loading');
                                img.parent('section').css('visibility', 'visible');
                                //container.masonry('prepended', section).masonry('layout');;
                            });
                            regRemove(section.find('.remove'));
                            regEdit(section.find('.edit'));
                            container.viewer('update');
                            console.log('上传成功');
                        } else {
                            tr.css('background', '#f2dede');
                            alert('上传失败');
                        }
                    }).fail(function(res) {
                        tr.css('background', '#f2dede');
                        alert('上传失败');
                    });
                });
            }
            var update_modal = $('#upload-modal');
            update_modal.find('.modal-body').html(table);
            update_modal.modal('show');
        });
    });

    // 生成标签
    var createTags = function (obj, tags, editor) {
        for (var tag of tags) {
            if (tag.name === '|') {
                obj.append('<hr>');
                continue;
            }
            var label = $('<label>').attr('for', editor.attr('id') + 'tag-' + tag.id);
            var checkbox = $('<input type="checkbox" id="' + editor.attr('id') + 'tag-' + tag.id + '" tag-id="' + tag.id + '" class="sr-only">');
            var span = $('<span>').addClass('label label-info').text(tag.name);
            obj.append(label.append(checkbox, span));
        }
        obj.find('input[type="checkbox"]').on('change', function (event) {
            var tag = $(event.currentTarget).siblings('span').text();
            if ($(event.currentTarget).is(":checked")) {
                // 选中
                editor.tagEditor('addTag', tag);
            } else {
                // 未选中
                editor.tagEditor('removeTag', tag);
            }
        });
    };

    $.get(imstags.attr('data-api'), {
        type: '足球'
    }, function (tags) {
        createTags($('#football-tags-label'), tags, $('#tags-editor'));
        createTags($('#football-tags-search'), tags, $('#search-image-mane'));
        // var football_tags_search = $('#football-tags-search');
        // for (var tag of tags) {
        //     if (tag.name === '|') {
        //         football_tags_search.append('<hr>');
        //         continue;
        //     }
        //     var label = $('<label>').attr('for', 'secrch-tag-' + tag.id);
        //     var checkbox = $('<input type="checkbox" id="secrch-tag-' + tag.id + '" tag-id="' + tag.id + '" class="sr-only">');
        //     var span = $('<span>').addClass('label label-info').text(tag.name);
        //     football_tags_search.append(label.append(checkbox, span));
        // }
    }, 'json');
    $.get(imstags.attr('data-api'), {
        type: '篮球'
    }, function (tags_nba) {
        createTags($('#nba-tags-label'), tags_nba, $('#tags-editor'));
        createTags($('#nba-tags-search'), tags_nba, $('#search-image-mane'));
    }, 'json');

    // 提交图片信息修改
    $('#update-image').on('click', function () {
        var id = $('#image-id').val();
        var name = $('#image-name').val();
        var game_id = $('#image-game_id').val();
        var tags = $('#tags-editor').tagEditor('getTags')[0].tags;
        $.post(container.attr('data-api') + '/update', {
            id: id,
            name: name,
            game_id: game_id,
            tags: tags
        }, function (data) {
            var image_id = $('#image-id').val();
            var section = $('section[image-id="' + image_id + '"]');
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

    var createLabels = function (obj, labels) {

        for (var $label of labels) {
            var label = $('<label>').attr('for', 'label-' + $label.id);
            var checkbox = $('<input type="checkbox" id="label-' + $label.id + '" label-id="' + $label.id + '" class="sr-only">');
            var span = $('<span>').addClass('label label-info').text($label.name);
            obj.append(label.append(checkbox, span));
        }
        obj.find('input[type="checkbox"]').on('change', function (event) {
            var tag = $(event.currentTarget).siblings('span').text();
            var labels = JSON.parse(obj.attr('data-labels') || '[]');
            if ($(event.currentTarget).is(":checked")) {
                // 选中
                labels = labels.concat([tag]);
                obj.attr('data-labels', JSON.stringify(labels));
            } else {
                // 未选中
                var index = labels.indexOf(tag);
                if (index > -1) {
                    labels.splice(index, 1);
                }
                obj.attr('data-labels', JSON.stringify(labels));
                //$('#tags-editor').tagEditor('removeTag', tag);
            }
            tree.show(function () {
                var game_id = container.attr('game-id');
                var conditions = {};
                conditions.game_id = game_id;
                refresh(conditions, game_id)
            }, labels);
        });
    };

    var labels = $('#labels');
    $.get(labels.attr('data-api'), function (data) {
        labels.show();
        createLabels(labels, data);
    }, 'json');

    $('.shaixuan').on('click', function (event) {
        var date = new Date;
        var start_time = $('#search-start-time');
        switch ($(event.currentTarget).attr('data-month')) {
            case '1':
            case '3':
            case '12':
                date.setMonth(date.getMonth() - $(event.currentTarget).attr('data-month'));
                start_time.val(date.Format('yyyy-MM-dd'));
                break;
            default:
                start_time.val('');
                break;
        }
        $('#conditions-search').trigger('click');
    });


    // 高级搜索
    $('#search-image-conditions').on('click', function(event) {
        $('#aside-search').removeClass('sr-only');
        $('#aside-tree').addClass('sr-only');
        $('.shaixuan').show();
        $('#game-tree-list').show();
        $(event.currentTarget).hide();
    });

    $('#game-tree-list').on('click', function(event) {
        $('#aside-search').addClass('sr-only');
        $('#aside-tree').removeClass('sr-only');
        $('.shaixuan').hide();
        $('#search-image-conditions').show();
        $(event.currentTarget).hide();
    });
    $('#conditions-search').on('click', function(event) {
        $('#aside-search').removeClass('sr-only');
        $('#aside-tree').addClass('sr-only');
        $('.shaixuan').show();
        if ($('#search-image-mane').val() == '') {
            return false;
        }
        var name = $('#search-image-mane');
        var start_time = $('#search-start-time');
        var end_time = $('#search-end-time');
        //var football_search = $('#football-search');
        //var nba_search = $('#nba-search');
        var tags = $('#search-image-mane').tagEditor('getTags')[0].tags;
        //var tags = football_search.tagEditor('getTags')[0].tags.concat(nba_search.tagEditor('getTags')[0].tags);
        //container.masonry('destroy');
        wall.clear();
        //container.masonry(options);
        wall.searchImage({
            name: name.val(),
            start_time: start_time.val(),
            tags: tags,
            page: 1
        }, function (count) {
            if (!count) {
                swal({
                    title: '没有数据',
                    type: 'error',
                    timer: 5000
                });
            }
            container.attr('data-load', 'true');
        });
        container.attr('data-page', 1);
        // container.attr('game-id', game_id);
    });

    var upload_modal = $('#upload-modal');
    upload_modal.find('button.finish').on('click', function (event) {
        var length = $(event.currentTarget).parents('.modal-content').find('.modal-body tr').length;
        var first_image = container.find('section.box').first();
        length === 1  && first_image.find('.glyphicon-pencil').trigger('click');
    });

    scrollBottom = function (conditions, game_id) {
        $(document).scroll(function (event) {
            var name = $('#search-image-mane');
            var start_time = $('#search-start-time');
            var end_time = $('#search-end-time');
            //var football_search = $('#football-search');
            //var nba_search = $('#nba-search');
            var tags = $('#search-image-mane').tagEditor('getTags')[0].tags;

            var viewH = $(window).height(),//可见高度
                contentH =$('body').get(0).scrollHeight,//内容高度
                scrollTop =$('body').scrollTop();//滚动高度
            if(container.attr('data-load') === 'true' && scrollTop/(contentH - viewH) >= 0.95){ //到达底部100px时,加载新内容
                container.attr('data-load', 'false');
                $('#is-loaded').text('正在加载');
                if (container.attr('data-method') === 'search') {
                    container.attr('data-page', container.attr('data-page') * 1 + 1);
                    //container.masonry('destory');
                    wall.searchImage({
                        name: name.val(),
                        start_time: start_time.val(),
                        tags: tags,
                        page: container.attr('data-page')
                    }, function (count) {
                        if (count) {
                            container.attr('data-load', 'true');
                        } else {
                            $('#is-loaded').text('加载完成').removeClass('text-warning').addClass('text-success');
                        }
                        $('#aside-search').removeClass('sr-only');
                        $('#aside-tree').addClass('sr-only');
                        $('.shaixuan').show();
                    });
                }
                //
            }
        });
    };
    scrollBottom();

});


$('#images-wall').on('click', '.box .title', function (event) {
    $(event.currentTarget).next('img').trigger('click');
});

// 树形菜单
function Tree(func) {
    var self = this;
    var container = $('#images-wall');
    this.func = func;
    this.api = $('#tree').attr('data-api');
    this.labels = [];
    this.tree = {};
    this.view = {};

    this.show = function (func, labels) {
        labels && (self.labels = labels);
        // 拿到数据
        self.getData(function () {
            // 绘制菜单
            self.paint();
            func();
        });
    };
    // 通过Ajax获取数据，并调用dateToTree转换为树状结构
    this.getData = function (func) {
        $.get(self.api, {
            labels: self.labels
        }, function (data) {
            self.tree = [];
            for (var key in data) {
                self.dateToTree(data[key]);
            }
            func();
        }, 'json');
    };
    // 将键值对格式数据转换为树形结构数据
    this.dateToTree = function (data) {
        // {date: "date_content", type: "type_content", id: 0, name: ""} --> {type_content: {date_content: {id: 0, name: ""}}}
        var type = data.type;
        var date = data.date;
        var name = data.name;
        var d = new Date(Date.parse(date));
        var year = d.Format('yyyy年');
        var month = d.Format('M月');
        typeof (self.tree[type]) === 'undefined' && (self.tree[type] = {});
        typeof (self.tree[type][year]) === 'undefined' && (self.tree[type][year] = {});
        typeof (self.tree[type][year][month]) === 'undefined' && (self.tree[type][year][month] = {});
        typeof (self.tree[type][year][month][date]) === 'undefined' && (self.tree[type][year][month][date] = {});
        if (year == '2000年') {
            self.tree[type][year] = data.id;
        } else {
            self.tree[type][year][month][date][name] = data.id;
        }
    };
    // 递归生成菜单树
    var d = new Date();
    this.year = d.Format('yyyy年');
    this.month = d.Format('M月');
    this.date = d.Format('yyyy-MM-dd');
    var lock = 0;
    var currentlock = 1;
    var type;
    this.toView = function (tree, depth) {
        var view = [];
        depth++;
        for (var key in tree) {
            if (depth === 1) {
                type = '#' + (key === '篮球' ? 'nba' : 'zuqiu ');
            }
            if (typeof (tree[key]) === 'object') {
                // 继续迭代
                if (self.year === key || self.month === key || self.date === key) {
                    var state = {expanded:true};
                    if (self.month === key || self.date === key) {
                        if (type == location.hash && self.date === key) {
                            //state.selected = true;
                            if (currentlock < 4)currentlock = 4;
                        }
                        if (currentlock < 3)currentlock = 3;
                    }
                    view.push({text: key, state:{expanded:true}, nodes: self.toView(tree[key], depth)});
                    if (currentlock < 2)currentlock = 2;
                } else {
                    view.push({text: key, nodes: self.toView(tree[key], depth)});
                }
            } else {
                // if (type == location.hash && currentlock > lock) {
                //     //type = null;
                //     container.attr('game-id', tree[key]);
                //     lock = currentlock;
                // }

                if (key === '2000年') {
                    if (type == location.hash) {
                        state.selected = true;
                        container.attr('game-id', tree[key]);
                    }
                    var text = type.toUpperCase() === '#NBA' ? '篮球新闻' : '足球新闻';
                    view.push({text: text, state:state, game_id: tree[key]});

                } else {
                    view.push({text: key, game_id: tree[key]});
                }
            }
        }
        depth--;
        return view;
    };
    // 绘制树形菜单
    this.paint = function () {
        var depth = 0;
        var view = self.toView(self.tree, depth);
        self.func(view);
    };
}

// 图片墙
// container:jquery对象
// func:生成html并插入页面之后调用等回调
function ImageWall(container, func) {
    var self = this;
    this.func = func;
    this.container = container;
    this.api = container.attr('data-api');

    this.game_id = '';
    this.tag = '';
    this.start_time = '';
    this.end_time = '';
    this.keywords = '';
    // 从比赛id中加载图片
    this.loadFromGame = function (conditions) {
        //game_id, type, year, mouth, date
        self.getImage({
            game_id: conditions.game_id || null,
            type: conditions.type || null,
            year: conditions.year || null,
            mouth: conditions.mouth || null,
            date: conditions.date || null,
        });
    };
    // 通过查询条件加载图片
    this.searchImage = function (data, func) {
        self.container.attr('data-method', 'search');
        $.get(self.api + '/search', data, function (data) {
            for (var key in data) {
                self.container.append(self.dataToHtml(data[key]));
            }
            var new3games = data[0]['new3games'];
            var length = new3games.length;
            var count = length > 3 ? 3 : length;
            for (var i = 0; i < count; i++) {
                $('#new3games button:eq(' + i + ')').text(new3games[i]['name'] + '(' + new3games[i]['date'] + ')').attr('data-search-id', new3games[i]['id']);
            }
            $('#new3games').show();
            self.func(data.length);
            func(data.length);
        }, 'json');
    };
    // ajax加载图片并插入到图片墙
    this.getImage = function (data) {
        self.container.attr('data-method', 'index');
        $.get(self.api, data, function (data) {
            for (var key in data) {
                self.container.append(self.dataToHtml(data[key]));
            }
            self.func(data.length);
        }, 'json');
        self.container.attr('data-load', 'false');
    };
    // 获取到的json数据转换为html数据
    this.dataToHtml = function (data) {
        // {"id": "1", "name": "test", "url": "http:\/\/your_path", "game_id": "1"} --> html
        var section = $('<section>').addClass('box col-md-3 col-sm-4 col-xs-6');
        section.attr('type', data.type);
        section.attr('game-id', data.game_id);
        section.attr('image-id', data.id);
        section.attr('image-name', data.name);
        section.attr('image-tags', JSON.stringify(data.tags.map( function (tag) {
            return tag.name;
        })));
        var title = $('<div>').addClass('title').html($('<h3>').text(data.name));
        var image = $('<img>').addClass('img-thumbnail');
        image.attr('src', data.thumb);
        image.attr('title', data.name);
        image.attr('data-original', data.url);
        var option = $('<div>').addClass('option');
        var ul = $('<ul>');
        var remove = $('<li title="删除" class="remove"><span class="glyphicon glyphicon glyphicon-trash" aria-hidden="true"></span></li>');
        var edit = $('<li title="编辑" class="edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true" data-toggle="modal" data-target="#image-editor"></span></li>');
        return section.append(title, image, option.append(ul.append(remove, edit)));
    };
    this.clear = function () {
        self.container.empty();
    }
}
