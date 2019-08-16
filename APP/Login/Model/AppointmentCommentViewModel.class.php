<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class AppointmentCommentViewModel extends ViewModel{
    protected $viewFields = array(
        'appointment_comment' => array('cid','aid','guest_id','fixer_id','comment','addtime','_type'=>'LEFT'),
        'garden_users'  => array('uid','truename'=>'fixer_name','_on'=>'garden_users.uid=appointment_comment.fixer_id'),
        'appointment_users'  => array('guest_id','truename'=>'guest_name','_on'=>'appointment_users.guest_id=appointment_comment.guest_id'),
    );
}