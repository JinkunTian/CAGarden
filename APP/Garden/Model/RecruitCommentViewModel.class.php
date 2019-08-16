<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class RecruitCommentViewModel extends ViewModel{
    protected $viewFields = array(
        'recruit_comment' => array('cid','recruit_uid','uid','content','addtime','_type'=>'LEFT'),
        'garden_user_view' => array('truename','img','_on'=>'garden_user_view.uid=recruit_comment.uid'),
    );
}