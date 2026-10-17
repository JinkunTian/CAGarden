<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018-8-19 
 * @Description: 社团电脑义诊预约管理（成员权限）控制器
 ***/
namespace Garden\Controller;
use Think\Controller;
class AppointmentController extends CommonController {

    private function notifyCsrfToken(){
        $token=session('appointment_notify_csrf_token');
        if(!is_string($token) || $token===''){
            $token=bin2hex(random_bytes(24));
            session('appointment_notify_csrf_token',$token);
        }
        return $token;
    }

	/**
	 * index显示已预约的记录
	 */
    public function index(){
        $myAppointment=M('appointment_view');
        $appointments=$myAppointment->where(array('status'=>'1'))->select();
        $this->assign('appointments',$appointments);
        $this->display();
    }

    /**
     * my显示成员完成的维修记录
     */
    public function my(){

        $uid=session('uid');

        $myAppointment=M('appointment_view');
        $appointments=$myAppointment->where(array('fixer_id'=>$uid))->select();

        $this->assign('appointments',$appointments);
        $this->display();
    }

    /**
     * view查看预约详情
     */
	public function view(){
		if(isset($_GET['aid'])){

			$aid=I('aid');

	        if($appointment=M('appointment_view')->where(array('aid'=>$aid))->find()){
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
     * edit显示编辑预约页面
     */
    public function edit(){
		if(isset($_GET['aid'])){

			$aid=I('aid');
            $uid=session('uid');

	        if($appointment=M('appointment_view')->where(array('aid'=>$aid,'fixer_id'=>$uid))->find()){

                $shift_count=0;
                $shift=array();
                for ($i=4; $i > 1; $i--) { 

                    if($appointment['fixer'.$i.'_id']){
                        $shift[$shift_count++]=M('garden_user_view')->where(array('uid'=>$appointment['fixer'.$i.'_id']))->find();
                    }
                }
                $this->assign('shift',$shift);
                $this->assign('shift_count',$shift_count);
                $this->assign('appointment',$appointment);
            }else{
                $this->error('记录不存在或着你不能编辑别人的记录');
            }        
		}else{
			$this->error('参数错误');
		}

        $this->assign('notify_csrf_token',$this->notifyCsrfToken());
        $this->display();
    }

    /**
     * select社团成员接受预约记录（接单）
     */
    public function select(){
    	$uid=intval(session('uid'));
    	$aid=intval(I('aid'));

        $data=array('fixer_id'=>$uid,'status'=>'2','edittime'=>date('y-m-d H:i:s'));
    	$result=M('appointment')->where(array('aid'=>$aid,'status'=>'1'))->save($data);
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

    	$uid=session('uid');
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
    	
        if($source=M('appointment')->where(array('aid'=>$aid,'fixer_id'=>$uid))->find()){

            if(I('shift_to_id')!=''){

                $str=$source['fixer_id'].','.$source['fixer2_id'].','.$source['fixer3_id'].','.$source['fixer4_id'];
                $shift_count=M('garden_users_extend')->where(array('uid'=>array('in',$str)))->count();

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
            $res=M('appointment')->where(array('aid'=>$aid))->save($data);
            if($res===false){
                $this->error('保存失败！',U('/Garden/Appointment/my'));
            }else{
                $this->success('保存成功！',U('/Garden/Appointment/my'));
            }
        }else{
            $this->error('记录不存在或着你不能编辑别人的记录');            
        }
	}

    /**
     * notify向当前维修人员负责的预约发送到场维修通知
     * 仅在本人明确提交并勾选确认后发送，不会随保存维修记录自动发送。
     */
    public function notify(){

        if($_SERVER['REQUEST_METHOD']!=='POST'){
            $this->error('请求方式不正确！');
        }
        $token=isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null;
        $expected=session('appointment_notify_csrf_token');
        if(!is_string($token) || $token==='' || !is_string($expected) || $expected==='' || !hash_equals($expected,$token)){
            $this->error('页面已失效，请刷新后重新提交。');
        }
        if(I('post.confirm_notify')!=='1'){
            $this->error('请核对邮箱和到场时间，并勾选确认发送。');
        }

        $aid = intval(I('post.aid'));
        $visit_time = trim((string)I('post.visit_time'));
        // Validate raw POST text here and escape exactly once when building the email.
        $visit_location = isset($_POST['visit_location']) && is_string($_POST['visit_location']) ? trim($_POST['visit_location']) : '';
        $uid = intval(session('uid'));

        if(!$aid || !$uid || $visit_time === ''){
            $this->error('请填写预约到场时间！');
        }

        $appointment = M('appointment_view')->where(array('aid'=>$aid,'fixer_id'=>$uid))->find();
        if(!$appointment){
            $this->error('记录不存在或你不能通知别人的预约！');
        }

        if((string)$appointment['status'] === '4'){
            $this->error('已取消的预约不能发送到场通知！');
        }

        $address = trim((string)$appointment['guest_email']);
        if(!filter_var($address, FILTER_VALIDATE_EMAIL)){
            $this->error('该预约没有有效的邮箱地址，请先补充用户邮箱！');
        }

        $visit_datetime = \DateTime::createFromFormat('Y-m-d\\TH:i', $visit_time);
        $datetime_errors = \DateTime::getLastErrors();
        if(!$visit_datetime || ($datetime_errors !== false && ($datetime_errors['warning_count'] || $datetime_errors['error_count']))){
            $this->error('到场时间格式不正确！');
        }

        $escape = function($value){
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        };
        if($visit_location === '' || mb_strlen($visit_location, 'UTF-8') > 255 || preg_match('/[\x00-\x1f\x7f]/', $visit_location)){
            $this->error('请填写有效的到场地点，最多255个字符！');
        }
        $guest_name = $escape($appointment['guest_name']);
        $model = $escape($appointment['model']);
        $issues = nl2br($escape($appointment['issues']));
        $time_text = $visit_datetime->format('Y年m月d日 H:i');

        require_once './ThinkPHP/Library/Vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once './ThinkPHP/Library/Vendor/phpmailer/phpmailer/src/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = C('SMTP_HOST');
        $mail->Port = C('SMTP_PORT');
        $mail->Username = C('SMTP_USER');
        $mail->Password = C('SMTP_PASS');
        $fromEmail = C('SMTP_FROM') ?: C('SMTP_USER');
        $fromName = C('SMTP_FROM_NAME') ?: '管理员';
        $replyEmail = C('SMTP_REPLY_TO') ?: $fromEmail;
        $mail->SetFrom($fromEmail, $fromName);
        $mail->AddReplyTo($replyEmail, $fromName);
        $mail->Subject = '个人维修预约到场通知';
        $signature = C('APPOINTMENT_MAIL_SIGNATURE') ?: (C('SITE_NAME') ?: '计算机协会');
        $mail->MsgHTML(
            '<p>'.$guest_name.'，您好：</p>'
            .'<p>您提交的个人维修预约已安排，请于以下时间到场：</p>'
            .'<p><strong>到场时间：</strong>'.$escape($time_text).'<br>'
            .'<strong>到场地点：</strong>'.$escape($visit_location).'<br>'
            .'<strong>设备型号：</strong>'.$model.'</p>'
            .'<p><strong>故障描述：</strong><br>'.$issues.'</p>'
            .'<p><strong>重要提醒：</strong>请带好电脑和电源，电源一定要携带。</p>'
            .'<p>请按时到场。如时间不便，请直接回复此邮件与我们联系。</p>'
            .'<p>—— '.$escape($signature).'</p>'
        );
        $mail->AddAddress($address, (string)$appointment['guest_name']);

        if($mail->send()){
            $this->success('预约通知邮件已发送！', U('/Garden/Appointment/edit', array('aid'=>$aid)));
        }else{
            error_log('[appointment-mail] send failed for aid '.$aid);
            $this->error('邮件发送失败，请稍后重试或检查邮件配置！');
        }
    }

	/**
	 * print打印预约信息
	 */
	public function print_info()
	{
		if(isset($_GET['aid'])){

			$aid=I('aid');
            $uid=session('uid');

	        if($appointment=M('appointment_view')->where(array('aid'=>$aid,'fixer_id'=>$uid))->find()){

                $shift_count=0;
                $shift[0]['truename']='';
                $shift[1]['truename']='';
                $shift[2]['truename']='';
                $shift[3]['truename']='';
                for ($i=4; $i > 1; $i--) { 

                    if($appointment['fixer'.$i.'_id']){
                        $shift[$shift_count++]=M('garden_user_view')->where(array('uid'=>$appointment['fixer'.$i.'_id']))->find();
                    }else{
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
     * del方法删除预约记录
     */
    public function del(){
        if(M('appointment')->where(array('aid'=>I('aid'),'fixer_id'=>intval(session('uid'))))->delete()){
            $this->success('删除成功！',U('/Garden/Appointment/my'));
        }else{
            $this->error('删除失败，目标记录不存在或者你不能删除别人的记录！',U('/Garden/Appointment/my'));
        }
    }
}
