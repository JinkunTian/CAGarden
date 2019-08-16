<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class LogsViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_logs' => array('log_id','log_prid','log_cuser','log_ctime','log_info','status','_type'=>'LEFT'),
        'garden_users' => array('truename','img','_on'=>'garden_users.uid=garden_logs.log_cuser'),
    );
}