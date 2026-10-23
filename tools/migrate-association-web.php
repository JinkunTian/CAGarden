<?php
/** Additive schema requirements for the association website; CLI only. */
function associationWebValidateEngines(array $engines)
{
    if (!isset($engines['index_config'])) {
        throw new RuntimeException('Run the base CAGarden installer first: missing index_config.');
    }
    foreach (array('index_config', 'work_study_grade', 'work_study_application', 'work_study_template') as $table) {
        if (isset($engines[$table]) && strtoupper($engines[$table]) !== 'INNODB') {
            throw new RuntimeException('InnoDB is required for transaction and project-lock safety.');
        }
    }
}

function associationWebSchemaPlan($prefix, array $existing)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/D', $prefix)) {
        throw new InvalidArgumentException('Invalid table prefix.');
    }
    $extensions = array(
        'users' => array(
            'political_status' => "varchar(32) NOT NULL DEFAULT '群众'",
            'is_work_study' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
        ),
        'recruit' => array(
            'political_status' => "varchar(32) NOT NULL DEFAULT '群众'",
            'is_work_study' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
        ),
        'common_departments' => array(
            'recruit_visible' => "tinyint(1) unsigned NOT NULL DEFAULT '1'",
        ),
        'common_institutes' => array(
            'status' => "tinyint(1) unsigned NOT NULL DEFAULT '1'",
        ),
    );
    $newTables = array(
        'work_study_grade' => "(
            wid int(11) NOT NULL AUTO_INCREMENT,
            wname varchar(100) NOT NULL,
            year varchar(32) NOT NULL,
            description text NOT NULL,
            message text NOT NULL,
            template_id int(11) NOT NULL DEFAULT 0,
            status tinyint(1) NOT NULL DEFAULT 2,
            addtime datetime NOT NULL,
            update_time datetime NOT NULL,
            PRIMARY KEY (wid), KEY idx_status_wid (status,wid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
        'work_study_application' => "(
            aid int(11) NOT NULL AUTO_INCREMENT,
            uid int(11) NOT NULL,
            wid int(11) NOT NULL,
            username varchar(64) NOT NULL,
            truename varchar(50) NOT NULL,
            college varchar(255) NOT NULL,
            class varchar(100) NOT NULL,
            major_id int(11) NOT NULL,
            major varchar(255) NOT NULL,
            political_status varchar(32) NOT NULL,
            qq varchar(30) NOT NULL,
            email varchar(120) NOT NULL,
            mobile varchar(24) NOT NULL,
            work_experience text NOT NULL,
            available_time varchar(255) NOT NULL,
            statement text NOT NULL,
            file_path varchar(255) NOT NULL,
            file_name varchar(120) NOT NULL,
            reg_ip varchar(64) NOT NULL,
            addtime datetime NOT NULL,
            update_time datetime NOT NULL,
            status tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (aid), UNIQUE KEY uniq_work_study_user (uid,wid),
            KEY idx_work_study_time (wid,addtime)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
        'work_study_template' => "(
            tid int(11) NOT NULL AUTO_INCREMENT,
            version_label varchar(100) NOT NULL DEFAULT '',
            file_path varchar(255) NOT NULL,
            file_name varchar(120) NOT NULL,
            addtime datetime NOT NULL,
            status tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (tid), KEY idx_status_tid (status,tid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
    );
    $plan = array();
    foreach ($extensions as $table => $fields) {
        if (!isset($existing[$table])) {
            throw new RuntimeException('Run the base CAGarden installer first: missing ' . $table);
        }
        foreach ($fields as $field => $definition) {
            if (!in_array($field, $existing[$table], true)) {
                $plan[] = 'ALTER TABLE `' . $prefix . $table . '` ADD COLUMN `' . $field . '` ' . $definition;
            }
        }
    }
    foreach ($newTables as $table => $definition) {
        if (!isset($existing[$table])) {
            $plan[] = 'CREATE TABLE IF NOT EXISTS `' . $prefix . $table . '` ' . $definition;
        }
    }
    if (isset($existing['work_study_grade'])) {
        foreach (array('description' => 'text NOT NULL', 'template_id' => 'int(11) NOT NULL DEFAULT 0') as $field => $definition) {
            if (!in_array($field, $existing['work_study_grade'], true)) {
                $plan[] = 'ALTER TABLE `' . $prefix . 'work_study_grade` ADD COLUMN `' . $field . '` ' . $definition;
            }
        }
    }
    return $plan;
}

// Including this file for its plan builder never connects to a database.
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') !== __FILE__) {
    return;
}
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
$mode = isset($argv[1]) ? $argv[1] : '--check';
if ($mode === '--help') {
    echo "Usage: php tools/migrate-association-web.php [--check|--apply]\n";
    echo "Default --check is read-only. --apply adds missing tables/columns only.\n";
    exit;
}
if (count($argv) > 2 || !in_array($mode, array('--check', '--apply'), true)) {
    fwrite(STDERR, "Use --check or --apply; see --help.\n");
    exit(1);
}
ini_set('display_errors', '0');
try {
    $configPath = dirname(__DIR__) . '/APP/Common/Conf/config.php';
    if (!is_file($configPath)) {
        throw new RuntimeException('Local database configuration is required.');
    }
    $config = require $configPath;
    $prefix = isset($config['DB_PREFIX']) ? $config['DB_PREFIX'] : 'pt_';
    if (!preg_match('/^[A-Za-z0-9_]+$/D', $prefix)) {
        throw new RuntimeException('Invalid table prefix.');
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PWD'], $config['DB_NAME'],
        empty($config['DB_PORT']) ? 3306 : (int)$config['DB_PORT']);
    $db->set_charset('utf8');
    $statement = $db->prepare('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?');
    $existing = array();
    foreach (array('users', 'recruit', 'common_departments', 'common_institutes', 'work_study_grade', 'work_study_application', 'work_study_template') as $table) {
        $name = $prefix . $table;
        $statement->bind_param('s', $name);
        $statement->execute();
        $statement->bind_result($column);
        $columns = array();
        while ($statement->fetch()) {
            $columns[] = $column;
        }
        if ($columns) {
            $existing[$table] = $columns;
        }
    }
    $statement->close();
    $statement = $db->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?');
    $engines = array();
    foreach (array('index_config', 'work_study_grade', 'work_study_application', 'work_study_template') as $table) {
        $name = $prefix . $table;
        $statement->bind_param('s', $name);
        $statement->execute();
        $statement->bind_result($engine);
        while ($statement->fetch()) {
            $engines[$table] = $engine;
        }
    }
    $statement->close();
    associationWebValidateEngines($engines);
    $plan = associationWebSchemaPlan($prefix, $existing);
    foreach ($plan as $sql) {
        if ($mode === '--apply') {
            $db->query($sql);
        }
    }
    echo json_encode(array('mode' => $mode, 'statement_count' => count($plan), 'schema_only' => true,
        'statements' => $plan), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    $db->close();
} catch (Exception $error) {
    // Database diagnostics can contain connection details; do not print them.
    fwrite(STDERR, "Schema check/apply failed; verify local configuration, base tables and InnoDB engines.\n");
    exit(1);
}
