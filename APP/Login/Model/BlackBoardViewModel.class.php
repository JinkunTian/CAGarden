<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class BlackBoardViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_blackboard' => array('id','title','author_id','content','visits','addtime','_type'=>'LEFT'),
        'garden_users' => array('truename','img','_on'=>'garden_users.uid=garden_blackboard.author_id'),
    );
}