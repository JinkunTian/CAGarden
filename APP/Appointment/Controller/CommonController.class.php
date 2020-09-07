<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020年8月31日
 * @Description: 预约板块公共模块
 ***/
namespace Appointment\Controller;
use Think\Controller;
class CommonController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        sys_log('Appointment');

        if (!(  isset($_SESSION['username']) && 
                isset($_SESSION['uid']) && 
                isset($_SESSION['truename']) && 
                isset($_SESSION[C('PASSWORD_KEY')]) )) 
        {
            session('req_url',$_SERVER["REQUEST_URI"]);
            $this->redirect('/Authentication');
        }else{

        }
    }
}