<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class CommentViewModel extends ViewModel{
    protected $viewFields = array(
        'garden_comment' => array('id','bid','uid','content','addtime','_type'=>'LEFT'),
        'garden_users' => array('truename','img','_on'=>'garden_users.uid=garden_comment.uid'),
    );
}