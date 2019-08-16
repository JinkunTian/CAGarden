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
            M('garden_users_extend')->where(array('uid'=>$id))->save(array('status'=>0,'status_info'=>'正常退会'));
            $this->success('已批准'.$user['truename'].'的退会申请',U('/Garden/Secedemanage'));
        }else{
            die('503 非法输入');
        }
    }
}
