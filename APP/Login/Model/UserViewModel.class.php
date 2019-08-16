<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class UserViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_users' => array('uid','username','truename','img','qq','mobile','email','major','dep','position','flag','reg_ip','type','status','_type'=>'LEFT'),
        'common_departments' => array('did','dname','_on'=>'common_departments.did=garden_users.dep'),
        'common_majors' => array('mid','mname','_on'=>'common_majors.mid=garden_users.major'),
    );
}
