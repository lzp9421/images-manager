<?php

use ImsGames as Games;
use ImsTags as Tags;
use Phalcon\Mvc\Url;

class ImsController extends ImsBaseController
{
    public function indexAction()
    {

        $url = new Url();

        // HTML 头部的js资源
        $headerCollection = $this->assets->collection("header")
            ->setPrefix($this->config->server->asset)
            ->addCss('bootstrap/css/bootstrap.min.css')
            ->addCss('treeview/bootstrap-treeview.min.css')
            ->addCss('viewer/viewer.min.css')
            ->addCss('tagEditor/css/jquery.tag-editor.css')
            ->addCss('datetimepicker/css/bootstrap-datetimepicker.min.css')
            ->addCss('sweetalert/sweetalert.css')
            ->addCss('css/index.css')
            ->join(true);

        // HTML尾部的js资源
        $footerCollection = $this->assets->collection("footer")
            ->setPrefix($this->config->server->asset)
            ->addJs('jquery/jquery.min.js')
            ->addJs('bootstrap/js/bootstrap.min.js')
            ->addJs('treeview/bootstrap-treeview.min.js')
            ->addJs('viewer/viewer.min.js')
            ->addJs('masonry/masonry.pkgd.min.js')
            ->addJs('imagesLoaded/imagesloaded.pkgd.min.js')
            ->addJs('tagEditor/js/jquery.caret.min.js')
            ->addJs('tagEditor/js/jquery.tag-editor.js')
            ->addJs('datetimepicker/js/bootstrap-datetimepicker.min.js')
            ->addJs('sweetalert/sweetalert.min.js')
            ->addJs("js/index.js");
        $url->setBaseUri($this->config->server->domain);
        $this->view->url = $url;
    }

