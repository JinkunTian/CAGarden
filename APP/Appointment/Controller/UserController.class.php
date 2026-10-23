<?php
namespace Appointment\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日15:04:47
 * @Description: 计算机协会预约维修平台（前台）用户控制器
 ***/
class UserController extends CommonController {
    /**
     * index方法展示用户信息
     */
    public function index(){
        $uid = session('uid');
        $this->majors=M('common_majors')->where(array('status'=>'1'))->select();
        $this->political_statuses=ca_political_status_options();
        $this->user_data = M('users')->where(array('uid'=>$uid))->select();
        $this->display();
    }

    /**
     * save_img方法保存已登录用户的头像。
     */
    public function save_img(){
        $uid=session('uid');
        if(!$uid){
            return_json(array('result'=>'failure','msg'=>'请先登录后再上传头像！'));
        }

        $base64_image_content=isset($_POST['file']) ? $_POST['file'] : '';
        if(strpos($base64_image_content,'data%3A')===0){
            $base64_image_content=urldecode($base64_image_content);
        }

        if(!preg_match('/^data:image\/(jpeg|jpg|png);base64,/i',$base64_image_content,$result)){
            return_json(array('result'=>'failure','msg'=>'只支持 JPG 或 PNG 图片！'));
        }

        $image_data=base64_decode(str_replace($result[0],'',$base64_image_content),true);
        $max_size=intval(C('MAX_PHOTO_POST_SIZE'));
        if($max_size<=0){
            $max_size=4194304;
        }
        if($image_data===false || strlen($image_data)==0 || strlen($image_data)>$max_size){
            return_json(array('result'=>'failure','msg'=>'图片不能为空且大小不能超过4MB！'));
        }

        $image_info=getimagesizefromstring($image_data);
        if(!$image_info || !in_array($image_info['mime'],array('image/jpeg','image/png'),true)){
            return_json(array('result'=>'failure','msg'=>'图片格式校验失败！'));
        }

        $file_url='/Uploads/Profiles/'.date('Y-m-d').'/';
        $site_root=dirname(dirname(dirname(dirname(__FILE__))));
        $upload_dir=$site_root.'/Public'.$file_url;
        if(!is_dir($upload_dir) && !mkdir($upload_dir,0755,true)){
            return_json(array('result'=>'failure','msg'=>'头像目录不可写！'));
        }

        $file_type=$image_info['mime']=='image/png' ? 'png' : 'jpg';
        $file_name=substr(sha1($image_data),0,20).'.'.$file_type;
        $new_file=$upload_dir.$file_name;
        if(file_put_contents($new_file,$image_data,LOCK_EX)===false){
            return_json(array('result'=>'failure','msg'=>'头像保存失败，请稍后重试！'));
        }

        if(M('users')->where(array('uid'=>$uid))->save(array('img'=>$file_url.$file_name))===false){
            @unlink($new_file);
            return_json(array('result'=>'failure','msg'=>'头像资料保存失败，请稍后重试！'));
        }

        return_json(array('result'=>'success','url'=>$file_url.$file_name,'msg'=>'头像上传成功！'));
    }

    /**
     * datapost方法保存用户修改的个人信息
     */
    public function datapost(){
        $uid = session('uid');
        $political_status=I('political_status');
        $is_work_study=I('is_work_study');
        if(!ca_validate_personal_fields($political_status,$is_work_study)){
            $this->error('请选择有效的政治面貌和勤工助学选项！');
        }
        $data = array(
            'uid' => $uid,
            'username' => I('username'),
            'truename' => I('truename'),
            'mobile' => I('mobile'),
            'qq' => I('qq'),
            'email' => I('email'),
            'major' => I('major'),
            'political_status' => $political_status,
            'is_work_study' => intval($is_work_study),
        );
        if(I('uid')==$uid){
            $result=M('users')->where(array('uid' => I('uid') ))->save($data);
            if ($result===false) {
                $this->error('保存失败！');
            }else{
                $this->success('保存成功！',U('/Appointment/'));
            }
        }else{
            $this->error('你不能编辑别人的信息！');
        }
    }
    /**
     * changgepwd方法显示更改密码
     */
    public function changgepwd(){
        $this->display();
    }
    /**
     * pwdpost方法保存用户修改过的密码
     */
    public function pwdpost(){
        $uid = session('uid');
        //对表单数据进行MD5加密
        $password = I('password','','md5');
        $newpws = I('newpws','','md5');
        $newpwss = I('newpwss','','md5');
        //把要写入的数据转换成数组
        $salt=md5(time());
        $data =array(
            'password' => md5($salt.$newpwss),
            'salt' => $salt,
        );
        $user = M('users')->where(array('uid' => $uid))->find();
        $old_pass=md5($user['salt'].$password);
        //判断输入的密码是否正确
        if ($newpws!=$newpwss || $user['password'] != $old_pass) {
            $this->error('旧密码错误或者新密码不一致');
        }else{
            //写入密码
            if (M('users')->where(array('uid' => $uid))->save($data)) {
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }
    }
    public function look(){
        if(isset($_GET['fixer_uid'])){

            $fixer_uid=I('fixer_uid');

            if($fixer_info=M('garden_users')->where(array('uid'=>$fixer_uid))->find()){
                $this->assign('fixer_info',$fixer_info);
                $this->display();
            }else{
                $this->error('用户不存在！');
            }
        }else{
            $this->error('非法参数');
        }
    }
}
