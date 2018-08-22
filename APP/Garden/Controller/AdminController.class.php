<?php
namespace Garden\Controller;
use Think\Controller;
class AdminController extends Controller {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        if (!(isset($_SESSION['id'])&&isset($_SESSION['6c440f695619e361040767ac9f6fb619']))) {
            $this->redirect('/Garden/Login');
        }
        
        if(!isadmin(intval(session('id')))){
            $this->error('无权查看!');
        }
    }
}