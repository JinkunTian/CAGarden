<?php
namespace Garden\Controller;
use Think\Controller;

/************************************************* 
Author: 田津坤
QQ    : 2961165914
GitHub: https://github.com/JinkunTian
Date:2018-8-19 
Description:社团纳新管理控制器
**************************************************/  
class RecruitController extends AdminController {
    /**
     * index显示纳新年度
     */
    public function index(){
        $id = (int)session('id');
        $recruits = M('recruit_grade')->select();
        $this->assign('recruits',$recruits);
        $this->display();
    }

    /**
     * edit显示编辑纳新年度
     */
    public function edit(){
        if(isset($_GET['gid'])){
            $this->assign('edit','1');
            $recruit = M('recruit_grade')->where(array('gid'=>I('gid')))->find();
            $this->assign('recruit',$recruit);
        }
        
        $this->display();
    }

    /**
     * save保存编辑好的纳新年度
     */
    public function save(){
        if(isset($_POST)){
            if(isset($_POST['edit'])){
                $data = array(
                    'gid'  => I('gid'),
                    'gname' => I('gname'),
                    'year' => I('year'),
                    'message'=>I('message'),
                    'status'=>I('status'),
                );
                $result=M('recruit_grade')->save($data);
            }else{
                $data = array(
                    'gname' => I('gname'),
                    'year' => I('year'),
                    'message'=>I('message'),
                    
                );
                $result=M('recruit_grade')->add($data);
            }
            
            if($result===false){
                $this->error('保存失败！');
            }else{
                $this->success('保存成功！',U('/Garden/Recruit'));
            }
        }
    }

    /**
     * view显示新成员信息
     */
    public function view(){

        $RecruitView=M('recruit_view');
        $recruit=$RecruitView->where(array('rid'=>I('rid')))->find();

        if(!$recruit){
            $this->error('找不到对象');
        }
        if($recruit['status']!='0'){
        	$garden_info=M('garden_users_extend')->where(array('username' => $recruit['username']))->find();

        	$recruit['recruited_dep']=M('common_departments')->where(array('did' => $garden_info['dep']))->find();
        }
        
        $departments=M('common_departments')->where(array('status'=>'1'))->select();

        $commentView=D('RecruitCommentView');
        $comments=$commentView->where(array('rid'=>$recruit['rid']))->select();

        $this->assign('departments',$departments);
        $this->assign('comments',$comments);
        $this->assign('recruit',$recruit);
        $this->display();
    }

    /**
     * recive接受新成员的纳新申请并将新成员的信息加入社团管理系统
     */
    public function recive(){
        
        $recruit = M('recruit')->where(array('rid'=>I('rid')))->find();
        if($recruit['status']=='0'){
            $recruit['status']='1';/*    更新纳新数据库状态   */
            $change=M('recruit')->where(array('uid'=>$recruit['uid']))->save(array('status'=>1));


            $extend_data = array(
                'uid' => $recruit['uid'],
                'username' => $recruit['username'],
                // 'truename' => $recruit['truename'],
                // 'password' => $recruit['password'],
                // 'salt' => $recruit['salt'],
                // 'reg_ip' => $recruit['reg_ip'],
                // 'addtime' => $recruit['addtime'],
                // 'qq' => $recruit['qq'],
                // 'mobile' => $recruit['mobile'],
                // 'email' => $recruit['email'],
                // 'major' => $recruit['major'],
                'position' => '新成员',
                'dep' =>  I('dep'),
                'flag' => $recruit['flag'],
                'status' => '1',
		        'status_info'=>'正常在任',
                'type' => '1',
                );
            /***    社团管理网站基于ProjectTree搭建，将新成员信息添加到ProjectTree数据库user表  ***/
            $checkExis=M('garden_users_extend')->where(array('username' => $recruit['username']))->find();
            if(!$checkExis){

                /** 
                 * 启用了LDAP就将密码同时写入LDAP和数据库
                 */
                if(C('USE_LDAP')){

                    $base_data=M('users')->->where(array('uid'=>$recruit['uid']))->find();
                    $major=M('common_majors')->where(array('mid'=>$base_data['major']))->find();
                    $dep=M('common_departments')->where(array('did'=>$extend_data['dep']))->find();

                    $UserInfo['truename']=$base_data['truename'];
                    $UserInfo['mail']=$base_data['email'];
                    $UserInfo['telephone']=$base_data['mobile'];
                    $UserInfo['qq']=$base_data['qq'];
                    $UserInfo['description']='协会成员';
                    $UserInfo['department']= $dep['dname']
                    $UserInfo['position']=$extend_data['position'];
                    $UserInfo['company'] = C('SITE_NAME');
                    $UserInfo['office'] = $major['mname'];
                    // $UserInfo['memberOf'] = "CN=Members,".C('BASE_DN');

                    $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
                    if($ds['result']){
                        $res=ldap_add_user_to_group($ds['resource'],C('BASE_DN'),$base_data['username'],'Members');
                        if($res){
                            $result=M('garden_users_extend')->add($newuser);
                            $result=M('users')->where(array('username'=>$recruit['username']))->save(array('userType'=>'garden'));
                            if (!$result===false) {
                                $this->success('纳新成功！',U('/Garden/Recruit/listrecruit',array('grade'=>$recruit['grade'])));
                            }else{
                                $this->error('纳新失败！将用户添加到users数据表时失败！',U('/Garden/Recruit/listrecruit',array('grade'=>$recruit['grade'])));
                            }        
                        }else{
                            $this->error('纳新失败！将用户添加到LDAP目录时失败！',U('/Garden/Recruit/listrecruit',array('grade'=>$recruit['grade'])));
                        }
                    }else{
                        $this->error('与LDAP服务器通信失败！');
                    }
                }else{
                    $result=M('garden_users_extend')->add($newuser);
                    $result=M('users')->where(array('username'=>$recruit['username']))->save(array('userType'=>'garden'));
                    if (!$result===false) {
                        $this->success('纳新成功！',U('/Garden/Recruit/listrecruit',array('grade'=>$recruit['grade'])));
                    }else{
                        $this->error('纳新失败！将用户添加到users数据表时失败！',U('/Garden/Recruit/listrecruit',array('grade'=>$recruit['grade'])));
                    }   
                }
            }else{
                $this->error('该用户名已存在！');
            }
        }else {
            $this->error('当前用户状态不可参加纳新');
        }
    }
    /**
     * addcomment保存面试过程中的记录，面试印象
     */
    public function addcomment(){
        if (!IS_AJAX) $this->error('页面不存在');
        $content=str_replace("\n","<br>",I('content')); //去回车
        $data = array(
            'rid' =>intval(I('rid')), //项目所属ID
            'uid' => intval(session('id')),//日志创建者ID
            'content' =>str_replace(" ","&nbsp;",$content),    //日志内容
            'addtime' =>date('y-m-d H:i:s'),    //日志内容
        );
        //添加数据
        if (M('recruit_comment')->add($data)) {
            $this->ajaxReturn(array('info' => '<i class="fa fa-check"></i> 发布成功','status' => 1), 'json');
        }else{
            $this->ajaxReturn(array('info' => '<i class="fa fa-remove"></i> 发布失败,请重试','status' => 0), 'json');
        }
    }

