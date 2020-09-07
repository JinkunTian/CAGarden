/*
CAGarden Basic Install Database Structure

CAGarden Version : 0.0.1

Target Server Type    : MYSQL

Date: 2020-09-06 16:01:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pt_appointment
-- ----------------------------
DROP TABLE IF EXISTS `pt_appointment`;
CREATE TABLE `pt_appointment` (
  `aid` int(10) NOT NULL AUTO_INCREMENT COMMENT '预约ID',
  `uid` varchar(255) NOT NULL COMMENT '预约用户ID',
  `fixer_id` int(10) DEFAULT NULL COMMENT '最终维修者ID',
  `fixer2_id` int(10) DEFAULT NULL COMMENT '维修ID',
  `fixer3_id` int(10) DEFAULT NULL COMMENT '维修ID',
  `fixer4_id` int(10) DEFAULT NULL COMMENT '维修ID',
  `brand` varchar(255) NOT NULL COMMENT '电脑品牌',
  `model` varchar(255) NOT NULL COMMENT '电脑型号',
  `issues` text NOT NULL COMMENT '故障问题',
  `addtime` datetime NOT NULL COMMENT '创建时间',
  `edittime` datetime NOT NULL COMMENT '修改时间',
  `status` int(1) NOT NULL COMMENT '预约状态',
  `result` text NOT NULL COMMENT '维修结果',
  `reward` varchar(255) NOT NULL DEFAULT '未奖励' COMMENT '奖励状态',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='计算机协会义修记录表';

-- ----------------------------
-- Table structure for pt_appointment_comment
-- ----------------------------
DROP TABLE IF EXISTS `pt_appointment_comment`;
CREATE TABLE `pt_appointment_comment` (
  `cid` int(10) NOT NULL AUTO_INCREMENT COMMENT '评论ID',
  `aid` int(10) NOT NULL COMMENT '所属预约ID',
  `comment` text NOT NULL COMMENT '评价',
  `uid` int(10) NOT NULL COMMENT '预约用户ID',
  `addtime` datetime NOT NULL COMMENT '创建时间',
  `fixer_id` int(10) NOT NULL COMMENT '对应维修用户ID',
  PRIMARY KEY (`cid`,`aid`),
  UNIQUE KEY `aid` (`aid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='计算机协会义修评价表';

-- ----------------------------
-- Table structure for pt_common_departments
-- ----------------------------
DROP TABLE IF EXISTS `pt_common_departments`;
CREATE TABLE `pt_common_departments` (
  `did` int(10) NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `dname` varchar(255) NOT NULL COMMENT '部门名称',
  `dep_brief` text NOT NULL COMMENT '部门简介',
  `dep_reqire` text NOT NULL COMMENT '部门要求',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`did`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='部门信息';

-- ----------------------------
-- Table structure for pt_common_institutes
-- ----------------------------
DROP TABLE IF EXISTS `pt_common_institutes`;
CREATE TABLE `pt_common_institutes` (
  `institute_id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_name` varchar(255) DEFAULT NULL COMMENT '学院名称',
  PRIMARY KEY (`institute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_common_majors
-- ----------------------------
DROP TABLE IF EXISTS `pt_common_majors`;
CREATE TABLE `pt_common_majors` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `mname` varchar(256) NOT NULL COMMENT '专业名称',
  `institute` varchar(255) NOT NULL COMMENT '所属学院',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='专业信息';

-- ----------------------------
-- Table structure for pt_garden_blackboard
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_blackboard`;
CREATE TABLE `pt_garden_blackboard` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '公告ID',
  `title` varchar(100) NOT NULL COMMENT '公告标题',
  `content` text NOT NULL COMMENT '公告内容',
  `author_id` int(10) NOT NULL COMMENT '作者ID',
  `visits` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '查看量',
  `addtime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='小黑板';

-- ----------------------------
-- Table structure for pt_garden_comment
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_comment`;
CREATE TABLE `pt_garden_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论ID',
  `bid` int(10) unsigned NOT NULL COMMENT '对应公告ID',
  `uid` int(8) unsigned NOT NULL COMMENT '用户ID',
  `content` text COMMENT '评论内容',
  `addtime` datetime NOT NULL COMMENT '评论添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='小黑板评论';

-- ----------------------------
-- Table structure for pt_garden_logs
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_logs`;
CREATE TABLE `pt_garden_logs` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `log_prid` int(10) NOT NULL COMMENT '所属项目ID',
  `log_cuser` int(10) NOT NULL COMMENT '日志创建者',
  `log_ctime` datetime NOT NULL COMMENT '日志创建时间',
  `log_info` text NOT NULL COMMENT '日志内容',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态[1正常/2删除]',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='后花园日志';

-- ----------------------------
-- Table structure for pt_garden_msg
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_msg`;
CREATE TABLE `pt_garden_msg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(8) unsigned NOT NULL,
  `content` text,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='碎语板块';

-- ----------------------------
-- Table structure for pt_garden_personal_password
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_personal_password`;
CREATE TABLE `pt_garden_personal_password` (
  `pw_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '密码单元ID',
  `pw_cuser` int(11) NOT NULL COMMENT '创建者ID',
  `pw_ctime` int(12) NOT NULL COMMENT '密码创建时间',
  `uid` int(12) NOT NULL COMMENT '所属用户ID',
  `pw_name` varchar(255) NOT NULL COMMENT '密码名称',
  `username` varchar(256) NOT NULL COMMENT '用户名',
  `password` text NOT NULL COMMENT '密码',
  `note` text COMMENT '安全注释',
  `type` int(11) NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '密码状态',
  PRIMARY KEY (`pw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_garden_personal_password_addition
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_personal_password_addition`;
CREATE TABLE `pt_garden_personal_password_addition` (
  `addition_password_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附加密码ID',
  `original_password_id` int(10) NOT NULL COMMENT '所属源密码ID',
  `key_name` varchar(255) NOT NULL COMMENT '附加密码名称',
  `key_value` text NOT NULL COMMENT '附加密码内容',
  `is_secret` int(11) DEFAULT '0' COMMENT '是否为敏感信息',
  `key_classify` int(11) NOT NULL DEFAULT '1' COMMENT '附加密码分类',
  PRIMARY KEY (`addition_password_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_garden_projects
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_projects`;
CREATE TABLE `pt_garden_projects` (
  `pr_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '项目ID',
  `pr_pid` int(10) NOT NULL COMMENT '项目父ID',
  `pr_dir` varchar(255) NOT NULL COMMENT '项目路径',
  `pr_name` varchar(255) NOT NULL DEFAULT '新建项目' COMMENT '项目名称',
  `pr_ctime` datetime NOT NULL COMMENT '项目创建时间',
  `pr_cuser` int(10) NOT NULL COMMENT '项目创建者',
  `pr_muser` text NOT NULL COMMENT '项目管理者',
  `pr_members` text COMMENT '项目成员',
  `pr_brief` varchar(255) NOT NULL COMMENT '项目简要',
  `pr_info` text NOT NULL COMMENT '项目详细介绍',
  `pr_status` int(1) NOT NULL COMMENT '项目状态（1正常，2待审核，3停用））',
  PRIMARY KEY (`pr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_garden_public_password
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_public_password`;
CREATE TABLE `pt_garden_public_password` (
  `pw_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '密码单元ID',
  `pw_prid` int(11) NOT NULL COMMENT '所属项目ID',
  `pw_cuser` int(11) NOT NULL COMMENT '创建者ID',
  `pw_ctime` datetime NOT NULL COMMENT '密码创建时间',
  `pw_muser` text NOT NULL COMMENT '管理者ID',
  `pw_name` varchar(255) NOT NULL COMMENT '密码名称',
  `pw_brief` varchar(255) NOT NULL COMMENT '密码摘要【公开显示】',
  `username` varchar(256) NOT NULL COMMENT '用户名',
  `password` text NOT NULL COMMENT '密码',
  `note` text COMMENT '安全注释',
  `pw_right` text COMMENT '密码权限',
  `open_access` tinyint(1) NOT NULL DEFAULT '0' COMMENT '公开密码',
  `group_members_access` tinyint(1) NOT NULL DEFAULT '0' COMMENT '允许密码所在项目组成员查看密码',
  `project_mamager_permit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '允许密码所在项目组管理员管理密码',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '密码状态',
  PRIMARY KEY (`pw_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_garden_public_password_addition
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_public_password_addition`;
CREATE TABLE `pt_garden_public_password_addition` (
  `addition_password_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附加密码ID',
  `original_password_id` int(10) NOT NULL COMMENT '所属源密码ID',
  `key_name` varchar(255) NOT NULL COMMENT '附加密码名称',
  `key_value` text NOT NULL COMMENT '附加密码内容',
  `is_secret` int(11) DEFAULT '0' COMMENT '是否为敏感信息',
  `key_classify` int(11) NOT NULL DEFAULT '1' COMMENT '附加密码分类',
  PRIMARY KEY (`addition_password_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_garden_reward_log
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_reward_log`;
CREATE TABLE `pt_garden_reward_log` (
  `reward_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `truename` varchar(255) NOT NULL,
  `before_add` int(11) DEFAULT NULL,
  `reward_count` int(11) NOT NULL,
  `after_add` int(11) DEFAULT NULL,
  `reward_reason` text NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`reward_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='奖励记录';

-- ----------------------------
-- Table structure for pt_garden_secede
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_secede`;
CREATE TABLE `pt_garden_secede` (
  `secede_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `username` varchar(255) NOT NULL COMMENT '学号',
  `truename` varchar(255) NOT NULL COMMENT '姓名',
  `secede_info` text NOT NULL COMMENT '退会原因',
  `status` int(10) NOT NULL DEFAULT '1' COMMENT '审核状态',
  `addtime` datetime NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`secede_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='退会申请表';

-- ----------------------------
-- Table structure for pt_garden_succeed
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_succeed`;
CREATE TABLE `pt_garden_succeed` (
  `succeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `username` varchar(255) NOT NULL COMMENT '学号',
  `truename` varchar(255) NOT NULL COMMENT '姓名',
  `type` varchar(255) NOT NULL,
  `succeed_info` text NOT NULL COMMENT '退会原因',
  `status` int(10) NOT NULL DEFAULT '1' COMMENT '审核状态',
  `addtime` datetime NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`succeed_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='退会申请表';

-- ----------------------------
-- Table structure for pt_garden_users_extend
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_users_extend`;
CREATE TABLE `pt_garden_users_extend` (
  `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(255) NOT NULL COMMENT '工号/用户名',
  `dep` int(10) DEFAULT NULL COMMENT '部门代码',
  `position` varchar(255) DEFAULT NULL COMMENT '职位',
  `flag` text COMMENT '个人签名',
  `is_admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '用户类型[1用户/2管理员]',
  `reward_sum` int(11) NOT NULL DEFAULT '0' COMMENT '总积分',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '用户状态[1正常/0禁用]',
  `status_info` varchar(255) DEFAULT NULL COMMENT '状态说明',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='协会用户补充信息';

-- ----------------------------
-- Table structure for pt_index_config
-- ----------------------------
DROP TABLE IF EXISTS `pt_index_config`;
CREATE TABLE `pt_index_config` (
  `cid` int(11) NOT NULL AUTO_INCREMENT COMMENT '网站首页配置信息',
  `config_name` varchar(255) NOT NULL,
  `config_commit` varchar(255) DEFAULT NULL,
  `config_key` varchar(255) DEFAULT NULL,
  `config_value_1` varchar(255) DEFAULT NULL,
  `config_value_2` varchar(255) DEFAULT NULL,
  `config_value_3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_index_feedback
-- ----------------------------
DROP TABLE IF EXISTS `pt_index_feedback`;
CREATE TABLE `pt_index_feedback` (
  `fb_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`fb_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_index_news
-- ----------------------------
DROP TABLE IF EXISTS `pt_index_news`;
CREATE TABLE `pt_index_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '新闻ID',
  `title` varchar(100) NOT NULL COMMENT '新闻标题',
  `picture` varchar(255) DEFAULT NULL COMMENT '首页展示图片',
  `content` text NOT NULL COMMENT '新闻内容',
  `author_id` int(10) NOT NULL COMMENT '作者ID',
  `author_name` varchar(255) DEFAULT NULL COMMENT '作者姓名',
  `visits` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '查看量',
  `addtime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_recruit
-- ----------------------------
DROP TABLE IF EXISTS `pt_recruit`;
CREATE TABLE `pt_recruit` (
  `rid` int(255) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `dep` varchar(255) DEFAULT NULL,
  `flag` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL,
  `website` text,
  `info` text,
  `apply` varchar(500) DEFAULT NULL,
  `grade` varchar(255) DEFAULT NULL,
  `reg_ip` varchar(255) DEFAULT NULL,
  `addtime` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='协会纳新登记';

-- ----------------------------
-- Table structure for pt_recruit_comment
-- ----------------------------
DROP TABLE IF EXISTS `pt_recruit_comment`;
CREATE TABLE `pt_recruit_comment` (
  `cid` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `content` text NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='纳新面试印象';

-- ----------------------------
-- Table structure for pt_recruit_grade
-- ----------------------------
DROP TABLE IF EXISTS `pt_recruit_grade`;
CREATE TABLE `pt_recruit_grade` (
  `gid` int(10) NOT NULL AUTO_INCREMENT,
  `gname` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '2' COMMENT '纳新状态1开放报名/2禁止报名/3开放面试/4纳新结束',
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='纳新年度记录';

-- ----------------------------
-- Table structure for pt_system_access_logs
-- ----------------------------
DROP TABLE IF EXISTS `pt_system_access_logs`;
CREATE TABLE `pt_system_access_logs` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `part` varchar(20) DEFAULT NULL COMMENT '板块',
  `uid` int(11) DEFAULT NULL,
  `truename` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  `url` text,
  `date` datetime DEFAULT NULL,
  `agent` text,
  PRIMARY KEY (`lid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统运行日志';

-- ----------------------------
-- Table structure for pt_system_login_logs
-- ----------------------------
DROP TABLE IF EXISTS `pt_system_login_logs`;
CREATE TABLE `pt_system_login_logs` (
  `lid` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `certify` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `result` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户登陆日志';

-- ----------------------------
-- Table structure for pt_users
-- ----------------------------
DROP TABLE IF EXISTS `pt_users`;
CREATE TABLE `pt_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `truename` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT '' COMMENT '/Public/images/hi.png',
  `college` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `major` varchar(255) DEFAULT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `reg_ip` varchar(255) NOT NULL,
  `addtime` datetime NOT NULL,
  `last_ip` varchar(255) NOT NULL,
  `last_login` datetime NOT NULL,
  `certify` varchar(255) DEFAULT NULL,
  `userType` varchar(255) NOT NULL,
  `FINDPASS_HASH` varchar(255) DEFAULT NULL,
  `FINDPASS_TIME` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_wifi
-- ----------------------------
DROP TABLE IF EXISTS `pt_wifi`;
CREATE TABLE `pt_wifi` (
  `au_id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `truename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`au_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='iKuai WiFi接入认证日志';

-- ----------------------------
-- View structure for pt_appointment_view
-- ----------------------------
DROP VIEW IF EXISTS `pt_appointment_view`;
CREATE VIEW `pt_appointment_view` AS select `pt_appointment`.`aid` AS `aid`,`pt_appointment`.`uid` AS `uid`,`pt_appointment`.`fixer_id` AS `fixer_id`,`pt_appointment`.`fixer2_id` AS `fixer2_id`,`pt_appointment`.`fixer3_id` AS `fixer3_id`,`pt_appointment`.`fixer4_id` AS `fixer4_id`,`pt_appointment`.`brand` AS `brand`,`pt_appointment`.`model` AS `model`,`pt_appointment`.`issues` AS `issues`,`pt_appointment`.`addtime` AS `addtime`,`pt_appointment`.`edittime` AS `edittime`,`pt_appointment`.`status` AS `status`,`pt_appointment`.`result` AS `result`,`pt_appointment`.`reward` AS `reward`,`guest_user`.`truename` AS `guest_name`,`fix_user`.`truename` AS `fixer_name`,`pt_common_majors`.`mname` AS `guest_major`,`guest_user`.`username` AS `guest_username`,`guest_user`.`qq` AS `guest_qq`,`guest_user`.`email` AS `guest_email`,`guest_user`.`mobile` AS `guest_mobile`,`pt_appointment_comment`.`comment` AS `comment`,`pt_appointment_comment`.`addtime` AS `comment_addtime` from ((((`pt_appointment` left join `pt_users` `guest_user` on((`guest_user`.`uid` = `pt_appointment`.`uid`))) left join `pt_users` `fix_user` on((`fix_user`.`uid` = `pt_appointment`.`fixer_id`))) left join `pt_common_majors` on((`pt_common_majors`.`mid` = `guest_user`.`major`))) left join `pt_appointment_comment` on((`pt_appointment_comment`.`aid` = `pt_appointment`.`aid`))) ;

-- ----------------------------
-- View structure for pt_garden_user_view
-- ----------------------------
DROP VIEW IF EXISTS `pt_garden_user_view`;
CREATE VIEW `pt_garden_user_view` AS select `pt_users`.`username` AS `username`,`pt_users`.`truename` AS `truename`,`pt_users`.`password` AS `password`,`pt_users`.`salt` AS `salt`,`pt_users`.`major` AS `major`,`pt_users`.`qq` AS `qq`,`pt_users`.`email` AS `email`,`pt_users`.`mobile` AS `mobile`,`pt_users`.`reg_ip` AS `reg_ip`,`pt_users`.`addtime` AS `addtime`,`pt_users`.`last_ip` AS `last_ip`,`pt_users`.`last_login` AS `last_login`,`pt_users`.`userType` AS `userType`,`pt_garden_users_extend`.`dep` AS `dep`,`pt_garden_users_extend`.`position` AS `position`,`pt_garden_users_extend`.`flag` AS `flag`,`pt_garden_users_extend`.`type` AS `type`,`pt_garden_users_extend`.`reward_sum` AS `reward_sum`,`pt_garden_users_extend`.`status` AS `status`,`pt_garden_users_extend`.`status_info` AS `status_info`,`pt_common_departments`.`dname` AS `dname`,`pt_common_majors`.`mname` AS `mname`,`pt_garden_users_extend`.`uid` AS `uid`,`pt_users`.`college` AS `college`,`pt_users`.`img` AS `img`,`pt_users`.`class` AS `class`,`pt_garden_users_extend`.`is_admin` AS `is_admin` from (((`pt_garden_users_extend` left join `pt_users` on((`pt_users`.`uid` = `pt_garden_users_extend`.`uid`))) left join `pt_common_departments` on((`pt_common_departments`.`did` = `pt_garden_users_extend`.`dep`))) left join `pt_common_majors` on((`pt_common_majors`.`mid` = `pt_users`.`major`))) ;

-- ----------------------------
-- View structure for pt_recruit_comment_count_view
-- ----------------------------
DROP VIEW IF EXISTS `pt_recruit_comment_count_view`;
CREATE VIEW `pt_recruit_comment_count_view` AS select `pt_recruit_comment`.`cid` AS `cid`,`pt_recruit_comment`.`rid` AS `rid`,`pt_recruit_comment`.`uid` AS `uid`,`pt_recruit_comment`.`content` AS `content`,`pt_recruit_comment`.`addtime` AS `addtime`,count(0) AS `sum_count` from `pt_recruit_comment` group by `pt_recruit_comment`.`rid` ;

-- ----------------------------
-- View structure for pt_recruit_comment_view
-- ----------------------------
DROP VIEW IF EXISTS `pt_recruit_comment_view`;
CREATE VIEW `pt_recruit_comment_view` AS select `pt_recruit_comment`.`cid` AS `cid`,`pt_recruit_comment`.`rid` AS `rid`,`pt_recruit_comment`.`content` AS `content`,`pt_recruit`.`dep` AS `dep`,`pt_recruit`.`info` AS `info`,`pt_recruit`.`apply` AS `apply`,`pt_recruit`.`uid` AS `ruid`,`pt_users`.`truename` AS `truename`,`pt_users`.`username` AS `username`,`pt_users`.`college` AS `college`,`pt_common_departments`.`dname` AS `dname`,`pt_recruit_comment`.`addtime` AS `addtime`,`pt_recruit`.`grade` AS `grade`,`pt_recruit`.`status` AS `status` from (((`pt_recruit_comment` left join `pt_recruit` on((`pt_recruit`.`rid` = `pt_recruit_comment`.`rid`))) left join `pt_users` on((`pt_users`.`uid` = `pt_recruit`.`uid`))) left join `pt_common_departments` on((`pt_common_departments`.`did` = `pt_recruit`.`dep`))) ;

-- ----------------------------
-- View structure for pt_recruit_view
-- ----------------------------
DROP VIEW IF EXISTS `pt_recruit_view`;
CREATE VIEW `pt_recruit_view` AS select `pt_recruit`.`rid` AS `rid`,`pt_recruit`.`uid` AS `uid`,`pt_recruit`.`username` AS `username`,`pt_recruit`.`dep` AS `dep`,`pt_recruit`.`flag` AS `flag`,`pt_recruit`.`github` AS `github`,`pt_recruit`.`website` AS `website`,`pt_recruit`.`info` AS `info`,`pt_recruit`.`grade` AS `grade`,`pt_recruit`.`addtime` AS `addtime`,`pt_recruit`.`status` AS `status`,`pt_users`.`truename` AS `truename`,`pt_users`.`class` AS `class`,`pt_users`.`password` AS `password`,`pt_users`.`salt` AS `salt`,`pt_users`.`major` AS `major`,`pt_users`.`qq` AS `qq`,`pt_users`.`email` AS `email`,`pt_users`.`mobile` AS `mobile`,`pt_users`.`reg_ip` AS `reg_ip`,`pt_users`.`last_ip` AS `last_ip`,`pt_users`.`last_login` AS `last_login`,`pt_users`.`certify` AS `certify`,`pt_users`.`userType` AS `userType`,`pt_common_majors`.`mname` AS `mname`,`pt_common_departments`.`dname` AS `dname`,`pt_recruit_grade`.`gid` AS `gid`,`pt_recruit_grade`.`gname` AS `gname`,`pt_recruit_grade`.`year` AS `year`,`pt_recruit_grade`.`status` AS `grade_status`,`pt_users`.`img` AS `img`,`pt_users`.`college` AS `college`,`pt_recruit`.`apply` AS `apply`,`pt_recruit_comment_count_view`.`sum_count` AS `comment_count` from (((((`pt_recruit` left join `pt_users` on((`pt_recruit`.`uid` = `pt_users`.`uid`))) left join `pt_recruit_grade` on((`pt_recruit_grade`.`gid` = `pt_recruit`.`grade`))) left join `pt_common_majors` on((`pt_common_majors`.`mid` = `pt_users`.`major`))) left join `pt_common_departments` on((`pt_common_departments`.`did` = `pt_recruit`.`dep`))) left join `pt_recruit_comment_count_view` on((`pt_recruit_comment_count_view`.`rid` = `pt_recruit`.`rid`))) ;
