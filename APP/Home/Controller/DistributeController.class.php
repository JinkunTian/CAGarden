<?php
namespace Home\Controller;use Think\Controller;
class DistributeController extends CommonController {
    public function index(){
        $my_uid=intval(session('id'));
        $MyDistPass=D('MyDistPassView');

        $count = $MyDistPass->where(array('pw_cuser'=>$my_uid,'type'=>'2'))->count();// 查询满足要求的总记录数
        $page = new \Think\Page($count,200);
        $page->setConfig('header','条数据');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','<<');
        $page->setConfig('last','>>');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% <span>共 %TOTAL_ROW% %HEADER%</span>');
        $page->rollPage=3; //控制页码数量
        $show = $page->show();// 分页显示输出
        $limit = $page->firstRow.','.$page->listRows;
        $pw_info = $MyDistPass->where(array('pw_cuser'=>$my_uid,'type'=>'2'))->order('pw_ctime DESC')->limit($limit)->select();

        if($len=count($pw_info,0)){
            for($i=0;$i<$len;$i++){
                if($pw_info[$i]['status']=='1'){
                    $pw_info[$i]['status']='已派发';
                }elseif($pw_info[$i]['status']=='2'){
                    $pw_info[$i]['status']='已查看';
                }
            }
        }
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('password',$pw_info);
    	$this->display();
    }
    public function upload_excel(){

       if (!empty($_FILES)) {
            $upload = new \Think\Upload();// 实例化上传类

            $upload->exts = array('xlsx','xls');   // 设置附件上传类型
            $upload->autoSub   =  false;        //自动使用子目录保存上传文件
            $upload->rootPath  =     './Public'; // 设置附件上传根目录
            $upload->savePath  =     '/Uploads/Excels/'; // 设置附件上传（子）目录
            if (!$info=$upload->uploadOne($_FILES['img'])) {
                $this->error('文件上传失败！'.$upload->getError());
            }
            if (!file_exists('./ThinkPHP/Library/Vendor/PHPExcel/Classes/PHPExcel.php')) {
                exit("PHPExcel库文件不存在！</br>" );
            }
            if (!file_exists('./ThinkPHP/Library/Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php')) {
                exit("IOFactory库文件不存在！</br>" );
            }
            require_once './ThinkPHP/Library/Vendor/PHPExcel/Classes/PHPExcel.php';
            require_once './ThinkPHP/Library/Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

            $file_name='./Public/'.$info['savepath'].$info['savename'];
            //var_dump($file_name);
            $extension = strtolower( pathinfo($file_name, PATHINFO_EXTENSION) );
            if (!file_exists($file_name)) {
                exit("Please run ".$file_name." first." );
            }

            $objPHPExcel = \PHPExcel_IOFactory::load($file_name);

            

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            var_dump($highestRow);
            // $j=0;
            $pw_ctime=time();
            $pw_cuser=intval(session('id'));
            $erro_count=0;
            $succ_count=0;
            for($i=4;$i<=$highestRow;$i++){
                $data[$i]['pw_cuser']=$pw_cuser;
                $data[$i]['pw_ctime']=$pw_ctime;
                $data[$i]['uid']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                $data[$i]['pw_name']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                $data[$i]['username']= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                $data[$i]['password']= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                $data[$i]['note']= $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                $data[$i]['type']= '2';
                $user=M('users')->where(array('uid' => $data[$i]['uid']))->find();
                if($user){
                    M('personal_password')->add($data[$i]);
                    $data[$i]['result']='成功';
                    $data[$i]['userinfo']=$user;
                    $succ_count++;
                }else{
                    $data[$i]['result']='失败';
                    $erro_count++;
                }
            } 
            $callEndTime = microtime(true);
            $callTime = $callEndTime - $callStartTime;
            echo 'Call time to read Workbook was ' , sprintf('%.4f',$callTime) , " seconds</br>";
            $this->success('导入成功'.$succ_count.'条！失败：'.$erro_count.'条！');
        }else{
            //
            $this->error('文件上传失败！');
        } 
        
    }
}