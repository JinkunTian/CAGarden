<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020-8-31
 * @Update：     2020-9-6
 * @Description: 用户（普通）控制器
 ***/
namespace Garden\Controller;
use Think\Controller;
class UserController extends CommonController {
    public function index(){
        $id = (int)session('uid');
        $UserView=M('garden_user_view');

        $map['status'] = array('gt',0);
        
        $this->departments=M('common_departments')->where($map)->select();
        $this->majors=M('common_majors')->where(array('status'=>'1'))->select();
        $this->user_data = $UserView->where(array('uid'=>$id))->select();

        $this->display();
    }
    
    /**
     * 显示留会页面
     */
    public function succeed(){
        $id = (int)session('uid');
        $this->user_data=M('garden_succeed')->where(array('uid'=>$id))->find();
        $this->display();
    }
    /**
     * 留会提交处理
     */
    public function succeed_post(){
        $id = (int)session('uid');
        $secede_info=array(
            'uid'=>$id,
            'username'=>session('username'),
            'truename'=>session('truename'),
            'type'=>I('type'),
            'succeed_info'=>I('succeed_info'),
            'addtime'=>date('y-m-d H:i:s')
        );
        if(M('garden_succeed')->where(array('uid'=>$id))->find()){
            if(!(M('garden_succeed')->where(array('uid'=>$id))->save($secede_info)===false)){
                $this->success('退留任信息更新成功！',U('/Garden/User/succeed?random='.rand()));
            }else{
                $this->error('退留任信息更新失败！',U('/Garden/User/succeed?random='.rand()));
            }
        }else{
            if(!(M('garden_succeed')->where(array('uid'=>$id))->add($secede_info)===false)){
                $this->success('退留任信息更新成功！',U('/Garden/User/succeed?random='.rand()));
            }else{
                $this->error('退留任信息更新失败！',U('/Garden/User/succeed?random='.rand()));
            }
        }
    }

