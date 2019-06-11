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
-- Records of pt_garden_projects
-- ----------------------------
INSERT INTO `pt_garden_projects` VALUES ('1', '0', '', '计算机协会', '2018-08-01 22:29:00', '0', '::', '::', '欢迎使用CAGarden！', '欢迎使用CAGarden！CAGarden为一款计算机社团设计的专用网站程序，是在ProjectTree项目的基础上添加了电脑义修预约功能以及社团纳新功能，不仅方便的实现了记录社团项目的运行情况，更方便每届的新成员加入社团了解社团和老成员交接已有项目给新成员！', '1');

-- ----------------------------
-- Records of pt_garden_users
-- ----------------------------
INSERT INTO `pt_garden_users` VALUES ('1', 'admin', 'admin', 'd74a1e9c94e0cdf5839216b41bfaa8eb', '20c0425278b2892c6a30550905b5432f', '/images/img.jpg', '2018-08-22 16:21:52', '192.168.16.8','2018-05-12 14:28:04', '10.2.24.167', '2961165914', '13088886666', 'admin@domain.com','1', '3', '部长', '一二三四五，上山打老虎！', '2', '1','1', NULL);

-- ----------------------------
-- Records of pt_index_config
-- ----------------------------
INSERT INTO `pt_index_config` VALUES ('1', 'association_slogan', '首页大图标语', null, '城科最有实力的计算机交流社团', null, null);
INSERT INTO `pt_index_config` VALUES ('2', 'main_button', '首页大图主按钮功能', null, '网上预约', '/Appointment', null);
INSERT INTO `pt_index_config` VALUES ('3', 'association_info', '社团介绍', null, '现教中心计算机协会是由重庆大学城市科技学院在读全日制本科或专科生参与构成的全校性、非政治性的为学生提供计算机学习交流的社团组织，由重庆大学城市科技现代教育技术中心指导。是目前校内最权威、覆盖面最广、规模最大的学生计算机交流组织。', null, null);
INSERT INTO `pt_index_config` VALUES ('4', 'major_activities', '社团主要活动', '0', '网站开发', '协会附有网站架设小组,<br/>\r\n职能为创建和维护协会相关网站.', null);
INSERT INTO `pt_index_config` VALUES ('5', 'major_activities', '社团主要活动', '1', '电脑维修', '协会技术部提供电脑系统重装,<br/>\r\n常见故障排除及维护等服务.', null);
INSERT INTO `pt_index_config` VALUES ('6', 'major_activities', '社团主要活动', '2', '软件安装', '协会提供常用软件安装服务,\r\n如:Office,Photoshop,AutoCAD.', null);
INSERT INTO `pt_index_config` VALUES ('7', 'major_activities', '社团主要活动', '3', '想起来再写', null, null);
INSERT INTO `pt_index_config` VALUES ('8', 'association_album', '首页相册', '0', '2017动员大会', 'http://photo.mecca.org.cn/_data/i/upload/2018/04/14/20180414000242-87733834-me.jpg', 'http://photo.mecca.org.cn/index.php?/category/4');
INSERT INTO `pt_index_config` VALUES ('9', 'association_album', '首页相册', '1', '表情包大赛', 'http://photo.mecca.org.cn/_data/i/upload/2018/04/15/20180415170923-5843adce-me.jpg', 'http://photo.mecca.org.cn/index.php?/category/3');
INSERT INTO `pt_index_config` VALUES ('10', 'association_album', '首页相册', '2', '网络安全周', 'http://photo.mecca.org.cn/_data/i/upload/2018/04/15/20180415171230-6b6e3c49-me.jpg', 'http://photo.mecca.org.cn/index.php?/category/5');
INSERT INTO `pt_index_config` VALUES ('11', 'association_album', '首页相册', '3', '电脑义诊', 'http://photo.mecca.org.cn/_data/i/upload/2018/04/15/20180415171549-63b32513-me.jpg', 'http://photo.mecca.org.cn/index.php?/category/2');
INSERT INTO `pt_index_config` VALUES ('12', 'association_album', '首页相册', '4', '周末影院', 'http://photo.mecca.org.cn/_data/i/upload/2018/04/15/20180415171429-7068c1f1-me.jpg', 'http://photo.mecca.org.cn/index.php?/category/6');
INSERT INTO `pt_index_config` VALUES ('13', 'association_album', '首页相册', '5', '电脑义诊', 'http://photo.mecca.org.cn/_data/i/upload/2018/04/15/20180415171548-60f3d0e6-me.jpg', 'http://photo.mecca.org.cn/index.php?/category/2');
INSERT INTO `pt_index_config` VALUES ('14', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-1.png', null, null);
INSERT INTO `pt_index_config` VALUES ('15', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-3.png', null, null);
INSERT INTO `pt_index_config` VALUES ('16', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-4.png', null, null);
INSERT INTO `pt_index_config` VALUES ('17', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-5.png', null, null);
INSERT INTO `pt_index_config` VALUES ('18', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-6.png', null, null);
INSERT INTO `pt_index_config` VALUES ('19', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-7.png', null, null);
INSERT INTO `pt_index_config` VALUES ('20', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-8.png', null, null);
INSERT INTO `pt_index_config` VALUES ('21', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-9.png', null, null);
INSERT INTO `pt_index_config` VALUES ('22', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-10.png', null, null);
INSERT INTO `pt_index_config` VALUES ('23', 'partner_association', '兄弟社团', null, 'Public/Index/images/demo/partners-11.png', null, null);
INSERT INTO `pt_index_config` VALUES ('24', '', '', null, '', null, null);
INSERT INTO `pt_index_config` VALUES ('25', 'association_address', '协会办公地址', null, '图书馆407室', null, null);
INSERT INTO `pt_index_config` VALUES ('26', 'contact_info', '联系方式', '0', 'https://jq.qq.com/?_wv=1027&k=5o26do6', 'QQ群1群：303770202', null);
INSERT INTO `pt_index_config` VALUES ('27', 'contact_info', '联系方式', '1', 'https://jq.qq.com/?_wv=1027&k=5aaFyV9', 'QQ群2群：345966568', null);
INSERT INTO `pt_index_config` VALUES ('28', 'navbar', '导航栏', null, '/Appointment', '预约维修', null);
INSERT INTO `pt_index_config` VALUES ('29', 'navbar', '导航栏', null, '/#about', '协会介绍', null);
INSERT INTO `pt_index_config` VALUES ('30', 'navbar', '导航栏', null, 'http://blog.ca.cqucc.edu.cn', '培训中心', null);
INSERT INTO `pt_index_config` VALUES ('31', 'navbar', '导航栏', null, 'http://file.ca.cqucc.edu.cn', '查看资源站', null);
INSERT INTO `pt_index_config` VALUES ('32', 'navbar', '导航栏', null, 'http://search.ca.cqucc.edu.cn', '搜索资源站', null);
INSERT INTO `pt_index_config` VALUES ('33', 'navbar', '导航栏', null, 'http://photo.ca.cqucc.edu.cn', '相册', null);
INSERT INTO `pt_index_config` VALUES ('34', 'navbar', '导航栏', null, '/#team', '大佬介绍', null);
INSERT INTO `pt_index_config` VALUES ('35', 'navbar', '导航栏', null, '/Recruit', '加入我们', null);