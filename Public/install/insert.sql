
-- ----------------------------
-- Records of pt_common_departments
-- ----------------------------
INSERT INTO `pt_common_departments` VALUES ('1', '技术部', '', '这是技术部的介绍', '1');
INSERT INTO `pt_common_departments` VALUES ('2', '项目部', '', '这是项目部的介绍', '1');
INSERT INTO `pt_common_departments` VALUES ('3', '文宣部', '', '这是文宣部的介绍', '1');
INSERT INTO `pt_common_departments` VALUES ('4', '组织部', '', '这是组织部的介绍', '1');
INSERT INTO `pt_common_departments` VALUES ('5', '办公室', '', '这是办公室的介绍', '1');
INSERT INTO `pt_common_departments` VALUES ('6', '主席团', '', '这是主席团的介绍', '20');

-- ----------------------------
-- Records of pt_common_institutes
-- ----------------------------
INSERT INTO `pt_common_institutes` VALUES ('1', '电气信息学院');
INSERT INTO `pt_common_institutes` VALUES ('2', '艺术设计学院');
INSERT INTO `pt_common_institutes` VALUES ('3', '土木工程学院');

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

-- ----------------------------
-- Records of pt_garden_projects
-- ----------------------------
INSERT INTO `pt_garden_projects` VALUES ('1', '0', '', '计算机协会', '2018-08-01 22:29:00', '0', '::', '::', '欢迎使用CAGarden！', '欢迎使用CAGarden！CAGarden为一款计算机社团设计的专用网站程序，是在ProjectTree项目的基础上添加了电脑义修预约功能以及社团纳新功能，不仅方便的实现了记录社团项目的运行情况，更方便每届的新成员加入社团了解社团和老成员交接已有项目给新成员！', '1');

-- ----------------------------
-- Records of pt_garden_users
-- ----------------------------
INSERT INTO `pt_users` VALUES ('1', 'admin', '天锦','/images/img.jpg', '','','d74a1e9c94e0cdf5839216b41bfaa8eb', '20c0425278b2892c6a30550905b5432f', null,'2961165914',  'admin@domain.com','13088886666', '192.168.16.8','2018-08-22 16:21:52', '10.2.24.167', '2020-08-31 12:09:25', null,'garden',null,null);

-- ----------------------------
-- Records of pt_garden_users_extend
-- ----------------------------
INSERT INTO `pt_garden_users_extend` VALUES ('1', '17100000', '7', '荣誉院长', '听！狗哭的声音！', '1', '2', '1', '1', null);

-- ----------------------------
-- Records of pt_garden_msg
-- ----------------------------
INSERT INTO `pt_garden_msg` VALUES ('1', '1', '欢迎来到计协后花园！', '2018-08-21 09:28:21');

