<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018-8-19 
 * @Description: ProjectTree 小黑板控制器，用于公布团队公告
 ***/
namespace Garden\Controller;
use Think\Controller;
class BlackBoardController extends CommonController {

    /**
     * index分页列出所有黑板报（公告）
     */

    public function index(){
        //分页开始
        $count = M('garden_blackboard')->count();// 查询满足要求的总记录数

        $page = new \Think\Page($count,12);
        $page->setConfig('header','条数据');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','<<');
        $page->setConfig('last','>>');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% <span>共 %TOTAL_ROW% %HEADER%</span>');
        $page->rollPage=3; //控制页码数量

        $show = $page->show();// 分页显示输出
        $limit = $page->firstRow.','.$page->listRows;
        $list =D('BlackBoardView')->order('addtime DESC')->limit($limit)->select();

        $this->assign('article',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /**
     * 新建公告
     */
    public function add(){
        $this->display();
    }

    /**
     * 查看黑板板文、公告
     */
    public function look(){
        $id = (int)$_GET['id'];
        /** 访客加一 **/
        M('garden_blackboard')->where(array('id'=>$id))->setInc('visits',1);

        //查找文章内容
        $article = D('BlackBoardView')->where(array('id'=>$id))->find();

        //评论分页
        $count = M('garden_comment')->where(array('bid'=>$id))->count();
        $page = new \Think\Page($count,8);
        $page->setConfig('header','条评论');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','<<');
        $page->setConfig('last','>>');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% <span>共 %TOTAL_ROW% %HEADER%</span>');
        $page->rollPage=3; //控制页码数量

        $show = $page->show();// 分页显示输出        
        $limit = $page->firstRow.','.$page->listRows;

        $list = D('CommentView')->where(array('bid'=>$id))->order('addtime DESC')->limit($limit)->select();

        //渲染模板输出
        $this->assign('look',$article);
        $this->assign('comment',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('ALLOW_BLACKBOARD_COMMENT',C('ALLOW_BLACKBOARD_COMMENT'));
        $this->display();
    }

    /**
     * edit编辑界面
     */
    public function edit(){
        $id =(int)$_GET['id'];
        $uid = intval(session('uid'));
        $result = M('garden_blackboard')->where(array('id'=>$id,'author_id'=>$uid))->find();
        if ($result){
            $this->assign('edit',$result);
            $this->display('add');
        }else{
            $this->error('你无法编辑别人的文章');
        }
    }

    /**
     * 编辑表单处理
     */
    public function editpost(){
        $data = array(
            'id'    =>  I('id'),
            'title' =>  I('title'),
            'content' => I('content'),
            //'time' => date('y-m-d H:i:s'),
        );
        $uid=intval(session('uid'));
        if(M('garden_blackboard')->where(array('id'=>I('id'),'author_id'=>$uid))->find()){
            if (M('garden_blackboard')->save($data)) {
                $this->success('保存成功！',U('/Garden/Blackboard/index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $this->error('你无法编辑别人的文章');
        }
    }

    /**
     * 发布表单处理
     */
    public function articlepost(){
        $data = array(
            'title' =>  I('title'),
            'content' => I('content'),
            'author_id' => (int)session('uid'),
            'addtime' => date('y-m-d H:i:s'),
        );
        //添加数据
        if (M('garden_blackboard')->add($data)) {
            $this->success('保存成功！',U('/Garden/Blackboard/index'));
        }else{
            $this->error('发布失败');
        }
    }

    /**
     * 删除公告
     */
    public function del(){
        $id = (int)$_GET['id'];
        $rbac = M('garden_blackboard')->where(array('id'=>$id))->field('author_id')->select();
        if ($rbac[0]['author_id'] == session('uid')) {
                if (M('garden_blackboard')->where(array('id' => $id))->delete()) {
                    //删除属于公告的所有评论
                    M('garden_comment')->where(array('bid'=>$id))->delete();
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
        }else{
            $this->error('你无法删除别人的文章');
        }
    }

    /**
     * 发布评论
     */

    /**
     * 公告系统中还是关掉评论比较好吧？！
     * 有需求的自行打开
     * [更新] 启用或禁用公告评论更改至编辑config.php
     *       中的ALLOW_BLACKBOARD_COMMENT参数
     */
    public function addcom(){
        if(C('ALLOW_BLACKBOARD_COMMENT')){
            if (!IS_AJAX) $this->error('页面不存在');
            $content=str_replace("\n","<br>",I('content')); //去回车
            $data = array(
                'content' => str_replace(" ","&nbsp;",$content), //去空格
                'uid' => (int)session('uid'),
                'bid' => I('bid'),
                'addtime' => date('y-m-d H:i:s'),
            );
            //添加数据
            if (M('garden_comment')->add($data)) {
                $this->ajaxReturn(array('info' => '<i class="fa fa-check"></i> 提交成功','status' => 1), 'json');
            }else{
                $this->ajaxReturn(array('info' => '<i class="fa fa-remove"></i> 提交失败,请重试','status' => 0), 'json');
            }
        }else{
            $this->ajaxReturn(array('info' => '<i class="fa fa-remove"></i> 评论功能已禁用','status' => 0), 'json');
        }
    }

    /**
     * 删除评论
     */
    public function delcom(){
        if(C('ALLOW_BLACKBOARD_COMMENT')){
            $id = (int)$_GET['id'];
            $uid=intval(session('uid'));
            if(M('garden_comment')->where(array('id'=>$id,'uid'=>$uid))->find()){
                if (M('garden_comment')->where(array('id' => $id))->delete()) {
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            }else{
                $this->error('你无法删除别人的评论');
            }
        }else{
            $this->error('评论功能已禁用');
        }
    }
}