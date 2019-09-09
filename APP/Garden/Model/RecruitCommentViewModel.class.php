<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class RecruitCommentViewModel extends ViewModel{
    protected $viewFields = array(
        'recruit_comment' => array('cid','rid','uid','content','addtime','_type'=>'LEFT'),
        'garden_user_view' => array('truename','img','_on'=>'garden_user_view.uid=recruit_comment.uid'),
    );
}