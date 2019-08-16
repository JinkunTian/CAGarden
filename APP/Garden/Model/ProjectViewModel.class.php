<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class ProjectViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_projects' => array('pr_id','pr_pid','pr_name','pr_ctime','pr_cuser','pr_muser','pr_members','pr_brief','pr_info','pr_status','_type'=>'LEFT'),
        'garden_user_view' => array('truename'=>'cuser_name','_on'=>'garden_user_view.uid=garden_projects.pr_cuser'),
    );
}