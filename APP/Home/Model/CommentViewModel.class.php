<?php
namespace Home\Model;
use Think\Model\ViewModel;

class CommentViewModel extends ViewModel{
    protected $viewFields = array(
        'comment' => array('id','bid','uid','content','time','del','_type'=>'LEFT'),
        'users' => array('truename','img','_on'=>'users.uid=comment.uid'),
    );
}