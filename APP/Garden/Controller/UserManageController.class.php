<?php
namespace Garden\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       tianjinkun@spyder.link
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日23:32:01
 * @Description: 用户管理（管理员）控制器
 ***/
class UserManageController extends AdminController {
    
    /**
     * index方法列出所有用户
     */
    public function index(){

        $count = M('garden_users')->where(array('status'=>'1'))->count();//正常状态的用户
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

        $users= D('UserView')->where(array('status'=>'1'))->order('type DESC')->limit($limit)->select();

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

    /**
     * edit方法显示编辑用户界面
     */
    public function edit(){
        $id =intval(I('uid'));
        
        $map['status'] = array('gt',0);

        $this->password_id=md5(time());
        $this->departments=M('common_departments')->where($map)->select();
        $this->majors=M('common_majors')->where(array('status'=>'1'))->select();
        $this->user_data = D('UserView')->where(array('uid'=>$id))->select();

        $this->display();
    }

    /**
     * 保存编辑过的用户信息
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
        /**
         * 上传头像
         */
        if(($_FILES['img']['type'])){
            
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     205800;// 设置附件上传大小
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

        /**
         * 判断是否有重设密码
         */
        $password_id=I('password_id');

        if(!(I($password_id)==''||I($password_id)=='留空则不修改密码')){
            $data['salt']=md5(time());
            $password = I('password','','md5');
            $data['password']=md5($data['salt'].$password);
        }
        /**
         * 保存修改
         */
        $result=M('garden_users')->where(array('uid' => I('uid') ))->save($data);
        if ($result===false) {
            $this->error('保存失败！');
        }else{
            $this->success('保存成功！',U('/Garden/User/look',array('uid'=>I('uid'))));
        }
    }

    /**
     * adduser方法显示添加用户页面
     */
    public function adduser(){
        if (session('admin') == '0') {
                $this->error('没有权限');
            }else{
                $this->majors=M('common_majors')->where(array('status'=>'1'))->select();
                $map['status'] = array('gt',0);
        		$this->departments=M('common_departments')->where($map)->select();
                $this->display();
        }
    }

    /**
     * adduserpost方法处理提交的添加用户表单
     */
    public function adduserpost(){
        if (IS_POST){

            $data = array(
                'truename' => I('truename'),
                'username' => I('username'),
                'mobile' => I('mobile'),
                'qq' => I('qq'),
                'email' => I('email'),
                'major' => I('major'),
                'dep' => I('dep'),
                'position' => I('position'),
                'address' => I('address'),
                'flag' => I('flag'),
            );

            /**
             * 上传头像
             */
            if(($_FILES['img']['type'])){
                
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     205800;// 设置附件上传大小
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
            $checkExis=M('garden_users')->where(array('username' => I('username')))->find();
            if(!$checkExis){
                $data['salt']=md5(time());
                $password = I('password','','md5');
                $data['password']=md5($data['salt'].$password);
                $result=M('garden_users')->add($data);
                if (!$result===false) {
                    $this->success('添加用户成功！',U('/Garden/User/look/',array('uid' => $result)));
                }else{
                    $this->error('添加失败！');
                }
            }else{
                $this->error('该用户名已存在！');
            }
        }else{
            $this->error('页面不存在');
        }
    }
}