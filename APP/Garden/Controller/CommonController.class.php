<?php
namespace Garden\Controller;
use Think\Controller;
class CommonController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        if (!(isset($_SESSION['id'])&&isset($_SESSION['6c440f695619e361040767ac9f6fb619'])&&isset($_SESSION['name']))) {
            $this->redirect('/Garden/Login');
        }
        $logs=array(
        	'part'=>1,
        	'truename'=>session('name'),
        	'username'=>session('username'),
        	'ip'=>get_client_ip(),
        	'agent'=>$_SERVER["HTTP_USER_AGENT"],
        	'url'=>$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
        	'date' => date('y-m-d H:i:s'),
        );
        M('system_logs')->add($logs);
    }
}