<?php
namespace Home\Model;
use Think\Model\ViewModel;

class MsgViewModel extends ViewModel{
    protected $viewFields = array(
        'msg' => array('id','uid','content','time','del','_type'=>'LEFT'),
        'users' => array('truename','img','_on'=>'users.uid=msg.uid'),
    );
}