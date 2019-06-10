SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pt_appointment
-- ----------------------------
DROP TABLE IF EXISTS `pt_appointment`;
CREATE TABLE `pt_appointment` (
  `aid` int(10) NOT NULL AUTO_INCREMENT COMMENT '预约ID',
  `guest_id` int(10) NOT NULL COMMENT '预约用户ID',
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='计算机协会义修记录表';

-- ----------------------------
-- Table structure for pt_appointment_comment
-- ----------------------------
DROP TABLE IF EXISTS `pt_appointment_comment`;
CREATE TABLE `pt_appointment_comment` (
  `cid` int(10) NOT NULL AUTO_INCREMENT COMMENT '评论ID',
  `aid` int(10) NOT NULL COMMENT '所属预约ID',
  `comment` text NOT NULL COMMENT '评价',
  `guest_id` int(10) NOT NULL COMMENT '预约用户ID',
  `addtime` datetime NOT NULL COMMENT '创建时间',
  `fixer_id` int(10) NOT NULL COMMENT '对应维修用户ID',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='计算机协会义修评价表';

-- ----------------------------
-- Table structure for pt_appointment_users
-- ----------------------------
DROP TABLE IF EXISTS `pt_appointment_users`;
CREATE TABLE `pt_appointment_users` (
  `guest_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '预约用户ID',
  `number` varchar(11) NOT NULL COMMENT '学号/教职工号',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `salt` varchar(32) NOT NULL COMMENT '盐值加密',
  `truename` varchar(20) NOT NULL COMMENT '姓名',
  `major` int(11) NOT NULL COMMENT '专业代码',
  `room` varchar(255) NOT NULL COMMENT '寝室，校区信息，需要时取消相关代码即可',
  `qq` varchar(15) NOT NULL COMMENT 'qq号码',
  `email` varchar(255) NOT NULL COMMENT '电子邮箱',
  `mobile` varchar(11) NOT NULL COMMENT '电话',
  `reg_ip` varchar(64) NOT NULL COMMENT '注册ip，兼容ipv6但未测试',
  `addtime` datetime NOT NULL,
  `last_ip` varchar(64) NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`guest_id`),
  UNIQUE KEY `number` (`number`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='计算机协会义修用户表';

-- ----------------------------
-- Table structure for pt_common_count
-- ----------------------------
DROP TABLE IF EXISTS `pt_common_count`;
CREATE TABLE `pt_common_count` (
  `site_id` int(11) NOT NULL,
  `visit_count` int(11) NOT NULL COMMENT '访问量',
  `fix_count` int(11) NOT NULL COMMENT '维修量',
  `members_count` int(11) NOT NULL COMMENT '成员量',
  `comment_count` int(11) NOT NULL COMMENT '总评论量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='部门信息';

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='专业信息';

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_garden_comment
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_comment`;
CREATE TABLE `pt_garden_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) unsigned NOT NULL,
  `uid` int(8) unsigned NOT NULL,
  `content` text,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退会申请表';

-- ----------------------------
-- Table structure for pt_garden_succeed
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_succeed`;
CREATE TABLE `pt_garden_succeed` (
  `succeed_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `username` varchar(255) NOT NULL COMMENT '学号',
  `truename` varchar(255) NOT NULL COMMENT '姓名',
  `succeed_info` text NOT NULL COMMENT '退会原因',
  `status` int(10) NOT NULL DEFAULT '1' COMMENT '审核状态',
  `addtime` datetime NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`succeed_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='退会申请表';

-- ----------------------------
-- Table structure for pt_garden_users
-- ----------------------------
DROP TABLE IF EXISTS `pt_garden_users`;
CREATE TABLE `pt_garden_users` (
  `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(255) NOT NULL COMMENT '工号/用户名',
  `truename` varchar(30) NOT NULL COMMENT '真实姓名',
  `password` varchar(256) NOT NULL COMMENT '密码',
  `salt` varchar(256) NOT NULL COMMENT '盐值加密',
  `img` varchar(255) NOT NULL DEFAULT '/images/img.jpg' COMMENT '用户头像',
  `last_login` datetime DEFAULT NULL COMMENT '最后一次登录时间',
  `last_ip` varchar(64) DEFAULT NULL COMMENT '最后一次登录IP',
  `addtime` datetime NOT NULL,
  `reg_ip` varchar(64) DEFAULT NULL COMMENT '注册IP',
  `qq` varchar(20) DEFAULT NULL COMMENT 'QQ',
  `mobile` varchar(20) DEFAULT NULL COMMENT '电话',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `dep` int(10) DEFAULT NULL COMMENT '部门代码',
  `major` int(10) DEFAULT NULL COMMENT '专业代码',
  `position` varchar(255) DEFAULT NULL COMMENT '职位',
  `flag` text COMMENT '个人签名',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '用户类型[1用户/2管理员]',
  `reward_sum` int(11) NOT NULL DEFAULT '0' COMMENT '总积分',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '用户状态[1正常/0禁用]',
  `status_info` varchar(255) DEFAULT NULL COMMENT '状态说明',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_recruit
-- ----------------------------
DROP TABLE IF EXISTS `pt_recruit`;
CREATE TABLE `pt_recruit` (
  `recruit_id` int(10) NOT NULL AUTO_INCREMENT,
  `number` int(10) NOT NULL COMMENT '学号',
  `truename` varchar(255) NOT NULL COMMENT '真实姓名',
  `college` varchar(255) NOT NULL COMMENT '学院信息',
  `password` varchar(256) NOT NULL COMMENT '密码',
  `salt` varchar(256) NOT NULL COMMENT '盐值加密',
  `img` varchar(255) DEFAULT '/images/hi.png' COMMENT '照片',
  `qq` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `major` varchar(255) NOT NULL COMMENT '专业',
  `class` int(11) NOT NULL COMMENT '班级',
  `dep` int(11) NOT NULL COMMENT '部门',
  `flag` text NOT NULL COMMENT '签名',
  `github` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `info` text NOT NULL COMMENT '介绍',
  `grade` varchar(255) NOT NULL COMMENT '纳新年度',
  `reg_ip` varchar(255) NOT NULL COMMENT '注册ip',
  `addtime` datetime NOT NULL,
  `status` int(11) NOT NULL COMMENT '纳新状态[0待纳/1已纳]',
  PRIMARY KEY (`recruit_id`),
  UNIQUE KEY `number` (`number`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_recruit_comment
-- ----------------------------
DROP TABLE IF EXISTS `pt_recruit_comment`;
CREATE TABLE `pt_recruit_comment` (
  `cid` int(10) NOT NULL AUTO_INCREMENT,
  `recruit_id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `content` text NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pt_system_logs
-- ----------------------------
DROP TABLE IF EXISTS `pt_system_logs`;
CREATE TABLE `pt_system_logs` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `part` int(11) NOT NULL COMMENT '板块',
  `uid` int(11) NOT NULL,
  `truename` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ip` varchar(128) NOT NULL,
  `agent` text NOT NULL,
  `url` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`lid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
