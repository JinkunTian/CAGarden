<?php
/**
 * 安装向导
 */
header('Content-type:text/html;charset=utf-8');
// 检测是否安装过
if (file_exists('./install.lock')) {
    echo '你已经安装过该系统，重新安装需要先删除./Public/install/install.lock 文件';
    die;
}
// 同意协议页面
if(@!isset($_GET['c']) || @$_GET['c']=='agreement'){
    require './agreement.html';
}
// 检测环境页面
if(@$_GET['c']=='test'){
    require './test.html';
}
// 创建数据库页面
if(@$_GET['c']=='create'){
    require './create.html';
}
// 安装成功页面
if(@$_GET['c']=='success'){
    // 判断是否为post
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $data=$_POST;
        // 连接数据库
        $link=@new mysqli("{$data['DB_HOST']}:{$data['DB_PORT']}",$data['DB_USER'],$data['DB_PWD']);
        // 获取错误信息
        $error=$link->connect_error;
        if (!is_null($error)) {
            // 转义防止和alert中的引号冲突
            $error=addslashes($error);
            die("<script>alert('数据库链接失败:$error');history.go(-1)</script>");
        }
        // 设置字符集
        $link->query("SET NAMES 'utf8'");
        $link->server_info>5.0 or die("<script>alert('请将您的mysql升级到5.0以上');history.go(-1)</script>");
        // 创建数据库并选中
        if(!$link->select_db($data['DB_NAME'])){
            $create_sql='CREATE DATABASE IF NOT EXISTS '.$data['DB_NAME'].' DEFAULT CHARACTER SET utf8;';
            $link->query($create_sql) or die('创建数据库失败');
            $link->select_db($data['DB_NAME']);
        }
        // 创建表
        $ptadmin_str=file_get_contents('./pt.sql');
        $sql_array=preg_split("/;[\r\n]+/", str_replace('pt_',$data['DB_PREFIX'],$ptadmin_str));
        foreach ($sql_array as $k => $v) {
            if (!empty($v)) {
                $link->query($v);
            }
        }
        // 导入sql数据
        $ptadmin_str=file_get_contents('./insert.sql');
        $sql_array=preg_split("/;[\r\n]+/", str_replace('pt_',$data['DB_PREFIX'],$ptadmin_str));
        foreach ($sql_array as $k => $v) {
            if (!empty($v)) {
                $link->query($v);
            }
        }
        $link->close();
        $db_str=<<<php
<?php
return array(

//*************************************数据库设置*************************************
    'DB_TYPE'               =>  'mysqli',                 // 数据库类型
    'DB_HOST'               =>  '{$data['DB_HOST']}',     // 服务器地址
    'DB_NAME'               =>  '{$data['DB_NAME']}',     // 数据库名
    'DB_USER'               =>  '{$data['DB_USER']}',     // 用户名
    'DB_PWD'                =>  '{$data['DB_PWD']}',      // 密码
    'DB_PORT'               =>  '{$data['DB_PORT']}',     // 端口
    'DB_PREFIX'             =>  '{$data['DB_PREFIX']}',   // 数据库表前缀
    
    /**
     * 站点名称
     */
    'SITE_NAME'             =>  '{$data['SITE_NAME']}',

    /**
     * 密码加密密钥
     */
    'PASSWORD_KEY'			=>	'{$data['PASSWORD_KEY']}',

    /**
     * 默认模块为前台
     */
    'DEFAULT_MODULE'		=>	'Index',

    /**
      * URL重写，隐藏index.php,
      * 请保证你的服务器支持伪静态
      */ 
    'URL_MODEL' => '2',

    'SHOW_PAGE_TRACE' => false,  //开启调试模式

    /**
     * 允许/禁止在公告中评论
     */
    'ALLOW_BLACKBOARD_COMMENT'  =>false,

    /**
     * 最大头像上传大小
     */
    'MAX_PHOTO_POST_SIZE'	=> 4194304,

    /**
     * GEETEST极验滑动验证码
     */
    'ENABLE_GEETEST'        =>  false,
    'CAPTCHA_ID'            =>  'YOUR_CAPTCHA_ID',
    'PRIVATE_KEY'           =>  'YOUR_PRIVATE_KEY',
    'ERROR_PAGE' =>'/Public/error.html',
);
php;


        // 创建数据库链接配置文件
        file_put_contents('../../APP/Common/Conf/config.php', $db_str);
        @touch('./install.lock');
        require './success.html';
    }

}

