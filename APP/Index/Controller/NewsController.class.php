<?php
namespace Index\Controller;
use Think\Controller;

/************************************************* 
Author: 田津坤
QQ    : 2961165914
GitHub: https://github.com/JinkunTian
Date:2018-8-19 
Description:ProjectTree 新闻控制器，用于发布首页新闻
**************************************************/ 
class NewsController extends CommonController {

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
            $this->error('404 页面没有找到！','/');
        }
    }
}