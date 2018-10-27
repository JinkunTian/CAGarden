

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
  `model` varchar(255) NOT NULL COMMENT '电脑型号',
  `issues` text NOT NULL COMMENT '故障问题',
  `addtime` datetime NOT NULL COMMENT '创建时间',
  `edittime` datetime NOT NULL COMMENT '修改时间',
  `status` int(1) NOT NULL COMMENT '预约状态',
  `result` text NOT NULL COMMENT '维修结果',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='计算机协会义修记录表';

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='计算机协会义修评价表';

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='计算机协会义修用户表';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='部门信息';

-- ----------------------------
-- Records of pt_common_departments
-- ----------------------------
INSERT INTO `pt_common_departments` VALUES ('1', '技术部', '', '', '1');
INSERT INTO `pt_common_departments` VALUES ('2', '项目部', '', '', '1');
INSERT INTO `pt_common_departments` VALUES ('3', '文宣部', '', '', '1');
INSERT INTO `pt_common_departments` VALUES ('4', '组织部', '', '', '1');
INSERT INTO `pt_common_departments` VALUES ('5', '办公室', '', '', '1');
INSERT INTO `pt_common_departments` VALUES ('6', '主席团', '', '', '2');
INSERT INTO `pt_common_departments` VALUES ('7', '已退会', '', '', '4');
INSERT INTO `pt_common_departments` VALUES ('8', '老干部', '', '', '6');


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='专业信息';

-- ----------------------------
-- Records of pt_common_majors
-- ----------------------------
INSERT INTO `pt_common_majors` VALUES ('1', '软件工程', '电气信息学院', '1');
INSERT INTO `pt_common_majors` VALUES ('2', '计算机科学与技术', '电气信息学院', '1');
INSERT INTO `pt_common_majors` VALUES ('3', '物联网工程', '电气信息学院', '1');
INSERT INTO `pt_common_majors` VALUES ('4', '电子信息工程', '电气信息学院', '1');
INSERT INTO `pt_common_majors` VALUES ('5', '电气工程及其自动化', '电气信息学院', '1');
INSERT INTO `pt_common_majors` VALUES ('6', '机械设计制造及其自动化', '电气信息学院', '1');
INSERT INTO `pt_common_majors` VALUES ('7', '机械电子工程', '电气信息学院', '1');
INSERT INTO `pt_common_majors` VALUES ('8', '网络与新媒体', '艺术设计学院', '1');
INSERT INTO `pt_common_majors` VALUES ('9', '视觉传达设计', '艺术设计学院', '1');
INSERT INTO `pt_common_majors` VALUES ('10', '艺术设计', '艺术设计学院', '1');
INSERT INTO `pt_common_majors` VALUES ('11', '环境设计', '艺术设计学院', '1');
INSERT INTO `pt_common_majors` VALUES ('12', '土木工程', '土木工程学院', '1');
INSERT INTO `pt_common_majors` VALUES ('13', '建筑环境与能源应用工程', '土木工程学院', '1');
INSERT INTO `pt_common_majors` VALUES ('14', '给排水科学与工程', '土木工程学院', '1');
INSERT INTO `pt_common_majors` VALUES ('15', '建筑工程技术', '土木工程学院', '1');
INSERT INTO `pt_common_majors` VALUES ('16', '工程造价', '建筑管理学院', '1');
INSERT INTO `pt_common_majors` VALUES ('17', '工程管理', '建筑管理学院', '1');
INSERT INTO `pt_common_majors` VALUES ('18', '城乡规划', '建筑学院', '1');
INSERT INTO `pt_common_majors` VALUES ('19', '建筑学', '建筑学院', '1');
INSERT INTO `pt_common_majors` VALUES ('20', '风景园林', '建筑学院', '1');
INSERT INTO `pt_common_majors` VALUES ('21', '会计学', '经济管理学院', '1');
INSERT INTO `pt_common_majors` VALUES ('22', '工商管理', '经济管理学院', '1');
INSERT INTO `pt_common_majors` VALUES ('23', '市场营销', '经济管理学院', '1');
INSERT INTO `pt_common_majors` VALUES ('24', '人力资源管理', '经济管理学院', '1');
INSERT INTO `pt_common_majors` VALUES ('25', '国际经济与贸易', '经济管理学院', '1');
INSERT INTO `pt_common_majors` VALUES ('26', '法学', '人文学院·基础部', '1');
INSERT INTO `pt_common_majors` VALUES ('29', '其他', '', '1');
INSERT INTO `pt_common_majors` VALUES ('30', '教师', '', '1');
INSERT INTO `pt_common_majors` VALUES ('31', '数据科学与大数据技术', '电气信息学院', '1');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pt_garden_projects
-- ----------------------------
INSERT INTO `pt_garden_projects` VALUES ('1', '0', '', '计算机协会', '2018-08-01 22:29:00', '0', '::', '::', '欢迎使用CAGarden！', '欢迎使用CAGarden！CAGarden为一款计算机社团设计的专用网站程序，是在ProjectTree项目的基础上添加了电脑义修预约功能以及社团纳新功能，不仅方便的实现了记录社团项目的运行情况，更方便每届的新成员加入社团了解社团和老成员交接已有项目给新成员！', '1');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '用户状态[1正常/0禁用]',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pt_garden_users
-- ----------------------------
INSERT INTO `pt_garden_users` VALUES ('1', 'admin', 'admin', 'd74a1e9c94e0cdf5839216b41bfaa8eb', '20c0425278b2892c6a30550905b5432f', '/images/img.jpg', '2018-08-22 16:21:52', '192.168.16.8', '2018-05-12 14:28:04', '10.2.24.167', '2961165914', '13088886666', 'admin@domain.com', '1', '3', '部长', '一二三四五，上山打老虎！', '2', '1');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

