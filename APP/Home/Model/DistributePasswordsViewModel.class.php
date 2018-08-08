<?php
namespace Home\Model;
use Think\Model\ViewModel;

class DistributePasswordsViewModel extends ViewModel{
    protected $viewFields = array(
        'personal_password' => array('pw_id','pw_cuser','pw_ctime','uid','pw_name','username','password','note','type','status','_type'=>'LEFT'),
        'users'  => array('uid','truename','img','_on'=>'users.uid=personal_password.pw_cuser'),
        //'cuser' => array('name','img','_on'=>'user.id=msg.uid'),
    );
}