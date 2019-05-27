<?php
namespace Garden\Controller;
use Think\Controller;
/************************************************* 
Author: 田津坤
QQ    : 2961165914
GitHub: https://github.com/JinkunTian
Date:2018-8-19 
Description:ProjectTree项目控制器，将团队项目，
            日志，密码统一组织管理
**************************************************/ 
class IndexController extends CommonController {
    /**
     * index主方法，列出项目与子项目，密码，日志
     * 实现方法过于复杂，SQL查询数过高有待优化
     * 先用着有空再说
     */
    public function index(){

        $uid=intval(session('id'));

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

        $project=project_manage_right_check_by_pr_id($pr_id);

        /**
         * 根项目管理员可编辑
         * 检查当前用户是否为管理员
         */
        if($pr_id==1){
            $userinfo=M('garden_users')->where(array('uid'=>$uid))->find();
            if($userinfo['type']=='2'){
                $project['manage_right_check']=true;
            }
        }
        //$this->self_info = M('garden_projects')->where(array('pr_id'=>$pr_id))->select(); //查询当前项目信息
        //$project=D('ProjectView')->where(array('pr_id'=>$pr_id))->find();
        $pr_p_info=M('garden_projects')->where(array('pr_id'=>$project['pr_pid']))->find(); //查询父项目信息
        //父权限，可管理当前项目及所有子项目
        // if($project['pr_cuser']==$uid){
        //     $right['creat']=true;
        // }
        // if($project['pr_muser']==$uid){
        //     $right['manage']=true;
        // }
        // $thisProject=M('garden_projects');
        // $fatherProject=M('garden_projects');
        // $list=$Demo->table('pt_projects projects,pt_projects p_projects')
        //     ->where('blog.typeid=type.id')
        //     ->field('blog.id as id,blog.title,blog.content,type.typename as type')
        //     ->order('blog.id desc' )
        //     ->limit(5)->select();
        //     echo $Demo->getLastSql(); 
        if($project['pr_members']){
            $this->members=explode_members($project['pr_members']);
        }else{
            $this->members=false;
        }
        $project['musers']=explode_members($project['pr_muser']);
        $this->assign('main_project',$project);

        $list = D('ProjectView')->where(array('pr_pid'=>$project['pr_id'],'pr_status'=>'1'))->select(); //查询子项目信息
        $len=count($list, 0);
        if($len){
            for($i=0;$i<$len;$i++)
            {
                $musers=explode_members($list[$i]['pr_muser']);
                $list[$i]['muser_info']=$musers[0];
                $musers_count=count($musers,0);

		        $list[$i]['right']=false;

                if($list[$i]['pr_cuser']==$uid){
                    $list[$i]['right']=true;
                }
                for ($z=0; $z < $musers_count; $z++) { 
                    if($musers[$z]['uid']==$uid){
                        $list[$i]['right']=true;
                    }
                }
            }
        }
        $this->assign('sub_projects',$list);
        $this->assign('pr_p_info',$pr_p_info);
        if($pr_id==1){$this->assign('isroot',true);}//根项目
        $password = D('PublicPasswordView')->where(array('pw_prid'=>$pr_id,'status'=>'1'))->select(); //查询项目密码信息        
        if($password){
            $len=count($password, 0);
            for($i=0;$i<$len;$i++)
            {
                $musers=explode_members($password[$i]['pw_muser']);
                $musers_count=count($musers,0);
                $password[$i]['muser_info']= $musers[0];//显示第一个密码管理员（主管理员）信息

                $list_right=false;
                if($password[$i]['pw_cuser']==$uid){
                    $list_right=true;
                }
                for ($z=0; $z < $musers_count; $z++) { 
                    if($musers[$z]['uid']==$uid){
                        $list_right=true;
                    }
                }
                $password[$i]['right']=($list_right||($project['manage_right_check']&&((int)$password[$i]['project_mamager_permit'])));
            }
        }
        $this->assign('password',$password);

        //日志
        $logs = D('LogsView')->where(array('log_prid'=>$pr_id,'status'=>'1'))->select(); //查询根项目信息
        $this->logs_info=$logs;
        $this->display();
    }

    /**
     * addlog方法添加日志
     */
    public function addlog(){
        if (!IS_AJAX) $this->error('页面不存在');
        $content=str_replace("\n","<br>",I('content')); 		//去回车
        $data = array(
            'log_prid' =>intval(I('prid')), 					//项目所属ID
            'log_cuser' => intval(session('id')),				//日志创建者ID
            'log_ctime' => date('y-m-d H:i:s'),								//创建时间
            'log_info' =>str_replace(" ","&nbsp;",$content),	//日志内容
        );
        //添加数据
        if (M('garden_logs')->add($data)) {
            $this->ajaxReturn(array('info' => '<i class="fa fa-check"></i> 发布成功','status' => 1), 'json');
        }else{
            $this->ajaxReturn(array('info' => '<i class="fa fa-remove"></i> 发布失败,请重试','status' => 0), 'json');
        }
    }

