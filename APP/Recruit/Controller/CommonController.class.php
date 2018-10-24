<?php
namespace Recruit\Controller;
use Think\Controller;
class CommonController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        if (!(isset($_SESSION['newmember_xm']) && isset($_SESSION['newmember_xh']) && isset($_SESSION['newmember_xy']) )) {
            $this->redirect('/Recruit/UserLogin');
        }
    }
}
