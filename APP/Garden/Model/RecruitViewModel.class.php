<?php
namespace Garden\Model;
use Think\Model\ViewModel;

class RecruitViewModel extends ViewModel{
    protected $viewFields = array(
        'recruit' => array('recruit_id','number','truename','img','qq','mobile','email','major','dep','flag','github','website','info','grade','reg_ip','status','_type'=>'LEFT'),
        'recruit_grade' => array('gid','gname','year','_on'=>'recruit_grade.gid=recruit.grade'),
        'common_departments' => array('did','dname','_on'=>'common_departments.did=recruit.dep'),
        'common_majors' => array('mid','mname','_on'=>'common_majors.mid=recruit.major'),
    );
}
