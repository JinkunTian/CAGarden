<?php
/** Read-only public homepage fields; no parameters, authentication or writes. */
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD') {
    header('Allow: GET, HEAD');
    http_response_code(405);
    echo json_encode(array('error'=>'只读接口'));
    exit;
}
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $config = require __DIR__.'/APP/Common/Conf/config.php';
    $prefix = isset($config['DB_PREFIX']) ? $config['DB_PREFIX'] : 'pt_';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $prefix)) throw new Exception('Unavailable');
    $db = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PWD'], $config['DB_NAME'],
                     isset($config['DB_PORT']) ? (int)$config['DB_PORT'] : 3306);
    $db->set_charset('utf8');
    $rows = $db->query("SELECT cid, config_value_1, config_value_2, config_value_3 FROM `".$prefix.
                      "index_config` WHERE config_name='association_album' ORDER BY config_key+0, cid");
    $items = array();
    while ($row = $rows->fetch_assoc()) {
        $items[] = array('cid'=>(int)$row['cid'], 'title'=>(string)$row['config_value_1'],
                         'image_url'=>(string)$row['config_value_2'], 'album_url'=>(string)$row['config_value_3']);
    }
    echo json_encode(array('source'=>'main-site', 'items'=>$items), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $db->close();
} catch (Exception $error) {
    http_response_code(503);
    echo json_encode(array('error'=>'主页引用暂不可用'), JSON_UNESCAPED_UNICODE);
}
