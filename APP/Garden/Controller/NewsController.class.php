<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018-8-19 
 * @Update：     2020-9-6
 * @Description: ProjectTree 新闻控制器，用于发布首页新闻
 ***/
namespace Garden\Controller;
use Think\Controller;
class NewsController extends AdminController {

    /**
     * index分页列出所有新闻
     */

    public function index(){
        //分页开始
        $count = M('index_news')->count();// 查询满足要求的总记录数

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
        $list =M('index_news')->order('addtime DESC')->limit($limit)->select();

        $this->assign('article',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /**
     * 新建新闻
     */
    public function add(){
        $this->display();
    }

    /**
     * 查看新闻
     */
    public function look(){
        if($id = (int)$_GET['id']){
            /** 访客加一 **/
            M('index_news')->where(array('id'=>$id))->setInc('visits',1);

            //查找文章内容
            $article = M('index_news')->where(array('id'=>$id))->find();        

            //渲染模板输出
            $this->assign('look',$article);
            $this->display();
        }else{
            $this->error('404 页面没有找到！','/Garden');
        }
    }

    /**
     * edit编辑界面
     */
    public function edit(){
        $id =(int)$_GET['id'];
        $uid = intval(session('uid'));
        $result = M('index_news')->where(array('id'=>$id,'author_id'=>$uid))->find();
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
        if($article=M('index_news')->where(array('id'=>I('id'),'author_id'=>session('uid')))->find()){
            $data = array(
                'id'=>I('id'),
                'title' =>  I('title'),
                'content' => I('content'),
                'author_name' => session('truename'));
            /**
             * 上传首页缩图
             */
            if(($_FILES['img'])){
                
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize   =     C('MAX_PHOTO_POST_SIZE');
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath  =     './Public'; // 设置附件上传根目录
                $upload->savePath  =     '/Uploads/'; // 设置附件上传（子）目录
                
                $info = $upload->uploadOne($_FILES['img']);

                if($info) {// 头像上传成功则保存头像
                    $data['picture'] = '/Public'.$info['savepath'].$info['savename'];
                    //添加数据
                    if (M('index_news')->save($data)) {
                        $this->success('保存成功！',U('/Garden/News/index'));
                    }else{
                        $this->error('发布失败');
                    }
                }else{
                    M('index_news')->save($data);
                    $this->success('保存成功！',U('/Garden/News/index'));
                }
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
            'picture'=>'',
            'content' => I('content'),
            'author_id' => (int)session('uid'),
            'author_name' => session('truename'),
            'addtime' => date('y-m-d H:i:s'));
        /**
         * 上传首页缩图
         */
        if(($_FILES['img'])){
            
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     C('MAX_PHOTO_POST_SIZE');
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Public'; // 设置附件上传根目录
            $upload->savePath  =     '/Uploads/'; // 设置附件上传（子）目录
            
            $info = $upload->uploadOne($_FILES['img']);

            if($info) {// 头像上传成功则保存头像
                $data['picture'] = '/Public'.$info['savepath'].$info['savename'];
                //添加数据
                if (M('index_news')->add($data)) {
                    $this->success('保存成功！',U('/Garden/News/index'));
                }else{
                    $this->error('发布失败');
                }
            }else{
                //上传失败，显示失败信息
                $this->error('上传图片失败：</br>'.$upload->getError());
            }
        }else{
            $this->error("请上传首页略缩图");
        }
    }

    /**
     * 删除新闻
     */
    public function del(){
        $id = (int)$_GET['id'];
        if(M('index_news')->where(array('id'=>$id,'author_id'=>session('uid')))->find()){
            if (M('index_news')->where(array('id' => $id))->delete()) {
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('删除失败');
        }
    }
}