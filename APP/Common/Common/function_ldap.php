<?php
function ldap_create_link_identifier($LDAP_SERVER_HOST,$LDAP_USER,$LDAP_PWD,$DOMAIN){
    $result['result']=false;
    $ds=ldap_connect($LDAP_SERVER_HOST);
    if($ds){
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);//声明使用版本3
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0); // Binding to ldap server
        $r=@ldap_bind($ds,$LDAP_USER.'@'.$DOMAIN,$LDAP_PWD);
        ldap_get_option($ds, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
        //验证账号是否过期
        if(!$extended_error){
            $result['result']=true;
            $result['resource']=$ds;
        }else{
            $result['result']=false;
            ldap_close($ds);
        }
    }
    return $result;
}
function ldap_change_password($ds,$target_user,$BASE_DN,$newPass){
    $target_user_dn='CN='.$target_user.','.$BASE_DN;
    $passdata["unicodepwd"] = $newPass; 
    $result = ldap_mod_replace($ds,$target_user_dn,$passdata);
    return $result; 
}
function ldap_add_user_to_group($ds,$BASE_DN, $target_user,$target_group){
    $target_user_dn='CN='.$target_user.','.$BASE_DN;
    $target_group_dn='CN='.$target_group.','.$BASE_DN;

    $entry['member']=$target_user_dn;
    $res=ldap_mod_add($ds,$target_group_dn,$entry);
    return $res;
}
function ldap_del_user_from_group($ds,$BASE_DN, $target_user,$target_group){
    $target_user_dn='CN='.$target_user.','.$BASE_DN;
    $target_group_dn='CN='.$target_group.','.$BASE_DN;

    $entry['member']=$target_user_dn;
    $res=ldap_mod_del($ds,$target_group_dn,$entry);
    return res;
}
function myldap_delete($ds,$dn,$recursive=false){
    if($recursive == false){
        return(ldap_delete($ds,$dn));
    }else{
        //searching for sub entries
        $sr=ldap_list($ds,$dn,"ObjectClass=*",array(""));
        $info = ldap_get_entries($ds, $sr);
        //var_dump($info);
        //var_dump($dn);
        for($i=0;$i<$info['count'];$i++){
            //deleting recursively sub entries
            $result=myldap_delete($ds,$info[$i]['dn'],$recursive);
            if(!$result){
                //return result code, if delete fails
                return($result);
            }
        }
        return(ldap_delete($ds,$dn));
    }
}

function ldap_user_esixt($LDAP_SERVER_HOST,$BASE_DN,$LDAP_ADMIN_USER,$LDAP_ADMIN_PWD,$USER,$DOMAIN){
    $check_esixt=array();
    //创建一个LDAP连接
    $ds=ldap_connect($LDAP_SERVER_HOST);
    if ($ds) {
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);//声明使用版本3
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0); // Binding to ldap server
        $r=@ldap_bind($ds,$LDAP_ADMIN_USER.'@'.$DOMAIN,$LDAP_ADMIN_PWD);
        ldap_get_option($ds, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
        //验证账号是否过期
        if(!$extended_error){
            $sr=ldap_search($ds,$BASE_DN,"CN=".$USER);
            $info = ldap_get_entries($ds, $sr);
            if($info['count']){
                $check_esixt['result']=true;
                $check_esixt['info']="success";
                $length=$info[0]['memberof']['count'];
                for($i=0;$i<$length;$i++){
                    $temp=explode(',',$info[0]['memberof'][$i]);
                    $group_temp=explode('=',$temp[0]);
                    $check_esixt['MemberOf'][]=$group_temp[1];
                }
            }else{
                $check_esixt['result']=false;
                $check_esixt['info']="用户不存在";
            }            
        }else{
            $check_esixt['result']=false;
            $check_esixt['info']="系统错误：无法与域控制器完成认证。".$extended_error;
        }
        ldap_close($ds);
    }else{
        $check_esixt['result']=false;
        $check_esixt['info']="系统错误：无法与域控制器进行通信。";
    }
    return $check_esixt;
}
function ldap_login($LDAP_SERVER_HOST,$BASE_DN,$USER,$PASSWORD,$DOMAIN){
    $login_success=false;
    //创建一个LDAP连接
    $ds=ldap_connect($LDAP_SERVER_HOST);
    if ($ds) {
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);//声明使用版本3
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0); // Binding to ldap server
        $r=@ldap_bind($ds,$USER.'@'.$DOMAIN,$PASSWORD);
        ldap_get_option($ds, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
        //验证账号是否过期
        if(!$extended_error){
            $login_success=true;
        }else{
            $login_success=false;
        }
        ldap_close($ds);
    } else {
        $login_success=false;
    }
    return $login_success;
}

