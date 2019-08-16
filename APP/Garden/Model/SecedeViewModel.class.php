<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class SecedeViewModel extends ViewModel{
    protected $viewFields = array(
    	'garden_secede'=>array('uid','secede_info','status','addtime','_type'=>'LEFT'),
        'garden_user_view' => array('uid','username','truename','img','qq','mobile','email','major','dep','position','flag','reg_ip','type','_on'=>'garden_user_view.uid=garden_secede.uid'),
        'common_departments' => array('did','dname','_on'=>'common_departments.did=garden_user_view.dep'),
        'common_majors' => array('mid','mname','_on'=>'common_majors.mid=garden_user_view.major'),
    );
}
