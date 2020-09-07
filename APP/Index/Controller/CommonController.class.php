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
        $navbar=M('index_config')->where(array('config_name'=>'navbar'))->select();
        $this->assign('navbar',$navbar);
    }
}