function to_gb2312($string_in){
    if(C('LDAP_ENCODE_CONV')){
        if($string_in){
            $str_encode = iconv( 'UTF-8', 'EUC-CN',$string_in );
        }else{
            $str_encode = iconv( 'UTF-8', 'EUC-CN','未填写' );
        } 
    }else{
        if($string_in){
            $str_encode = $string_in;
        }else{
            $str_encode = '未填写';
        } 
    }
    
    return $str_encode;
}

/**
 * $UserInfo['username']    //用户名
 * $UserInfo['truename']    //昵称、真实姓名、显示名称
 * $UserInfo['password']    //密码
 * $UserInfo['company']
 * $UserInfo['department']
 * $UserInfo['office']
 * $UserInfo['position']
 * $UserInfo['website']
 * $UserInfo['mail']
 * $UserInfo['description']
 * $UserInfo['telephone']
 * $UserInfo['qq']
 * $UserInfo['telephone']
 * $UserInfo['telephone']
 * 
 */
function ldap_add_user($LDAP_SERVER_HOST,$BASE_DN,$LDAP_ADMIN_USER,$LDAP_ADMIN_PWD,$DOMAIN,$UserInfo){

    $add_result['result']=false;
    //LDAPS添加用户测试

    //构建LDAP的管理用户唯一辨识符
    $manager_user_dn="CN=".$LDAP_ADMIN_USER.",".$BASE_DN;
    //构建需要添加的目标用户DN
    $target_user_dn="CN=".$UserInfo['username'].",".$BASE_DN;

    /**
     * 表分类信息，保持不变即可
     */
    $userdata["objectClass"][0] = 'top';
    $userdata["objectClass"][1] = 'person';
    $userdata["objectClass"][2] = 'organizationalPerson';
    $userdata["objectClass"][3] = 'user';
    $userdata["instanceType"] = 4;
    /**
     * UserPrincipalName：指定要由客户端进行身份验证的服务的用户主体名称 (UPN)。
     *      一个用户帐户名（有时称为“用户登录名”）
     *      和一个域名（标识用户帐户所在的域），
     *      这是登录到Windows域的标准用法。
     *      格式是： xiaowen@azureyun.com （类电子邮件地址）。
     * SamAccountName：在AD属性AMAccountName中，存储帐户登录名或用户对象,
     *      实际上是命名符号“Domain\LogonName ”中使用的旧NetBIOS表单，
     *      该属性是域用户对象的必需属性；而SAMAccountName应始终与UPN主体名称保持一致，
     *      即SAMAccountName必须等于属性“UserPrincipalName” 的前缀部分。
     *      并且应该与CN保持相同。
     *      
     * 格式规定
     *      不能超过20个字符，不允许出现\ / [] :; | =，+ *？<> @等特殊字符；
     */
    $userdata["sAMAccountName"] = $UserInfo['username'];
    $userdata["userPrincipalName"] = $UserInfo['username']."@".$DOMAIN;
    /**
     * name：账户名称，应与sAMAccountName保持一致
     */
    $userdata["name"] = $UserInfo['username'];
    /**
     * userAccountControl记录了用户的AD账号的很多属性信息，该属性标志是累积性的。
     *      也就是各属性相加之和。常见的有以下几种属性：
     * 禁用账户-----------------2
     * 需要主文件夹-------------8
     * 不需要密码--------------32
     * 不能更改密码-------------64
     * 使用可逆加密存储密码-----128
     * 默认账户类型------------512
     * 密码永不过期----------65536
     * 
     *      没有声明该值为514即默认账户类型+禁用账户
     *      设为66048即为默认账户类型+密码永不过期
     */
    // $userdata["userAccountControl"] = 66048;
    $userdata["displayName"] = to_gb2312($UserInfo['truename']);//用户姓名
    $userdata["company"] = to_gb2312($UserInfo['company']);
    $userdata["department"] = to_gb2312($UserInfo['department']);
    $userdata["physicalDeliveryOfficeName"] = to_gb2312($UserInfo['office']);
    $userdata["title"] = to_gb2312($UserInfo['position']);//职位
    $userdata["wWWHomePage"] = to_gb2312($UserInfo['website']);//个人主页
    $userdata["mail"] = to_gb2312($UserInfo['mail']);
    $userdata["description"] = to_gb2312($UserInfo['description']);
    $userdata["telephoneNumber"] = to_gb2312($UserInfo['telephone']);
    $userdata["ipPhone"] = to_gb2312($UserInfo['qq']);//IP电话，可以用来存储QQ号码

    //创建一个LDAP连接
    $ds=ldap_connect($LDAP_SERVER_HOST);
    if ($ds) {

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);//声明使用版本3
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0); // Binding to ldap server
        // $r=@ldap_bind($ds,$USER.'@'.$DOMAIN,$PASSWORD);

        //绑定连接，如果用户唯一辨识符和密码不正确，将无法绑定
        $r=ldap_bind($ds,$manager_user_dn,$LDAP_ADMIN_PWD);

        $result = ldap_add($ds,$target_user_dn,$userdata); 

        if($result===true){

            $target_pass='"'.$UserInfo['password'].'"';
            $newPass = iconv( 'UTF-8', 'UTF-16LE', $target_pass );
            //通过重写unicodepwd值来更改密码
            $passdata["unicodepwd"] = $newPass; 
            $result = ldap_mod_replace($ds,$target_user_dn,$passdata); 
            if($result===true){
                $account_right["userAccountControl"] = 66048;
                $change=ldap_mod_replace($ds,$target_user_dn,$account_right); 
                if($change===true){

                    //添加组信息
                    $entry['member']=$target_user_dn;
                    $res=ldap_mod_add($ds,$UserInfo['memberOf'],$entry);
                    if($res){
                        if($UserInfo['is_admin']){
                            $entry['member']=$target_user_dn;
                            $res=ldap_mod_add($ds,"CN=Managers,".$BASE_DN,$entry);
                            if($res){
                                $add_result['result']=true;
                            }else{
                                $add_result['result']=false;
                                $add_result['info']='设置管理分组时出错。';
                            }
                        }else{
                            $add_result['result']=true;
                        }
                    }else{
                        $add_result['result']=false;
                        $add_result['info']='设置账户分组时出错。';
                    }
                }else{
                    $add_result['result']=false;
                    $add_result['info']='设置账户权限时出错。';
                }
            }else{
                $add_result['result']=false;
                $add_result['info']='设置密码时出错。密码不够安全，请设置一个安全性较强的密码！';
            }
            
            if($add_result['result']==false){
                // $target_user_dn;
                myldap_delete($ds,$target_user_dn,$recursive=true);
            }
        }else{
            $add_result['result']=false;
            $add_result['info']='添加用户至LDAP域控失败。';
        }
        
        ldap_close($ds);
    } else {
        $add_result['result']=false;
        $add_result['info']='与域控通信时出错。';
    }
    return $add_result;
}

function ldap_add_user_to_group($LDAP_SERVER_HOST,$BASE_DN,$LDAP_ADMIN_USER,$LDAP_ADMIN_PWD,$DOMAIN,$UserInfo){
    
    
}