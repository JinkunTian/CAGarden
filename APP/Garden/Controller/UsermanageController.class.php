<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020-8-31
 * @Update：     2020-9-6
 * @Description: 用户管理（管理员）控制器
 ***/
namespace Garden\Controller;
use Think\Controller;
class UsermanageController extends AdminController {
    
    /**
     * index方法列出所有用户
     */
    public function index(){

        if(I('dep')){
            if(I('dep')=='all'){
                $dep_select=array();
                $dep_name='全部';
            }elseif(I('dep')=='secede'){
                $dep_name='已退会-';
                $dep_select=array('status'=>'0');
            }elseif(I('dep')=='retire'){
                $dep_name='已卸任-';
                $dep_select=array('status'=>'3');
            }else{
                if($dep=M('common_departments')->where(array('did'=>I('dep')))->find()){
                    $dep_name=$dep['dname'].'-';
                    $dep_select=array('status'=>'1','dep'=>I('dep'));
                }else{
                    die('503 非法输入');
                }
            }
        }else{
            $dep_select=array('status'=>'1');
            $dep_name='';           
        }
        $count = M('garden_users_extend')->where($dep_select)->count();//正常状态的用户
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

        $users= M('garden_user_view')->where($dep_select)->order('type DESC')->limit($limit)->select();

        if($len=count($users,0)){
            for($i=0;$i<$len;$i++){
                if($users[$i]['type']=='1'){
                    $users[$i]['type']='用户';
                }elseif($users[$i]['type']=='2'){
                    $users[$i]['type']='管理员';
                }else{
                    $users[$i]['type']='未知';
                }
            }
        }

        $departments=M('common_departments')->select();
        $this->assign('departments',$departments);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('users',$users);
        $this->assign('dep_name',$dep_name);
        $this->display();
    }

    /**
     * edit方法显示编辑用户界面
     */
    public function edit(){
        $id =intval(I('uid'));
        
        $map['status'] = array('gt',0);

        $this->random=md5(time());
        $this->departments=M('common_departments')->where($map)->select();
        $this->majors=M('common_majors')->where(array('status'=>'1'))->select();
        $this->user_data = M('garden_user_view')->where(array('uid'=>$id))->select();

        $this->display();
    }

