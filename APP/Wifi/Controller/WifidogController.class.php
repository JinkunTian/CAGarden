<?php
namespace Wifi\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2021年3月14日14:45:47
 * @Description: 计算机协会WiFi接入审计控制器
 ***/
class WifidogController extends Controller {

    private function Init()
	{
        sys_log('WiFi');

        if (!(  isset($_SESSION['username']) && 
                isset($_SESSION['uid']) && 
                isset($_SESSION['truename']) && 
                isset($_SESSION[C('PASSWORD_KEY')]) )) 
        {
            session('req_url',$_SERVER["REQUEST_URI"]);
            $this->redirect('/Authentication');
        }else{

        }
	}
    
    /**
     * index方法根据用户信息登记审计并放行WiFi接入
     */
    public function index(){ 
    
        die("Bad request");
        
    }
    /**
     * index方法根据用户信息登记审计并放行WiFi接入
     */
    public function login(){
        
        $this->Init();
        
        session('wifi_req_url',I('url'));
        
        if( I('mac') && I('ip') && I('gw_address') && I('gw_port')){
            $wifi_log=array(
                'mac'=>I('mac'),
                'ip'=>I('ip'),
                'type'=>'Wifidog',
                'token'=>md5(I('user_ip').I('mac').time()),
                'timestamp'=>date("Y-m-d H:i:s",time()),
                'username'=>session('username'),
                'truename'=>session('truename')
            );
            M('wifi')->add($wifi_log);
            $location = 'Location: http://'.I('gw_address').':' .I('gw_port').'/wifidog/auth?token='.$wifi_log['token'];
            header($location);

        }else{
            $this->error('参数错误！','/');
        }
    }
    /**
     * auth WiFidog 会通过auth定时检测用户在线的合法性
     */
    public function auth(){ 
    
        if(M('wifi')->where(array('ip'=>I('ip'),'mac'=>I('mac'),'token'=>I('token')))->find()){
            die('Auth: 1');
        }else{
            die('Auth: 0');
        }
      
    }
    /**
     * portal 用户登录成功后重定向到原先请求的地址
     */
    public function portal(){ 

        // $location = 'Location: '. session('wifi_req_url');
        // header($location);
        
        $location = 'Location: /';
        header($location);
      
    }
    /**
     * message方法根据用户信息登记审计并放行WiFi接入
     */
    public function message(){ 
    
        var_dump($_GET);
      
    }
    /**
     * wifidog 使用ping检测认证服务器在线
     */
    public function ping(){ 
    
        echo('Pong');
      
    }
}
