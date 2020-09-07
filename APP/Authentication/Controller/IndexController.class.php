<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020年8月31日
 * @Description: 认证控制器
 ***/
namespace Authentication\Controller;
use Think\Controller;
class IndexController extends Controller {

    private function login_success($userType){
        $login_result['result']='success';

        if($url=session('req_url')){
            session('req_url',null);
            $login_result['url']=$url;
        }else{
            switch($userType){
                case 'guest':
                    $login_result['url']=U('/Appointment/Index');break;
                case 'garden':
                    $login_result['url']=U('/Garden/Index');break;
                default:
                    $login_result['result']='error';
                    $login_result['msg']='未知用户类型';break;
            }
        }
        return_json($login_result);

    }
    /**
     * 默认数据库账户密码登陆
     */
    private function login_func_default_passwd($username,$password){

        $user = M('users')->where(array('username'=>$username))->find();
        $login_log=array(
            'uid'=> $user['uid'],
            'username' => $username,
            'certify'  => 'password',
            'ip' => get_client_ip(),
            'datetime' => date('y-m-d H:i:s')
        );

        if ($user['password'] == md5($user['salt'].$password)) {
            if($user['userType']=='garden'){
                $user_extend=M('garden_users_extend')->where(array('uid'=>$user['uid']))->find();
                session('admin',$user_extend['is_admin']);
            }
            
            //存储Session
            session('userType',$user['userType']);
            session(C('PASSWORD_KEY'),true);//防止跨站
            session('username',$user['username']);
            session('truename',$user['truename']);
            session('uid',$user['uid']);

            //存储日志
            $login_log['result']='Success';
            M('system_login_logs')->add($login_log);
            $log_data['certify']='password';
            $log_data['last_login']=date('y-m-d H:i:s');
            $log_data['last_ip']=get_client_ip();
            M('users')->where(array('uid'=>$user['uid']))->save($log_data);
            $this->login_success($user['userType']);

        }else{
            $login_log['result'] = 'Failure';
            M('system_login_logs')->add($login_log);

            $login_result['result']='failure';
            $login_result['msg']='帐号或者密码错误！';
            return_json($login_result);
            
        }
    }
    private function login_func_qiangzhi_jiaowu(){
        
    }
    private function login_func_ldap(){
        
    }
    // 显示登录页面
    public function index(){

        $this->assign('ENABLE_GEETEST',C('ENABLE_GEETEST'));
        $this->assign('USE_QIANGZHI_JIAOWU',C('USE_QIANGZHI_JIAOWU'));
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
            $password = I('password');
            $pwd_md5 = I('password','','md5');

            switch(C('AUTHENTICATION_FUNCTION')){
                case 'by_default_passwd':
                    $this->login_func_default_passwd($username,$pwd_md5);break;
                // case 'by_qiangzhi_jiaowu':
                //     $this->login_func_qiangzhi_jiaowu($username,$password);break;
                // case 'by_ldap':
                //     $this->login_func_ldap($username,$password);break;
                // default:
                //     $this->login_func_default_passwd($username,$pwd_md5);break;
            }           
        } else {
            $login_result['result']='error';
            $login_result['msg']='验证码错误！';
            return_json($login_result);
            // $this->error('验证码错误');
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
    public function logout () {
        session_unset();
        session_destroy();
        $this->redirect('/Authentication');
    }
}