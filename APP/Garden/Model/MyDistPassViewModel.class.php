<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class MyDistPassViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_personal_password' => array('pw_id','pw_ctime','uid','pw_name','type','status','_type'=>'LEFT'),
        'garden_users'  => array('uid','username','truename','_on'=>'garden_users.uid=garden_personal_password.uid'),
    );
}