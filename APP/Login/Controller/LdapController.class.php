<?php
namespace Login\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020年4月21日
 * @Description: LDAP域控支持模块，将用户从普通数据库添加至LDAP目录
 ***/
class LdapController extends Controller {
    
    public function index(){
         
    }

    public function Update(){
        $this->display();
    }
    public function Updatepost(){
        if($sess_user=session('upgrade_to_ldap_user')){
            //构建信息
            $UserInfo['username']=$sess_user['username'];    //用户名
            $UserInfo['truename']=$sess_user['truename'];    //昵称、真实姓名、显示名称
            $UserInfo['password']=I('newpassword');    //密码
            $UserInfo['mail']=$sess_user['email'];
            $UserInfo['telephone']=$sess_user['mobile'];
            $UserInfo['qq']=$sess_user['qq'];
            if($sess_user['userType']=='garden'){
                $user_extend=M('garden_user_view')->where(array('username'=>$sess_user['username']))->find();

                $UserInfo['description']='协会成员';
                $UserInfo['department']= $user_extend['dname'];
                $UserInfo['position']=$user_extend['position'];
                $UserInfo['company'] = C('SITE_NAME');
                $UserInfo['office'] = $user_extend['mname'];
                $UserInfo['memberOf'] = "CN=Members,".C('BASE_DN');
                $UserInfo['is_admin']=$user_extend['is_admin']; 
            }else{
                $UserInfo['description']='注册用户';
                $UserInfo['memberOf'] = "CN=Students,".C('BASE_DN');
            }
            $user_add=ldap_add_user(C('LDAP_SERVER_HOST'),C('BASE_DN'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'),$UserInfo);
            if($user_add['result']){
                //将新密码回注至网站数据库，防止某天停用LDAP后密码不同步。
                $password = I('newpassword','','md5');
                $salt=md5(time());
                $data =array(
                    'password' => md5($salt.$password),
                    'salt' => $salt,
                );
                M('users')->where(array('uid'=>$sess_user['uid']))->save($data);
                $this->success('账户升级成功，请重新登录即可','/Login');
            }else{
                $this->error('账户升级失败，'.$user_add['info'],'/Login/Ldap/Update',5);
            }
        }else{
            $this->redirect('/Login');
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