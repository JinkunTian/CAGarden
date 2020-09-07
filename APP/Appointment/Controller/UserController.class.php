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
        $this->user_data = M('users')->where(array('uid'=>$uid))->select();
        $this->display();
    }

    /**
     * datapost方法保存用户修改的个人信息
     */
    public function datapost(){
        $uid = session('uid');
        $data = array(
            'uid' => $uid,
            'username' => I('username'),
            'truename' => I('truename'),
            'mobile' => I('mobile'),
            'qq' => I('qq'),
            'email' => I('email'),
            'major' => I('major'),
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
