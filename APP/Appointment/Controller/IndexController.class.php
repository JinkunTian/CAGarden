<?php
namespace Appointment\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       tianjinkun@spyder.link
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日14:45:47
 * @Description: 计算机协会预约维修平台（前台）预约控制器
 ***/
class IndexController extends CommonController {
    /**
     * index方法列出用户所有预约信息
     */
    public function index(){
    	$guest_id = (int)session('guest_id');

        $appointments=D('MyAppointmentView')->where(array('guest_id'=>$guest_id))->select();

        $this->assign('appointments',$appointments);
        $this->display();
    }
    /**
     * view方法查看详细预约信息
     */
	public function view(){
		if(isset($_GET['aid'])){

			$aid=I('aid');
            $guest_id = (int)session('guest_id');

            $appointment=D('MyAppointmentView')->where(array('aid'=>$aid,'guest_id'=>$guest_id))->find();
            $comments=D('AppointmentCommentView')->where(array('aid'=>$aid))->select();

            $this->assign('appointment',$appointment);
            $this->assign('comments',$comments);
		}else{
            $this->error('参数错误');
        }
        $this->display();
    }

    /**
     * addAppointment方法添加新的预约
     */
    public function addAppointment(){
    	$this->display();
    }

    /**
     * addpost方法保存用户提交的预约记录
     */
    public function addpost(){
        $guest_id = (int)session('guest_id');

        $data = array(
            'guest_id' =>$guest_id,
            'issues' =>I('issues'),
            'model' =>I('model'),
            'addtime' => date('y-m-d H:i:s'),
            'edittime' => date('y-m-d H:i:s'),
            'status' => '1',
        );
        //添加数据
        if (M('appointment')->add($data)) {
            $this->success('保存成功',U('/Appointment/'));
        }else{
            $this->error('预约失败！',U('/Appointment/'));
        }
    }

    /**
     * addcomment方法保存用户提交的评论
     */
    public function addcomment(){

    	$guest_id = (int)session('guest_id');

        if (IS_AJAX){

            $content=str_replace("\n","<br>",I('content'));
            $data = array(
                'aid' =>intval(I('aid')),
                'fixer_id' => intval(I('fixer_id')),
                'guest_id' => $guest_id,
                'addtime'   => date('y-m-d H:i:s'), 
                'comment' =>str_replace(" ","&nbsp;",$content),
            );

            if (M('appointment_comment')->add($data)) {
                $this->ajaxReturn(array('info' => '<i class="fa fa-check"></i> 发布成功','status' => 1), 'json');
            }else{
                $this->ajaxReturn(array('info' => '<i class="fa fa-remove"></i> 发布失败,请重试','status' => 0), 'json');
            }
        }else{
            $this->error('页面不存在');
        }
    }

    /**
     * delcomment方法删除用户提交的评论
     */
    public function delcomment(){

        $guest_id = (int)session('guest_id');

        if (M('appointment_comment')->where(array('cid'=>I('cid'),'guest_id'=>$guest_id))->find()) {

            if(M('appointment_comment')->where(array('cid'=>I('cid'),'guest_id'=>$guest_id))->delete()){
                $this->success('删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }else{
            $this->error('删除失败！目标评论不存在或者你不能删除别人的评论！');
        }
    }

    /**
     * cancelAppointment方法取消预约记录
     */
    public function cancelAppointment(){

    	$guest_id = (int)session('guest_id');

        if(isset($_GET['aid'])){
        	$aid=I('aid');

        	if(M('appointment')->where(array('aid'=>$aid,'guest_id'=>$guest_id))->find()){
        		if(M('appointment')->where(array('aid'=>$aid))->save(array('status'=>'4'))){

				    $this->success('取消成功！');
                }else{
                    $this->error('取消失败！');
                }
        	}else{
        		$this->error('记录不存在或者你不能取消别人的记录！');
        	} 
        }else{
            $this->error('参数错误！');
        }
    }
    /**
     * 别看了，没有deleteAppointment方法的，不允许用户单方面删除记录的
     * 年纪轻轻的，学锤子计算机？有头发不好吗！
     */
}