<?php
namespace Wifi\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2018年8月21日14:45:47
 * @Description: 计算机协会WiFi接入审计控制器
 ***/
class IndexController extends CommonController {

    private function getData($url, $token = "")
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
    
    /**
     * index方法根据用户信息登记审计并放行WiFi接入
     */
    public function index(){
        if( I('mac') && I('user_ip') && I('timestamp') ){
            $wifi_log=array(
                'mac'=>I('mac'),
                'ip'=>I('user_ip'),
                'timestamp'=>date("Y-m-d H:i:s",I('timestamp')),
                'username'=>session('username'),
                'truename'=>session('name')
            );
            M('wifi')->add($wifi_log);
            $base_url='https://portal.ikuai8-wifi.com/Action/webauth-up?type=20';
            $token=md5('user_ip='.$wifi_log['ip'].'&timestamp='.I('timestamp').'&mac='.$wifi_log['mac'].'&upload=0&download=0&key='.C('iKuai_APPKEY'));
            $url=$base_url.'&user_id='.$wifi_log['username'].'&name='.$wifi_log['name'].'&user_ip='.$wifi_log['ip']."&timestamp=".I('timestamp').'&mac='.$wifi_log['mac'].'&upload=0&download=0&token='.$token;
header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
            $this->assign('url',$url);
            $this->display();
        }else{
            $this->error('参数错误！','/');
        }
        
        // $resault=$this->getData($url);
        // if($resault['errmsg']=='认证成功'){
        //     $this->success('WiFi接入成功！','http://www.mecca.org.cn',2);
        // }else{
        //     $this->error('WiFi接入失败，请暂时访问内网资源！','http://www.mecca.org.cn');
        // }
    }
    
    /**
     * 别看了，没有deleteAppointment方法的，不允许用户单方面删除记录的
     * 年纪轻轻的，学锤子计算机？有头发不好吗！
     */
}
