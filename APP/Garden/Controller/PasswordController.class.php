<?php
namespace Garden\Controller;
use Think\Controller;
class PasswordController extends CommonController {
    public function view(){
        $my_uid=intval(session('id'));
    	if(isset($_GET['pw_id']))
        {
            if(intval($_GET['pw_id'])>0)
            {
                $pw_id = intval($_GET['pw_id']);
            }else{
                die('非法参数！');
            }
        }else{
            die('非法参数！');
        }
        $pw_info=M('garden_public_password')->where(array('pw_id'=>$pw_id))->find(); //查询密码
        if(!$pw_info){
        	$this->error('密码不存在！');
        }
        if(!password_access_right_check($pw_id,$my_uid)){
        	$this->error('没有权限查看！');
        }
        $pr_p_info=M('garden_projects')->where(array('pr_id'=>$pw_info['pw_prid']))->select(); //查询密码所在项目信息
        $pw_info['password']=decode($pw_info['password']);
        $pw_info['note']=decode($pw_info['note']);

        $pw_addition=M('garden_public_password_addition')->where(array('original_password_id'=>$pw_id))->select();
        $pw_addition=decode_addition_password($pw_addition);

        $this->assign('pr_p_info',$pr_p_info);
        $this->assign('password',$pw_info);
        $this->assign('addition_password',$pw_addition);
    	$this->display();
    }
    public function edit(){
        if ( isset($_GET['pw_id']))
        {
            if(intval($_GET['pw_id'])>0)
            {
                $pw_id = intval($_GET['pw_id']);
            }else{
                $this->error('非法参数！');
            }
            $this->assign('edit',true);
            $pw_info=M('garden_public_password')->where(array('pw_id'=>$pw_id))->find(); //查询密码

            if(!$pw_info){
                $this->error('密码不存在！');
            }

            $pw_info['password']=decode($pw_info['password']);
            $pw_info['note']=decode($pw_info['note']);

            $pw_addition=M('garden_public_password_addition')->where(array('original_password_id'=>$pw_id))->select();
            $pw_addition=decode_addition_password($pw_addition);

            if($pw_info['pw_right']){
                $members=explode_members($pw_info['pw_right']);//展开成员信息
            }else{
                $members=false;
            }
            if($pw_info['pw_muser']){
                $managers=explode_members($pw_info['pw_muser']);//展开成员信息
            }else{
                $managers=false;
            }
            //var_dump($managers);
            $this->assign('managers',$managers);
            $this->assign('members',$members);
            $this->assign('pr_id',$pw_info['pw_prid']);
            $this->assign('password',$pw_info);
            $this->assign('addition_password',$pw_addition);
        }elseif (isset($_GET['pr_id'])) {
            if (intval($_GET['pr_id'])==1) {
                $this->error('不允许在根项目下新建密码！');
            }
            $this->assign('add',true);
            $this->assign('pr_id',intval($_GET['pr_id']));
            # code...
        }
        $all_project = M('garden_projects')->where(array('pr_status'=>'1'))->select(); //查询所有项目信息，做为父项目参考
        $this->assign('all_project',$all_project);    
        $this->display();
    }
    public function del(){
        if (isset($_GET['pw_id'])) {
            $pw_info=password_manage_right_check(I('pw_id'));
            if($pw_info){
                $a=M('garden_public_password')->where(array('pw_id'=>$pw_info['pw_id']))->delete();
                $b=M('garden_public_password_addition')->where(array('original_password_id'=>$pw_info['pw_id']))->delete();
                if((!($a===false))&&(!($b===false))){
                    $this->success('删除成功！',U('/Garden/Index/index',array('pr_id'=>$pw_info['pw_prid'])));
                }else{
                    $this->error('删除失败！');
                }
            }else{
                $this->error('无权操作！');
            }
        }else{
            $this->error('非法参数！');
        }
    }
    public function save(){

        $pw_id=intval(I('pw_id'));


        $group_members_access=isset($_POST['group_members_access'])?1:0;

        if(isset($_POST['managers_uids'])){
            $managers_input=':'.implode(":",I('managers_uids')).':';
        }else{
            $managers_input=':'.intval(session('id')).':';
        }
        if(isset($_POST['edit'])){
            $data = array(
                'pw_prid' =>I('pw_prid'),
                'pw_cuser' => intval(session('id')),
                'pw_muser' => $managers_input,
                'pw_name' =>I('pw_name'), 
                'pw_brief' =>I('pw_brief'), 
                'username' =>I('username'), 
                'password' => encode(I('password')),
                'note' => encode(I('note')),
                'pw_right' => ':'.implode(":",I('members_uids')).':',
                'open_access' => I('open_access'),
                'group_members_access'=>$group_members_access,
                'status' => '1',
            );
            
            $pass_update=M('garden_public_password')->where(array('pw_id' =>$pw_id))->save($data);
            //保存数据
            if($pass_update===false) {
                $this->error('保存失败，请重试！',U('/Garden/Index/index/pr_id/'.I('pr_id')));
            }else{
                M('garden_public_password_addition')->where(array('original_password_id' => $pw_id, ))->delete();
                $addition_password=get_addition_password($pw_id);
                if($addition_password['count']>0){
                    $addition_password['password']=encode_addition_password($addition_password['password']);
                    $addition_update=M('garden_public_password_addition')->addAll($addition_password['password']);
                }
                if($addition_update===false){
                    $this->error('保存失败，请重试！',U('/Garden/Index/index/pr_id/'.I('pr_id')));
                }else{
                    $this->success('保存成功',U('/Garden/Password/view/pw_id/'.I('pw_id')));
                }       
            }
        }elseif(isset($_POST['add'])) {
            $data = array(
                'pw_prid' =>I('pw_prid'),
                'pw_cuser' => intval(session('id')),
                'pw_ctime' => date('y-m-d H:i:s'),             
                'pw_muser' => $managers_input,
                'pw_name' =>I('pw_name'),
                'pw_brief' =>I('pw_brief'), 
                'username' =>I('username'),
                'password' => encode(I('password')),
                'note' => encode(I('note')),
                'pw_right' => ':'.implode(":",I('members_uids')).':',
                'open_access' => I('open_access'),
                'group_members_access'=>$group_members_access,
                'status' => '1',
            );
            //先保存主密码数据，得到主密码id后保存附加密码
            $result=M('garden_public_password')->add($data);
            $addition_password=get_addition_password($result);
            if(!($result===false)){
                if($addition_password['count']>0){

                    $addition_password['password']=encode_addition_password($addition_password['password']);
                    $addition_update=M('garden_public_password_addition')->addAll($addition_password['password']);

                    if($addition_update===false){
                        $this->error('添加失败，请重试！',U('/Garden/Index/index/pr_id/'.$data['pw_prid']));
                    }else{
                        $this->success('添加成功',U('/Garden/Index/index/pr_id/'.$data['pw_prid']));
                    }
                }
                $this->success('添加成功',U('/Garden/Index/index/pr_id/'.$data['pw_prid']));
            }else{
                $this->error('添加失败，请重试！',U('/Garden/Index/index/pr_id/'.$data['pw_prid']));     
            }
        }
    }
}