<?php
namespace Garden\Controller;
use Think\Controller;
class LoginController extends Controller {
    // 显示登录页面
    public function index(){
        $this->assign('ENABLE_GEETEST',C('ENABLE_GEETEST'));
        $this->display();
    }

        //登陆信息判断
    public function login() {
        if (!IS_POST) die('页面不存在');

        //行为验证码
        if(C('ENABLE_GEETEST')){
            require_once './ThinkPHP/Library/Vendor/Geetest/class.geetestlib.php';

            $GtSdk = new \GeetestLib(C('CAPTCHA_ID'),C('PRIVATE_KEY'));

            $data = array(
                    "user_id" => $_SESSION['gt_user_id'], # 网站用户id
                    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
                );
            if ($_SESSION['gtserver'] == 1) {   //服务器正常
                $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
                if ($result) {
                    $validate_response=true;
                } else{
                    $validate_response=false;
                }
            }else{  //服务器宕机,走failback模式
                if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                    $validate_response=true;
                }else{
                    $validate_response=false;
                }
            }
        }else{
            $validate_response=true;
        }
        


        if ($validate_response) {
            $username = I('username');
            $pwd = I('password','','md5');
            $map['username']=array('eq',$username);
            $map['status']=array('gt',0);
            $user = M('garden_users')->where($map)->find();
            if (!$user || $user['password'] != md5($user['salt'].$pwd)) {
                $this->error('帐号或者密码错误');
            };
            $data =array(
                'uid' => $user['uid'],
                'last_login' => date('y-m-d H:i:s'),
                'last_ip' => get_client_ip(),
            );
            M('garden_users')->save($data);

            session_unset();

            session('id',$user['uid']);
            session('username',$user['username']);
            session('6c440f695619e361040767ac9f6fb619',true);//防止跨站
            if($user['status']==1){
            	session('admin',$user['type']);
            }else{
            	session('admin',1);
            }
            
            session('name',$user['truename']);

            $this->redirect('/Garden/Index');
        } else {
         $this->error('验证码错误');
        }
    }
    public function StartCaptchaServlet(){

        if(C('ENABLE_GEETEST')){

            require_once './ThinkPHP/Library/Vendor/Geetest/class.geetestlib.php';

            $GtSdk = new \GeetestLib(C('CAPTCHA_ID'),C('PRIVATE_KEY'));

            session_start();

            $data = array(
                    "user_id" => microtime(), # 网站用户id
                    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
                );

            $status = $GtSdk->pre_process($data, 1);
            $_SESSION['gtserver'] = $status;
            $_SESSION['gt_user_id'] = $data['user_id'];
            echo $GtSdk->get_response_str();
        }

        
    }
}