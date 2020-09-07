<?php
namespace Garden\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020年8月31日
 * @Description: 首页管理（管理员）控制器
 ***/
class IndexmanageController extends AdminController {
    
    /**
     * index方法列出所有用户
     */
    public function index(){

        
        $this->display();
    }
}
