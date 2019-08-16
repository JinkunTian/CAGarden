<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class DistributePasswordsViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_personal_password' => array('pw_id','pw_cuser','pw_ctime','uid','pw_name','username','password','note','type','status','_type'=>'LEFT'),
        'garden_users'  => array('uid','truename','img','_on'=>'garden_users.uid=garden_personal_password.pw_cuser'),
    );
}