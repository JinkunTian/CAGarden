<?php
namespace Appointment\Model;
use Think\Model\ViewModel;

class MyAppointmentViewModel extends ViewModel{
    protected $viewFields = array(
        'appointment' => array('aid','uid','fixer_id','brand','model','issues','addtime','edittime','status','result','_type'=>'LEFT'),
        'users'  => array('uid'=>'fixer_uid','truename'=>'fixer_name','_on'=>'users.uid=appointment.fixer_id'),
    );
}