-- ----------------------------
-- Records of pt_index_config
-- ----------------------------
INSERT INTO `pt_index_config` VALUES ('1', 'association_slogan', '首页大图标语', null, '城科最骚气的计算机交流社团', null, null);
INSERT INTO `pt_index_config` VALUES ('2', 'main_button', '首页大图主按钮功能', null, '网上预约', '/Appointment', null);
INSERT INTO `pt_index_config` VALUES ('3', 'association_info', '社团介绍', null, '现教中心计算机协会是由重庆大学城市科技学院在读全日制本科或专科生参与构成的全校性、非政治性的为学生提供计算机学习交流的社团组织，由重庆大学城市科技现代教育技术中心指导。是目前校内最权威、覆盖面最广、规模最大的学生计算机交流组织。', null, null);
INSERT INTO `pt_index_config` VALUES ('4', 'major_activities', '社团主要活动', '0', '网站开发', '协会附有网站架设小组,<br/>\r\n职能为创建和维护协会相关网站.', null);
INSERT INTO `pt_index_config` VALUES ('5', 'major_activities', '社团主要活动', '1', '电脑维修', '协会技术部提供电脑系统重装,<br/>\r\n常见故障排除及维护等服务.', null);
INSERT INTO `pt_index_config` VALUES ('6', 'major_activities', '社团主要活动', '2', '软件安装', '协会提供常用软件安装服务,\r\n如:Office,Photoshop,AutoCAD.', null);
INSERT INTO `pt_index_config` VALUES ('7', 'major_activities', '社团主要活动', '3', '前沿技术', '大数据、物联网IoT、AI</br>等各种前沿科技一起研究.', NULL);
INSERT INTO `pt_index_config` VALUES ('8', 'association_album', '首页相册', '0', '2017动员大会', '/Public/Index/images/demo/20180414000242-87733834-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('9', 'association_album', '首页相册', '1', '表情包大赛', '/Public/Index/images/demo/20180415170923-5843adce-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('10', 'association_album', '首页相册', '2', '网络安全周', '/Public/Index/images/demo/20180415171230-6b6e3c49-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('11', 'association_album', '首页相册', '3', '电脑义诊', '/Public/Index/images/demo/20180415171549-63b32513-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('12', 'association_album', '首页相册', '4', '周末影院', '/Public/Index/images/demo/20180415171429-7068c1f1-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('13', 'association_album', '首页相册', '5', '电脑义诊', '/Public/Index/images/demo/20180415171548-60f3d0e6-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('14', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-1.png', null, null);
INSERT INTO `pt_index_config` VALUES ('15', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-3.png', null, null);
INSERT INTO `pt_index_config` VALUES ('16', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-4.png', null, null);
INSERT INTO `pt_index_config` VALUES ('17', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-5.png', null, null);
INSERT INTO `pt_index_config` VALUES ('18', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-6.png', null, null);
INSERT INTO `pt_index_config` VALUES ('19', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-7.png', null, null);
INSERT INTO `pt_index_config` VALUES ('20', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-8.png', null, null);
INSERT INTO `pt_index_config` VALUES ('21', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-9.png', null, null);
INSERT INTO `pt_index_config` VALUES ('22', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-10.png', null, null);
INSERT INTO `pt_index_config` VALUES ('23', 'partner_association', '兄弟社团', null, '/Public/Index/images/demo/partners-11.png', null, null);
INSERT INTO `pt_index_config` VALUES ('24', '', '', null, '', null, null);
INSERT INTO `pt_index_config` VALUES ('25', 'association_address', '协会办公地址', null, '图书馆407室', null, null);
INSERT INTO `pt_index_config` VALUES ('26', 'contact_info', '联系方式', '0', 'https://jq.qq.com/?_wv=1027&k=5o26do6', 'QQ群1群：303770202', null);
INSERT INTO `pt_index_config` VALUES ('27', 'contact_info', '联系方式', '1', 'https://jq.qq.com/?_wv=1027&k=5aaFyV9', 'QQ群2群：345966568', null);
INSERT INTO `pt_index_config` VALUES ('28', 'navbar', '导航栏', null, '/Appointment', '预约维修', null);
INSERT INTO `pt_index_config` VALUES ('29', 'navbar', '导航栏', null, '/#about', '协会介绍', null);
INSERT INTO `pt_index_config` VALUES ('30', 'navbar', '导航栏', null, '/Appointment/Recruit', '加入计协', null);
INSERT INTO `pt_index_config` VALUES ('31', 'navbar', '导航栏', null, '#', '资源站', null);
INSERT INTO `pt_index_config` VALUES ('32', 'navbar', '导航栏', null, '#', 'LinuxOnWeb', null);
INSERT INTO `pt_index_config` VALUES ('33', 'navbar', '导航栏', null, '#', '相册', null);
INSERT INTO `pt_index_config` VALUES ('34', 'navbar', '导航栏', null, 'https://blog.tianjinkun.com', '会长博客', null);
INSERT INTO `pt_index_config` VALUES ('35', 'navbar', '导航栏', null, '/Garden', '后花园', null);

-- ----------------------------
-- Records of pt_index_news
-- ----------------------------
INSERT INTO `pt_index_news` VALUES ('1', 'CAGarden 程序发布啦', '/Public/Index/images/demo/news/news1.jpg', '&lt;p&gt;经过两年的校内实际使用和打磨，CAGarden终于发布了！。&lt;/p&gt;&lt;p&gt;&lt;/p&gt;', '1', '天锦', '29', '2019-06-11 17:37:04');
INSERT INTO `pt_index_news` VALUES ('2', 'LinuxOnWeb活动上线', '/Public/Index/images/demo/news/5eed85ff98c50.png', '&lt;p&gt;各位计算机协会的同学们，因疫情原因协会没有办法开展线下活动，但经过一系列努力，我们研究出了一个线上的活动：LinuxOnWeb--即线上体验Linux系统，给各位想玩Linux但因内存不够跑虚拟机的、安装不好系统等各种原因与Linux系统擦肩而过的同学们提供一个点击即可得的Linux桌面。打开浏览器就能得到一个独立的Linux桌面。并已经安装了如下软件在Linux&amp;nbsp;On&amp;nbsp;Web中跟同学们Meet。&lt;/p&gt;&lt;pre class=&quot;prism-highlight prism- prism-line-numbers language-bash&quot; data-language=&quot;Bash&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0.5em; padding: 1em 1em 1em 3.8em; text-shadow: white 0px 1px; font-family: Consolas, Monaco, &amp;quot;Andale Mono&amp;quot;, &amp;quot;Ubuntu Mono&amp;quot;, monospace; direction: ltr; word-break: normal; overflow-wrap: normal; line-height: 1.5; tab-size: 4; hyphens: none; overflow: auto; background: rgb(245, 242, 240); position: relative; counter-reset: linenumber 0; font-size: 12px;&quot;&gt;Chrome--Google开发的浏览器，360浏览器，QQ浏览器，2345浏览器等众多国产浏览器它爸爸。\r\n腾讯QQ--腾讯官方Linux版QQ，简单的功能、复古的界面带你来一场跨度十年的考古。\r\n网易云音乐--国产良心，界面精良。\r\nSteam--谁说Linux不能玩游戏！\r\nStellarium--一款虚拟天文馆，想看星星没问题！\r\nWPS--悄悄告诉你：WPS是Microsoft Office它爸爸！\r\nMindMaster--一款国内开发的思维导图软件\r\nGIMP--Linux下的PhotoShoop\r\n搜狗拼音输入法--中文必备，不二之选。\r\nFree Download Manager--少看片，多学习！\r\n---三大文本编辑器，别争了，都安装了---\r\nSublime Text\r\nVisual Studio Code\r\nVim--骨灰级Linuxer最爱，提示小白，退出vim是冒号加q回车。\r\n---------\r\nGit--它的爸爸是Linus，就写了Linux操作系统的那个。跟Linux是亲兄弟。&lt;/pre&gt;&lt;p&gt;使用方法：&lt;/p&gt;&lt;p&gt;从协会网站导航栏LINUXONWEB进入系统或访问&lt;a href=&quot;https://vd.ca.cqucc.edu.cn/&quot;&gt;https://vd.ca.cqucc.edu.cn/&lt;/a&gt;&amp;nbsp;进入系统，输入你在计协的账户登录就能使用，没有账户的注册一个，如果你是年后首次使用，请先登录后花园升级账户后方可使用。&lt;/p&gt;&lt;p&gt;活动截图：&lt;/p&gt;&lt;p style=&quot;text-align:center&quot;&gt;&lt;img src=&quot;/Public/Index/images/demo/news/1592624320875016.png&quot; title=&quot;1592624320875016.png&quot; style=&quot;width: 100%;&quot; alt=&quot;linux_on_web_1.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Index/images/demo/news/1592756202895611.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756202895611.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Index/images/demo/news/1592756202687880.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756202687880.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Index/images/demo/news/1592756202653849.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756202653849.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Index/images/demo/news/1592756203931365.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756203931365.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Index/images/demo/news/1592756203240350.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756203240350.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1', '天锦', '20', '2020-06-20 11:43:59');


-- ----------------------------
-- Records of pt_recruit_grade
-- ----------------------------
INSERT INTO `pt_recruit_grade` VALUES ('1', '计算机协会2020纳新', '恭喜你已经成功提交纳新申请，请加QQ群：xxxxxx以及时获取面试信息！', '2020', '1');
