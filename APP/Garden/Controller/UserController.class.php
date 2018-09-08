<?php
namespace Garden\Controller;
use Think\Controller;
class UserController extends CommonController {
    public function index(){
        $id = (int)session('id');
        $UserView=D('UserView');

        $map['status'] = array('gt',0);
        
        $this->departments=M('common_departments')->where($map)->select();
        $this->majors=M('common_majors')->where(array('status'=>'1'))->select();
        $this->user_data = $UserView->where(array('uid'=>$id))->select();

        $this->display();
    }

    /**
     * 保存用户编辑的个人信息
     */
    public function datapost(){
        /**
         * 基础信息
         */
        $data = array(
                'truename' => I('truename'),
                'mobile' => I('mobile'),
                'qq' => I('qq'),
                'email' => I('email'),
                'major' => I('major'),
                'dep' => I('dep'),
                'position' => I('position'),
                'flag' => I('flag'),
            );

        if(($_FILES['img']['type'])){
            /**
             * 上传头像
             */
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =	C('MAX_PHOTO_POST_SIZE');
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Public'; // 设置附件上传根目录
            $upload->savePath  =     '/Uploads/'; // 设置附件上传（子）目录
            
            $info = $upload->uploadOne($_FILES['img']);

            if($info) {// 头像上传成功则保存头像
                $data['img'] = $info['savepath'].$info['savename'];
            }else{
                //上传失败，显示失败信息
                $this->error($upload->getError());
            }
        }

        if(I('uid')==intval(session('id'))){
            $result=M('garden_users')->where(array('uid' => I('uid')))->save($data);
            if($result===false){
                $this->error('保存失败！');
            }else{
                $this->success('保存成功！',U('/Garden/User/look',array('uid'=>I('uid'))));
            }
        }else{
            $this->error('你不能编辑别人的信息');
        }
    }
    
    /**
     * 更改密码
     */
    public function changgepwd(){
        $this->display();
    }
    //更改密码表单
    public function pwdpost(){
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

      $user = M('garden_users')->where(array('uid' => session('id')))->find();
      $old_pass=md5($user['salt'].$password);
      //判断输入的密码是否正确
      if ($newpws!=$newpwss || $user['password'] != $old_pass) {
          $this->error('旧密码错误或者新密码不一致');
      }else{
            //写入密码
            if (M('garden_users')->where(array('uid' => session('id')))->save($data)) {
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }
    }

    /**
     * 查看用户资料
     */
    public function look(){
        $uid = (int)$_GET['uid'];

        $UserView=D('UserView');
        $this->look = $UserView->where(array('uid'=>$uid))->select();

        $this->display();
    }
    public function AddUserHelp(){
        if(isset($_GET['queryString'])||isset($_POST['queryString'])){
            $userForm=D('UserView');
            $where['_string']='(truename like "%'.I('queryString').'%") 
            OR (username like "%'.I('queryString').'%") 
            OR (mobile like "%'.I('queryString').'%") 
            OR (email like "%'.I('queryString').'%") 
            OR (qq like "%'.I('queryString').'%")
            OR (position like "%'.I('queryString').'%") 
            OR (dname like "%'.I('queryString').'%") 
            OR (major like "%'.I('queryString').'%")';
            $data=$userForm->where($where)->select();
            if($data){
                $len=count($data,0);
                for ($i=0; $i < $len; $i++) { 
                    echo '<li onClick="'.I('call_func').'fill(Array(\''.$data[$i]['uid'].'\',\''.$data[$i]['truename'].'\',\''.$data[$i]['dname'].'\'));">'.$data[$i]['truename'].'</li>';
                }
            }
        }else{
            die('参数错误！');
        }
    }

}