    /**
     * delcomment删除面试过程中的记录，面试印象
     */
    public function delcomment(){
        $uid=intval(session('id'));
        if(isset($_GET['cid'])){
            $cid=I('cid');
            $comment=M('recruit_comment')->where(array('cid'=>$cid,'uid'=>$uid))->find();
            if($comment){
                M('recruit_comment')->where(array('cid'=>$cid))->delete();
                $this->redirect('/Garden/Recruit/view/rid/'.$comment['rid']);
            }else{
                $this->error('无权操纵！');
            }
        }
    }
    /**
     * listRecruit列出某个纳新年度的成员信息列表
     * grade指定纳新年度，缺省为最新纳新年度
     */
    public function listrecruit(){

        if(isset($_GET['grade'])){
            $grade=I('grade');
            $Recruit=M('recruit_grade')->where(array('gid'=>$grade))->find();
        }else{
            $Recruit=M('recruit_grade')->order(array('gid'=>'desc'))->find();
            $grade=$Recruit['gid'];
        }

        $count = M('recruit')->where(array('grade'=>$grade))->count();//正常状态的用户
        $page = new \Think\Page($count,200);
        $page->setConfig('header','条数据');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','<<');
        $page->setConfig('last','>>');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% <span>共 %TOTAL_ROW% %HEADER%</span>');
        $page->rollPage=3; //控制页码数量
        $show = $page->show();// 分页显示输出
        $limit = $page->firstRow.','.$page->listRows;

        $RecruitView=M('recruit_view');
        $users=$RecruitView->where(array('grade'=>$grade))->order('addtime DESC')->limit($limit)->select();

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('gname',$Recruit['gname']);
        $this->assign('users',$users);
        $this->display();
    }
    
    /**
     * AddUserHelp快速查找某个成员
     */
    public function AddUserHelp(){
        if(isset($_GET['queryString'])||isset($_POST['queryString'])){
            $userForm=M('recruit');
            $where['_string']='(truename like "%'.I('queryString').'%") 
            OR (number like "%'.I('queryString').'%") 
            OR (mobile like "%'.I('queryString').'%") 
            OR (email like "%'.I('queryString').'%") 
            OR (qq like "%'.I('queryString').'%")';
            $data=$userForm->where($where)->select();
            if($data){
                $len=count($data,0);
                for ($i=0; $i < $len; $i++) { 
                    echo '<li onClick="'.I('call_func').'fill(Array(\''.$data[$i]['uid'].'\',\''.$data[$i]['truename'].'\',\''.$data[$i]['major'].'\'));">'.$data[$i]['truename'].'</li>';
                }
            }
        }else{
            die('参数错误！');
        }
    }

}
