<?php
namespace Appointment\Controller;
use Think\Controller;
class CommonController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        if (!(isset($_SESSION['guest']) && isset($_SESSION['guest_id']) && isset($_SESSION['name']) )) {
            $this->redirect('/Appointment/Login');
        }
    }
}