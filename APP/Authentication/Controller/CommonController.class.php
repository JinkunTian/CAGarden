<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020年8月31日
 * @Description: 用户认证公共模块
 ***/
namespace Authentication\Controller;
use Think\Controller;
class CommonController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        sys_log('Authentication');
        //未登陆
        if (!(  isset($_SESSION['username']) && 
                isset($_SESSION['id']) && 
                isset($_SESSION['truename']) && 
                isset($_SESSION[C('PASSWORD_KEY')]) )) 
        {
            //Nothing to do.
        }else{
            //已登陆用户
            $user_info=M('users')->where(array('uid'=>session('uid')))->find();
            switch($user_info['userType']){
                case 'guest':
                    $this->redirect('/Appointment/Index');break;
                case 'garden':
                    $this->redirect('/Garden/Index');break;
                default:break;
            }
        }
    }
}