    /**
     * 保存用户的头像
     */
    public function save_img(){
        $base64_image_content = $_POST['file'];
        $root_path="Public";
        $sub_path="/Uploads/Profiles";
        
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $file_name=substr(md5($_POST['file']),0,13).".{$type}";
            $file_url=$sub_path."/".date('Y-m-d',time())."/";
            $new_file = $root_path.$file_url;

            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                if(!(mkdir($new_file, 0777,true))){
                    $this->error('目录不可写，上传失败！');
                }
            }
            $new_file = $root_path.$file_url.$file_name;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                echo 'success';
                $result2=M('users')->where(array('uid'=>session('uid')))->save(array('img'=>$file_url.$file_name));
            }else{
                echo false;
            }
        }else{
            echo false;
        }
    }

    /**
     * 保存用户编辑的个人信息
     */
    public function datapost(){

        $update_result['result']='error';
        $update_result['msg']='未知错误！';

        /**
         * 基础信息
         */
        $base_data=array(
            'truename' => I('truename'),
            'mobile' => I('mobile'),
            'qq' => I('qq'),
            'email' => I('email'),
            'major' => I('major')
        );
        /** 
         * 社团信息
         */
        $extend_data=array(
            'dep' => I('dep'),
            'position' =>I('position'),
            'flag' => I('flag')
            );

        if(I('uid')==intval(session('uid'))){

            /** 
             * 启用了LDAP就将密码同时写入LDAP和数据库
             */
            // if(C('USE_LDAP')){

            //     $major=M('common_majors')->where(array('mid'=>$base_data['major']))->find();
            //     $dep=M('common_departments')->where(array('did'=>$extend_data['dep']))->find();

            //     $UserInfo['truename']=$base_data['truename'];
            //     $UserInfo['mail']=$base_data['email'];
            //     $UserInfo['telephone']=$base_data['mobile'];
            //     $UserInfo['qq']=$base_data['qq'];
            //     $UserInfo['description']='协会成员';
            //     $UserInfo['department']= $dep['dname'];
            //     $UserInfo['position']=$extend_data['position'];
            //     $UserInfo['company'] = C('SITE_NAME');
            //     $UserInfo['office'] = $major['mname'];

            //     $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
            //     if($ds['result']){
            //         $res=ldap_change_user_info($ds['resource'],session('username'),C('BASE_DN'),$UserInfo);
            //         if($res){
            //             $result1=M('users')->where(array('uid' => I('uid') ))->save($base_data);
            //             $result2=M('garden_users_extend')->where(array('uid' => I('uid') ))->save($extend_data);
            //             if ($result1===false||$result2===false) {
            //                 $this->error('保存失败！');
            //             }else{

            //                 $this->success('保存成功！',U('/Garden/User/look',array('uid'=>I('uid'))));
            //             }   
            //         }else{
            //             $this->error('保存到LDAP目录失败！');
            //         }
            //     }
            // }else{
                $result1=M('users')->where(array('uid' => I('uid') ))->save($base_data);
                $result2=M('garden_users_extend')->where(array('uid' => I('uid') ))->save($extend_data);
                if ($result1===false||$result2===false) {
                    $update_result['result']='error';
                    $update_result['msg']='保存数据时出错！请联系管理员处理！';
                }else{
                    $update_result['result']='success';
                    $update_result['msg']='用户信息保存成功！';
                    $update_result['url']=U('/Garden/User/look',array('uid'=>I('uid')));
                }  
            // }
        }else{
            $update_result['result']='error';
            $update_result['msg']='你不能编辑别人的信息！';
        }
        return_json($update_result);
    }
    
    /**
     * 更改密码
     */
    public function changgepwd(){
        $this->display();
    }
    //更改密码表单
    public function pwdpost(){
      //对表单数据进行MD5加密
      $password = I('password','','md5');
      $newpws = I('newpws','','md5');
      $newpwss = I('newpwss','','md5');
      //把要写入的数据转换成数组
      $salt=md5(time());
      $data =array(
        'password' => md5($salt.$newpwss),
        'salt' => $salt,
      );

      $user = M('users')->where(array('uid' => session('uid')))->find();
      $old_pass=md5($user['salt'].$password);
      //判断输入的密码是否正确
      if ($newpws!=$newpwss || $user['password'] != $old_pass) {
          $this->error('旧密码错误或者新密码不一致');
      }else{
            if(C('USE_LDAP')){
                $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
                if($ds['result']){
                    $res=ldap_change_password($ds['resource'],$user['username'],C('BASE_DN'),$newpws);
                    if($res){
                        if (M('users')->where(array('uid' => session('uid')))->save($data)) {
                            $this->success('修改成功');
                        }else{
                            $this->error('修改网站账户失败');
                        } 
                    }else{
                        $this->error('修改域账户失败，密码不够安全，请设置一个安全性较强的密码！');
                    }
                }else{
                    $this->error('与LDAP服务器通信失败！');
                }
            }else{
                //写入密码
                if (M('users')->where(array('uid' => session('uid')))->save($data)) {
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败');
                } 
            }
        }
    }

    /**
     * 查看用户资料
     */
    public function look(){
        $uid = (int)$_GET['uid'];

        $UserView=M('garden_user_view');
        $this->look = $UserView->where(array('uid'=>$uid))->select();

        $this->display();
    }
    public function AddUserHelp(){
        if(isset($_GET['queryString'])||isset($_POST['queryString'])){
            $userForm=M('garden_user_view');
            $where['_string']='(truename like "%'.I('queryString').'%") 
            OR (username like "%'.I('queryString').'%") 
            OR (mobile like "%'.I('queryString').'%") 
            OR (email like "%'.I('queryString').'%") 
            OR (qq like "%'.I('queryString').'%")
            OR (position like "%'.I('queryString').'%") 
            OR (dname like "%'.I('queryString').'%") 
            OR (major like "%'.I('queryString').'%")';
            $data=$userForm->where($where)->select();
            if($data){
                $len=count($data,0);
                for ($i=0; $i < $len; $i++) { 
                    echo '<li onClick="'.I('call_func').'fill(Array(\''.$data[$i]['uid'].'\',\''.$data[$i]['truename'].'\',\''.$data[$i]['dname'].'\'));">'.$data[$i]['truename'].'</li>';
                }
            }
        }else{
            die('参数错误！');
        }
    }

}