    /**
     * dellog方法删除日志
     */
    public function dellog(){
        $id = (int)$_GET['log_id'];
        $log_info=M('garden_logs')->where(array('log_id'=>$id))->find();
        if($log_info['log_cuser']==intval(session('id')))
        {
            if (M('garden_logs')->where(array('log_id' => $id))->save(array('status'=>'2'))) {
                $this->redirect('/Garden/Index/index/pr_id/'.intval($_GET['pr_id']));
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('你不能删除别人的日志！');
        }        
    }

    /**
     * editproject方法编辑项目
     */
    public function editproject(){
        if(isset($_GET['pr_id']))
        {
            //获取项目ID
            if(intval($_GET['pr_id'])>=1)
            {
                $pr_id = intval($_GET['pr_id']);
            }else{
                $this->error('页面不存在！');
            }

            //查询项目信息
            $projects = M('garden_projects')->where(array('pr_id'=>$pr_id))->find();
            if($projects){
	            //查询所有项目信息，做为父项目参考
	            $all_project = M('garden_projects')->where(array('pr_status'=>'1'))->select(); 
	            
	            //展开管理员与成员信息
	            if($projects['pr_members']){
	                $members=explode_members($projects['pr_members']);
	            }else{
	                $members=false;
	            }
	            if($projects['pr_muser']){
	                $managers=explode_members($projects['pr_muser']);
	            }else{
	                $managers=false;
	            }

	            $this->assign('edit',true);
	            $this->assign('projects',$projects);
	            $this->assign('all_project',$all_project);
	            $this->assign('members',$members);
	            $this->assign('managers',$managers);
	
            }else{
            	$this->error('项目不存在！');
            }

        }else{

            //新建项目

			//查询所有项目信息，做为父项目参考
            $all_project = M('garden_projects')->where(array('pr_status'=>'1'))->select(); 

            //指定父项目
            if(isset($_GET['pr_pid'])){
                $projects['pr_pid']=intval($_GET['pr_pid']);              
            }else{
                $projects=NULL;
            }

            $this->assign('all_project',$all_project);
            $this->assign('projects',$projects);
        }
        $this->display();
    }

    /**
     * save_project方法保存项目
     */
    public function save_project(){

    	//预处理管理员列表
        if(isset($_POST['managers_uids'])){
            $managers_input=':'.implode(":",I('managers_uids')).':';
        }else{
            $managers_input=':'.intval(session('id')).':';
        }

        //预处理项目成员列表
        if(I('members_uids')==''){
            $members_input='';
        }else{
            $members_input=':'.implode(":",I('members_uids')).':';
        }

        //保存编辑内容
        if(isset($_POST['edit'])){
            $data = array(
                'pr_id' =>I('pr_id'),
                'pr_pid' =>I('pr_pid'),
                'pr_name' =>I('pr_name'), //项目所属ID
                'pr_muser' => $managers_input,
                'pr_members' => $members_input,
                'pr_brief' => I('pr_brief'),
                'pr_info' => I('pr_info'),//
                'pr_status' => I('pr_status'),
            );
            if($data['pr_id']=='1'){
                $data['pr_pid']='0';
            }
            $updata=M('garden_projects')->where(array('pr_id' =>I('pr_id')))->save($data);
            if($updata===false){
                $this->error('保存失败',U('/Garden/Index/index/pr_id/'.I('pr_id')));
            }else{
                $this->success('保存成功',U('/Garden/Index/index/pr_id/'.I('pr_id')));
            }
        }elseif(isset($_POST['add'])) {

        	//保存新建内容
            $data = array(
                'pr_pid' =>I('pr_pid'),
                'pr_name' =>I('pr_name'), //项目所属ID
                'pr_ctime' => date('y-m-d H:i:s'),//日志创建者ID
                'pr_cuser' => intval(session('id')),
                'pr_muser' => $managers_input,
                'pr_members' => implode(":",I('members_uids')),
                'pr_brief' => I('pr_brief'),
                'pr_info' => I('pr_info'),
                'pr_status' => I('pr_status'),
            );
            //添加数据
            if (M('garden_projects')->add($data)) {
                $this->success('保存成功',U('/Garden/Index/index/pr_id/'.$data['pr_pid']));
            }else{
                $this->error('保存失败',U('/Garden/Index/index/'));
            }
        }
    }

    /**
     * dell_project方法删除项目
     */
    public function dell_project(){
        if(isset($_GET['pr_id'])&&isset($_GET['pr_pid'])){

        	$projects=project_manage_right_check_by_pr_id(I('pr_id'));

        	//不允许删除根项目
            if(I('pr_id')==1){
                $this->error('删除失败！不允许删除根项目！');
            }elseif($projects['manage_right_check']){
            	if(M('garden_projects')->where(array('pr_id' =>I('pr_id')))->save(array('pr_status' =>'25'))) {
                	$this->redirect('/Garden/Index/index/pr_id/'.intval($_GET['pr_pid']));
                }else{
                	$this->error('删除失败！');
                }
            }else{
                //$this->error('你没有权限删除这个项目！');
            }
        }else {
            $this->error('非法参数！');
        }
    }
    //退出登录并跳转到登录页面
    public function logout () {
        session_unset();
        session_destroy();
        $this->redirect('/Garden/Login');
    }
}