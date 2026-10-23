<?php
namespace Garden\Controller;
use Think\Controller;
/***
 * @Author:      田津坤
 * @Email:       me@tianjinkun.com
 * @QQ:          2961165914
 * @Blog         https://blog.tianjinkun.com
 * @GitHub:      https://github.com/JinkunTian
 * @DateTime:    2020年8月31日
 * @Description: 首页管理（管理员）控制器
 ***/
class IndexmanageController extends AdminController {

    private function photoPublicHost(){
        $host=parse_url((string)C('PHOTO_PUBLIC_BASE_URL'),PHP_URL_HOST);
        return is_string($host) ? strtolower(rtrim($host,'.')) : '';
    }

    private function photoMainHosts(){
        $hosts=C('PHOTO_MAIN_HOSTS');
        $hosts=is_array($hosts) ? $hosts : array();
        $requestHost=isset($_SERVER['HTTP_HOST']) ? parse_url('http://'.$_SERVER['HTTP_HOST'],PHP_URL_HOST) : '';
        $hosts[]=$requestHost;$hosts[]='';
        return array_map(function($host){return strtolower(rtrim((string)$host,'.'));},$hosts);
    }

    /** Only deployment configuration selects the upstream; never a submitted URL. */
    private function photoApiUrl($path){
        $base=rtrim((string)C('PHOTO_API_BASE_URL'),'/');
        $parts=parse_url($base);
        if(!$parts || empty($parts['host']) || !isset($parts['scheme']) || !in_array(strtolower($parts['scheme']),array('http','https'),true)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])){
            throw new \RuntimeException('请先配置协会相册接口地址。');
        }
        return $base.'/api/'.$path;
    }

    private function csrfToken(){
        $token=session('indexmanage_csrf_token');
        if(!is_string($token) || $token===''){
            $token=bin2hex(random_bytes(24));
            session('indexmanage_csrf_token',$token);
        }
        return $token;
    }

    private function checkWriteRequest(){
        if($_SERVER['REQUEST_METHOD']!=='POST'){
            $this->error('请求方式不正确！');
        }
        $token=isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null;
        $expected=session('indexmanage_csrf_token');
        if(!is_string($token) || $token==='' || !is_string($expected) || $expected==='' || !hash_equals($expected,$token)){
            $this->error('页面已失效，请刷新后重新提交。');
        }
    }

    /** URL 字段只接受网页、联系链接和相对地址，文字在输出时转义。 */
    private function validateHomepageUrls($type,$data){
        $fields=array(
            'main_button'=>array('config_value_2'=>false),
            'association_info'=>array('config_value_2'=>false),
            'association_album'=>array('config_value_2'=>true,'config_value_3'=>false),
            'partner_association'=>array('config_value_1'=>true),
            'contact_info'=>array('config_value_1'=>false),
            'navbar'=>array('config_value_1'=>false)
        );
        if(!isset($fields[$type])) return;
        foreach($fields[$type] as $field=>$image){
            if(!isset($data[$field]) || $data[$field]==='') continue;
            $url=html_entity_decode($data[$field],ENT_QUOTES,'UTF-8');
            $parts=parse_url($url);
            $scheme=is_array($parts) && isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
            $allowed=$image ? array('http','https') : array('http','https','mailto','tel');
            if(preg_match('/[\x00-\x20\x7f\\\\]/',$url) || !is_array($parts) || isset($parts['user']) || isset($parts['pass'])
                || (isset($parts['host']) && preg_match('/[%\x80-\xFF]/',$parts['host']))
                || ($scheme!=='' && !in_array($scheme,$allowed,true))
                || (in_array($scheme,array('http','https'),true) && empty($parts['host']))){
                throw new \RuntimeException('链接或图片地址格式无效，请使用安全的网页、联系链接或相对地址。');
            }
            if($type==='partner_association'){
                // Decode the path and collapse dot segments so encoded/relative Photo routes cannot bypass this guard.
                $decoded=parse_url(rawurldecode($url));
                $host=is_array($decoded) && isset($decoded['host']) ? strtolower(rtrim($decoded['host'],'.')) : '';
                $segments=array();
                foreach(explode('/',is_array($decoded) && isset($decoded['path']) ? $decoded['path'] : '') as $segment){
                    if($segment==='..'){array_pop($segments);}elseif($segment!=='' && $segment!=='.'){$segments[]=$segment;}
                }
                $photoHost=$this->photoPublicHost();
                if(($photoHost!=='' && $host===$photoHost) || (in_array($host,$this->photoMainHosts(),true) && isset($segments[0]) && strtolower($segments[0])==='photo-media')){
                    throw new \RuntimeException('兄弟社团 Logo 不能引用 Photo，请上传图片或选择主站图片。');
                }
            }
        }
    }

    /**
     * 首页配置的单项和多项类型白名单。
     */
    private function getConfigTypes(){
        return array(
            'admin_brand',
            'association_slogan',
            'main_button',
            'association_info',
            'association_address',
            'major_activities',
            'association_album',
            'partner_association',
            'contact_info',
            'navbar'
        );
    }

    /**
     * 取安全的 POST 字段，避免把数组等异常值写入配置表。
     */
    private function postValue($source, $key){
        if(!is_array($source) || !array_key_exists($key, $source) || is_array($source[$key])){
            return '';
        }
        return trim((string)$source[$key]);
    }

    /** 与 Photo 的引用检查共用文件锁，防止检查与主页保存交错。 */
    private function lockPhotoHomepage(){
        if(!C('PHOTO_PUBLIC_BASE_URL') && !C('PHOTO_API_BASE_URL')) return true;
        $path=(string)C('PHOTO_REFERENCE_LOCK_PATH');
        $lock=$path!=='' ? @fopen($path,'r+') : false;
        if($lock){
            $deadline=microtime(true)+5;
            do{
                if(@flock($lock,LOCK_EX|LOCK_NB)) return $lock;
                usleep(100000);
            }while(microtime(true)<$deadline);
            fclose($lock);
        }
        http_response_code(503);
        $this->error('首页相册保护暂不可用或正在更新，请稍后重试；本次未保存。');
        return false;
    }

    private function unlockPhotoHomepage($lock){
        if(is_resource($lock)){
            flock($lock,LOCK_UN);
            fclose($lock);
        }
    }

    /** Normalize browser/server dot segments before identifying protected media routes. */
    private function homepagePhotoPath($path){
        $decoded=str_replace('\\','/',rawurldecode($path));
        // Repeated separators and dot segments are interpreted differently by URL consumers.
        // Picked Photo URLs are canonical; reject ambiguous local/Photo paths instead of guessing.
        if(strpos($decoded,'//')!==false) throw new \RuntimeException('Photo 地址包含重复斜杠，请重新选图。');
        $segments=array();
        foreach(explode('/',$decoded) as $segment){
            if($segment==='..'){array_pop($segments);}
            elseif($segment!=='' && $segment!=='.'){$segments[]=$segment;}
        }
        return '/'.implode('/',$segments);
    }

    /** 只识别配置的 Photo 域名和主站地址，不将用户输入的 URL 转发。 */
    private function homepagePhotoUrl($url){
        $parts=parse_url($url);
        if(!is_array($parts)) return array();
        $host=isset($parts['host']) ? strtolower(rtrim($parts['host'],'.')) : '';
        if(isset($parts['host'])) $parts['host']=$host;
        $allowed=$this->photoMainHosts();$allowed[]=$this->photoPublicHost();
        if($host!=='' && !in_array($host,$allowed,true)) return array();
        $parts['path']=$this->homepagePhotoPath(isset($parts['path']) ? $parts['path'] : '');
        $scheme=isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
        if($host===''){
            return $scheme==='' && substr($url,0,2)!=='//' ? $parts : array();
        }
        $invalid=!in_array($scheme,array('http','https'),true) || isset($parts['user']) || isset($parts['pass']);
        $invalid=$invalid || (isset($parts['port']) && $parts['port']!==($scheme==='https' ? 443 : 80));
        if($invalid){
            if($host===$this->photoPublicHost() || (isset($parts['path']) && strpos($parts['path'],'/photo-media/')===0)){
                throw new \RuntimeException('Photo 地址格式无效，请使用正常的 HTTP/HTTPS 公开地址。');
            }
            return array();
        }
        return $parts;
    }

    private function normalizeHomepagePhotoImage($image,&$photoId){
        $photoId=0;
        $parts=$this->homepagePhotoUrl($image);
        if(!$parts) return $image;
        $path=isset($parts['path']) ? $parts['path'] : '';
        $photoHost=isset($parts['host']) && strtolower($parts['host'])===$this->photoPublicHost();
        $pattern=$photoHost ? '#^/api/media/([1-9][0-9]*)/(?:thumb|small|medium|large|original)/?$#D' : '#^/photo-media/([1-9][0-9]*)/(?:thumb|small|medium|large|original)/?$#D';
        if(preg_match($pattern,$path,$matches)){
            $photoId=(int)$matches[1];
            if($photoId<1) throw new \RuntimeException('Photo 图片编号无效，请重新选图。');
            return '/photo-media/'.$photoId.'/medium';
        }
        if($photoHost || strpos($path,'/photo-media/')===0){
            throw new \RuntimeException('Photo 图片地址无效，请从协会相册重新选图。');
        }
        return $image;
    }

    private function homepagePhotoAlbumId($link){
        $parts=$this->homepagePhotoUrl($link);
        if(!$parts || !isset($parts['host']) || strtolower($parts['host'])!==$this->photoPublicHost()) return 0;
        $path=isset($parts['path']) ? $parts['path'] : '';
        if(preg_match('#^/albums/([1-9][0-9]*)/?$#D',$path,$matches)) return (int)$matches[1];
        if(strpos($path,'/albums/')===0) throw new \RuntimeException('Photo 相册链接无效，请重新选择相册。');
        return 0;
    }

    /** 无登录 Cookie 的本机公开接口，404/私密/处理中一律拒绝引用。 */
    private function publicHomepagePhotoData($kind,$id){
        if(!function_exists('curl_init')) throw new \RuntimeException('无法验证协会相册，服务器未启用 cURL；本次未保存。');
        $url=$this->photoApiUrl($kind.'/'.(int)$id);
        $ch=curl_init($url);
        curl_setopt_array($ch,array(CURLOPT_RETURNTRANSFER=>true,CURLOPT_PROXY=>'',CURLOPT_CONNECTTIMEOUT=>1,CURLOPT_TIMEOUT=>3,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_HTTPHEADER=>array('Accept: application/json')));
        $body=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
        $result=is_string($body) && strlen($body)<1048576 ? json_decode($body,true) : null;
        if($code!=200 || !is_array($result) || !isset($result['id']) || (int)$result['id']!==(int)$id){
            throw new \RuntimeException('Photo '.($kind==='photos' ? '图片' : '相册').' #'.(int)$id.' 不可公开访问或服务暂不可用，请重新选择公开内容；本次未保存。');
        }
        return $result;
    }

    private function validateHomepagePhotoReference($image,$link){
        $image=$this->normalizeHomepagePhotoImage($image,$photoId);
        $albumId=$this->homepagePhotoAlbumId($link);
        if($photoId){
            $photo=$this->publicHomepagePhotoData('photos',$photoId);
            if(!isset($photo['status']) || $photo['status']!=='ready') throw new \RuntimeException('Photo 图片 #'.$photoId.' 尚未处理完成，请稍后重新选图；本次未保存。');
            if($albumId && (!isset($photo['albums']) || !is_array($photo['albums']) || !in_array($albumId,array_map('intval',$photo['albums']),true))){
                throw new \RuntimeException('Photo 图片 #'.$photoId.' 不属于链接中的公开相册 #'.$albumId.'，请重新选择；本次未保存。');
            }
        }elseif($albumId){
            $this->publicHomepagePhotoData('albums',$albumId);
        }
        return $image;
    }

    /**
     * index方法展示首页配置。
     */
    public function index(){
        $rows=M('index_config')->order('cid ASC')->select();
        $groups=array();
        foreach((array)$rows as $row){
            if(!isset($groups[$row['config_name']])){
                $groups[$row['config_name']]=array();
            }
            $groups[$row['config_name']][]=$row;
        }

        $single=array('admin_brand','association_slogan','main_button','association_info','association_address');
        foreach($single as $name){
            $this->assign($name, isset($groups[$name][0]) ? $groups[$name][0] : array());
        }

        $multi=array('major_activities','association_album','partner_association','contact_info','navbar');
        foreach($multi as $name){
            $this->assign($name, isset($groups[$name]) ? $groups[$name] : array());
        }

        $this->assign('csrf_token',$this->csrfToken());
        $this->display();
    }

    /**
     * save方法保存已有首页配置。
     */
    public function save(){
        $this->checkWriteRequest();

        $configs=isset($_POST['configs']) && is_array($_POST['configs']) ? $_POST['configs'] : array();
        $allowed=$this->getConfigTypes();
        $fields=array('config_value_1','config_value_2','config_value_3');
        $model=M('index_config');
        $lock=$this->lockPhotoHomepage();
        if(!$lock) return;
        $errorMessage=null;
        $transactionStarted=false;
        try{
        $updates=array();
        // Validate every proposed Photo reference before writing any setting.
        foreach($configs as $cid=>$values){
            $cid=intval($cid);
            if($cid<1 || !is_array($values)){
                continue;
            }

            $row=$model->where(array('cid'=>$cid))->find();
            if(!$row || !in_array($row['config_name'],$allowed,true)){
                continue;
            }

            if($row['config_name']==='admin_brand'){
                $icon=$this->postValue($values,'config_value_1');
                $default='/Public/images/ca-admin-hammer.webp';
                $valid=$icon===$default || preg_match('#^/Public/Uploads/Indexmanage/Brand/[0-9]{4}-[0-9]{2}-[0-9]{2}/[a-f0-9]{32}\.webp$#D',$icon);
                if(!$valid || !is_file('.'.$icon)){
                    throw new \RuntimeException('请上传有效的后台图标。');
                }
                $updates[]=array('cid'=>$cid,'data'=>array('config_value_1'=>$icon));
                continue;
            }

            $data=array();
            foreach($fields as $field){
                if(array_key_exists($field,$values)){
                    $data[$field]=$this->postValue($values,$field);
                }
            }
            if($data){
                $this->validateHomepageUrls($row['config_name'],$data);
                if($row['config_name']==='association_album'){
                    $image=array_key_exists('config_value_2',$data) ? $data['config_value_2'] : $row['config_value_2'];
                    $link=array_key_exists('config_value_3',$data) ? $data['config_value_3'] : $row['config_value_3'];
                    if($image!==$row['config_value_2'] || $link!==$row['config_value_3']){
                        $data['config_value_2']=$this->validateHomepagePhotoReference($image,$link);
                    }
                }
                $updates[]=array('cid'=>$cid,'data'=>$data);
            }
        }
        if($model->startTrans()===false){
            throw new \RuntimeException('无法开始保存，请稍后重试！');
        }
        $transactionStarted=true;
        foreach($updates as $update){
            if($model->where(array('cid'=>$update['cid']))->save($update['data'])===false){
                throw new \RuntimeException('保存失败，本次修改未保存，请稍后重试！');
            }
        }
        if($model->commit()===false){
            throw new \RuntimeException('保存失败，本次修改未保存，请稍后重试！');
        }
        $transactionStarted=false;
        }catch(\Throwable $error){
            $errorMessage=!$transactionStarted && get_class($error)==='RuntimeException' ? $error->getMessage() : '保存失败，本次修改未保存，请稍后重试！';
        }finally{
            try{
                if($transactionStarted){$model->rollback();}
            }finally{
                $this->unlockPhotoHomepage($lock);
            }
        }
        if($errorMessage!==null){$this->error($errorMessage);return;}
        $this->success('首页配置已保存！',U('/Garden/Indexmanage/'));
    }

    /**
     * add_item方法新增活动、相册、兄弟社团、联系方式或导航栏项目。
     */
    public function add_item(){
        $this->checkWriteRequest();

        $type=$this->postValue($_POST,'config_name');
        $commits=array(
            'major_activities'=>'社团主要活动',
            'association_album'=>'首页相册',
            'partner_association'=>'兄弟社团',
            'contact_info'=>'联系方式',
            'navbar'=>'导航栏'
        );
        if(!isset($commits[$type])){
            $this->error('配置类型不正确！');
            return;
        }
        $lock=$this->lockPhotoHomepage();
        if(!$lock) return;
        $errorMessage=null;
        try{
        $model=M('index_config');
        $maxKey=$model->where(array('config_name'=>$type))->max('config_key');
        $nextKey=is_numeric($maxKey) ? intval($maxKey)+1 : 0;
        $data=array(
            'config_name'=>$type,
            'config_commit'=>$commits[$type],
            'config_key'=>(string)$nextKey,
            'config_value_1'=>$this->postValue($_POST,'config_value_1'),
            'config_value_2'=>$this->postValue($_POST,'config_value_2'),
            'config_value_3'=>$this->postValue($_POST,'config_value_3')
        );
        $this->validateHomepageUrls($type,$data);
        if($type==='association_album'){
            $data['config_value_2']=$this->validateHomepagePhotoReference($data['config_value_2'],$data['config_value_3']);
        }
        if($model->add($data)===false){
            throw new \RuntimeException('新增失败，请稍后重试！');
        }
        }catch(\RuntimeException $error){
            $errorMessage=$error->getMessage();
        }finally{
            $this->unlockPhotoHomepage($lock);
        }
        if($errorMessage!==null){$this->error($errorMessage);return;}
        $this->success('新增成功！',U('/Garden/Indexmanage/'));
    }

    /**
     * upload_image方法上传首页相册或兄弟社团图片。
     * 上传目录固定在 Public/Uploads/Indexmanage 下，不允许写入其他位置。
     */
    public function upload_image(){
        $this->checkWriteRequest();
        if(!isset($_FILES['image']) || !is_array($_FILES['image'])){
            $this->ajaxReturn(array('status'=>0,'info'=>'请选择图片！'));
        }

        $upload=new \Think\Upload();
        $upload->maxSize=C('MAX_PHOTO_POST_SIZE');
        $upload->exts=array('jpg','jpeg','png','gif');
        $upload->rootPath='./Public';
        $upload->savePath='/Uploads/Indexmanage/';

        $info=$upload->uploadOne($_FILES['image']);
        if(!$info){
            $this->ajaxReturn(array('status'=>0,'info'=>'图片上传失败：'.$upload->getError()));
        }

        $url='/Public'.$info['savepath'].$info['savename'];
        $this->ajaxReturn(array('status'=>1,'url'=>$url,'info'=>'图片上传成功！'));
    }

    /**
     * browse_images方法分页浏览网站已有的公开图片。
     * 仅扫描 Public/Uploads 和 Public/Index/images，并排除个人头像目录。
     */

    /** 新相册公开图片选择器：固定上游，只读，不接收任意 URL。 */
    public function browse_photo_images(){
        $page=max(1,min(10000,intval(isset($_GET['page']) ? $_GET['page'] : 1)));
        $keyword=isset($_GET['keyword']) && !is_array($_GET['keyword']) ? substr(trim((string)$_GET['keyword']),0,200) : '';
        try{
            $url=$this->photoApiUrl('integrations/picker?'.http_build_query(array('page'=>$page,'keyword'=>$keyword)));
        }catch(\RuntimeException $error){
            $this->ajaxReturn(array('status'=>0,'info'=>$error->getMessage()));
            return;
        }
        if(!function_exists('curl_init')){
            $this->ajaxReturn(array('status'=>0,'info'=>'服务器未启用 cURL，请使用主站图片入口。'));
            return;
        }
        $ch=curl_init($url);
        curl_setopt_array($ch,array(CURLOPT_RETURNTRANSFER=>true,CURLOPT_PROXY=>'',CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>8,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2));
        $body=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
        $result=is_string($body) && strlen($body)<1048576 ? json_decode($body,true) : null;
        if($code!=200 || !is_array($result) || !isset($result['status']) || $result['status']!=1 || !isset($result['items']) || !is_array($result['items'])){
            $this->ajaxReturn(array('status'=>0,'info'=>'协会相册暂不可用，可切换到主站图片。'));
            return;
        }
        // Main-site images stay same-origin; album destinations stay public.
        foreach($result['items'] as &$item){
            if(isset($item['id']) && (int)$item['id']>0){
                $item['url']='/photo-media/'.(int)$item['id'].'/medium';
            }
        }
        unset($item);
        $this->ajaxReturn($result);
    }

    /** 管理页面图标上传：仅管理员，固定目录，转换为 192px WebP。 */
    public function upload_brand_icon(){
        $this->checkWriteRequest();
        $file=isset($_FILES['image']) && is_array($_FILES['image']) ? $_FILES['image'] : null;
        if(!$file || !isset($file['error']) || $file['error']!=UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])){
            $this->ajaxReturn(array('status'=>0,'info'=>'请选择有效图片，文件最大 4MB。'));
            return;
        }
        if($file['size']<1 || $file['size']>4194304){
            $this->ajaxReturn(array('status'=>0,'info'=>'图片文件最大 4MB。'));
            return;
        }
        $relative='/Public/Uploads/Indexmanage/Brand/'.date('Y-m-d').'/';
        $directory='.'. $relative;
        if(!is_dir($directory) && !mkdir($directory,0755,true)){
            $this->ajaxReturn(array('status'=>0,'info'=>'无法保存图片，请稍后重试。'));
            return;
        }
        $name=bin2hex(random_bytes(16)).'.webp';
        $target=$directory.$name;
        try{
            $this->compressBrandIcon($file['tmp_name'],$target);
        }catch(\Exception $error){
            if(is_file($target)) unlink($target);
            $this->ajaxReturn(array('status'=>0,'info'=>$error->getMessage()));
            return;
        }
        $this->ajaxReturn(array('status'=>1,'url'=>$relative.$name,'bytes'=>filesize($target),'width'=>192,'height'=>192,'info'=>'图标已压缩，保存修改后生效。'));
    }

    /** 图像解码后重新编码，保留透明度，不保留上传文件内容或元数据。 */
    private function compressBrandIcon($source,$target){
        if(!function_exists('imagewebp')) throw new \RuntimeException('服务器暂不支持图标处理。');
        $info=@getimagesize($source);
        if(!$info || !in_array($info[2],array(IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_WEBP),true)){
            throw new \RuntimeException('请选择 JPG、PNG 或 WebP 图片。');
        }
        if($info[0]<1 || $info[1]<1 || $info[0]*$info[1]>8388608){
            throw new \RuntimeException('图片像素过大，请先缩小到 800 万像素以内。');
        }
        $image=@imagecreatefromstring(file_get_contents($source));
        if(!$image) throw new \RuntimeException('图片无法读取，请换一张图片。');
        $output=imagecreatetruecolor(192,192);
        imagealphablending($output,false);
        imagesavealpha($output,true);
        imagefill($output,0,0,imagecolorallocatealpha($output,0,0,0,127));
        $scale=min(192/$info[0],192/$info[1]);
        $width=max(1,(int)round($info[0]*$scale));
        $height=max(1,(int)round($info[1]*$scale));
        $ok=imagecopyresampled($output,$image,(int)floor((192-$width)/2),(int)floor((192-$height)/2),0,0,$width,$height,$info[0],$info[1]);
        if($ok) $ok=imagewebp($output,$target,88);
        imagedestroy($image);
        imagedestroy($output);
        if(!$ok || !is_file($target) || filesize($target)<1) throw new \RuntimeException('图片压缩失败，请重试。');
    }

    public function browse_images(){
        $siteRoot=dirname(dirname(dirname(dirname(__FILE__))));
        $roots=array(
            $siteRoot.'/Public/Uploads',
            $siteRoot.'/Public/Index/images'
        );
        $profileRoot=$siteRoot.'/Public/Uploads/Profiles';
        $extensions=array('jpg','jpeg','png','gif','webp');
        $keyword=isset($_GET['keyword']) && !is_array($_GET['keyword']) ? trim((string)$_GET['keyword']) : '';
        $keyword=strtolower($keyword);
        $images=array();

        foreach($roots as $root){
            if(!is_dir($root)){
                continue;
            }
            try{
                $iterator=new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($root,\FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach($iterator as $file){
                    if(!$file->isFile()){
                        continue;
                    }
                    $path=$file->getPathname();
                    if(strpos($path,$profileRoot)===0){
                        continue;
                    }
                    $extension=strtolower($file->getExtension());
                    if(!in_array($extension,$extensions,true)){
                        continue;
                    }

                    $relative=str_replace('\\','/',substr($path,strlen($siteRoot)));
                    if($keyword!=='' && strpos(strtolower($relative),$keyword)===false){
                        continue;
                    }
                    $images[]=array(
                        'url'=>$relative,
                        'name'=>$file->getFilename(),
                        'mtime'=>$file->getMTime()
                    );
                }
            }catch(\Exception $e){
                continue;
            }
        }

        usort($images,function($left,$right){
            if($left['mtime']==$right['mtime']){
                return strcmp($left['url'],$right['url']);
            }
            return ($left['mtime']>$right['mtime']) ? -1 : 1;
        });

        $page=max(1,intval(isset($_GET['page']) ? $_GET['page'] : 1));
        $pageSize=48;
        $total=count($images);
        $pages=max(1,intval(ceil($total/$pageSize)));
        $page=min($page,$pages);
        $items=array_slice($images,($page-1)*$pageSize,$pageSize);
        foreach($items as &$item){
            unset($item['mtime']);
        }
        unset($item);

        $this->ajaxReturn(array(
            'status'=>1,
            'items'=>$items,
            'page'=>$page,
            'pages'=>$pages,
            'total'=>$total
        ));
    }

    /**
     * delete_item方法删除可重复的首页配置项目。
     */
    public function delete_item(){
        $this->checkWriteRequest();
        $cid=intval(I('post.cid'));
        if($cid<1){
            $this->error('参数不正确！');
            return;
        }
        $lock=$this->lockPhotoHomepage();
        if(!$lock) return;
        $errorMessage=null;
        try{
        $model=M('index_config');
        $row=$model->where(array('cid'=>$cid))->find();
        $deletable=array('major_activities','association_album','partner_association','contact_info','navbar');
        if(!$row || !in_array($row['config_name'],$deletable,true)){
            throw new \RuntimeException('该配置不能删除！');
        }
        if($model->where(array('cid'=>$cid))->delete()===false){
            throw new \RuntimeException('删除失败，请稍后重试！');
        }
        }catch(\RuntimeException $error){
            $errorMessage=$error->getMessage();
        }finally{
            $this->unlockPhotoHomepage($lock);
        }
        if($errorMessage!==null){$this->error($errorMessage);return;}
        $this->success('删除成功！',U('/Garden/Indexmanage/'));
    }
}
