<?php
namespace Appointment\Controller;

class WorkstudyController extends CommonController {
    private $storageRoot = '/www/secure/workstudy-uploads/';
    private $templateRoot = '/www/secure/workstudy-templates/';

    private function projects(){
        $applications = M('work_study_application')->where(array('uid'=>(int)session('uid')))->field('wid')->select();
        $ids = array();
        foreach ((array)$applications as $application) { $ids[] = (int)$application['wid']; }
        $where = array('status'=>array('in', array(1, 2)));
        if ($ids) { $where = array('status'=>array('in', array(1, 2)), 'wid'=>array('in', $ids), '_logic'=>'OR'); }
        return M('work_study_grade')->where($where)->order('status ASC, addtime DESC, wid DESC')->select();
    }

    private function project($wid=0){
        $model = M('work_study_grade');
        if ((int)$wid > 0) {
            return $model->where(array('wid'=>(int)$wid))->find();
        }
        return $model->where(array('status'=>array('in',array(1,2))))->order('status ASC, addtime DESC, wid DESC')->find();
    }

    private function activeProject($wid=0){
        $project = $this->project($wid);
        return $project && intval($project['status']) === 1 ? $project : null;
    }

    private function csrfToken(){
        $token = session('workstudy_csrf_token');
        if (!$token) {
            $token = bin2hex(openssl_random_pseudo_bytes(24));
            session('workstudy_csrf_token', $token);
        }
        return $token;
    }

    private function checkCsrf(){
        $token = (string)I('post.csrf_token');
        if (!$token || !hash_equals((string)session('workstudy_csrf_token'), $token)) {
            $this->error('页面已失效，请刷新后重新提交。');
        }
    }

    private function renderEnded($project=null){
        $this->assign('project', $this->viewData($project));
        $this->assign('application', $project ? $this->viewData($this->submittedApplication($project['wid'])) : null);
        $this->display('ended');
        exit;
    }

    private function requireActiveProject($wid=0){
        $project = $this->project($wid);
        if (!$project || (int)$project['status'] !== 1) {
            $this->renderEnded($project);
        }
        return $project;
    }

    private function user(){
        return M('users')->where(array('uid'=>(int)session('uid')))->find();
    }

    private function submittedApplication($wid){
        return M('work_study_application')->where(array(
            'wid'=>(int)$wid,
            'uid'=>(int)session('uid')
        ))->find();
    }

    private function applicationMajor($majorId, $application){
        if ($majorId <= 0) { return null; }
        if ($application && (int)$application['major_id'] === $majorId) {
            // Keep the application's snapshot even if its catalogue entry was retired or renamed.
            return array('mid'=>$majorId, 'institute'=>$application['college'], 'mname'=>$application['major']);
        }
        return M('common_majors')->where(array('mid'=>$majorId, 'status'=>'1'))->find();
    }

    private function latestApplication(){
        return M('work_study_application')->where(array(
            'uid'=>(int)session('uid')
        ))->order('addtime DESC, aid DESC')->find();
    }

    private function inputValue($name){
        return trim((string)I('post.'.$name, ''));
    }

    private function viewData($data){
        if (!is_array($data)) {
            return is_string($data) ? htmlspecialchars_decode($data, ENT_QUOTES) : $data;
        }
        foreach ($data as $key => $value) {
            $data[$key] = $this->viewData($value);
        }
        return $data;
    }

    private function cleanFileName($name){
        $name = preg_replace('/[\\\\\\/\x00-\x1F]+/', '_', basename((string)$name));
        return mb_substr($name, 0, 120, 'UTF-8');
    }

