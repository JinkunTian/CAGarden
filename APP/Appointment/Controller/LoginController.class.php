<?php
namespace Appointment\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       tianjinkun@spyder.link
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日15:32:56
 * @Description: 计算机协会预约维修平台（前台）登入控制器
 ***/
class LoginController extends Controller {
    /**
     * index方法显示登入页面
     */
    public function index(){
        $this->assign('ENABLE_GEETEST',C('ENABLE_GEETEST'));
        $this->display();
    }

    /**
     * login方法处理提交的登入信息
     */
    public function login() {
        if(IS_POST){
            /**
             * GEETEST行为验证码
             */
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
            
            /**
             * 验证成功
             */
            if($validate_response){
                $username = I('username');
                $pwd = I('password','','md5');
                $user = M('appointment_users')->where(array('number' => $username))->find();
                if ($user) {

                    if($user['password'] != md5($user['salt'].$pwd)){
                        $this->error('帐号或者密码错误');
                    }else{

                        session_unset();

                        session('guest_id',$user['guest_id']);
                        session('number',$user['number']);
                        session('guest',true);
                        session('name',$user['truename']);

                        $data =array(
                            'guest_id' => $user['guest_id'],
                            'last_login' => date('y-m-d H:i:s'),
                            'last_ip' => get_client_ip(),
                        );
                        M('appointment_users')->save($data);

                        $this->redirect('/Appointment/Index');
                    }
                    
                }else{
                    //用户不存在，引导用户前去注册
                    $this->redirect('/Appointment/Login/register',array('number'=>$username));
                }
            }else{
                $this->error('验证码错误');
            }
        }else{
            $this->error('页面不存在');
        }
    }

    /**
     * register方法显示注册页面
     */
    public function register(){

        if(isset($_GET['number'])){
            //由登入界面引导至注册的，会带有用户学号
            $check=M('appointment_users')->where(array('number'=>I('number')))->select();
            //如果该学号存在，则重新引导用户登入，不进行注册
            if($check){
                $this->error('密码错误！',U('/Appointment/Login'));
            }else{
                $this->assign('number',I('number'));
            }
        }
        //渲染学校专业信息至页面
        $majors=M('common_majors')->where(array('status'=>'1'))->select();
        $this->assign('majors',$majors);
        $this->assign('ENABLE_GEETEST',C('ENABLE_GEETEST'));
        $this->display();
        
    }
    /**
     * registerpost方法处理用户提交的注册信息
     */
    public function registerpost(){
        if(IS_POST){
            /**
             * GEETEST行为验证码
             */
            if(C('ENABLE_GEETEST')){
                require_once './ThinkPHP/Library/Vendor/Geetest/class.geetestlib.php';

                $GtSdk = new \GeetestLib(C('CAPTCHA_ID'),C('PRIVATE_KEY'));

                session_start();
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

            if($validate_response){
                $data = array(
                    'truename' => I('truename'),
                    'number' => I('number'),
                    'mobile' => I('mobile'),
                    'qq' => I('qq'),
                    'email' => I('email'),
                    'major' => I('major'),
                    //'room' => I('room'),  //寝室或校区信息，需要时删掉注释
                    'reg_ip' => get_client_ip(),
                    'addtime'   =>  date('y-m-d H:i:s'),
                );
                $checkExis=M('appointment_users')->where(array('number'=>I('number')))->select();
                if(!$checkExis){
                    $data['salt']=md5(time());
                    $password = I('password','','md5');
                    $data['password']=md5($data['salt'].$password);
                    $result=M('appointment_users')->add($data);
                    if (!$result===false) {
                        $this->success('注册成功！',U('/Appointment/Login/'));
                    }else{
                        $this->error('注册失败！请联系管理员');
                    }
                }else{
                    $this->error('该用户名已存在！');
                }
            }else{
                $this->error('验证码错误');
            }
        }else{
            $this->error('页面不存在');
        }
    }

    /**
     * 初始化行为验证
     */
    public function StartCaptchaServlet(){

        if(C('ENABLE_GEETEST')){

            require_once './ThinkPHP/Library/Vendor/Geetest/class.geetestlib.php';

            $GtSdk = new \GeetestLib(C('CAPTCHA_ID'),C('PRIVATE_KEY'));

            session_start();

            $data = array(
                    "user_id" => microtime(), # 网站用户id
                    "client_type" => "h5", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
                );

            $status = $GtSdk->pre_process($data, 1);
            $_SESSION['gtserver'] = $status;
            $_SESSION['gt_user_id'] = $data['user_id'];
            echo $GtSdk->get_response_str();
        }

        
    }
}