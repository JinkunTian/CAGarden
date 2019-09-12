<?php
namespace Login\Controller;
use Think\Controller;
class IndexController extends Controller {
    private function getData($url, $token = "")
	{
		$ch = curl_init();
		$timeout = 3;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'token:' . $token
		));
		$handles = curl_exec($ch);
		curl_close($ch);
		return json_decode($handles,true);
	}
    private function login_by_qz($username,$password){
        $login_data['success']=false;
        header("Content-type: text/html; charset=utf-8");
        //超时重发设置为10次
        for($i=0;$i<10;$i++){
            if($data = $this->getData("http://cquccjw.minghuaetc.com/cqdxcskjxy/app.do?method=authUser&xh=" . $username . "&pwd=" . $password)){
                $i=12;
            }
        }
        if($i==10){
            $this->error('强智教务系统响应超时，请稍后重试');
        }else{
            if($data['token'] == -1){
                $login_data['success']=false;
                $login_data['err_info']=$data['msg'];
            }else{
                $login_data['success']=$data['success'];
                $login_data['name']=$data['userrealname'];
                $login_data['username']=$data['user']['useraccount'];
                $login_data['college']=$data['userdwmc'];
            }
        }
        return $login_data;
            
    }
    // 显示登录页面
    public function index(){

        if ((isset($_SESSION['id'])&&isset($_SESSION[C('PASSWORD_KEY')])&&isset($_SESSION['name'])&& isset($_SESSION['userType']) )) {
            switch($login_data['userType']){
                case 'guest':
                    $this->redirect('/Appointment/Index');break;
                case 'garden':
                    $this->redirect('/Garden/Index');break;
            }
        }
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
            $pwd = I('password','','md5');
            $map['username']=array('eq',$username);
            //$map['status']=array('gt',0);
            $user = M('users')->where($map)->find();

            $log_data =array(
                'uid' => $user['uid'],
                'last_login' => date('y-m-d H:i:s'),
                'last_ip' => get_client_ip(),
            );
            $login_data=array(
                'username'=>$username,
            );

            //session_unset();
            
            if($user){//有该用户信息
                if($user['userType']=='garden'){//协会成员必须使用账户密码认证
                    if ($user['password'] == md5($user['salt'].$pwd)) {
                        $user_extend=M('garden_users_extend')->where(array('uid'=>$user['uid']))->find();
                        $log_data['certify']='password';
                        $login_data['success']=true;
                        $login_data['id']=$user['uid'];
                        $login_data['name']=$user['truename'];
                        $login_data['userType']=$user['userType'];
                        //ar_dump($user_extend);die();
                        session('admin',$user_extend['type']);
                    }else{
                        $this->error('CA帐号或者密码错误');
                    }
                    //其他身份认证
                }else if($user['password'] == md5($user['salt'].$pwd)) {
                    $log_data['certify']='password';
                    $login_data['success']=true;
                    $login_data['id']=$user['uid'];
                    $login_data['name']=$user['truename'];
                    $login_data['username']=$username;
                    $login_data['userType']=$user['userType']; 
                }else if(C('USE_QIANGZHI_JIAOWU')){
                    //如果启用了强智教务认证，一般用户既可以使用强智登陆
                    $login_data=$this->login_by_qz(I('username'),I('password'));
                    if($login_data['success']){
                        $log_data['certify']='qiangzhi';
                        $login_data['id']=$user['uid'];
                        $login_data['userType']=$user['userType'];
                        //也可以使用密码登陆
                    }else {
                        $this->error('帐号或者密码错误！强智教务系统请到教务处找回密码！'.$login_data['err_info']);
                    }
                }else{
                    $login_data['success']=false;
                    $this->error('用户名或密码错误');
                }//登陆成功
                if($login_data['success']){
                    M('users')->save($log_data);
                    
                    session('id',$login_data['id']);
                    session('username',$login_data['username']);
                    session('name',$login_data['name']);
                    session('userType',$login_data['userType']);
                    session(C('PASSWORD_KEY'),true);//防止跨站
                    if($url=session('req_url')){
                        session('req_url',null);
                        $this->redirect($url);
                    }else{
                        switch($login_data['userType']){
                            case 'guest':
                                $this->redirect('/Appointment/Index');break;
                            case 'garden':
                                $this->redirect('/Garden/Index');break;
                        }
                    }
                }
            }else{//协会没有该用户的信息，添加新用户
                //如果启用了强智学工认证则走强智学工认证，
                if(C('USE_QIANGZHI_JIAOWU')){
                    $login_data=$this->login_by_qz(I('username'),I('password')); //var_dump($login_data);
                    if($login_data['success']){
                        session('userType','guest');
                        session(C('PASSWORD_KEY'),true);//防止跨站
                        session('username',$login_data['username']);
                        session('college',$login_data['college']);
                        session('name',$login_data['name']);
                        $this->redirect('/Login/Reg');
                    }else{
                        $this->error('强智教务系统：'.$login_data['err_info']);
                    }
                }else{
                    session('username',I('username'));
                    session(C('PASSWORD_KEY'),true);//防止跨站
                    $this->redirect('/Login/Reg');
                }
            }
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
    public function logout () {
        session_unset();
        session_destroy();
        $this->redirect('/Login');
    }
}