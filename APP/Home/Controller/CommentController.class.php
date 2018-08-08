<?php
namespace Home\Controller;
use Think\Controller;
class CommentController extends CommonController {
    public function index(){
        $comment = D('CommentView');
        $this->comment = $comment->where(array('del'=>'0'))->order('time DESC')->select();
        $this->display();
    }

    //留言表单处理
    public function addcomment(){
        $data = array(
            'content' => $_POST['content'],
            'uid' => (int)session('id'),
            'time' => time(),
        );
        //添加数据
        if (M('comment')->add($data)) {
        $this->success('发布成功');
        }else{
            $this->error('发布失败');
        }
    }

    //删除
    public function delcomment(){
        $id = (int)$_GET['id'];
            if (M('comment')->where(array('id' => $id))->save(array('del' => '1'))) {
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
    }
}