<?php
namespace Home\Model;
use Think\Model\ViewModel;

class MyDistPassViewModel extends ViewModel{
    protected $viewFields = array(
        'personal_password' => array('pw_id','pw_ctime','uid','pw_name','type','status','_type'=>'LEFT'),
        'users'  => array('uid','username','truename','_on'=>'users.uid=personal_password.uid'),
    );
}