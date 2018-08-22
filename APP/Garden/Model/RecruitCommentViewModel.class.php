<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class RecruitCommentViewModel extends ViewModel{
    protected $viewFields = array(
        'recruit_comment' => array('cid','recruit_id','uid','content','addtime','_type'=>'LEFT'),
        'garden_users' => array('truename','img','_on'=>'garden_users.uid=recruit_comment.uid'),
    );
}