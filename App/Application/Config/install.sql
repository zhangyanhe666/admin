/*
SQLyog Ultimate v11.25 (64 bit)
MySQL - 5.6.21-log : Database - sys
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*Table structure for table `sys_authority` */

DROP TABLE IF EXISTS `sys_authority`;

CREATE TABLE `sys_authority` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `menu_id` int(11) NOT NULL COMMENT '项目id',
  `authority` bigint(20) DEFAULT '0' COMMENT '权限值',
  `action` varchar(11) DEFAULT NULL COMMENT '操作',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='权限表';

/*Data for the table `sys_authority` */

insert  into `sys_authority`(`menu_id`,`authority`,`action`) values (1,2,'add'),(1,4,'edit'),(1,8,'del'),(1,16,'index'),(2,2,'add'),(2,4,'edit'),(2,8,'del'),(2,16,'index'),(3,2,'add'),(3,4,'edit'),(3,8,'del'),(3,16,'index'),(4,2,'add'),(4,4,'edit'),(4,8,'del'),(4,16,'index'),(5,2,'add'),(5,4,'edit'),(5,8,'del'),(5,16,'index'),(6,2,'add'),(6,4,'edit'),(6,8,'del'),(6,16,'index'),(7,2,'add'),(7,4,'edit'),(7,8,'del'),(7,16,'index'),(8,2,'add'),(8,4,'edit'),(8,8,'del'),(8,16,'index'),(9,2,'add'),(9,4,'edit'),(9,8,'del'),(9,16,'index');



/*Table structure for table `sys_childmenu` */

DROP TABLE IF EXISTS `sys_childmenu`;

CREATE TABLE `sys_childmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `parent_id` int(11) DEFAULT NULL COMMENT '项目父id',
  `name` varchar(200) DEFAULT NULL COMMENT '项目名',
  `action` varchar(200) DEFAULT NULL COMMENT '项目链接',
  `table_name` varchar(100) DEFAULT '' COMMENT '映射表',
  `sort` int(11) DEFAULT '0' COMMENT '排列顺序',
  `attach` varchar(255) DEFAULT '' COMMENT '附加值',
  `is_show` int(2) DEFAULT '0' COMMENT '是否显示',
  `person_in_charge` VARCHAR(255) DEFAULT '' COMMENT '管理员提示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='项目表';

/*Data for the table `sys_childmenu` */

insert  into `sys_childmenu`(`parent_id`,`name`,`action`,`table_name`,`sort`,`attach`,`is_show`) values(1,'数据库管理','structure','',0,'',0),
(1,'项目管理','menu','Model\Menu',0,'',0),
(1,'子项目管理','dispatch','Model\ChildMenu',0,'',0),
(1,'用户管理','adminuser','Model\AdminUser',0,'',0),
(1,'用户组管理','admingroup','Model\AdminGroup',0,'',0),
(1,'ftp-cdn管理','ftp','',0,'',0),
(1,'管理员日志','dispatch','sys.sys_log',0,'',0),
(1,'缓存管理','dispatch','sys.sys_memcache',0,'',0),
(1,'ftp配置','dispatch','sys.sys_ftp_config',0,'',0);

/*Table structure for table `sys_ftp_config` */

DROP TABLE IF EXISTS `sys_ftp_config`;

CREATE TABLE `sys_ftp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `url` varchar(255) DEFAULT NULL COMMENT '网址',
  `path` varchar(50) DEFAULT NULL COMMENT '管理文件夹路径',
  `ip` varchar(50) DEFAULT NULL COMMENT 'ip',
  `username` varchar(50) DEFAULT NULL COMMENT '用户名',
  `passwd` varchar(50) DEFAULT NULL COMMENT '密码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='ftp配置';


/*Table structure for table `sys_group_map` */

DROP TABLE IF EXISTS `sys_group_map`;

