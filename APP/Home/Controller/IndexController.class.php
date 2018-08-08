<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        if(isset($_GET['pr_id']))
        {
            if(intval($_GET['pr_id'])>=1)
            {
                $pr_id = intval($_GET['pr_id']);
            }else{
                die('非法参数！');
            }
        }else{
            $pr_id=1; //没有指定项目ID则默认进入根项目
        }

        $this->self_info = M('projects')->where(array('pr_id'=>$pr_id))->select(); //查询当前项目信息
        $pr_p_info=M('projects')->where(array('pr_id'=>$this->self_info[0]['pr_pid']))->select(); //查询父项目信息
        //父权限，可管理当前项目及所有子项目
        if($this->self_info[0]['pr_cuser']==intval(session('id'))){
            $right['creat']=true;
        }
        if($this->self_info[0]['pr_muser']==intval(session('id'))){
            $right['manage']=true;
        }

        if($this->self_info[0]['pr_members']){
            $this->members=explode_members($this->self_info[0]['pr_members']);
        }else{
            $this->members=false;
        }
        //var_dump($this->members);
        $this->assign('right',($right['creat']||$right['manage']));

        $this->assign('project_info',$this->self_info[0]['pr_info']);


        $list = M('projects')->where(array('pr_pid'=>$this->self_info[0]['pr_id'],'pr_status'=>'1'))->select(); //查询子项目信息
        $len=count($list, 0);
        if($len){
            for($i=0;$i<$len;$i++)
            {
                $list[$i]['cuser_info']= M('users')->where(array('uid'=>$list[$i]['pr_cuser']))->find();
                $musers=explode_members($list[$i]['pr_muser']);
                $list[$i]['muser_info']=$musers[0];
		        $list[$i]['right']=true;
            }

        }
        $this->projects= $list;
        $this->assign('ProjectTitle',$this->self_info[0]['pr_name']);
        $this->assign('prid',$pr_id);
        $this->assign('pr_p_info',$pr_p_info);
        if($pr_id==1){$this->assign('isroot',true);}//根项目
        $password = M('public_password')->where(array('pw_prid'=>$pr_id,'status'=>'1'))->select(); //查询项目密码信息
        $len=count($password, 0);
        if($len){
            for($i=0;$i<$len;$i++)
            {
                $password[$i]['cuser_info']= M('users')->where(array('uid'=>$password[$i]['pw_cuser']))->find();

                $musers=explode_members($password[$i]['pw_muser']);
                $password[$i]['muser_info']= $musers[0];

                $list_right=false;
                if($password[$i]['pw_cuser']==intval(session('id'))){
                    $list_right=true;
                }
                if($password[$i]['pw_muser']==intval(session('id'))){
                    $list_right=true;
                }
                $password[$i]['right']=($list_right||$right['creat']||$right['manage']);
            }
        }
        
        $this->assign('password',$password);

        //日志
        $logs = M('logs')->where(array('log_prid'=>$pr_id,'status'=>'1'))->select(); //查询根项目信息
        $len=count($logs,0);
        for ($i=0; $i < $len; $i++) { 
            $logs[$i]['user_info']=M('users')->where(array('uid'=>$logs[$i]['log_cuser']))->select();
        }
        $this->logs_info=$logs;
        $this->display();
    }

    public function addlog(){
        if (!IS_AJAX) $this->error('页面不存在');
        $content=str_replace("\n","<br>",I('content')); //去回车
        $data = array(
            'log_prid' =>intval(I('prid')), //项目所属ID
            'log_cuser' => intval(session('id')),//日志创建者ID
            'log_ctime' => time(),              //创建时间
            'log_info' =>str_replace(" ","&nbsp;",$content),    //日志内容
        );
        //添加数据
        if (M('logs')->add($data)) {
            $this->ajaxReturn(array('info' => '<i class="fa fa-check"></i> 发布成功','status' => 1), 'json');
        }else{
            $this->ajaxReturn(array('info' => '<i class="fa fa-remove"></i> 发布失败,请重试','status' => 0), 'json');
        }
    }
    public function dellog(){
        $id = (int)$_GET['log_id'];
        $log_info=M('logs')->where(array('log_id'=>$id))->select();
        if($log_info[0]['log_cuser']==intval(session('id')))
        {
            if (M('logs')->where(array('log_id' => $id))->save(array('status'=>'2'))) {
                $this->redirect('/Home/Index/index/pr_id/'.intval($_GET['pr_id']));
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('没有权限删除！');
        }        
    }
    public function editproject(){
        if(isset($_GET['pr_id']))
        {
            //编辑项目
            if(intval($_GET['pr_id'])>=1)
            {
                $pr_id = intval($_GET['pr_id']);
            }else{
                $this->error('页面不存在！');
            }
            $this->self_info = M('projects')->where(array('pr_id'=>$pr_id))->find(); //查询根项目信息
            $this->assign('projects',$this->self_info);
            $this->assign('edit',true);
            $all_project = M('projects')->where(array('pr_status'=>'1'))->select(); //查询所有项目信息，做为父项目参考
            $this->assign('all_project',$all_project);

            if($this->self_info['pr_members']){
                $members=explode_members($this->self_info['pr_members']);//展开成员信息
            }else{
                $members=false;
            }
            if($this->self_info['pr_muser']){
                $managers=explode_members($this->self_info['pr_muser']);//展开成员信息
            }else{
                $managers=false;
            }
            $this->assign('members',$members);
            $this->assign('managers',$managers);

        }else{
            //新建项目
            $all_project = M('projects')->where(array('pr_status'=>'1'))->select(); //查询所有项目信息，做为父项目参考
            $this->assign('all_project',$all_project);
            if(isset($_GET['pr_pid'])){
                $projects['pr_pid']=intval($_GET['pr_pid']);              
            }else{
                $projects=NULL;
            }
            $this->assign('projects',$projects);
            
        }
        $this->display();
    }
    public function save_project(){
        if(isset($_POST['managers_uids'])){
            $managers_input=':'.implode(":",I('managers_uids')).':';
        }else{
            $managers_input=':'.intval(session('id')).':';
        }
        if(I('members_uids')==''){
            $members_input='';
        }else{
            $members_input=':'.implode(":",I('members_uids')).':';
        }
        if(isset($_POST['edit'])){
            $data = array(
                'pr_pid' =>I('pr_pid'),
                'pr_name' =>I('pr_name'), //项目所属ID
                'pr_muser' => $managers_input,
                'pr_members' => $members_input,
                'pr_biref' => I('pr_biref'),
                'pr_info' => I('pr_info'),//
                'pr_status' => I('pr_status'),
            );
            if($data['pr_id']=='1'){
                $data['pr_pid']='0';
            }
            $updata=M('projects')->where(array('pr_id' =>I('pr_id')))->save($data);
            if($updata===false){
                $this->error('保存失败',U('/Home/Index/index/pr_id/'.I('pr_id')));
            }else{
                $this->success('保存成功',U('/Home/Index/index/pr_id/'.I('pr_id')));
            }
        }elseif(isset($_POST['add'])) {
            $data = array(
                'pr_pid' =>I('pr_pid'),
                'pr_name' =>I('pr_name'), //项目所属ID
                'pr_ctime' => time(),//日志创建者ID
                'pr_cuser' => intval(session('id')),
                'pr_muser' => $managers_input,
                'pr_members' => implode(":",I('members_uids')),
                'pr_biref' => I('pr_biref'),
                'pr_info' => I('pr_info'),
                'pr_status' => I('pr_status'),
            );
            //添加数据
            if (M('projects')->add($data)) {
                $this->success('保存成功',U('/Home/Index/index/pr_id/'.$data['pr_pid']));
            }else{
                $this->error('保存失败',U('/Home/Index/index/'));
            }
        }
    }
    public function dell_project(){
        if(isset($_GET['pr_id'])&&isset($_GET['pr_pid'])){
            if(I('pr_id')==1){
                $this->error('删除失败！');
            }elseif(M('projects')->where(array('pr_id' =>I('pr_id')))->save(array('pr_status' =>'5'))){
                $this->redirect('/Home/Index/index/pr_id/'.intval($_GET['pr_pid']));
            }else{
                $this->error('删除失败！');
            }
        }else {
            $this->error('非法参数！');
        }
    }
    //退出登录并跳转到登录页面
    public function logout () {
        session_unset();
        session_destroy();
        $this->redirect('/Home/Login');
    }
}