<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日
 * @Description: 用户注册控制器
 ***/
namespace Authentication\Controller;
use Think\Controller;
class RegController extends CommonController {
    // 显示登录页面
    public function index(){
        $majors=M('common_majors')->where(array('status'=>'1'))->order('institute desc')->select();
        $this->assign('username',I('username'));
        $this->assign('password',I('password'));
        $this->assign('majors',$majors);
        $this->assign('ENABLE_GEETEST',C('ENABLE_GEETEST'));
        $this->display();
    }

    /**
     * registerpost方法处理用户提交的注册信息
     */
    public function registerpost(){

        $reg_result['result']='error';
        $reg_result['msg']='未知错误！';
        

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
                    'username' => I('username'),
                    'img'   => '/images/hi.png',
                    'mobile' => I('mobile'),
                    'qq' => I('qq'),
                    'email' => I('email'),
                    'major' => I('major'),
                    'reg_ip' => get_client_ip(),
                    'addtime'   =>  date('y-m-d H:i:s'),
                    'college'=> I('college'),
                    'userType'=>'guest',
                );
                $checkExis=M('users')->where(array('username'=>session('username')))->find();
                if($checkExis){
                    $this->error('用户已经存在！');
                }
                $data['salt']=md5(time());
                $password = I('password','','md5');
                $data['password']=md5($data['salt'].$password);

                //写入信息到数据库
                $result=M('users')->add($data);
                if (!($result===false)) {
                    $reg_result['result']='success';
                    $reg_result['msg']='注册成功！请登陆系统！';
                }else{
                    $reg_result['msg']='注册失败！请联系管理员';
                } 
                
            }else{
                $reg_result['msg']='验证码错误';
            }
        }else{
            $reg_result['msg']='非法请求！';
        }

        return_json($reg_result);
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