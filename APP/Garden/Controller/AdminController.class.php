<?php
namespace Garden\Controller;
use Think\Controller;
class AdminController extends CommonController {
    //自动运行，判断如果没有登录则跳转到登录页面
    public function _initialize() {
        if(!session('admin')){
            $this->error('无权查看!');
        }
    }
}
