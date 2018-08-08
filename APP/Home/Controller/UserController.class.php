<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
    public function index(){
        $id = (int)session('id');
        $this->user_data = M('users')->where(array('uid'=>$id))->select();
        $this->display();
    }
    public function edit(){
        if(!isadmin(intval(session('id')))){
            $this->error('无权查看!');
        }
        $this->user_data = M('users')->where(array('uid'=>I('uid')))->select();
        $this->display();
    }
    public function listuser(){
        if(!isadmin(intval(session('id')))){
            $this->error('无权查看!');
        }

        $count = M('users')->where(array('status'=>'1'))->count();//正常状态的用户
        $page = new \Think\Page($count,200);
        $page->setConfig('header','条数据');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','<<');
        $page->setConfig('last','>>');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% <span>共 %TOTAL_ROW% %HEADER%</span>');
        $page->rollPage=3; //控制页码数量
        $show = $page->show();// 分页显示输出
        $limit = $page->firstRow.','.$page->listRows;
        $users =  M('users')->where(array('status'=>'1'))->order('type DESC')->limit($limit)->select();

        if($len=count($users,0)){
            for($i=0;$i<$len;$i++){
                if($users[$i]['type']=='1'){
                    $users[$i]['type']='用户';
                }elseif($users[$i]['type']=='2'){
                    $users[$i]['type']='管理员';
                }
            }
        }
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('users',$users);
        $this->display();
    }
    public function datapost(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     205800;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public'; // 设置附件上传根目录
        $upload->savePath  =     '/Uploads/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->uploadOne($_FILES['img']);
        if(!$info) {// 上传错误提示错误信息
            $data = array(
                'truename' => I('truename'),
                'tel' => I('tel'),
                'qq' => I('qq'),
                'email' => I('email'),
                'major' => I('major'),
                'position' => I('position'),
                'address' => I('address'),
                'flag' => I('flag'),
            );
             //$this->error($upload->getError());
        }else{// 上传成功
            $data = array(
                'truename' => I('truename'),
                'tel' => I('tel'),
                'qq' => I('qq'),
                'email' => I('email'),
                'major' => I('major'),
                'position' => I('position'),
                'address' => I('address'),
                'flag' => I('flag'),
                'img' => $info['savepath'].$info['savename'],
            );
        }
        if(I('uid')==intval(session('id'))){
            $result=M('users')->where(array('uid' => I('uid') ))->save($data);
        }elseif(isadmin()){
            $data['type']=I('type');
            $result=M('users')->where(array('uid' => I('uid') ))->save($data);
        }else{
            $this->error('无权操作！');
        }
        if ($result===false) {
            $this->error('保存失败！');
        }else{
            $this->success('保存成功！',U('/Home/User/look',array('uid'=>I('uid'))));
        }
    }
    //更改密码
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

      $user = M('users')->where(array('uid' => session('id')))->find();
      $old_pass=md5($user['salt'].$password);
      //判断输入的密码是否正确
      if ($newpws!=$newpwss || $user['password'] != $old_pass) {
          $this->error('旧密码错误或者新密码不一致');
      }else{
            //写入密码
            if (M('users')->where(array('uid' => session('id')))->save($data)) {
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
      }
    }
    //添加用户视图
    public function adduser(){
        if (session('admin') == '0') {
                $this->error('没有权限');
            }else{
                $this->display();
        }
    }
    //添加用户表单
    public function adduserpost(){
        if (!IS_POST) E('页面不存在');
            //对表单数据进行MD5加密
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     205800;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public'; // 设置附件上传根目录
        $upload->savePath  =     '/Uploads/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->uploadOne($_FILES['img']);
        if(!$info) {// 上传错误提示错误信息
            $data = array(
                'truename' => I('truename'),
                'username' => I('username'),
                'tel' => I('tel'),
                'qq' => I('qq'),
                'email' => I('email'),
                'major' => I('major'),
                'position' => I('position'),
                'address' => I('address'),
                'flag' => I('flag'),
            );
             //$this->error($upload->getError());
        }else{// 上传成功
            $data = array(
                'truename' => I('truename'),
                'username' => I('username'),
                'tel' => I('tel'),
                'qq' => I('qq'),
                'email' => I('email'),
                'major' => I('major'),
                'position' => I('position'),
                'address' => I('address'),
                'flag' => I('flag'),
                'img' => $info['savepath'].$info['savename'],
            );
        }
        $checkExis=M('users')->where(array('username' => I('username')))->find();
        if(!$checkExis){
            $data['salt']=md5(time());
            $password = I('password','','md5');
            $data['password']=md5($data['salt'].$password);
            $result=M('users')->add($data);
            if (!$result===false) {
                $this->success('添加用户成功！',U('/Home/User/look/',array('uid' => $result)));
            }else{
                $this->error('添加失败！');
            }
        }else{
            $this->error('该用户名已存在！');
        }
    }

    //查看用户资料
    public function look(){
        $uid = (int)$_GET['uid'];
        $this->look = M('users')->where(array('uid'=>$uid))->select();
        $this->display();
    }
    public function AddUserHelp(){
        if(isset($_GET['queryString'])||isset($_POST['queryString'])){
            $userForm=M('users');
            $where['_string']='(truename like "%'.I('queryString').'%") 
            OR (username like "%'.I('queryString').'%") 
            OR (tel like "%'.I('queryString').'%") 
            OR (email like "%'.I('queryString').'%") 
            OR (qq like "%'.I('queryString').'%")
            OR (position like "%'.I('queryString').'%") 
            OR (major like "%'.I('queryString').'%")';
            $data=$userForm->where($where)->select();
            if($data){
                $len=count($data,0);
                for ($i=0; $i < $len; $i++) { 
                    echo '<li onClick="'.I('call_func').'fill(Array(\''.$data[$i]['uid'].'\',\''.$data[$i]['truename'].'\',\''.$data[$i]['major'].'\'));">'.$data[$i]['truename'].'</li>';
                }
            }
        }else{
            die('参数错误！');
        }
    }

}