    private function uploadApplicationFile(){
        if (!isset($_FILES['application_file']) || $_FILES['application_file']['error'] === UPLOAD_ERR_NO_FILE) {
            return array('success'=>false, 'message'=>'请上传填写完成的报名表格。');
        }
        $file = $_FILES['application_file'];
        if ($file['error'] !== UPLOAD_ERR_OK || !$file['tmp_name'] || !is_uploaded_file($file['tmp_name'])) {
            return array('success'=>false, 'message'=>'报名表格上传失败，请重新选择文件。');
        }
        if (intval($file['size']) <= 0 || intval($file['size']) > 10 * 1024 * 1024) {
            return array('success'=>false, 'message'=>'报名表格需小于 10MB。');
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, array('doc', 'docx'), true)) {
            return array('success'=>false, 'message'=>'仅支持 DOC 或 DOCX Word 文件。');
        }
        $mime = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
            if ($finfo) {
                finfo_close($finfo);
            }
        }
        $allowedMimes = array(
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip', 'application/x-zip-compressed', 'application/CDFV2',
            'application/x-ole-storage'
        );
        if ($mime && !in_array($mime, $allowedMimes, true)) {
            return array('success'=>false, 'message'=>'报名表格格式校验未通过。');
        }
        if (!ca_workstudy_valid_word_file($file['tmp_name'], $extension)) {
            return array('success'=>false, 'message'=>'报名表格文件内容校验未通过。');
        }
        $subDir = date('Ymd') . '/';
        $targetDir = $this->storageRoot . $subDir;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0750, true)) {
            return array('success'=>false, 'message'=>'报名表格存储目录不可用，请稍后重试。');
        }
        $storedName = sha1(uniqid('', true) . mt_rand()) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $targetDir . $storedName)) {
            return array('success'=>false, 'message'=>'报名表格保存失败，请稍后重试。');
        }
        chmod($targetDir . $storedName, 0640);
        return array(
            'success'=>true,
            'file_path'=>$subDir . $storedName,
            'file_name'=>$this->cleanFileName($file['name'])
        );
    }

    private function sendFile($application){
        $storedPath = (string)$application['file_path'];
        if (!preg_match('#^[0-9]{8}/[a-f0-9]{40}\.(doc|docx)$#', $storedPath)) {
            $this->error('报名表格路径无效。');
        }
        $path = $this->storageRoot . $storedPath;
        if (!is_file($path) || !is_readable($path)) {
            $this->error('报名表格文件不存在或暂不可下载。');
        }
        $downloadName = $this->cleanFileName($application['file_name']);
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($downloadName));
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        readfile($path);
        exit;
    }

    private function projectTemplate($project){
        if (empty($project['template_id'])) { return null; }
        return M('work_study_template')->where(array('tid'=>(int)$project['template_id'], 'status'=>1))->find();
    }

    private function sendTemplateFile($template){
        $storedPath = (string)$template['file_path'];
        if (!preg_match('#^[0-9]{8}/[a-f0-9]{40}\.(doc|docx)$#', $storedPath)) {
            $this->error('模板路径无效。');
        }
        $path = $this->templateRoot . $storedPath;
        if (!is_file($path) || !is_readable($path)) {
            $this->error('模板文件不存在或暂不可下载。');
        }
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($this->cleanFileName($template['file_name'])));
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        readfile($path);
        exit;
    }

    public function index(){
        $projects = $this->projects();
        $projects = is_array($projects) ? $projects : array();
        $openCount = 0;
        foreach ($projects as &$project) {
            $project['has_application'] = (bool)$this->submittedApplication($project['wid']);
            $project['has_template'] = (bool)$this->projectTemplate($project);
            if ((int)$project['status'] === 1) { $openCount++; }
        }
        unset($project);
        $this->assign('projects', $this->viewData($projects));
        $this->assign('open_count', $openCount);
        $this->display();
    }

    public function form(){
        $project = $this->viewData($this->requireActiveProject(intval(I('get.wid'))));
        if (!$this->projectTemplate($project)) { $this->error('该项目暂未配置报名模板，请联系管理员。', U('/Appointment/Workstudy')); }
        $user = $this->viewData($this->user());
        if (!$user) {
            $this->error('未找到当前用户资料，请重新登录后再试。');
        }
        $application = $this->viewData($this->submittedApplication($project['wid']));
        $form = array(
            'username'=>$user['username'],
            'truename'=>isset($application['truename']) ? $application['truename'] : $user['truename'],
            'major_id'=>isset($application['major_id']) ? $application['major_id'] : $user['major'],
            'class'=>isset($application['class']) ? $application['class'] : '',
            'political_status'=>isset($application['political_status']) ? $application['political_status'] : $user['political_status'],
            'qq'=>isset($application['qq']) ? $application['qq'] : $user['qq'],
            'email'=>isset($application['email']) ? $application['email'] : $user['email'],
            'mobile'=>isset($application['mobile']) ? $application['mobile'] : $user['mobile'],
            'available_time'=>isset($application['available_time']) ? $application['available_time'] : '',
            'work_experience'=>isset($application['work_experience']) ? $application['work_experience'] : '',
            'statement'=>isset($application['statement']) ? $application['statement'] : ''
        );
        $this->assign('project', $project);
        $this->assign('application', $application);
        $this->assign('form', $form);
        $majors = $this->viewData(M('common_majors')->where(array('status'=>'1'))->order('institute ASC, mname ASC')->select());
        $majors = is_array($majors) ? $majors : array();
        $hasExistingMajor = false;
        foreach ($majors as $major) {
            if ($application && (int)$major['mid'] === (int)$application['major_id']) { $hasExistingMajor = true; }
        }
        if ($application && (int)$application['major_id'] > 0 && !$hasExistingMajor) {
            $majors[] = array('mid'=>(int)$application['major_id'], 'institute'=>$application['college'], 'mname'=>$application['major'], 'historical'=>1);
        }
        $this->assign('majors', $majors);
        $this->assign('political_statuses', ca_political_status_options());
        $this->assign('csrf_token', $this->csrfToken());
        $this->display();
    }

    public function save(){
        if (!IS_POST) {
            $this->error('请求无效。');
        }
        $this->checkCsrf();
        $wid = intval(I('post.wid'));
        if ($wid <= 0) { $this->error('缺少项目编号，请从项目列表重新进入。', U('/Appointment/Workstudy')); }
        $project = $this->activeProject($wid);
        if (!$project) {
            $this->error('该项目已结束，不能新增或修改报名。', U('/Appointment/Workstudy'));
        }
        $user = $this->user();
        if (!$user) {
            $this->error('未找到当前用户资料，请重新登录后再试。');
        }
        $truename = $this->inputValue('truename');
        $class = $this->inputValue('class');
        $mobile = $this->inputValue('mobile');
        $qq = $this->inputValue('qq');
        $email = $this->inputValue('email');
        $politicalStatus = $this->inputValue('political_status');
        $availableTime = $this->inputValue('available_time');
        $workExperience = $this->inputValue('work_experience');
        $statement = $this->inputValue('statement');
        $majorId = intval(I('post.major_id'));
        $existing = $this->submittedApplication($project['wid']);
        $major = $this->applicationMajor($majorId, $existing);
        if ($existing) {
            if ($availableTime === '' && isset($existing['available_time'])) {
                $availableTime = (string)$existing['available_time'];
            }
            if ($workExperience === '' && isset($existing['work_experience'])) {
                $workExperience = (string)$existing['work_experience'];
            }
            if ($statement === '' && isset($existing['statement'])) {
                $statement = (string)$existing['statement'];
            }
        }
        if (!$truename || !$class || !$mobile || !$qq || !$email || !$major) {
            $this->error('请完整填写基本资料并选择有效专业。');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9+\-()\s]{6,24}$/', $mobile)) {
            $this->error('请填写有效的邮箱和联系电话。');
        }
        if (!in_array($politicalStatus, ca_political_status_options(), true)) {
            $this->error('请选择有效的政治面貌。');
        }
        if (mb_strlen($statement, 'UTF-8') > 1000 || mb_strlen($workExperience, 'UTF-8') > 1000 || mb_strlen($availableTime, 'UTF-8') > 255) {
            $this->error('填写内容过长，请适当精简后再提交。');
        }
        $model = M('work_study_application');
        $hasNewFile = isset($_FILES['application_file']) && $_FILES['application_file']['error'] !== UPLOAD_ERR_NO_FILE;
        $uploaded = null;
        if (!$existing || $hasNewFile) {
            $uploaded = $this->uploadApplicationFile();
            if (!$uploaded['success']) {
                $this->error($uploaded['message']);
            }
        }
        $data = array(
            'uid'=>(int)session('uid'),
            'wid'=>(int)$project['wid'],
            'username'=>$user['username'],
            'truename'=>$truename,
            'college'=>(string)$major['institute'],
            'class'=>$class,
            'major_id'=>$majorId,
            'major'=>(string)$major['mname'],
            'political_status'=>$politicalStatus,
            'qq'=>$qq,
            'email'=>$email,
            'mobile'=>$mobile,
            'work_experience'=>$workExperience,
            'available_time'=>$availableTime,
            'statement'=>$statement,
            'reg_ip'=>(string)get_client_ip(),
            'update_time'=>date('Y-m-d H:i:s')
        );
        if ($uploaded) {
            $data['file_path'] = $uploaded['file_path'];
            $data['file_name'] = $uploaded['file_name'];
        }
        $model->startTrans();
        $lockedProject = M('work_study_grade')->where(array('wid'=>$wid))->lock(true)->find();
        if (!$lockedProject || (int)$lockedProject['status'] !== 1) {
            $model->rollback();
            if ($uploaded) { @unlink($this->storageRoot . $uploaded['file_path']); }
            $this->error('该项目已结束，不能新增或修改报名。', U('/Appointment/Workstudy'));
        }
        $existing = $model->where(array('wid'=>$wid,'uid'=>(int)session('uid')))->lock(true)->find();
        $major = $this->applicationMajor($majorId, $existing);
        if (!$major || (!$existing && !$uploaded)) {
            $model->rollback();
            if ($uploaded) { @unlink($this->storageRoot . $uploaded['file_path']); }
            $this->error('报名记录或专业已变更，请刷新页面后重新提交。');
        }
        $data['college'] = (string)$major['institute'];
        $data['major'] = (string)$major['mname'];
        if ($existing) {
            $result = $model->where(array('aid'=>(int)$existing['aid'], 'uid'=>(int)session('uid')))->save($data);
        } else {
            $data['addtime'] = date('Y-m-d H:i:s');
            $data['status'] = 0;
            $result = $model->add($data);
        }
        if ($result === false) {
            $model->rollback();
            if ($uploaded) {
                @unlink($this->storageRoot . $uploaded['file_path']);
            }
            $this->error('保存失败，请稍后重试。');
        }
        $model->commit();
        $message = trim((string)$lockedProject['message']);
        $this->success($message !== '' ? $message : '报名表格已提交。', U('/Appointment/Workstudy/view', array('wid'=>$project['wid'])));
    }

    public function view(){
        $wid = intval(I('get.wid'));
        $application = $wid ? $this->submittedApplication($wid) : $this->latestApplication();
        if (!$application) {
            $this->error('暂未找到你的勤工助学报名记录。', U('/Appointment/Workstudy'));
        }
        $project = $this->project($application['wid']);
        if (!$project) {
            $this->error('未找到该报名所属项目。', U('/Appointment/Workstudy'));
        }
        $this->assign('project', $this->viewData($project));
        $this->assign('application', $this->viewData($application));
        $this->display();
    }

    public function download(){
        $wid = intval(I('get.wid'));
        $application = $wid ? $this->submittedApplication($wid) : $this->latestApplication();
        if (!$application) {
            $this->error('无权下载此报名表格。');
        }
        $this->sendFile($application);
    }

    public function templateDownload(){
        $project = $this->project(intval(I('get.wid')));
        if (!$project || ((int)$project['status'] === 3 && !$this->submittedApplication($project['wid']))) {
            $this->error('该项目暂不可访问。', U('/Appointment/Workstudy'));
        }
        $template = $this->projectTemplate($project);
        if (!$template) {
            $this->error('报名表模板暂不可用，请联系管理员。');
        }
        $this->sendTemplateFile($template);
    }
}
