<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class MsgViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_msg' => array('id','uid','content','addtime','_type'=>'LEFT'),
        'garden_users' => array('truename','img','_on'=>'garden_users.uid=garden_msg.uid'),
    );
}