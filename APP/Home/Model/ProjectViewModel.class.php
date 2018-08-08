<?php
namespace Home\Model;
use Think\Model\ViewModel;

class ProjectViewModel extends ViewModel{
    protected $viewFields = array(
        'projects' => array('pr_id','pr_pid','pr_name','pr_ctime','pr_cuser','pr_muser','pr_members','pr_biref','pr_info','pr_status','_type'=>'LEFT'),
        'users' => array('truename'=>'cuser_name','_on'=>'users.uid=projects.pr_cuser'),
        //'users' => array('truename'=>'muser_name','_on'=>'users.uid=projects.pr_muser'),
    );
}