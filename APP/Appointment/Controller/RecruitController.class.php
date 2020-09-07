<?php
namespace Appointment\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日16:30:58
 * @Description: 社团纳新控制器
 ***/
class RecruitController extends CommonController {
    /**
     * index方法显示纳新提交页面
     */
    public function index(){
        if(isset($_GET['grade'])){
            $Recruit=M('recruit_grade')->where(array('gid'=>I('grade')))->find();
        }else{
            $Recruit=M('recruit_grade')->order(array('gid'=>'desc'))->find();
        }

        if(!$Recruit){
            $this->error('对应年度的纳新不存在！','/Appointment');
        }else{
            if($Recruit['status']!='1'){
                $this->error('当前纳新已经关闭了！','/Appointment');
            }
        }

        $departments=M('common_departments')->where(array('status'=>'1'))->select();
        $majors=M('common_majors')->where(array('status'=>'1'))->select();
        if(M('garden_users_extend')->where(array('uid'=>session('uid')))->find()){
            $this->error('你已经加入过计算机协会了！','/Appointment');
        }

        if($recruit_info=M('recruit_view')->where(array('uid'=>session('uid'),'grade'=>$Recruit['gid']))->find()){
            $this->assign('recruit_info',$recruit_info);
        }elseif($user_info=M('users')->where(array('uid'=>session('uid')))->find()){
            $this->assign('recruit_info',$user_info);
        }else{
            $this->assign('recruit_info',NULL);
        }
        
        $institute=M('common_majors')->distinct(true)->field('institute')->select();
        $this->assign('institute',$institute);
        $this->assign('ENABLE_GEETEST',C('ENABLE_GEETEST'));
        $this->assign('departments',$departments);
        $this->assign('majors',$majors);
        $this->assign('recruit_name',$Recruit['gname']);
        $this->assign('grade',$Recruit['gid']);
        $this->display();
    }

    public function save_img(){
        $base64_image_content = $_POST['file'];
        $root_path="Public";
        $sub_path="/Uploads/Profiles";
        
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $file_name=substr(md5($_POST['file']),0,13).".{$type}";
            $file_url=$sub_path."/".date('Y-m-d',time())."/";
            $new_file = $root_path.$file_url;

            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                if(!(mkdir($new_file, 0777,true))){
                    $this->error('目录不可写，上传失败！');
                }
            }
            $new_file = $root_path.$file_url.$file_name;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                echo 'success';
                $result2=M('users')->where(array('uid'=>session('uid')))->save(array('img'=>$file_url.$file_name));
            }else{
                echo false;
            }
        }else{
            echo false;
        }
    }
    /**
     * save方法保存纳新表格
     */
    public function save(){

        /**
         * GEETEST行为验证码
         */
        if(C('ENABLE_GEETEST')){
            require_once './ThinkPHP/Library/Vendor/Geetest/class.geetestlib.php';

            $GtSdk = new \GeetestLib(C('CAPTCHA_ID'),C('PRIVATE_KEY'));

            $geetest_data = array(
                    "user_id" => $_SESSION['gt_user_id'], # 网站用户id
                    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
                );
            if ($_SESSION['gtserver'] == 1) {   //服务器正常
                $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $geetest_data);
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

            /**
             * 设置纳新年度，没有指定则选择最新一届
             */
            if(isset($_POST['grade'])){
                $Recruit=M('recruit_grade')->where(array('gid'=>I('grade')))->find();
                if(!$Recruit){
                    $this->error('对应年度的纳新不存在！');
                }
            }else{
                $Recruit=M('recruit_grade')->order(array('gid'=>'desc'))->find();
            }

            if($Recruit['status']!='1'){
                $this->error('当前纳新已经关闭了！');
            }

            if(I('flag')==''){
                return_json(array('result'=>'warning','msg'=>'签名填写完整才能提交哦！'));
            }else if(I('class')==''){
                return_json(array('result'=>'warning','msg'=>'班级信息要填写哦！'));
            }else if(I('info')==''){
                return_json(array('result'=>'warning','msg'=>'个人简介要填写哦！'));
            }else if(I('apply')==''){
                return_json(array('result'=>'warning','msg'=>'入会申请是一定要填写的哦！'));
            }
            /**
             * 基础信息
             */
            $base_data = array(
                //'number' => session('newmember_xh'),
                //'truename' => session('newmember_xm'),
                //'college' => session('newmember_xy'),
                'username' => session('username'),
                'truename' => session('truename'),
                'college' => I('college'),
                'class' => I('class'),
                'major' => I('major'),
                'qq' => I('qq'),
                'email' => I('email'),
                'mobile' => I('mobile')
            );
            $recruit_info=array(
                'uid' => session('uid'),
                'username' => session('username'),
                'dep' => I('dep'),
                'flag' => I('flag'),
                'github' => I('github'),
                'website' => I('website'),
                'apply' => I('apply'),
                'info' => I('info'),
                'grade' => $Recruit['gid'],
                'reg_ip' => get_client_ip(),
                'addtime' =>date('y-m-d H:i:s'),
                'status'    =>  0,
            );

            /**
             * 检查头像是否上传
             */
            $user_info=M('users')->where(array('username' => session('username')))->find();
            if($user_info['img']=='/images/hi.png'){
                return_json(array('result'=>'failure','msg'=>'请先上传本人照片！'));
            }

            /**
             * 检查本届纳新中该用户是否提交了申请
             */
            $checkExis=M('recruit')->where(array('username' => session('username'),'grade'=>$Recruit['gid']))->find();
            
            $checkExis_garden=M('garden_users_extend')->where(array('username' => session('username')))->find();
            if(!($checkExis_garden)){
                $data['salt']=md5(time());
                $password = I('password','','md5');
                $data['password']=md5($data['salt'].$password);
                if($checkExis){
                    $result1=M('recruit')->where(array('uid'=>session('uid')))->save($recruit_info);
                    $result2=M('users')->where(array('uid'=>session('uid')))->save($base_data);
                }else{
                    $result1=M('recruit')->add($recruit_info);
                    $result2=M('users')->where(array('uid'=>session('uid')))->save($base_data);
                }
                
                if (!($result===false||$result2===false)) {
                    return_json(array('result'=>'success','msg'=>$Recruit['message']));
                }else{
                    return_json(array('result'=>'failure','msg'=>'提交失败（CODE1），请联系管理员解决！'));
                }
            }else{
                return_json(array('result'=>'failure','msg'=>'你已经加入了计算机协会！'));
            }
        }else{
            return_json(array('result'=>'failure','msg'=>'验证码错误！'));
        }  
    }
    public function message(){
    	$Recruit=M('recruit_grade')->order(array('gid'=>'desc'))->find();
    	$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>你的信息我们已经收到！<br/>请牢记你的密码！</p>[ '.$Recruit['message'].'] <br/>[<a href="/"> 返回首页 </a>] [<a href="/Appointment/Recruit.html"> 查看申请 </a>]</div>','utf-8');
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