CREATE TABLE `sys_group_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `gid` int(11) DEFAULT NULL COMMENT '用户组id',
  `menu_id` int(11) DEFAULT NULL COMMENT '项目id',
  `authority` bigint(20) DEFAULT '0' COMMENT '权限值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ugroup_id` (`gid`,`menu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限映射表';


/*Table structure for table `sys_log` */

DROP TABLE IF EXISTS `sys_log`;

CREATE TABLE `sys_log` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `username` varchar(100) DEFAULT '' COMMENT '用户名',
  `action` varchar(100) DEFAULT '' COMMENT '用户操作',
  `table` varchar(100) DEFAULT '' COMMENT '表名',
  `details` text COMMENT '操作详情',
  `create_time` datetime DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户日志表';


/*Table structure for table `sys_memcache` */

DROP TABLE IF EXISTS `sys_memcache`;

CREATE TABLE `sys_memcache` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `menu_id` int(11) DEFAULT NULL COMMENT '项目id',
  `mem_url` varchar(500) DEFAULT NULL COMMENT '清缓存链接',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='缓存管理';


/*Table structure for table `sys_menu` */

DROP TABLE IF EXISTS `sys_menu`;

CREATE TABLE `sys_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(200) DEFAULT NULL COMMENT '项目名',
  `sort` int(11) DEFAULT '0' COMMENT '排列顺序',
  `is_show` int(2) DEFAULT '0' COMMENT '是否显示',
  `parent_id` INT(11) NOT NULL DEFAULT 0 COMMENT '父id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='项目表';

/*Data for the table `sys_menu` */

insert  into `sys_menu`(`name`,`sort`,`is_show`) values ('后台管理',0,0);
/*Table structure for table `sys_user` */

DROP TABLE IF EXISTS `sys_user`;

CREATE TABLE `sys_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` varchar(32) DEFAULT NULL COMMENT '用户名',
  `password` varchar(64) DEFAULT NULL COMMENT '密码',
  `login_num` int(11) DEFAULT '0' COMMENT '登陆次数',
  `group_id` int(11) DEFAULT '0' COMMENT '用户组id',
  `nickname` varchar(50) DEFAULT '' COMMENT '用户昵称',
  `isdisable` tinyint(1) DEFAULT '0' COMMENT '是否禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`nickname`),
  KEY `ug_id` (`group_id`),
  CONSTRAINT `ug_id` FOREIGN KEY (`group_id`) REFERENCES `sys_user_group` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户表';

/*Data for the table `sys_user` */

insert  into `sys_user`(`username`,`password`,`login_num`,`group_id`,`nickname`,`isdisable`) values ('admin','87d9bb400c0634691f0e3baaf1e2fd0d',0,1,'超级管理员',0);
/*Table structure for table `sys_user_group` */

DROP TABLE IF EXISTS `sys_user_group`;

CREATE TABLE `sys_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户组id',
  `group_name` varchar(64) NOT NULL COMMENT '组名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户组表';

/*Data for the table `sys_user_group` */

insert  into `sys_user_group`(`group_name`) values ('超级管理员');

/*Table structure for table `sys_user_map` */

DROP TABLE IF EXISTS `sys_user_map`;

CREATE TABLE `sys_user_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编码',
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `menu_id` int(11) DEFAULT NULL COMMENT '项目id',
  `authority` bigint(20) DEFAULT '0' COMMENT '权限值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `umid` (`uid`,`menu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户权限映射表';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
CREATE TABLE sys_custom_table_config(
id INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
`table_name`  VARCHAR(50) DEFAULT '' COMMENT '表名',
`menu_id` INT(11) DEFAULT 0 COMMENT '标签id',
`config` TEXT COMMENT '配置',
PRIMARY KEY(`id`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='配置信息';

CREATE TABLE sys_custom(
id INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
`shielded_column`  VARCHAR(255) DEFAULT '' COMMENT '标记列',
`uid` INT(11) DEFAULT 0 COMMENT '用户id',
`menu_id` INT(11) DEFAULT 0 COMMENT '标签id',
PRIMARY KEY(`id`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='自定义列';