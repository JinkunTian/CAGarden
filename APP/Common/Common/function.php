<?php
    /***
     * @Author:      田津坤
     * @Email:       me@tianjinkun.com
     * @GitHub:      https://github.com/JinkunTian
     * @DateTime:    2020年8月31日
     * @Description: 应用公共函数
     ***/

    function return_json($return_array){
        header('Content-Type:text/json;charset=utf-8');
        echo json_encode($return_array);
        die();
    }
    /**
     * sys_log用于向数据库中记录访问数据
     */
    function sys_log($part){
        if(true){
            $logs=array(
                'part'=>$part,
                'username'=>session('uid'),
                'truename'=>session('truename'),
                'username'=>session('username'),
                'ip'=>get_client_ip(),
                'agent'=>$_SERVER["HTTP_USER_AGENT"],
                'url'=>$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
                'date' => date('y-m-d H:i:s'),
            );
            M('system_access_logs')->add($logs);
        }
    }
    function project_manage_right_check_by_pr_id($pr_id){
        $uid=intval(session('uid'));
        $project=D('ProjectView')->where(array('pr_id'=>$pr_id))->find();
        $project['manage_right_check']=false;

        if($project['pr_cuser']==$uid){
            $project['manage_right_check']=true;
        }else{
            $members_id=explode(':',$project['pr_muser']);
            $len=count($members_id);
            for ($i=0; $i < $len; $i++) {
                if($members_id[$i]==$uid){
                    $project['manage_right_check']=true;
                    break;
                }
            }
        }

        return $project;
    }
    function password_manage_right_check($pw_id){
        $my_uid=intval(session('uid'));
        $result=false;
        $password=M('garden_public_password')->where(array('pw_id'=>$pw_id))->find();
        if($password){
            $public_password=M('garden_public_password');
            $where['_string']='pw_id="'.$pw_id.'" AND ( (pw_muser like "%:'.$my_uid.':%") OR (pw_cuser = "'.$my_uid.'"))';
            $password_right=$public_password->where($where)->find();
            if($password_right){
                $result=$password_right;
            }else{
                $project=M('garden_projects');
                $where['_string']='pr_id="'.$password['pw_prid'].'" AND ( (pr_muser like "%:'.$my_uid.':%") OR (pr_cuser = "'.$my_uid.'"))';
                $project_right=$project->where($where)->find();
                if($project_right){
                    $result=$password;
                }
            }
        }
        return $result;
    }
    function password_access_right_check($pw_id){
        $my_uid=intval(session('uid'));
        $result=false;
        $temp=M('garden_public_password')->where(array('pw_id'=>$pw_id))->find();
        /**
         * 判断密码是否公开
         */
        if($temp['open_access']=='1'){
            $result=$temp;
        }
        /**
         * 判断是否有管理权限
         */
        elseif($manage=password_manage_right_check($pw_id)){
                $result=$manage;
        }
        /**
         * 判断是否开放项目成员访问权限
         */
        elseif(($temp['group_members_access']=='1')||($temp['pw_right'])){
            $project=M('garden_projects');
            $where['_string']='pr_id="'.$temp['pw_prid'].'" AND (pr_members like "%:'.$my_uid.':%")';
            $project_right=$project->where($where)->find();
            if($project_right){
                $result=$temp;
            }
            /**
             * 判断是否有指定访问权限
             */
            else{
                $where['_string']='pw_id="'.$pw_id.'" AND (pw_right like "%:'.$my_uid.':%")';
                $related_passwords=M('garden_public_password')->where($where)->find();
                if($related_passwords){
                    $result=$related_passwords;
                }
            }
        }
    	return $result;
    }
    function explode_members($str){

        $members=false;
        $members_id=explode(':',$str);

        if($len=count($members_id)) {

            $z=0;
            for ($i=0; $i < $len; $i++) {
                if($members_id[$i]>0){
                    $members[$z++]=M('garden_user_view')->where(array('uid'=>$members_id[$i]))->find();
                }
            }
        }else{
            $members=false;
        }

        return $members;
    }
    function get_addition_password($pw_id){
    	if(isset($_POST['key_id'])){
            if(!(count(I('key_id'),0)==count(I('key_name'),0))&&(count(I('key_name'),0)==count(I('key_value'),0))){
                $this->error('附加密码信息不完整！');
            }else{
                $key_ids=I('key_id');
                $key_names=I('key_name');
                $key_values=I('key_value');

                $addition['count']=count(I('key_id'),0);

                if($addition['count']>0){
                    for ($i=0; $i < $addition['count']; $i++) { 
                        $addition['password'][$i]['original_password_id']=$pw_id;
                        $addition['password'][$i]['key_name']=$key_names[$i];
                        $addition['password'][$i]['key_value']=$key_values[$i];
                    }
                }

            }  
        }else{
        	$addition['count']=0;
        }
        return $addition;
    }
    
/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 */
function encode($string = '') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split(C('PASSWORD_KEY')) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 */
function decode($string = '') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split(C('PASSWORD_KEY')) as $key => $value){
        if(isset($strArr[$key][1])&&isset($strArr[$key][0])){
            $key <= $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
        }
    }
    return base64_decode(join('', $strArr));
}
function encode_addition_password($addition_password){
    $len=count($addition_password,0);
    if($len>0){
        for ($i=0; $i < $len; $i++) {
            $addition_password[$i]['key_name']=encode($addition_password[$i]['key_name']);
            $addition_password[$i]['key_value']=encode($addition_password[$i]['key_value']);
        }
    }
    return $addition_password;
}
function decode_addition_password($addition_password){
    $len=count($addition_password,0);
    if($len>0){
        for ($i=0; $i < $len; $i++) {
            $addition_password[$i]['key_name']=decode($addition_password[$i]['key_name']);
            $addition_password[$i]['key_value']=decode($addition_password[$i]['key_value']);
        }
    }
    return $addition_password;
}