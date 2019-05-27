<?php
namespace Garden\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       tianjinkun@spyder.link
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2019年5月27日15:50
 * @Description: 退会管理（管理员）控制器
 ***/
class SecedemanageController extends AdminController {
    
    /**
     * index方法列出所有用户
     */
    public function index(){

        $count = M('garden_secede')->count();//正常状态的用户
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

        $users= D('SecedeView')->order('addtime DESC')->limit($limit)->select();

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('users',$users);
        $this->display();
    }

    /**
     * view方法显示审核界面
     */
    public function view_secede(){
        $id =intval(I('uid'));
        if($user= D('SecedeView')->where(array('uid'=>$id))->find()){
            $this->user=$user;
            $this->display();
        }else{
            die('503 非法输入');
        }
    }

    /**
     * 批准退会申请
     */
    public function accept_secede(){
        $id =intval(I('uid'));
        if($user= D('SecedeView')->where(array('uid'=>$id))->find()){
            M('garden_secede')->where(array('uid'=>$id))->save(array('status'=>2));
            M('garden_users')->where(array('uid'=>$id))->save(array('status'=>0));
            $this->success('已批准'.$user['truename'].'的退会申请',U('/Garden/Secedemanage'));
        }else{
            die('503 非法输入');
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
