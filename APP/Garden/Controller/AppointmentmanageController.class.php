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
class AppointmentManageController extends AdminController {

	/**
	 * index显示已预约的记录
	 */
    public function index(){

        $count = M('appointment_view')->count();
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

        $appointments= M('appointment_view')->order('addtime DESC')->limit($limit)->select();

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('appointments',$appointments);
        $this->display();
    }

    /**
     * edit显示编辑预约页面
     */
    public function edit(){
		if(isset($_GET['aid'])){

			$aid=I('aid');

	        if($appointment=M('appointment_view')->where(array('aid'=>$aid))->find()){


                $shift_count=0;

                for ($i=4; $i > 1; $i--) { 

                    if($appointment['fixer'.$i.'_id']){
                        $shift[$shift_count++]=M('garden_user_view')->where(array('uid'=>$appointment['fixer'.$i.'_id']))->find();
                    }
                }

                $this->assign('shift',$shift);
                $this->assign('shift_count',$shift_count);
                $this->assign('appointment',$appointment);
            }else{
                $this->error('记录不存');
            }        
		}else{
			$this->error('参数错误');
		}

        $this->display();
    }
    
    /**
     * save保存预约记录
     */
    public function save(){

    	$aid=intval(I('aid'));

        if(((I('status')=='2')&&(I('result')!=''))){
            $status='3';
        }else{
            $status=I('status');
        }

        $data = array(
                    'result' =>I('result'),
                    'edittime'=>date('y-m-d H:i:s'), 
                    'status' =>$status,
                );

        //填写处理结果后自动升级状态为已处理
    	
        if($source=M('appointment')->where(array('aid'=>$aid))->find()){

        	if($source['fixer_id']){

	            if(I('shift_to_id')!=''){

	                $str=$source['fixer_id'].','.$source['fixer2_id'].','.$source['fixer3_id'].','.$source['fixer4_id'];
	                $shift_count=M('garden_user_view')->where(array('uid'=>array('in',$str)))->count();

	                /**
	                 * fixer右移插入，保证fixer_id为最移交后的最终负责人
	                 */

	                switch ($shift_count) {
	                    case '1':$data['fixer2_id']=$source['fixer_id'];$data['fixer_id']=intval(I('shift_to_id'));break;
	                    case '2':$data['fixer3_id']=$source['fixer2_id'];$data['fixer2_id']=$source['fixer_id'];$data['fixer_id']=intval(I('shift_to_id'));break;
	                    case '3':$data['fixer4_id']=$source['fixer3_id'];$data['fixer3_id']=$source['fixer2_id'];$data['fixer2_id']=$source['fixer_id'];$data['fixer_id']=intval(I('shift_to_id'));break;
	                    default:break;
	                }
	            }
	            
	        }else{
	        	$data['fixer_id']=intval(session('id'));
	        }
            $res=M('appointment')->where(array('aid'=>$aid))->save($data);
            if($res===false){
                $this->error('保存失败！',U('/Garden/Appointment/my'));
            }else{
                $this->success('保存成功！',U('/Garden/Appointment/my'));
            }
        }else{
            $this->error('记录不存');            
        }
	}
  
     /**
	 * print打印预约信息
	 */
	public function print_info()
	{
		if(isset($_GET['aid'])){

			$aid=I('aid');

	        if($appointment=M('appointment_view')->where(array('aid'=>$aid))->find()){

                $shift_count=0;
              $shift[0]['truename']='';
              $shift[1]['truename']='';
              $shift[2]['truename']='';
              $shift[3]['truename']='';
                for ($i=4; $i > 1; $i--) { 

                    if($appointment['fixer'.$i.'_id']){
                        $shift[$shift_count++]=M('garden_user_view')->where(array('uid'=>$appointment['fixer'.$i.'_id']))->find();
                    }	
                }
				$shift[$shift_count++]=M('garden_user_view')->where(array('uid'=>$appointment['fixer_id']))->find();
                $this->assign('shift_3',$shift[3]);
                $this->assign('shift_2',$shift[2]);
                $this->assign('shift_1',$shift[1]);
                $this->assign('shift_0',$shift[0]);
                $this->assign('appoint',$appointment);
            }else{
                $this->error('记录不存在或着你不能编辑别人的记录');
            }        
		}else{
			$this->error('参数错误');
		}
        $this->display();
	}

	/**
	 * reward 统计奖励信息
	 */
	public function add_reward()
	{
		$aid=I('aid');
		if($add_info=M('appointment_view')->where(array('aid'=>$aid,'reward'=>'未奖励'))->find()){
            $user=M('garden_user_view')->where(array('uid'=>$add_info['fixer_id']))->find();
			$add_reward_info=array(
              'user_id'=>$add_info['fixer_id'],
              'truename'=>$add_info['fixer_name'],
              'before_add'=>(int)$user['reward_sum'],
              'reward_count'=>1,
              'after_add'=>((int)$user['reward_sum']+1),
              'reward_reason'=>'义诊奖励，维修['.$add_info['guest_name'].']的电脑[AID'.$add_info['aid'].']',
              'datetime'=>date('y-m-d H:i:s'));
			M('garden_reward_log')->add($add_reward_info);
          	$data=array('uid'=>$add_info['fixer_id'],'reward_sum'=>($user['reward_sum']+1));
          	M('garden_users_extend')->where(array('uid'=>$add_info['fixer_id']))->save($data);
			M('appointment')->where(array('aid'=>$aid))->save(array('aid'=>$aid,'reward'=>'已奖励'));
			$this->success('成功！');
		}else{
			$this->error('失败！');
		}
	}
  	/**
     * del方法删除预约记录
     */
    public function del(){
        if(M('appointment')->where(array('aid'=>I('aid')))->delete()){
            $this->success('删除成功！',U('/Garden/Appointmentmanage'));
        }else{
            $this->error('删除失败，目标记录不存在！',U('/Garden/Appointmentmanage'));
        }
    }
}
