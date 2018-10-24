<?php
namespace Recruit\Controller;
use Think\Controller;
class UserLoginController extends Controller {
    public function index(){
		$this->display();
    }
    public function login(){
		/*
			一个简单的获取当前一周的课程表的示例
			请修改$xh及$pass变量为你的教务系统学号及密码
			https://github.com/TLingC/GDUF-QZAPI
		*/
		$xh = $_POST['userxh'];
		$pass = $_POST['password'];
		header("Content-type: text/html; charset=utf-8");
		function getData($url, $token = "")
		{
			$ch = curl_init();
			$timeout = 3;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'token:' . $token
			));
			$handles = curl_exec($ch);
			curl_close($ch);
			return json_decode($handles,true);
		}
		while(!($data = getData("http://cquccjw.minghuaetc.com/cqdxcskjxy/app.do?method=authUser&xh=" . $xh . "&pwd=" . $pass)));
		if($data['token'] == -1){
			exit($data['msg']);
		}else{

			session('newmember_xm',$data['user']['username']);
			session('newmember_xh',$data['user']['useraccount']);	
			session('newmember_xy',$data['user']['userdwmc']);

			$this->redirect('/Recruit/Recruit');
		}
    }
}
