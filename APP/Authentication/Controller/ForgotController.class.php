<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020年8月31日
 * @Description: 找回密码控制器
 ***/
namespace Authentication\Controller;
use Think\Controller;
class ForgotController extends Controller { 
    /**
     * 发送邮件子函数
     */
    private function send_mail($address,$name,$title,$context){
        require_once './ThinkPHP/Library/Vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once './ThinkPHP/Library/Vendor/phpmailer/phpmailer/src/SMTP.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer();           //实例化PHPMailer对象

        $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();                    // 设定使用SMTP服务
        $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
        $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl';          // 使用安全协议
        $mail->Host = C('SMTP_HOST');  // SMTP 服务器
        $mail->Port = C('SMTP_PORT');                  // SMTP服务器的端口号
        $mail->Username = C('SMTP_USER');    // SMTP服务器用户名
        $mail->Password = C('SMTP_PASS');     // SMTP服务器密码
        $mail->SetFrom(C('SMTP_USER'), '管理员');
        $replyEmail = C('SMTP_USER');                   //留空则为发件人EMAIL
        $replyName = '管理员';                    //回复名称（留空则为发件人名称）
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject = $title;
        $mail->MsgHTML($context);
        $mail->AddAddress($address,$name);
    
        return $mail;
    }
    /**
     * 显示登录页面
     */ 
    public function index(){

        $this->display();
    }
    /**
     * 显示重置密码界面
     */
    public function reset(){
        if(I('rid')){
            $this->assign('rid',I('rid'));
            $this->display();
        }else{
            die();
        }
        
    }
    /**
     * 匹配用户名与邮箱并发送邮件
     * */
    public function found_pass_post(){

        $username=I('username');
        $email=I('email');

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
        if($validate_response){
            if($user=M('users')->where(array('username'=>$username,'email'=>$email))->find()){
                $user['FINDPASS_HASH']=md5(md5(time()).md5($uid).rand()).md5(rand());
                $user['FINDPASS_TIME']=time();

                M('users')->where(array('username'=>I('username')))->save($user);

                $text='你好，'.$user['truename'].'！<br>点击链接以重新设置你在['.C('SITE_NAME').']的密码，<b>如果不是你操作的，请忽视这封邮件</b><br>
                        <a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].U('/authentication/forgot/reset',array('rid'=>$user['FINDPASS_HASH'])).'">'.
                        $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].U('/authentication/forgot/reset',array('rid'=>$user['FINDPASS_HASH'])).'</a></br>
                        有效时间为'.(C('Find_Passwd_Time')/60).'分钟';

                $mail=$this->send_mail($user['email'],$user['truename'],C('SITE_NAME').' - 找回密码',$text);
                if($mail->Send()){
                    return_json(array('result'=>'success','msg'=>'密码重置链接已发送至 '.$user['email'].'，请登录你的邮箱以重置密码！'));
                }else{
                    return_json(array('result'=>'failure','msg'=>'发送邮件至'.$user['email'].'时失败，请稍后重试或联系管理员！'));
                    // $mail->ErrorInfo;
                }
            }else{
                return_json(array('result'=>'failure','msg'=>'用户名与邮箱不匹配，请重试！'));
            }    
        }else{
            return_json(array('result'=>'failure','msg'=>'验证码错误！'));
        }
    }
    /**
     * 重置密码
     **/
    public function setpassword_post(){
        $rid=I('rid');
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
        if($validate_response){
            $Authentication=M('users')->where(array('FINDPASS_HASH'=>$rid))->find();
            if($Authentication){
                if(time()-$Authentication['FINDPASS_TIME']<C('Find_Passwd_Time')){

                    $salt=md5(time());

                    $pass_encode=md5($salt.I('password','','md5'));

                    $Authentication['FINDPASS_HASH']=null;
                    $Authentication['FINDPASS_TIME']=null;
                    $Authentication['salt']=$salt;
                    $Authentication['password']=$pass_encode;

                    M('users')->where(array('FINDPASS_HASH'=>$rid))->save($Authentication);

                    return_json(array('result'=>'success','msg'=>'密码重置成功！'));
                }else{
                    return_json(array('result'=>'failure','msg'=>'操作超时'));
                }
            }else{
                return_json(array('result'=>'failure','msg'=>'地址错误'));
            }
        }else{
            return_json(array('result'=>'failure','msg'=>'验证码错误！'));
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