<?php
namespace Garden\Controller;
use Think\Controller;
class MsgController extends CommonController {
    public function index(){

        $count = M('garden_msg')->count();// 查询满足要求的总记录数

        $page = new \Think\Page($count,15);
        $page->setConfig('header','条数据');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','<<');
        $page->setConfig('last','>>');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% <span>共 %TOTAL_ROW% %HEADER%</span>');
        $page->rollPage=3; //控制页码数量
        $show = $page->show();// 分页显示输出
        $limit = $page->firstRow.','.$page->listRows;

        $list =  D('MsgView')->order('addtime DESC')->limit($limit)->select();

        $this->assign('msg',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function addmsg(){
        if (!IS_AJAX) $this->error('页面不存在');
        $content=str_replace("\n","<br>",I('content')); //去回车
        $data = array(
            'content' =>str_replace(" ","&nbsp;",$content), //去空格
            'uid' => (int)session('id'),
            'addtime' => date('y-m-d H:i:s'),
        );
        //添加数据
        if (M('garden_msg')->add($data)) {
            $this->ajaxReturn(array('info' => '<i class="fa fa-check"></i> 发布成功','status' => 1), 'json');
        }else{
            $this->ajaxReturn(array('info' => '<i class="fa fa-remove"></i> 发布失败,请重试','status' => 0), 'json');
        }
    }

    //删除
    public function del(){
        $id = (int)$_GET['id'];
        $uid=intval(session('id'));
        if (M('garden_msg')->where(array('id'=>$id,'uid'=>$uid))->delete()) {
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}