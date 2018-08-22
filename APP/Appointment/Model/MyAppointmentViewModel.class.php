<?php
namespace Appointment\Model;
use Think\Model\ViewModel;

class MyAppointmentViewModel extends ViewModel{
    protected $viewFields = array(
        'appointment' => array('aid','guest_id','fixer_id','model','issues','addtime','edittime','status','_type'=>'LEFT'),
        'garden_users'  => array('uid','truename'=>'fixer_name','_on'=>'garden_users.uid=appointment.fixer_id'),
    );
}