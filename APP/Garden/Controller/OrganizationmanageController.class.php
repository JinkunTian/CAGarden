<?php
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020-8-31
 * @Update：     2020-9-6
 * @Description: 组织管理（管理员）控制器
 ***/
namespace Garden\Controller;
use Think\Controller;
class OrganizationmanageController extends AdminController {

    private function departmentVisibilityCsrfToken(){
        $token=session('department_visibility_csrf_token');
        if(!$token){
            $token=bin2hex(openssl_random_pseudo_bytes(24));
            session('department_visibility_csrf_token',$token);
        }
        return $token;
    }

    private function checkDepartmentVisibilityCsrf(){
        $token=(string)I('post.csrf_token');
        if(!$token || !hash_equals((string)session('department_visibility_csrf_token'),$token)){
            $this->error('页面已失效，请刷新后重新提交。');
        }
    }
    
    /**
     * index方法列出所有用户
     */
    public function index(){

        
        $this->display();
    }

    /**
     * 列出学院
     **/
    public function list_institutes(){
        $institutes=M('common_institutes')->where(array('status'=>1))->select();
        $this->assign('institutes',$institutes);
        $this->display();
    }

    /**
     * 添加学院
     **/
    public function add_institute(){
        if(I('institute_name')){
            $new_institute['institute_name']=I('institute_name');
            M('common_institutes')->add($new_institute);
            $this->success('添加成功！');
        }else{
            $this->error('非法输入');
        }
    }

    /**
     * 删除学院
     **/
    public function del_institute(){
        if(I('institute_id')){
            $institute=M('common_institutes')->where(array('institute_id'=>I('institute_id')))->find();
            if($institute){
                $majors=M('common_majors')->where(array('institute'=>$institute['institute_name']))->select();
                if($majors){
                    $this->error('该学院下还有相关专业，无法删除！');
                }else{
                    M('common_institutes')->where(array('institute_id'=>I('institute_id')))->delete();
                    $this->success('删除成功！');
                }
            }else{
                $this->error('学院不存在，无法删除！');
            }
        }else{
            $this->error('非法输入');
        }
    }

    /**
     * 列出专业
     **/
    public function list_majors(){
        $institutes=M('common_institutes')->where(array('status'=>1))->select();
        $majors=M('common_majors')->where(array('status'=>1))->select();
        $this->assign('institutes',$institutes);
        $this->assign('majors',$majors);
        $this->display();
    }

    /**
     * 添加专业
     **/
    public function add_major(){
        if(I('institute') && I('major_name')){
            $major=M('common_majors')->where(array('mname'=>I('major_name')))->find();
            if($major){
                $this->error('该专业已存在！');
            }else{
                $major_data['mname']=I('major_name');
                $major_data['institute']=I('institute');
                $major_data['status']=1;
                M('common_majors')->add($major_data);
                $this->success('添加成功！');
            }
        }else{
            $this->error('非法输入!');
        }
    }

    /**
     * 删除专业
     **/
    public function del_major(){
        // echo '666';
        if(I('major_id')){
            $major=M('common_majors')->where(array('mid'=>I('major_id')))->find();
            if($major){
                $students=M('users')->where(array('major'=>$major['mid']))->select();
                if($students){
                    $this->error('该专业下还有相关同学，无法删除！');
                }else{
                    M('common_majors')->where(array('mid'=>I('major_id')))->delete();
                    $this->success('删除成功！');
                }
            }else{
                $this->error('专业不存在，无法删除！');
            }
        }else{
            $this->error('非法输入');
        }
    }

    /**
     * 列出部门
     **/
    public function list_dep(){
        $institutes=M('common_departments')->select();
        $this->assign('departments',$institutes);
        $this->assign('department_visibility_csrf_token',$this->departmentVisibilityCsrfToken());
        $this->display();
    }

    /**
     * 添加部门
     **/
    public function add_dep(){
        if(I('dep_name')){
            $new_dep['dname']=I('dep_name');
            $new_dep['status']=1;
            $new_dep['recruit_visible']=1;
            M('common_departments')->add($new_dep);
            $this->success('添加成功！');
        }else{
            $this->error('非法输入');
        }
    }

    /**
     * 设置部门是否显示在新生纳新表单中
     **/
    public function set_dep_recruit_visibility(){
        if($_SERVER['REQUEST_METHOD']!='POST'){
            $this->error('非法请求');
        }
        $this->checkDepartmentVisibilityCsrf();

        $did=(int)I('post.did');
        $recruit_visible=I('post.recruit_visible');
        if($did<=0 || !in_array((string)$recruit_visible,array('0','1'),true)){
            $this->error('非法输入');
        }

        $department=M('common_departments')->where(array('did'=>$did))->find();
        if(!$department){
            $this->error('部门不存在！');
        }

        $result=M('common_departments')->where(array('did'=>$did))->save(array('recruit_visible'=>(int)$recruit_visible));
        if($result===false){
            $this->error('保存失败！');
        }
        $this->success('纳新显示设置已保存！',U('/Garden/Organizationmanage/list_dep'));
    }

    /**
     * 删除部门
     **/
    public function del_dep(){
        if(I('did')){
            $department=M('common_departments')->where(array('did'=>I('did')))->find();
            if($department){
                $students=M('garden_users_extend')->where(array('dep'=>$department['did']))->select();
                if($students){
                    $this->error('该部门下还有相关同学，无法删除！');
                }else{
                    M('common_departments')->where(array('did'=>I('did')))->delete();
                    $this->success('删除成功！');
                }
            }else{
                $this->error('学院不存在，无法删除！');
            }
        }else{
            $this->error('非法输入');
        }
    }
}
