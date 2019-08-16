<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class MyDistPassViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_personal_password' => array('pw_id','pw_cuser','pw_ctime','uid','pw_name','type','status','_type'=>'LEFT'),
        'garden_user_view'  => array('uid','username','truename','_on'=>'garden_user_view.uid=garden_personal_password.uid'),
    );
}