<?php
namespace Index\Controller;
use Think\Controller;
class IndexController extends CommonController {
	/************************************************* 
	Author: 田津坤
	QQ    : 2961165914
	GitHub: https://github.com/JinkunTian
	Date:2018-8-19 
	Description:首页信息控制器
	**************************************************/ 
	public function index(){
		/**
		 * 获取协会访问量，注册用户数量等统计信息
		*/
		$common_count['comment_count']=M('appointment_comment')->count();
      	$common_count['members_count']=M('users')->count();
      	$common_count['fix_count']=M('appointment')->where(array('status'=>3))->count();
      	$common_count['visit_count']=M('system_logs')->count();    

		/**	
		 * 展示协会成员风采 
		*/  
		$best=M('garden_user_view')->where(array('status'=>1))->order('rand()')->limit(3)->select();

		/**
		 * 首页大图标语
		*/
		$association_slogan=M('index_config')->where(array('config_name'=>'association_slogan'))->find();

		/**
		 * 首页大图主按钮功能
		*/
		$main_button=M('index_config')->where(array('config_name'=>'main_button'))->find();

		/**
		 * 社团介绍
		*/
		$association_info=M('index_config')->where(array('config_name'=>'association_info'))->find();

		/**
		 * 社团主要活动
		*/
		$major_activities=M('index_config')->where(array('config_name'=>'major_activities'))->limit(4)->select();

		/**
		 * 社团新闻
		*/
		$association_news=M('index_news')->order('id DESC')->limit(6)->select();

		/**
		 * 首页相册
		*/
		$association_album=M('index_config')->where(array('config_name'=>'association_album'))->limit(6)->select();

		/**
		 * 社团介绍
		*/
		$association_address=M('index_config')->where(array('config_name'=>'association_address'))->find();

		/**
		 * 兄弟协会
		*/
		$partner_association=M('index_config')->where(array('config_name'=>'partner_association'))->select();

		/**
		 * 联系方式
		*/
		$contact_info=M('index_config')->where(array('config_name'=>'contact_info'))->limit(4)->select();
      
		$this->assign('best',$best);
		$this->assign('common_count',$common_count);
		$this->assign('association_slogan',$association_slogan);
		$this->assign('main_button',$main_button);
		$this->assign('association_info',$association_info);
		$this->assign('major_activities',$major_activities);
		$this->assign('association_news',$association_news);
		$this->assign('association_album',$association_album);
		$this->assign('association_address',$association_address);
		$this->assign('partner_association',$partner_association);
		$this->assign('contact_info',$contact_info);
		$this->display();
    }
    public function feedback(){
    	$data=array(
    		'name'=>I('name'),
    		'email'=>I('email'),
    		'message'=>I('message'),
    	);
    	M('index_feedback')->add($data);
    	$this->success('反馈成功！');
    }
}
