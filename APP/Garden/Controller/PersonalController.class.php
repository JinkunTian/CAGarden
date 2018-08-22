<?php
namespace Garden\Controller;
use Think\Controller;
class PersonalController extends CommonController {
    public function index(){
        $my_uid=intval(session('id'));

        $distribute_passwords = D('DistributePasswordsView')->where(array('uid' => $my_uid,'type'=> '2'))->select();

        $where['_string']='(pw_muser like "%:'.$my_uid.':%") OR (pw_cuser = "'.$my_uid.'") OR (pw_right like "%:'.$my_uid.':%")';
        $related_passwords=M('garden_public_password')->where($where)->select();

        $personal_passwords=M('garden_personal_password')->where( array('uid' => intval(session('id')),'type'=> '1'))->select();

        $where['_string']='(pr_muser like "%:'.$my_uid.':%") OR (pr_cuser = "'.$my_uid.'")';
        $where['pr_status']=array('lt',20);
        $my_projects=M('garden_projects')->where($where)->select();

        $where['_string']='(pr_members like "%:'.$my_uid.':%")';
        $where['pr_status']=array('lt',20);
        $my_join_projects=M('garden_projects')->where($where)->select();

        $this->assign('distribute_passwords',$distribute_passwords);
        $this->assign('related_passwords',$related_passwords);
        $this->assign('personal_passwords',$personal_passwords);
        $this->assign('my_projects',$my_projects);
        $this->assign('my_join_projects',$my_join_projects);
        $this->display();

    }
    public function view(){
        $my_uid=intval(session('id'));
    	if(isset($_GET['pw_id']))
        {
            if(intval($_GET['pw_id'])>0)
            {
                $pw_id = intval($_GET['pw_id']);
                $pw_info=M('garden_personal_password')->where(array('pw_id'=>$pw_id))->find(); //查询密码
                if(!$pw_info){
                    $this->error('密码不存在！');
                }

                $pw_info['password']=decode($pw_info['password']);
                $pw_info['note']=decode($pw_info['note']);

                if($pw_info['uid']==$my_uid){   //判断该密码是否为本人所有
                    $pw_addition=M('garden_personal_password_addition')->where(array('original_password_id'=>$pw_id))->select();
                    $pw_addition=decode_addition_password($pw_addition);
                    if(($pw_info['type']=='2')&&($pw_info['status']=='1')){
                        M('garden_personal_password')->where(array('pw_id'=>$pw_id))->save(array('status'=>'2'));
                    }
                    $this->assign('password',$pw_info);
                    $this->assign('addition_password',$pw_addition);
                    $this->display();
                }else{
                    $this->error('没有权限查看！'); //不是本人所属的密码
                }  
            }else{
                $this->error('非法参数！');
            }
        }else{
            $this->error('非法参数！');
        }	
    }
    public function edit(){

        $my_uid=intval(session('id'));

        if (isset($_GET['pw_id']))
        {
            if(intval($_GET['pw_id'])>0)
            {
                $pw_id = intval($_GET['pw_id']);
                $this->assign('edit',true);
                $pw_info=M('garden_personal_password')->where(array('pw_id'=>$pw_id))->find(); //查询密码
                if($pw_info){
                    if($pw_info['uid']==$my_uid){

                        $pw_info['password']=decode($pw_info['password']);
                        $pw_info['note']=decode($pw_info['note']);

                        $pw_addition=M('garden_personal_password_addition')->where(array('original_password_id'=>$pw_id))->select();
                        $pw_addition=decode_addition_password($pw_addition);

                        $this->assign('password',$pw_info);
                        $this->assign('addition_password',$pw_addition);
                        $this->display();
                    }else{
                        $this->error('没有权限查看！');//不是本人所属的密码
                    }
                }else{
                    $this->error('密码不存在！');
                }
            }else{
                $this->error('非法参数！');
            }
        }else{
            $this->display();
        } 
    }
    public function del(){
        if (isset($_GET['pw_id'])) {
            $pw_info=M('garden_personal_password')->where(array('pw_id'=>I('pw_id')))->find();
            if($pw_info['uid']==intval(session('id'))){
                $a=M('garden_personal_password')->where(array('pw_id'=>$pw_info['pw_id']))->delete();
                $b=M('garden_personal_password_addition')->where(array('original_password_id'=>$pw_info['pw_id']))->delete();
                if((!($a===false))&&(!($b===false))){
                    $this->success('删除成功！',U('/Garden/Personal/index'));
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

        $my_uid=intval(session('id'));

        if(isset($_POST['edit'])){
            $data = array(
                'pw_name' =>I('pw_name'),
                'pw_brief' =>I('pw_brief'), 
                'username' =>I('username'),
                'password' => encode(I('password')),
                'note' => encode(I('note')),
                'status' => '1',
            );
            $pass_find=M('garden_personal_password')->where(array('pw_id' =>$pw_id))->find();
            if($pass_find['uid']==$my_uid){
                $password_update=M('garden_personal_password')->where(array('pw_id' =>$pw_id))->save($data);
                M('garden_personal_password_addition')->where(array('original_password_id' => $pw_id, ))->delete();
                $addition=get_addition_password($pw_id);
                if($addition['count']>0){

                    $addition['password']=encode_addition_password($addition['password']);
                    $addition_update=M('garden_personal_password_addition')->addAll($addition['password']);

                    if(($addition_update===false)||($password_update===false)){
                        $this->error('保存失败，请重试！',U('/Garden/Personal/'));
                    }else{
                        $this->success('保存成功',U('/Garden/Personal/view',array('pw_id' => $pw_id , )));
                    }
                }
                if($password_update===false){
                    $this->error('保存失败，请重试！',U('/Garden/Personal/'));
                }else{
                    $this->success('保存成功',U('/Garden/Personal/view',array('pw_id' => $pw_id , )));
                }
            }else{
                $this->error('密码不存在或没有权限查看！');
            }
        }elseif(isset($_POST['add'])) {
            $data = array(
                'pw_cuser' => $my_uid,
                'pw_ctime' => time(),
                'uid' => $my_uid,
                'pw_name' =>I('pw_name'),
                'pw_brief' =>I('pw_brief'),
                'username' =>I('username'),
                'password' => encode(I('password')),
                'note' => encode(I('note')),
                'type' => '1',//用户自添加，2为集体分发
                'status' => '1',
            );
            //先保存主密码数据，得到主密码id后保存附加密码
            $result=M('garden_personal_password')->add($data);
            if($result)
            {
                $addition=get_addition_password($result);
                if($addition['count']>0){

                    $addition['password']=encode_addition_password($addition['password']);
                    $addition_update=M('garden_personal_password_addition')->addAll($addition['password']);

                    if($addition_update===false){

                        $this->error('保存失败，请重试！',U('/Garden/Personal/'));
                    }
                }
                $this->success('保存成功',U('/Garden/Personal/view',array('pw_id' => $result)));
            }else{
                $this->error('保存失败，请重试！',UU('/Garden/Personal/'));
                
            }
        }
    }
}