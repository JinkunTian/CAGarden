<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class PublicPasswordViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_public_password' => array('pw_id','pw_prid','pw_cuser','pw_ctime','pw_muser','pw_name','pw_brief','username','password','note','pw_right','group_members_access','status','_type'=>'LEFT'),
        'garden_users'  => array('uid','truename'=>'cuser_name','_on'=>'garden_users.uid=garden_public_password.pw_cuser'),
    );
}