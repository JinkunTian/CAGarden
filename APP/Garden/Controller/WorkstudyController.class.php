<?php
namespace Garden\Controller;

class WorkstudyController extends AdminController {
    private $storageRoot = '/www/secure/workstudy-uploads/';
    private $templateRoot = '/www/secure/workstudy-templates/';

    public function _initialize(){
        if (!session('admin')) {
            session('req_url', $_SERVER['REQUEST_URI']);
            $this->redirect('/Authentication');
        }
    }

    private function csrfToken(){
        $token = session('workstudy_admin_csrf_token');
        if (!$token) {
            $token = bin2hex(openssl_random_pseudo_bytes(24));
            session('workstudy_admin_csrf_token', $token);
        }
        return $token;
    }

    private function checkCsrf(){
        $token = (string)I('post.csrf_token');
        if (!$token || !hash_equals((string)session('workstudy_admin_csrf_token'), $token)) {
            $this->error('页面已失效，请刷新后重新提交。');
        }
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

    private function uploadDocument($field, $root){
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return array('success'=>false, 'message'=>'请上传 Word 模板文件。');
        }
        $file = $_FILES[$field];
        if ($file['error'] !== UPLOAD_ERR_OK || !$file['tmp_name'] || !is_uploaded_file($file['tmp_name'])) {
            return array('success'=>false, 'message'=>'文件上传失败，请重新选择。');
        }
        if (intval($file['size']) <= 0 || intval($file['size']) > 10 * 1024 * 1024) {
            return array('success'=>false, 'message'=>'Word 模板需小于 10MB。');
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, array('doc', 'docx'), true)) {
            return array('success'=>false, 'message'=>'模板仅支持 DOC 或 DOCX 文件。');
        }
        if (!ca_workstudy_valid_word_file($file['tmp_name'], $extension)) {
            return array('success'=>false, 'message'=>'模板文件内容校验未通过。');
        }
        $subDir = date('Ymd') . '/';
        $targetDir = $root . $subDir;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0750, true)) {
            return array('success'=>false, 'message'=>'模板存储目录不可用，请稍后重试。');
        }
        $storedName = sha1(uniqid('', true) . mt_rand()) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $targetDir . $storedName)) {
            return array('success'=>false, 'message'=>'模板保存失败，请稍后重试。');
        }
        chmod($targetDir . $storedName, 0640);
        return array('success'=>true, 'file_path'=>$subDir . $storedName, 'file_name'=>$this->cleanFileName($file['name']));
    }

    private function sendStoredFile($root, $storedPath, $downloadName){
        if (!preg_match('#^[0-9]{8}/[a-f0-9]{40}\.(doc|docx)$#', (string)$storedPath)) {
            $this->error('文件路径无效。');
        }
        $path = $root . $storedPath;
        if (!is_file($path) || !is_readable($path)) {
            $this->error('文件不存在或暂不可下载。');
        }
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($this->cleanFileName($downloadName)));
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        readfile($path);
        exit;
    }

    private function currentTemplate($tid=0){
        $model = M('work_study_template')->where(array('status'=>1));
        return $tid > 0 ? $model->where(array('tid'=>(int)$tid,'status'=>1))->find() : $model->order('tid DESC')->find();
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
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($this->cleanFileName($application['file_name'])));
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        readfile($path);
        exit;
    }

    // Older filter links omitted the index action. Keep saved links usable.
    public function filter(){
        $filter = 'current';
        $path = (string)parse_url((string)$_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('#(?:^|/)Workstudy/filter/(current|archived|all)(?:\.html)?/?$#i', $path, $matches)) {
            $filter = strtolower($matches[1]);
        }
        $this->redirect('/Garden/Workstudy/index', array('filter'=>$filter));
    }

    public function index(){
        $filter = (string)I('get.filter', 'current');
        if (!in_array($filter,array('current','archived','all'),true)) { $filter = 'current'; }
        $where = $filter === 'all' ? array() : array('status'=>$filter === 'archived' ? 3 : array('in',array(1,2)));
        $projects = M('work_study_grade')->where($where)->order('status ASC, addtime DESC, wid DESC')->select();
        if (!is_array($projects)) {
            $projects = array();
        }
        foreach ($projects as &$project) {
            $project['application_count'] = (int)M('work_study_application')->where(array('wid'=>(int)$project['wid']))->count();
            $project['template'] = $this->currentTemplate((int)$project['template_id']);
        }
        unset($project);
        $this->assign('projects', $this->viewData($projects));
        $this->assign('filter', $filter);
        $this->assign('archived_count', (int)M('work_study_grade')->where(array('status'=>3))->count());
        $this->assign('csrf_token', $this->csrfToken());
        $this->assign('template', $this->viewData($this->currentTemplate()));
        $this->display();
    }

    public function edit(){
        $project = array('wname'=>'','year'=>'','description'=>'','message'=>'','status'=>2,'template_id'=>0);
        $wid = intval(I('wid'));
        if ($wid) {
            $project = M('work_study_grade')->where(array('wid'=>$wid))->find();
            if (!$project) {
                $this->error('未找到该勤工助学项目。');
            }
        } else {
            $template = $this->currentTemplate();
            $project['template_id'] = $template ? (int)$template['tid'] : 0;
        }
        $this->assign('project', $this->viewData($project));
        $this->assign('is_new', !$wid);
        $this->assign('templates', $this->viewData(M('work_study_template')->where(array('status'=>1))->order('tid DESC')->select()));
        $this->assign('csrf_token', $this->csrfToken());
        $this->display();
    }

    public function save(){
        if (!IS_POST) {
            $this->error('请求无效。');
        }
        $this->checkCsrf();
        $wid = intval(I('post.wid'));
        $wname = trim((string)I('post.wname'));
        $year = trim((string)I('post.year'));
        $message = trim((string)I('post.message'));
        $description = trim((string)I('post.description'));
        $templateId = intval(I('post.template_id'));
        $status = intval(I('post.status'));
        if (!$wname || !$year || !in_array($status, array(1, 2, 3), true)) {
            $this->error('请完整填写项目名称、年度和申请状态。');
        }
        if (mb_strlen($wname, 'UTF-8') > 100 || mb_strlen($year, 'UTF-8') > 32 || mb_strlen($message, 'UTF-8') > 1000 || mb_strlen($description, 'UTF-8') > 2000) {
            $this->error('填写内容过长，请适当精简后再保存。');
        }
        $template = $templateId > 0 ? $this->currentTemplate($templateId) : null;
        if (!$template || !preg_match('#^[0-9]{8}/[a-f0-9]{40}\.(doc|docx)$#',(string)$template['file_path']) || !is_readable($this->templateRoot . $template['file_path'])) {
            $this->error('请选择可下载的报名模板；如暂无模板，请先上传到模板库。');
        }
        $data = array(
            'wname'=>$wname,
            'year'=>$year,
            'message'=>$message,
            'description'=>$description,
            'template_id'=>$templateId,
            'status'=>$status,
            'update_time'=>date('Y-m-d H:i:s')
        );
        if ($wid) {
            if (!M('work_study_grade')->where(array('wid'=>$wid))->find()) {
                $this->error('未找到该勤工助学项目。');
            }
            $result = M('work_study_grade')->where(array('wid'=>$wid))->save($data);
        } else {
            $data['addtime'] = date('Y-m-d H:i:s');
            $result = M('work_study_grade')->add($data);
        }
        if ($result === false) {
            $this->error('保存失败，请稍后重试。');
        }
        $this->success('勤工助学项目已保存。', U('/Garden/Workstudy'));
    }

    private function changeArchiveState($archive){
        if (!IS_POST) { $this->error('请求无效。'); }
        $this->checkCsrf();
        $wid = intval(I('post.wid'));
        $model = M('work_study_grade');
        $project = $wid > 0 ? $model->where(array('wid'=>$wid))->find() : null;
        if (!$project) { $this->error('未找到该勤工助学项目。'); }
        if (!$archive && (int)$project['status'] !== 3) { $this->error('该项目未归档。'); }
        $result = $model->where(array('wid'=>$wid))->save(array('status'=>$archive ? 3 : 2,'update_time'=>date('Y-m-d H:i:s')));
        if ($result === false) { $this->error('保存失败，请稍后重试。'); }
        $this->success($archive ? '项目已归档，报名记录和文件已保留。' : '项目已恢复，当前为已结束状态；开放报名请编辑项目。', U('/Garden/Workstudy/index',array('filter'=>$archive ? 'archived' : 'current')));
    }

    public function archiveProject(){ $this->changeArchiveState(true); }
    public function restoreProject(){ $this->changeArchiveState(false); }

    public function deleteProject(){
        if (!IS_POST) {
            $this->error('请求无效。');
        }
        $this->checkCsrf();
        $wid = intval(I('post.wid'));
        if (!$wid) {
            $this->error('缺少项目编号。');
        }

        $projectModel = M('work_study_grade');
        $applicationModel = M('work_study_application');
        $projectModel->startTrans();
        $project = $projectModel->where(array('wid'=>$wid))->lock(true)->find();
        if (!$project) {
            $projectModel->rollback();
            $this->error('未找到该勤工助学项目。');
        }
        $applicationCount = (int)$applicationModel->where(array('wid'=>$wid))->count();
        if ($applicationCount > 0) {
            $projectModel->rollback();
            $this->error('该项目已有报名记录，请使用归档保留记录和文件。');
        }
        $deleted = $projectModel->where(array('wid'=>$wid))->delete();
        if ($deleted === false || (int)$deleted !== 1) {
            $projectModel->rollback();
            $this->error('删除失败，请稍后重试。');
        }
        $projectModel->commit();
        $this->success('勤工助学项目已删除。', U('/Garden/Workstudy'));
    }

    public function templateManage(){
        $wid = intval(I('get.wid'));
        $project = $wid > 0 ? M('work_study_grade')->where(array('wid'=>$wid))->find() : null;
        if ($wid > 0 && !$project) { $this->error('未找到该勤工助学项目。'); }
        $this->assign('project', $this->viewData($project));
        $this->assign('templates', $this->viewData(M('work_study_template')->where(array('status'=>1))->order('tid DESC')->select()));
        $this->assign('template', $this->viewData($project ? $this->currentTemplate((int)$project['template_id']) : $this->currentTemplate()));
        $this->assign('csrf_token', $this->csrfToken());
        $this->display('template');
    }

    public function templateUpload(){
        if (!IS_POST) {
            $this->error('请求无效。');
        }
        $this->checkCsrf();
        $wid = intval(I('post.wid'));
        $project = $wid > 0 ? M('work_study_grade')->where(array('wid'=>$wid))->find() : null;
        if ($wid > 0 && !$project) { $this->error('未找到该勤工助学项目。'); }
        $label = trim((string)I('post.version_label'));
        if (mb_strlen($label,'UTF-8') > 100) { $this->error('版本说明不能超过 100 字。'); }
        $word = $this->uploadDocument('template_word', $this->templateRoot);
        if (!$word['success']) {
            $this->error($word['message']);
        }
        $model = M('work_study_template');
        $model->startTrans();
        $result = $model->add(array(
            'version_label'=>$label,
            'file_path'=>$word['file_path'],
            'file_name'=>$word['file_name'],
            'addtime'=>date('Y-m-d H:i:s'),
            'status'=>1
        ));
        if ($result !== false && $project) {
            $bound = M('work_study_grade')->where(array('wid'=>$wid))->save(array('template_id'=>(int)$result,'update_time'=>date('Y-m-d H:i:s')));
            if ($bound === false) { $result = false; }
        }
        if ($result === false) {
            $model->rollback();
            @unlink($this->templateRoot . $word['file_path']);
            $this->error('模板保存失败，请稍后重试。');
        }
        $model->commit();
        $this->success($project ? '该项目模板已更新，其他项目不受影响。' : '新版本已加入模板库，请在项目编辑页选择使用。', U('/Garden/Workstudy/templateManage', $project ? array('wid'=>$wid) : array()));
    }

    public function templateDownload(){
        $tid = intval(I('get.tid'));
        $template = $this->currentTemplate($tid);
        if (!$template) {
            $this->error('当前模板不存在。');
        }
        $this->sendStoredFile($this->templateRoot, $template['file_path'], $template['file_name']);
    }

    public function applications(){
        $wid = intval(I('wid'));
        if (!$wid) {
            $project = M('work_study_grade')->order('wid DESC')->find();
            $wid = $project ? intval($project['wid']) : 0;
        } else {
            $project = M('work_study_grade')->where(array('wid'=>$wid))->find();
        }
        if (!$project) {
            $this->error('暂未创建勤工助学项目。', U('/Garden/Workstudy/edit'));
        }
        $query = mb_substr(trim((string)I('get.q','')),0,100,'UTF-8');
        // Pagination carries the original input, not the HTML-filtered database query.
        $paginationQuery = mb_substr(trim((string)I('get.q','','')),0,100,'UTF-8');
        $where = array('wid'=>$wid);
        if ($query !== '') {
            $pattern = '%' . addcslashes($query, '\\%_') . '%';
            $where['_complex'] = array('username'=>array('like',$pattern),'truename'=>array('like',$pattern),'college'=>array('like',$pattern),'major'=>array('like',$pattern),'class'=>array('like',$pattern),'_logic'=>'OR');
        }
        $total = (int)M('work_study_application')->where($where)->count();
        $pageCount = max(1,(int)ceil($total / 20));
        $page = max(1,min($pageCount,intval(I('get.p',1))));
        $applications = M('work_study_application')->where($where)->order('addtime DESC, aid DESC')->limit(($page-1)*20,20)->select();
        $this->assign('query', $this->viewData($query));
        $this->assign('total', $total);
        $this->assign('page', $page);
        $this->assign('page_count', $pageCount);
        $this->assign('previous_url', U('/Garden/Workstudy/applications',array('wid'=>$wid,'q'=>$paginationQuery,'p'=>max(1,$page-1))));
        $this->assign('next_url', U('/Garden/Workstudy/applications',array('wid'=>$wid,'q'=>$paginationQuery,'p'=>min($pageCount,$page+1))));
        $this->assign('project', $this->viewData($project));
        $this->assign('applications', $this->viewData($applications));
        $this->assign('csrf_token', $this->csrfToken());
        $this->display();
    }

    public function view(){
        $application = M('work_study_application')->where(array('aid'=>intval(I('aid'))))->find();
        if (!$application) {
            $this->error('未找到该报名记录。');
        }
        $project = M('work_study_grade')->where(array('wid'=>(int)$application['wid']))->find();
        $this->assign('project', $this->viewData($project));
        $this->assign('application', $this->viewData($application));
        $this->display();
    }

    public function download(){
        $application = M('work_study_application')->where(array('aid'=>intval(I('aid'))))->find();
        if (!$application) {
            $this->error('未找到该报名记录。');
        }
        $this->sendFile($application);
    }

    public function deleteApplications(){
        if (!IS_POST) {
            $this->error('请求无效。');
        }
        $this->checkCsrf();
        $wid = intval(I('post.wid'));
        $postedIds = I('post.aids');
        if (!$wid || !is_array($postedIds)) {
            $this->error('请选择要删除的报名记录。');
        }
        $aidMap = array();
        foreach ($postedIds as $aid) {
            $aid = intval($aid);
            if ($aid > 0) {
                $aidMap[$aid] = $aid;
            }
        }
        $aidList = array_values($aidMap);
        if (!$aidList) {
            $this->error('请选择要删除的报名记录。');
        }
        if (!M('work_study_grade')->where(array('wid'=>$wid))->find()) {
            $this->error('未找到该勤工助学项目。');
        }
        $model = M('work_study_application');
        $where = array('wid'=>$wid, 'aid'=>array('in', $aidList));
        $applications = $model->where($where)->field('aid,file_path')->select();
        if (!$applications) {
            $this->error('没有找到可删除的报名记录。');
        }
        $model->startTrans();
        $deleted = $model->where($where)->delete();
        if ($deleted === false) {
            $model->rollback();
            $this->error('删除失败，请稍后重试。');
        }
        $model->commit();
        foreach ($applications as $application) {
            $relative = (string)$application['file_path'];
            if (preg_match('#^[0-9]{8}/[a-f0-9]{40}\.(doc|docx)$#', $relative)) {
                $path = $this->storageRoot . $relative;
                if (is_file($path)) {
                    @unlink($path);
                }
            }
        }
        $this->success('已删除 ' . intval($deleted) . ' 条报名记录。', U('/Garden/Workstudy/applications', array('wid'=>$wid)));
    }

    public function downloadAll(){
        $wid = intval(I('wid'));
        if (!$wid) {
            $this->error('缺少项目编号。');
        }
        $project = M('work_study_grade')->where(array('wid'=>$wid))->find();
        if (!$project) {
            $this->error('未找到该勤工助学项目。');
        }
        $applications = M('work_study_application')->where(array('wid'=>$wid))->order('aid ASC')->select();
        $zipPath = tempnam('/tmp', 'work-study-') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->error('压缩包创建失败。');
        }
        $added = 0;
        foreach ((array)$applications as $application) {
            $relative = (string)$application['file_path'];
            if (!preg_match('#^[0-9]{8}/[a-f0-9]{40}\.(doc|docx)$#', $relative)) {
                continue;
            }
            $path = $this->storageRoot . $relative;
            if (!is_file($path) || !is_readable($path)) {
                continue;
            }
            $name = $this->cleanFileName($application['username'] . '-' . $application['truename'] . '-' . $application['file_name']);
            $zip->addFile($path, $name ?: ('application-' . $application['aid'] . '.docx'));
            $added++;
        }
        $zip->close();
        if (!$added) {
            @unlink($zipPath);
            $this->error('当前项目没有可下载的 Word 报名表。');
        }
        $downloadName = $this->cleanFileName($project['wname'] . '-Word报名表.zip');
        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($downloadName));
        header('Content-Length: ' . filesize($zipPath));
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        readfile($zipPath);
        @unlink($zipPath);
        exit;
    }
}
