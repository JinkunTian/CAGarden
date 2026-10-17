<?php
namespace Index\Controller;
use Think\Controller;
class CommonController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        sys_log('Index');
        /**
		 * 首页导航栏
		*/
        $navbar=M('index_config')->where(array('config_name'=>'navbar'))->order('cid ASC')->select();
        $navbarFirst=array();
        $navbarMiddle=array();
        $navbarLast=array();
        $navbarWorkstudy=array();
        foreach ((array)$navbar as $item) {
            if ($item['config_value_2'] === '加入我们') {
                $navbarFirst[]=$item;
            } elseif ($item['config_value_2'] === '预约维修') {
                $navbarLast[]=$item;
            } elseif ($item['config_value_2'] === '报名勤工助学') {
                $navbarWorkstudy[]=$item;
            } else {
                $navbarMiddle[]=$item;
            }
        }
        // 统一桌面与移动端的视觉、键盘导航顺序。
        $navbar=array_merge($navbarFirst,$navbarMiddle,$navbarLast);
        // 勤工助学是独立入口，固定紧跟“协会介绍”，避免后台编辑导航时改变约定位置。
        if ($navbarWorkstudy) {
            $ordered=array();
            $inserted=false;
            foreach ($navbar as $item) {
                $ordered[]=$item;
                if (!$inserted && $item['config_value_2'] === '协会介绍') {
                    $ordered=array_merge($ordered,$navbarWorkstudy);
                    $inserted=true;
                }
            }
            if (!$inserted) {
                $ordered=array_merge($navbarWorkstudy,$ordered);
            }
            $navbar=$ordered;
        }
        $this->assign('navbar',$navbar);
    }
}
