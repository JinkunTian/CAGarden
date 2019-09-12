<?php
namespace Appointment\Controller;
use Think\Controller;
class CommonController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        if (!(isset($_SESSION['username']) && isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION[C('PASSWORD_KEY')]) )) {
            $logs=array(
                'part'=>3,
                'truename'=>'游客',
                'username'=>'游客',
                'ip'=>get_client_ip(),
                'agent'=>$_SERVER["HTTP_USER_AGENT"],
                'url'=>$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
                'date' => date('y-m-d H:i:s'));
            M('system_logs')->add($logs);
            session('req_url',$_SERVER["REQUEST_URI"]);
            $this->redirect('/Login');
        }else{
            $logs=array(
                'part'=>3,
                'truename'=>session('name'),
                'username'=>session('username'),
                'ip'=>get_client_ip(),
                'agent'=>$_SERVER["HTTP_USER_AGENT"],
                'url'=>$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
                'date' => date('y-m-d H:i:s'));
            M('system_logs')->add($logs);
        }
    }
}