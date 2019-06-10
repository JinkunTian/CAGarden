<?php
namespace Index\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index(){


		M('common_count')->where(array('site_id'=>1))->setInc('visit_count',1);
		$base_count=M('common_count')->where(array('site_id'=>1))->find();
      	//基础数据，在老系统中，不再做数据导入，只将老记录相加后展示
		$common_count['comment_count']=$base_count['comment_count']+M('appointment_comment')->count();
      	$common_count['members_count']=$base_count['members_count']+M('appointment_users')->count()+M('garden_users')->count;
      	$common_count['fix_count']=$base_count['fix_count']+M('appointment')->where(array('status'=>3))->count();
      	$common_count['visit_count']=$base_count['visit_count'];    
      
		$best=M('garden_users')->where(array('status'=>1))->order('rand()')->limit(3)->select();
		//$best=M('garden_users')->order('last_login desc')->limit(3)->select();
      
		$this->assign('best',$best);
		$this->assign('common_count',$common_count);
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
