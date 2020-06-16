<?php
namespace Login\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日
 * @Description: 用户注册控制器
 * @Update：2020/6/16 添加域控支持，将注册信息同步到LDAP目录
 ***/
class RegController extends CommonController {
    // 显示登录页面
    public function index(){
        $majors=M('common_majors')->where(array('status'=>'1'))->select();
        $institute=M('common_majors')->distinct(true)->field('institute')->select();
        if(C('USE_QIANGZHI_JIAOWU')){
            $this->assign('student_institute',session('college'));
        }else{
            $this->assign('student_institute','');
        }
        $this->assign('institute',$institute);
        $this->assign('majors',$majors);
        $this->assign('ENABLE_GEETEST',C('ENABLE_GEETEST'));
        $this->assign('USE_QIANGZHI_JIAOWU',C('USE_QIANGZHI_JIAOWU'));
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
                    //'truename' => I('truename'),
                    'username' => session('username'),
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
                if(C('USE_QIANGZHI_JIAOWU')){
                    $data['truename']=session('name');
                }else{
                    $data['truename']=I('truename');
                }
                $data['salt']=md5(time());
                $password = I('password','','md5');
                $data['password']=md5($data['salt'].$password);

                if(C('USE_LDAP')){
                    $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
                    if($ds['result']){
                        /**
                         * 构建用户信息
                         */
                        $major=M('common_majors')->where(array('mid'=>$data['major']))->find();

                        $UserInfo['username']=$data['username'];    //用户名
                        $UserInfo['truename']=$data['truename'];    //昵称、真实姓名、显示名称
                        $UserInfo['password']=I('password');    //密码
                        $UserInfo['mail']=$data['email'];
                        $UserInfo['telephone']=$data['mobile'];
                        $UserInfo['qq']=$data['qq'];
                        $UserInfo['office'] = $major['mname'];
                        $UserInfo['memberOf'] = "CN=Students,".C('BASE_DN');

                        $user_add=ldap_add_user(C('LDAP_SERVER_HOST'),C('BASE_DN'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'),$UserInfo);
                        if($user_add['result']){
                            $result=M('users')->add($data);
                            if (!($result===false)) {
                                session('id',$result);
                                session('name',I('truename'));
                                if(session('req_url')){
                                    $url=session('req_url');
                                    session('req_url',null);
                                    $this->success('注册成功！',$url);
                                }else{
                                    $this->success('注册成功！',U('/Appointment'));
                                }
                            }else{
                                $this->error('注册失败！请联系管理员');
                            } 
                        }else{
                            $this->error('注册失败！'.$user_add['info'],'/Login/Ldap/Update',5);
                        }
                    }else{
                        $this->error('与LDAP服务器通信失败！');
                    }
                }else{
                    //只写入信息到数据库
                    $result=M('users')->add($data);
                    if (!($result===false)) {
                        session('id',$result);
                        session('name',I('truename'));
                        if(session('req_url')){
                            $url=session('req_url');
                            session('req_url',null);
                            $this->success('注册成功！',$url);
                        }else{
                            $this->success('注册成功！',U('/Appointment'));
                        }
                    }else{
                        $this->error('注册失败！请联系管理员');
                    } 
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