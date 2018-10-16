<?php
namespace Index\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index(){


		M('common_count')->where(array('site_id'=>1))->setInc('visit_count',1);

		// $common_count=where()->find();
		// $visit=$common_count['visit_count']+1;
		// $data = array(
	 //            'visit_count' => $visit,
	 //        );
		// M('common_count')->where(array('site_id' => 1))->save($data);
		$common_count=M('common_count')->where(array('site_id'=>1))->find();
		$best=M('garden_users')->order('rand()')->limit(3)->select();
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
