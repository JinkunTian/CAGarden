<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    // 显示登录页面
    public function index(){
        $this->display();
    }

        //登陆信息判断
    public function login() {
        if (!IS_POST) halt('页面不存在');

        //行为验证码
        //import('Home.Class.Geetestlib');
        //$PRIVATE_KEY = "28fe09475d641de19b0c5fa2c8562891";
        //$geetest = new \GeetestLib ( $PRIVATE_KEY );
        //$validate_response = $geetest->geetest_validate ( @$_POST ['geetest_challenge'],
        //@$_POST ['geetest_validate'], @$_POST ['geetest_seccode'] );
        //if ($validate_response) {
      	if (true) {
            $username = I('username');
            $pwd = I('password','','md5');
            $user = M('users')->where(array('username' => $username))->find();
            if (!$user || $user['password'] != md5($user['salt'].$pwd)) {
                $this->error('帐号或者密码错误');
            };
            $data =array(
                'uid' => $user['uid'],
                'last_login' => time(),
                'last_ip' => get_client_ip(),
            );
            M('users')->save($data);

            session('id',$user['uid']);
            session('username',$user['username']);
            session('logintime',date('Y-m-d H:i:s',$user['last_login']));
            session('loginip',$user['last_ip']);
            session('admin',$user['user_classify']);
            session('name',$user['truename']);

            $this->redirect('/Home/Index');
        } else {
         $this->error('验证码错误');
        }
    }
}