SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `pt_blackboard` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text,
  `uid` int(8) UNSIGNED NOT NULL,
  `author` varchar(10) NOT NULL,
  `visits` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_comment`
--

CREATE TABLE `pt_comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `bid` int(10) UNSIGNED NOT NULL,
  `uid` int(8) UNSIGNED NOT NULL,
  `content` text,
  `time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `del` tinyint(1) UNSIGNED DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_logs`
--

CREATE TABLE `pt_logs` (
  `log_id` int(10) NOT NULL COMMENT '日志ID',
  `log_prid` int(10) NOT NULL COMMENT '所属项目ID',
  `log_cuser` int(10) NOT NULL COMMENT '日志创建者',
  `log_ctime` int(12) NOT NULL COMMENT '日志创建时间',
  `log_info` text NOT NULL COMMENT '日志内容',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_msg`
--

CREATE TABLE `pt_msg` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(8) UNSIGNED NOT NULL,
  `content` text,
  `time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_personal_password`
--

CREATE TABLE `pt_personal_password` (
  `pw_id` int(11) NOT NULL COMMENT '密码单元ID',
  `pw_cuser` int(11) NOT NULL COMMENT '创建者ID',
  `pw_ctime` int(12) NOT NULL COMMENT '密码创建时间',
  `uid` int(12) NOT NULL COMMENT '所属用户ID',
  `pw_name` varchar(255) NOT NULL COMMENT '密码名称',
  `username` varchar(256) NOT NULL COMMENT '用户名',
  `password` text NOT NULL COMMENT '密码',
  `note` text COMMENT '安全注释',
  `type` int(11) NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '密码状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_personal_password_addition`
--

CREATE TABLE `pt_personal_password_addition` (
  `addition_password_id` int(10) NOT NULL COMMENT '附加密码ID',
  `original_password_id` int(10) NOT NULL COMMENT '所属源密码ID',
  `key_name` varchar(255) NOT NULL COMMENT '附加密码名称',
  `key_value` text NOT NULL COMMENT '附加密码内容',
  `is_secret` int(11) DEFAULT '0' COMMENT '是否为敏感信息',
  `key_classify` int(11) NOT NULL DEFAULT '1' COMMENT '附加密码分类'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_projects`
--

CREATE TABLE `pt_projects` (
  `pr_id` int(10) NOT NULL COMMENT '项目ID',
  `pr_pid` int(10) NOT NULL COMMENT '项目父ID',
  `pr_dir` varchar(255) NOT NULL COMMENT '项目路径',
  `pr_name` varchar(255) NOT NULL DEFAULT '新建项目' COMMENT '项目名称',
  `pr_ctime` int(12) NOT NULL COMMENT '项目创建时间',
  `pr_cuser` int(10) NOT NULL COMMENT '项目创建者',
  `pr_muser` text NOT NULL COMMENT '项目管理者',
  `pr_members` text COMMENT '项目成员',
  `pr_biref` varchar(255) NOT NULL COMMENT '项目简要',
  `pr_info` text NOT NULL COMMENT '项目详细介绍',
  `pr_status` int(11) NOT NULL COMMENT '项目状态（1正常，2待审核，3停用））'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pt_projects`
--

INSERT INTO `pt_projects` (`pr_id`, `pr_pid`, `pr_dir`, `pr_name`, `pr_ctime`, `pr_cuser`, `pr_muser`, `pr_members`, `pr_biref`, `pr_info`, `pr_status`) VALUES
(1, 0, '/', 'ProjectTree', 1533133794, 1, ':1:', '', 'ProjectTree默认主项目', 'ProjectTree默认主项目', 1);

-- --------------------------------------------------------

--
-- 表的结构 `pt_public_password`
--

CREATE TABLE `pt_public_password` (
  `pw_id` int(11) NOT NULL COMMENT '密码单元ID',
  `pw_prid` int(11) NOT NULL COMMENT '所属项目ID',
  `pw_cuser` int(11) NOT NULL COMMENT '创建者ID',
  `pw_ctime` int(12) NOT NULL COMMENT '密码创建时间',
  `pw_muser` text NOT NULL COMMENT '管理者ID',
  `pw_name` varchar(255) NOT NULL COMMENT '密码名称',
  `username` varchar(256) NOT NULL COMMENT '用户名',
  `password` text NOT NULL COMMENT '密码',
  `note` text COMMENT '安全注释',
  `pw_right` text COMMENT '密码权限',
  `group_members_access` int(1) NOT NULL DEFAULT '0' COMMENT '允许密码所在项目组成员查看密码',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '密码状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_public_password_addition`
--

CREATE TABLE `pt_public_password_addition` (
  `addition_password_id` int(10) NOT NULL COMMENT '附加密码ID',
  `original_password_id` int(10) NOT NULL COMMENT '所属源密码ID',
  `key_name` varchar(255) NOT NULL COMMENT '附加密码名称',
  `key_value` text NOT NULL COMMENT '附加密码内容',
  `is_secret` int(11) DEFAULT '0' COMMENT '是否为敏感信息',
  `key_classify` int(11) NOT NULL DEFAULT '1' COMMENT '附加密码分类'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pt_users`
--

CREATE TABLE `pt_users` (
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `username` varchar(255) NOT NULL COMMENT '工号/用户名',
  `truename` varchar(30) NOT NULL COMMENT '真实姓名',
  `password` varchar(256) NOT NULL COMMENT '密码',
  `salt` varchar(256) NOT NULL COMMENT '盐值加密',
  `img` varchar(255) NOT NULL DEFAULT '/images/img.jpg' COMMENT '用户头像',
  `last_login` int(20)  COMMENT '最后一次登录时间',
  `last_ip` varchar(256) COMMENT '最后一次登录IP',
  `reg_ip` varchar(256) COMMENT '注册IP',
  `qq` varchar(20) COMMENT 'QQ',
  `tel` varchar(20) COMMENT '电话',
  `email` varchar(255) COMMENT '邮箱',
  `user_classify` int(1) DEFAULT '1' COMMENT '用户类型',
  `major` varchar(150) COMMENT '专业/部门',
  `position` varchar(255) COMMENT '职位',
  `flag` text COMMENT '个人签名',
  `address` varchar(512) COMMENT '地址',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '用户类型',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '用户状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pt_users`
--

INSERT INTO `pt_users` (`uid`, `username`, `truename`, `password`, `salt`, `img`, `last_login`, `last_ip`, `reg_ip`, `qq`, `tel`, `email`, `user_classify`, `major`, `position`, `flag`, `address`, `type`, `status`) VALUES
(1, 'admin', 'admin', 'd74a1e9c94e0cdf5839216b41bfaa8eb', '20c0425278b2892c6a30550905b5432f', '/images/img.jpg', 1533686782, '', '', '', '', '', 0, '技术部', '管理员', '', '', 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pt_blackboard`
--
ALTER TABLE `pt_blackboard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pt_comment`
--
ALTER TABLE `pt_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pt_logs`
--
ALTER TABLE `pt_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `pt_msg`
--
ALTER TABLE `pt_msg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pt_personal_password`
--
ALTER TABLE `pt_personal_password`
  ADD PRIMARY KEY (`pw_id`);

--
-- Indexes for table `pt_personal_password_addition`
--
ALTER TABLE `pt_personal_password_addition`
  ADD PRIMARY KEY (`addition_password_id`);

--
-- Indexes for table `pt_projects`
--
ALTER TABLE `pt_projects`
  ADD PRIMARY KEY (`pr_id`);

--
-- Indexes for table `pt_public_password`
--
ALTER TABLE `pt_public_password`
  ADD PRIMARY KEY (`pw_id`);

--
-- Indexes for table `pt_public_password_addition`
--
ALTER TABLE `pt_public_password_addition`
  ADD PRIMARY KEY (`addition_password_id`);

--
-- Indexes for table `pt_users`
--
ALTER TABLE `pt_users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `pt_blackboard`
--
ALTER TABLE `pt_blackboard`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_comment`
--
ALTER TABLE `pt_comment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_logs`
--
ALTER TABLE `pt_logs`
  MODIFY `log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '日志ID', AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_msg`
--
ALTER TABLE `pt_msg`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_personal_password`
--
ALTER TABLE `pt_personal_password`
  MODIFY `pw_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '密码单元ID', AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_personal_password_addition`
--
ALTER TABLE `pt_personal_password_addition`
  MODIFY `addition_password_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附加密码ID', AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_projects`
--
ALTER TABLE `pt_projects`
  MODIFY `pr_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '项目ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `pt_public_password`
--
ALTER TABLE `pt_public_password`
  MODIFY `pw_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '密码单元ID', AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_public_password_addition`
--
ALTER TABLE `pt_public_password_addition`
  MODIFY `addition_password_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附加密码ID', AUTO_INCREMENT=1;

--
-- 使用表AUTO_INCREMENT `pt_users`
--
ALTER TABLE `pt_users`
  MODIFY `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID', AUTO_INCREMENT=2;
COMMIT;