    public function migrateAction($test = null)
    {
        $nba_label = array('NBA','美国男篮','|',
            '西部', '勇士','湖人', '火箭','马刺', '雷霆',  '快船','开拓者', '森林狼','小牛','鹈鹕','国王','爵士', '灰熊', '掘金','太阳','|',
            '东部', '骑士','篮网','公牛','尼克斯','猛龙','步行者','凯尔特人','76人','老鹰','热火',       '雄鹿',   '活塞', '奇才',  '黄蜂', '魔术','|',
            'CBA', '中国男篮', '广东', '山东', '北京', '新疆', '辽宁', '广厦', '深圳', '浙江', '八一', '山西', '广州', '江苏', '福建', '吉林', '天津', '上海', '青岛','四川','北控','同曦','中国女篮','|',
            '库里','杜兰特','詹姆斯','欧文','哈登','威少','林书豪','韦德','安东尼','莱昂纳德','保罗','格里芬','利拉德','安东尼戴维斯','保罗乔治','霍华德','罗斯','诺维斯基','麦基','恩比德','易建联','周琦','科比','姚明','奥尼尔','麦迪','乔丹','艾弗森','卡特','加内特','邓肯','|',
            '比赛集锦','比赛录像','十佳球','篮球公园','NBA最前线','NCAA','欧篮','SBL','CUBA','原创','花边','转载','深度','精华','彩经','足球','其他','五大囧','|','奥运','中国奥运','置顶','|',
        );
        foreach ($nba_label as $label) {
            $tag = new Tags;
            $tag->type = '篮球';
            $tag->name = $label;
            //$tag->save();
        }


        $zuqiu_label = array('英超', '曼联', '切尔西', '阿森纳', '曼城', '利物浦', '热刺' ,'埃弗顿' ,'南安普顿' ,'斯托克城' ,'西汉姆' ,'斯旺西' ,'西布朗' ,'水晶宫' ,'桑德兰' ,'莱斯特城' ,'伯恩茅斯' ,'沃特福德','伯恩利','米德尔斯堡','赫尔城'  ,'|',
            '西甲', '皇家马德里', '巴塞罗那', '瓦伦西亚','马德里竞技','毕尔巴鄂' ,'皇家社会' ,'维拉利尔' ,'塞维利亚' ,'西班牙人'  ,'格拉纳达'  ,'马拉加' ,'塞尔塔'  ,'拉科鲁尼亚' ,'埃瓦尔' ,'贝蒂斯' ,'希洪竞技' ,'拉斯帕尔马斯','阿拉维斯','莱加内斯','奥萨苏纳','|',
            '意甲', 'AC米兰', '国际米兰', '尤文图斯', '罗马', '那不勒斯','佛罗伦萨'  ,'都灵' ,'拉齐奥' ,'热那亚' ,'桑普' ,'亚特兰大' ,'乌迪内斯' ,'切沃' ,'莎索罗' ,'恩波利' ,'巴勒莫'  ,'博洛尼亚','卡利亚里','克罗托内','佩斯卡拉' ,'|',
            '德甲', '拜仁慕尼黑', '多特蒙德', '勒沃库森', '沙尔克','门兴' ,'沃尔夫斯堡' ,'柏林赫塔' ,'奥格斯堡' ,'美因茨'  ,'不莱梅' ,'霍芬海姆'  ,'汉堡' ,'法兰克福' ,'科隆' ,'因戈尔施塔特','达姆施塔特','弗赖堡','莱比锡红牛','|',
            '法甲','巴黎圣日耳曼','摩纳哥','|',
            '中超', '中国男足', '广州恒大', '北京国安', '山东鲁能',  '上海申花', '江苏苏宁', '广州富力', '上海上港', '辽宁宏运',  '天津泰达', '长春亚泰', '河南建业','重庆力帆','河北华夏','延边富德','贵州恒丰','天津权健','中国女足','|',
            '中甲','石家庄永昌','杭州绿城','上海申鑫','北京人和','北京北控','浙江毅腾','大连一方','内蒙古中优','武汉卓尔','青岛黄海','新疆雪豹','深圳佳兆业','大连超越','梅州客家','丽江飞虎','保定容大','|',
            '中乙','青岛中能','湖南湘涛','江西联盛','四川隆发','银川贺兰山','河北精英','梅县铁汉','沈阳城市','成都钱宝','黑龙江火山鸣泉','深圳人人','内蒙古包头南郊','苏州东吴','北京理工','南通支云','海南博盈','江苏盐城鼎立','沈阳东进','上海聚运动','大连博阳','陕西长安竞技','上海申梵','吉林百嘉','镇江华萨','|',
            '欧冠', '欧联杯', '亚冠', 'J联赛','K联赛','阿甲','巴甲','解放者杯','荷甲','葡超','俄超','苏超','澳超','MLS','C罗','梅西','游戏','|',
            '西班牙','英格兰','德国','意大利','法国','葡萄牙','俄罗斯','比利时','克罗地亚','瑞士','罗马尼亚','阿尔巴尼亚','威尔士','斯洛伐克','乌克兰','北爱尔兰','波兰','土耳其','捷克','爱尔兰','瑞典','冰岛','匈牙利','奥地利','欧洲杯','世界杯','亚洲杯','美洲杯','巴西','阿根廷','日本','韩国','美国','乌拉圭','墨西哥','澳大利亚','科特迪瓦','喀麦隆','加纳','智利','哥伦比亚','希腊','哥斯达黎加','厄瓜多尔','洪都拉斯','波黑','伊朗','尼日利亚','阿尔及利亚','荷兰','|',
            '比赛集锦','比赛录像','十佳球','天下足球','英超精华','足球之夜','冠军欧洲','其他','原创','深度','精华','转会','花边','足彩','转载','每轮集锦','篮球','|','奥运','中国奥运','置顶');
        foreach ($zuqiu_label as $zuqiu) {
            $tag = new Tags;
            $tag->type = '足球';
            $tag->name = $zuqiu;
            //$tag->save();
        }

        if ($test === 'test') {

            $nba_games = [
                ['date' => '2017-04-15', 'name' => '朝鲜VS韩国', 'type' => '篮球'],
                ['date' => '2017-04-16', 'name' => '中国VS韩国', 'type' => '篮球'],
                ['date' => '2017-04-17', 'name' => '中国VS美国', 'type' => '篮球'],
                ['date' => '2017-04-18', 'name' => '朝鲜VS韩国', 'type' => '篮球'],
                ['date' => '2017-04-19', 'name' => '中国VS韩国', 'type' => '篮球'],
                ['date' => '2017-04-10', 'name' => '中国VS美国', 'type' => '篮球'],
            ];
            foreach ($nba_games as $val) {
                $date = urlencode($val['date']);
                $name = urlencode($val['name']);
                $type = urlencode($val['type']);
                //$result = file_get_contents("http://${_SERVER['HTTP_HOST']}/games/create?date=${date}&name=${name}&type=${type}");
                //echo($result);
            }
            $football_games = [
                ['date' => '2017-04-15', 'name' => '美国VS日本', 'type' => '足球'],
                ['date' => '2017-04-14', 'name' => '日本VS德国', 'type' => '足球'],
                ['date' => '2017-04-16', 'name' => '法国VS美国', 'type' => '足球'],
                ['date' => '2017-04-17', 'name' => '美国VS日本', 'type' => '足球'],
                ['date' => '2017-04-18', 'name' => '日本VS德国', 'type' => '足球'],
                ['date' => '2017-04-19', 'name' => '法国VS美国', 'type' => '足球'],
            ];

            foreach ($football_games as $val) {
                $date = urlencode($val['date']);
                $name = urlencode($val['name']);
                $type = urlencode($val['type']);
                //$result = file_get_contents("http://${_SERVER['HTTP_HOST']}/games/create?date=${date}&name=${name}&type=${type}");
                //echo($result);
            }
        }
        exit;
    }
}
