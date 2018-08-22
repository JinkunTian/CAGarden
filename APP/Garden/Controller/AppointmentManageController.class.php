<?php
namespace Garden\Controller;
use Think\Controller;
/************************************************* 
Author: 田津坤
QQ    : 2961165914
GitHub: https://github.com/JinkunTian
Date:2018-8-19 
Description:社团电脑义诊预约管理（管理权限）控制器
**************************************************/  
class AppointmentManageController extends CommonController {

	/**
	 * index显示已预约的记录
	 */
    public function index(){

        $count = D('AppointmentView')->count();
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

        $appointments= D('AppointmentView')->order('type DESC')->limit($limit)->select();

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('appointments',$appointments);
        $this->display();
    }

    /**
     * view查看预约详情
     */
	public function view(){
		if(isset($_GET['aid'])){

			$aid=I('aid');

	        $myAppointment=D('AppointmentView');
	        $appointment=$myAppointment->where(array('aid'=>$aid))->find();

	        $comment=D('AppointmentCommentView');
	        $comments=$comment->where(array('aid'=>$aid))->select();

	        $this->assign('appointment',$appointment);
	        $this->assign('comments',$comments);
		}else{
			$this->error('参数错误');
		}

        $this->display();
    }

    /**
     * edit显示编辑预约页面
     */
    public function edit(){
		if(isset($_GET['aid'])){

			$aid=I('aid');

	        $myAppointment=D('AppointmentView');
	        $appointment=$myAppointment->where(array('aid'=>$aid))->find();

	        $comment=D('AppointmentCommentView');
	        $comments=$comment->where(array('aid'=>$aid))->select();

	        $this->assign('appointment',$appointment);
	        $this->assign('comments',$comments);
		}else{
			$this->error('参数错误');
		}

        $this->display();
    }

    /**
     * my显示成员完成的维修记录
     */
    public function my(){

    	$uid=intval(session('id'));

    	$myAppointment=D('AppointmentView');
        $appointments=$myAppointment->where(array('fixer_id'=>$uid))->select();

        $this->assign('appointments',$appointments);
        $this->display();
    }

    /**
     * select社团成员接受预约记录（接单）
     */
    public function select(){
    	$uid=intval(session('id'));
    	$aid=intval(I('aid'));
    	$result=M('appointment')->where(array('aid'=>$aid,'status'=>'1'))->save(array('fixer_id'=>$uid,'status'=>'2'));
    	if($result){
    		$this->success('接单成功！以添加到我的维修列表！',U('/Garden/Appointment/my'));
    	}else{
    		$this->error('接单失败！',U('/Garden/Appointment'));
    	}
    }
    
    /**
     * save保存预约记录
     */
    public function save(){
    	$uid=intval(session('id'));
    	$aid=intval(I('aid'));
    	if(((I('status')=='2')&&(I('result')!=''))){
    		$status='3';
    	}else{
    		$status=I('status');
    	}
		$data = array(
			'fixer_id' =>$uid,
			'result' =>I('result'), 
			'status' =>$status,
		);
		$res=M('appointment')->where(array('aid'=>$aid,'fixer_id'=>$uid))->save($data);
		if($res===false){
			$this->error('保存失败！',U('/Garden/Appointment/my'));
		}else{
			$this->success('保存成功！',U('/Garden/Appointment/my'));
		}
	}
}