    /**
     * 保存编辑过的用户信息
     */
    public function datapost(){
        /**
         * 基础信息
         */
        $base_data=array(
            'truename' => I('truename_'.I('random')),
            'mobile' => I('mobile_'.I('random')),
            'qq' => I('qq_'.I('random')),
            'email' => I('email_'.I('random')),
            'major' => I('major_'.I('random')),
        );
        /** 
         * 社团信息
         */
        $extend_data=array(
            'type' => I('type_'.I('random')),
            'dep' => I('dep_'.I('random')),
            'position' =>I('position_'.I('random')),
            'flag' => I('flag_'.I('random')),
            );
        if($extend_data['type']=='2'){
            $extend_data['is_admin']=1;
        }else{
            $extend_data['is_admin']=0;
        }
        /**
         * 上传头像
         */
        if(($_FILES['img']['type'])){
            
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     C('MAX_PHOTO_POST_SIZE');
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Public'; // 设置附件上传根目录
            $upload->savePath  =     '/Uploads/'; // 设置附件上传（子）目录
            
            $info = $upload->uploadOne($_FILES['img']);

            if($info) {// 头像上传成功则保存头像
                $base_data['img'] = $info['savepath'].$info['savename'];
            }else{
                //上传失败，显示失败信息
                $this->error($upload->getError());
            }
        }

        /**
         * 判断是否有重设密码
         */
        $password_id='password_'.I('random');

        if(!(I($password_id)==''||I($password_id)=='留空则不修改密码')){
            $base_data['salt']=md5(time());
            $password = I($password_id,'','md5');
            $base_data['password']=md5($base_data['salt'].$password);

            /** 
             * 启用了LDAP就将密码同时写入LDAP和数据库
             */
            // if(C('USE_LDAP')){
            //     $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
            //     if($ds['result']){
            //         $res=ldap_change_password($ds['resource'],session('username'),C('BASE_DN'),$newpws);
            //         if($res){
            //             if (M('users')->where(array('uid' => session('uid')))->save($base_data)) {
            //                 $this->success('修改成功');
            //             }else{
            //                 $this->error('修改网站账户失败');
            //             } 
            //         }else{
            //             $this->error('修改域账户失败，密码不够安全，请设置一个安全性较强的密码！');
            //         }
            //     }else{
            //         $this->error('与LDAP服务器通信失败！');
            //     }
            // }else{
                //只写入密码到数据库
                if (M('users')->where(array('uid' => session('uid')))->save($base_data)) {
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败');
                } 
            // }
            //不修改密码
        }else{
            
            /** 
             * 启用了LDAP就将信息同时写入LDAP和数据库
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
            //     // $UserInfo['memberOf'] = "CN=Members,".C('BASE_DN');

            //     $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
            //     if($ds['result']){
            //         $res=ldap_change_user_info($ds['resource'],session('username'),C('BASE_DN'),$UserInfo);
            //         if($res){
            //             if($extend_data['is_admin']==1){
            //                 ldap_add_user_to_group($ds['resource'],C('BASE_DN'),session('username'),'Managers');
            //             }else{
            //                 ldap_del_user_from_group($ds['resource'],C('BASE_DN'),session('username'),'Managers');
            //             }
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
            //     }else{
            //         $this->error('与LDAP服务器通信失败！');
            //     }
            // }else{
                // $result1=M('users')->where(array('uid' => I('uid') ))->save($base_data);
                // $result2=M('garden_users_extend')->where(array('uid' => I('uid') ))->save($extend_data);
                // if ($result1===false||$result2===false) {
                //     $this->error('保存失败！');
                // }else{

                //     $this->success('保存成功！',U('/Garden/User/look',array('uid'=>I('uid'))));
                // }  
                $password = I('password', '', 'md5');
                $newpwd = I('newpwss', '', 'md5');
                //把要写入的数据转换成数组
                $salt = md5(time());
                $data = array(
                    'password' => md5($salt . $newpwd),
                    'salt' => $salt,
                );
                if (M('users')->where(array('uid' => I('uid')))->save($data)) {
                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            // }
        }
    }

    /**
     * retire方法将用户设置为干部卸任状态
     */
    public function retireuser(){
        if($user=M('garden_users_extend')->where(array('uid'=>I('uid')))->find()){
            
            $user['position']='干部卸任';
            $user['type']=1;//置为普通用户，撤销管理员权限
            $user['status']=3;//1正常用户/0禁用/3干部卸任
            $user['status_info']='干部卸任';
            
            /** 
             * 启用了LDAP就将密码同时写入LDAP和数据库
             */
            // if(C('USE_LDAP')){
            //     $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
            //     if($ds['result']){
            //         ldap_del_user_from_group($ds['resource'],C('BASE_DN'),$base_data['username'],'Members');
            //         ldap_del_user_from_group($ds['resource'],C('BASE_DN'),$base_data['username'],'Managers');

            //         M('users')->where(array('uid'=>I('uid')))->save(array('userType'=>'guest'));
            //         M('garden_users_extend')->where(array('uid'=>I('uid')))->save($user);
            //         ldap_close($ds['resource']);

            //         $this->success('已将'.$user['truename'].'标记为干部卸任！');
            //     }else{
            //         $this->error('与LDAP服务器通信失败！');
            //     }
            // }else{
                M('users')->where(array('uid'=>I('uid')))->save(array('userType'=>'guest'));
                M('garden_users_extend')->where(array('uid'=>I('uid')))->save($user);
                $this->success('已将'.$user['truename'].'标记为干部卸任！');
            // }
        }else{
            $this->error('输入非法，没有找到对应用户！');
        }
    }
    /**
     * reneging_post方法将用户设置为中途退会状态
     */
    public function reneging_post(){
        if($user=M('garden_users_extend')->where(array('uid'=>I('uid')))->find()){

            $user['position']='中途退会';
            $user['type']=1;//置为普通用户，撤销管理员权限
            $user['status']=0;//退会用户状态为0，封号，不可再登陆后花园
            $user['status_info']='中途退会';

            /** 
             * 启用了LDAP就将密码同时写入LDAP和数据库
             */
            // if(C('USE_LDAP')){
            //     $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
            //     if($ds['result']){
            //         ldap_del_user_from_group($ds['resource'],C('BASE_DN'),$base_data['username'],'Members');
            //         ldap_del_user_from_group($ds['resource'],C('BASE_DN'),$base_data['username'],'Managers');

            //         M('users')->where(array('uid'=>I('uid')))->save(array('userType'=>'guest'));
            //         M('garden_users_extend')->where(array('uid'=>I('uid')))->save($user);
                    
            //         ldap_close($ds['resource']);
            //         $this->success('已将'.$user['truename'].'标记为中途退会！该用户账号已被封禁！');
            //     }else{
            //         $this->error('与LDAP服务器通信失败！');
            //     }
            // }else{
                M('users')->where(array('uid'=>I('uid')))->save(array('userType'=>'guest'));
                M('garden_users_extend')->where(array('uid'=>I('uid')))->save($user);
                $this->success('已将'.$user['truename'].'标记为中途退会！该用户账号已被封禁！');
            // }
        }else{
            $this->error('没有找到对应用户！');
        }
    }
    /**
     * normal_exit方法将用户设置为中途退会状态
     */
    public function normal_exit(){
        if($user=M('garden_users_extend')->where(array('uid'=>I('uid')))->find()){

            $user['position']='正常退会';
            $user['type']=1;//置为普通用户，撤销管理员权限
            $user['status']=0;//退会用户状态为0，封号，不可再登陆后花园
            $user['status_info']='正常退会';

            /** 
             * 启用了LDAP就将密码同时写入LDAP和数据库
             */
            // if(C('USE_LDAP')){
            //     $ds = ldap_create_link_identifier(C('LDAP_SERVER_HOST'),C('LDAP_ADMIN_ACCOUNT'),C('LDAP_ADMIN_PASSWD'),C('DOMAIN'));
            //     if($ds['result']){
            //         ldap_del_user_from_group($ds['resource'],C('BASE_DN'),$base_data['username'],'Members');
            //         ldap_del_user_from_group($ds['resource'],C('BASE_DN'),$base_data['username'],'Managers');

            //         M('users')->where(array('uid'=>I('uid')))->save(array('userType'=>'guest'));
            //         M('garden_users_extend')->where(array('uid'=>I('uid')))->save($user);
                    
            //         ldap_close($ds['resource']);
            //         $this->success('已将'.$user['truename'].'标记为正常退会！');
            //     }else{
            //         $this->error('与LDAP服务器通信失败！');
            //     }
            // }else{
                M('users')->where(array('uid'=>I('uid')))->save(array('userType'=>'guest'));
                M('garden_users_extend')->where(array('uid'=>I('uid')))->save($user);
                $this->success('已将'.$user['truename'].'标记为正常退会！');
            // }
        }else{
            $this->error('没有找到对应用户！');
        }
    }
}
