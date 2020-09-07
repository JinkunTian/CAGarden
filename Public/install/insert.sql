
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
INSERT INTO `pt_garden_users_extend` VALUES ('1', '17102136', '7', '荣誉院长', '听！狗哭的声音！', '1', '2', '1', '1', null);

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
INSERT INTO `pt_index_config` VALUES ('8', 'association_album', '首页相册', '0', '2017动员大会', 'Public/Index/images/demo/20180414000242-87733834-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('9', 'association_album', '首页相册', '1', '表情包大赛', 'Public/Index/images/demo/20180415170923-5843adce-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('10', 'association_album', '首页相册', '2', '网络安全周', 'Public/Index/images/demo/20180415171230-6b6e3c49-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('11', 'association_album', '首页相册', '3', '电脑义诊', 'Public/Index/images/demo/20180415171549-63b32513-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('12', 'association_album', '首页相册', '4', '周末影院', 'Public/Index/images/demo/20180415171429-7068c1f1-me.jpg', '#');
INSERT INTO `pt_index_config` VALUES ('13', 'association_album', '首页相册', '5', '电脑义诊', 'Public/Index/images/demo/20180415171548-60f3d0e6-me.jpg', '#');
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
INSERT INTO `pt_index_config` VALUES ('30', 'navbar', '导航栏', null, '/Appointment/Recruit', '加入计协', null);
INSERT INTO `pt_index_config` VALUES ('31', 'navbar', '导航栏', null, '#', '资源站', null);
INSERT INTO `pt_index_config` VALUES ('32', 'navbar', '导航栏', null, '#', 'LinuxOnWeb', null);
INSERT INTO `pt_index_config` VALUES ('33', 'navbar', '导航栏', null, '#', '相册', null);
INSERT INTO `pt_index_config` VALUES ('34', 'navbar', '导航栏', null, 'https://blog.tianjinkun.com', '会长博客', null);
INSERT INTO `pt_index_config` VALUES ('35', 'navbar', '导航栏', null, '/Garden', '后花园', null);

-- ----------------------------
-- Records of pt_index_news
-- ----------------------------
INSERT INTO `pt_index_news` VALUES ('8', '城科现教计算机协会义诊活动', '/Public/Uploads/2019-06-11/5cff764053235.jpg', '&lt;p&gt;2019. 4 .9城科现教计算机协会在AB栋篮球场举行了义诊活动。协会的小伙伴带着饱满的热情和专业的技术，为城科学子解决电脑的各种问题。&lt;br/&gt;不管你是否在为电脑卡顿而一筹莫展？还是在为清除病毒而眼花缭乱？是否因突然的死机而愤怒起身？计算机协会的大佬依然会用最认真的工作态度为同学们服务。&lt;br/&gt;当时我们的义诊内容包括：内存条清理；硬件检测及部分硬件维修；装软件；病毒杀除，系统优化……。还记得那天烈日炎炎，同学们也不忘自己的义务，坚持想用自己的所学将大家的电脑问题处理到最好。有些同学的电脑出现的问题比较大或者在修理中途也会遇到很多困难，修理一台电脑可以花费几个小时的时间甚至大家熬夜也要完成自己的工作。在这次活动中，不仅帮助了城科学子，还可以锻炼大家自己的能力，在繁忙中感受到快乐，乐此不疲。&lt;/p&gt;&lt;p&gt;虽然只有三天时间，但是这次义诊活动依然举行的如火如荼。&lt;/p&gt;&lt;p&gt;最后祝贺这次义诊活动取得圆满成功！&lt;/p&gt;&lt;p&gt;城科的小伙伴们，我们来年再见。&lt;/p&gt;&lt;p&gt;&lt;/p&gt;', '29', '郑学艺', '29', '2019-06-11 17:37:04');
INSERT INTO `pt_index_news` VALUES ('9', '城科现教计算机协会换届大会', '/Public/Uploads/2019-06-11/5cff78bb73c0e.jpg', '&lt;p&gt;#2019年计协换届大会#&lt;br/&gt;今天是2019.5.31，城科现教中心计算机协会举行了换届大会。时光如白驹过隙，一年时光匆匆而逝，这又是一个辞旧迎新的时刻。&lt;br/&gt;过去一年里，在协会所有成员的共同努力下，计协也取得了非常不错的成绩，我们的义诊活动也得到了很多同学的好评。现在又换了一批新的血液，希望计算机协会能够总结过去的经验，继续发扬光大!&lt;br/&gt;&lt;/p&gt;', '29', '郑学艺', '38', '2019-06-11 17:47:39');
INSERT INTO `pt_index_news` VALUES ('10', '城科现教计算机协会19届迎新动员大会', '/Public/Uploads/2019-10-14/5da47f2967c0f.jpg', '&lt;p&gt;城科现教中心计算机协会10.14日成功举办了19届的迎新动员大会，这次大会的主题是“编译青春，驱动未来”，大会上我们的老干部用幽默且热情的一面鼓励我们继续向前航行不息，新干事虽然第一次上台发言，却彰显了我们协会自信阳光的一面，他们坚定的眼神透露着对协会未来发展的美好期待。而我们的主席团代表也是认真负责总结过去的经验，并用激情的言语教我们如何利用这次机会去成为更好的自己！加油吧！大家！新学期新气象一起努力吧！&lt;br/&gt;&lt;/p&gt;', '29', '郑学艺', '17', '2019-10-14 21:59:05');
INSERT INTO `pt_index_news` VALUES ('11', '城科现教计算机协会19届成员第一次培训', '/Public/Uploads/2019-10-16/5da7258369e13.jpg', '&lt;p&gt;城科现教中心计算机协会于10.15日正式举行了19届新成员的第一次技术培训，小萌新们带着满满的激情和好奇心来到这里进行第一次学习，学长学姐也是热情认真负责，他们首先从协会网站的应用、预约维修、流满软件与反套路、虚拟机与虚拟光驱这四个点切入，让大家慢慢了解协会的主要工作。萌新们学得都格外认真，不懂的地方学长学姐也是耐心讲解，好一副生机和谐的画面呀！期待着协会的未来定是一片光明！&lt;/p&gt;&lt;p&gt;&lt;/p&gt;&lt;p&gt;&lt;img title=&quot;1571235188525905.jpg&quot; style=&quot;&quot; src=&quot;/Public/Uploads/ueditor/image/20191016/1571235188525905.jpg&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img title=&quot;1571235188171483.jpg&quot; style=&quot;&quot; src=&quot;/Public/Uploads/ueditor/image/20191016/1571235188171483.jpg&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '29', '郑学艺', '17', '2019-10-16 21:58:51');
INSERT INTO `pt_index_news` VALUES ('12', '城科现教计算机协会19届成员第二次培训', '/Public/Uploads/2019-10-16/5da724ea32564.jpg', '&lt;p&gt;10.16，众望所归，今天进行了协会的第二次培训，不再是像昨天一样只是单纯讲述，萌新们都带着电脑，带着想要学习的急迫愿望，期待着一个又一个神奇而有趣的世界。为了给之后协会的义诊活动做下坚实的铺垫，给大家讲了如何重装系统，大家应该都知道每当电脑出现问题，最方便快捷的方式就是重装系统，这必须是每一位同学都应该牢牢掌握的东西哦。首先需要做PE盘然后进入F站下载镜像备份到C盘，将pe、镜像拷进u盘，进入pe系统就可以开始重装系统啦！大家一天比一天认真，都想要进入大学学到更多实用的东西，值得称赞！&lt;/p&gt;&lt;p&gt;&lt;img title=&quot;1571235257609279.jpg&quot; src=&quot;/Public/Uploads/ueditor/image/20191016/1571235257609279.jpg&quot; width=&quot;100%&quot; height=&quot;&quot; border=&quot;0&quot; vspace=&quot;0&quot; alt=&quot;1571235257609279.jpg&quot; style=&quot;width: 100%;&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img title=&quot;1571235258970775.jpg&quot; src=&quot;/Public/Uploads/ueditor/image/20191016/1571235258970775.jpg&quot; width=&quot;100%&quot; height=&quot;&quot; border=&quot;0&quot; vspace=&quot;0&quot; alt=&quot;1571235258970775.jpg&quot;/&gt;&lt;/p&gt;', '29', '郑学艺', '32', '2019-10-16 22:10:50');
INSERT INTO `pt_index_news` VALUES ('13', 'LinuxOnWeb活动上线', '/Public/Uploads/2020-06-20/5eed85ff98c50.png', '&lt;p&gt;各位计算机协会的同学们，因疫情原因协会没有办法开展线下活动，但经过一系列努力，我们研究出了一个线上的活动：LinuxOnWeb--即线上体验Linux系统，给各位想玩Linux但因内存不够跑虚拟机的、安装不好系统等各种原因与Linux系统擦肩而过的同学们提供一个点击即可得的Linux桌面。打开浏览器就能得到一个独立的Linux桌面。并已经安装了如下软件在Linux&amp;nbsp;On&amp;nbsp;Web中跟同学们Meet。&lt;/p&gt;&lt;pre class=&quot;prism-highlight prism- prism-line-numbers language-bash&quot; data-language=&quot;Bash&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0.5em; padding: 1em 1em 1em 3.8em; text-shadow: white 0px 1px; font-family: Consolas, Monaco, &amp;quot;Andale Mono&amp;quot;, &amp;quot;Ubuntu Mono&amp;quot;, monospace; direction: ltr; word-break: normal; overflow-wrap: normal; line-height: 1.5; tab-size: 4; hyphens: none; overflow: auto; background: rgb(245, 242, 240); position: relative; counter-reset: linenumber 0; font-size: 12px;&quot;&gt;Chrome--Google开发的浏览器，360浏览器，QQ浏览器，2345浏览器等众多国产浏览器它爸爸。\r\n腾讯QQ--腾讯官方Linux版QQ，简单的功能、复古的界面带你来一场跨度十年的考古。\r\n网易云音乐--国产良心，界面精良。\r\nSteam--谁说Linux不能玩游戏！\r\nStellarium--一款虚拟天文馆，想看星星没问题！\r\nWPS--悄悄告诉你：WPS是Microsoft Office它爸爸！\r\nMindMaster--一款国内开发的思维导图软件\r\nGIMP--Linux下的PhotoShoop\r\n搜狗拼音输入法--中文必备，不二之选。\r\nFree Download Manager--少看片，多学习！\r\n---三大文本编辑器，别争了，都安装了---\r\nSublime Text\r\nVisual Studio Code\r\nVim--骨灰级Linuxer最爱，提示小白，退出vim是冒号加q回车。\r\n---------\r\nGit--它的爸爸是Linus，就写了Linux操作系统的那个。跟Linux是亲兄弟。&lt;/pre&gt;&lt;p&gt;使用方法：&lt;/p&gt;&lt;p&gt;从协会网站导航栏LINUXONWEB进入系统或访问&lt;a href=&quot;https://vd.ca.cqucc.edu.cn/&quot;&gt;https://vd.ca.cqucc.edu.cn/&lt;/a&gt;&amp;nbsp;进入系统，输入你在计协的账户登录就能使用，没有账户的注册一个，如果你是年后首次使用，请先登录后花园升级账户后方可使用。&lt;/p&gt;&lt;p&gt;活动截图：&lt;/p&gt;&lt;p style=&quot;text-align:center&quot;&gt;&lt;img src=&quot;/Public/Uploads/ueditor/image/20200620/1592624320875016.png&quot; title=&quot;1592624320875016.png&quot; style=&quot;width: 100%;&quot; alt=&quot;linux_on_web_1.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Uploads/ueditor/image/20200622/1592756202895611.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756202895611.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Uploads/ueditor/image/20200622/1592756202687880.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756202687880.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Uploads/ueditor/image/20200622/1592756202653849.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756202653849.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Uploads/ueditor/image/20200622/1592756203931365.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756203931365.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/Public/Uploads/ueditor/image/20200622/1592756203240350.png&quot; style=&quot;width: 100%;&quot; title=&quot;1592756203240350.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1', '天锦', '20', '2020-06-20 11:43:59');


-- ----------------------------
-- Records of pt_recruit_grade
-- ----------------------------
INSERT INTO `pt_recruit_grade` VALUES ('1', '计算机协会2020纳新', '恭喜你已经成功提交纳新申请，请加QQ群：xxxxxx以及时获取面试信息！', '2020', '1');
