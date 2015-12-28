-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2007 年 08 月 13 日 15:27
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.0
-- 
-- 数据库: `faithdb`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_menu`
-- 

CREATE TABLE `admin_menu` (
  `pk_menu` int(10) unsigned NOT NULL auto_increment,
  `sn` smallint(3) NOT NULL default '100' COMMENT '排序',
  `name` varchar(30) NOT NULL default '',
  `title` varchar(60) NOT NULL default '',
  `href` varchar(100) NOT NULL default '',
  `target` varchar(10) NOT NULL,
  PRIMARY KEY  (`pk_menu`),
  KEY `idx_sn` (`sn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理资源菜单' AUTO_INCREMENT=1007 ;

-- 
-- 导出表中的数据 `admin_menu`
-- 

INSERT INTO `admin_menu` (`pk_menu`, `sn`, `name`, `title`, `href`, `target`) VALUES 
(10, 100, '内容管理', '内容管理', '', ''),
(11, 102, '权限管理', '权限管理', '', ''),
(1001, 201, '商店管理', '商店管理', '', ''),
(1002, 202, '诗歌管理', '赞美诗歌管理', '', ''),
(1003, 203, '圣经管理', '圣经管理', '', ''),
(1004, 204, '视频管理', '视频管理', '', ''),
(1005, 205, '文章管理', '文章管理', '', ''),
(1006, 206, '帖吧管理', '信仰帖吧管理', '', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_operate`
-- 

CREATE TABLE `admin_operate` (
  `pk_operate` tinyint(2) unsigned NOT NULL default '0',
  `operate_name` varchar(20) NOT NULL default '',
  `remark` tinytext NOT NULL,
  PRIMARY KEY  (`pk_operate`),
  UNIQUE KEY `operate_name` (`operate_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员操作';

-- 
-- 导出表中的数据 `admin_operate`
-- 

INSERT INTO `admin_operate` (`pk_operate`, `operate_name`, `remark`) VALUES 
(0, 'browse', '浏览'),
(1, 'view', '查看'),
(2, 'search', '查询'),
(3, 'download', '下载'),
(4, 'create', '创建'),
(5, 'create2', '高级创建'),
(6, 'edit', '编辑'),
(7, 'edit2', '高级编辑'),
(10, 'publish', '发布'),
(11, 'unpublish', '取消发布'),
(12, 'check', '审核'),
(13, 'check2', '高级审核'),
(14, 'commend', '推荐'),
(15, 'uncommend', '取消推荐'),
(16, 'setlevel', '分级'),
(17, 'unsetlevel', '取消分级'),
(18, 'enable', '启用'),
(19, 'disable', '禁用'),
(30, 'delete', '删除'),
(31, 'delete2', '高级删除');

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_privilege`
-- 

CREATE TABLE `admin_privilege` (
  `pk_privilege` int(10) unsigned NOT NULL auto_increment COMMENT '权限编号',
  `fk_role` int(10) unsigned NOT NULL default '0' COMMENT '角色',
  `fk_menu` int(10) unsigned NOT NULL default '0' COMMENT '资源编号',
  `fk_operate` int(10) unsigned NOT NULL default '0' COMMENT '操作编号',
  PRIMARY KEY  (`pk_privilege`),
  UNIQUE KEY `uni_role_menu` (`fk_role`,`fk_menu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员访问权限' AUTO_INCREMENT=10 ;

-- 
-- 导出表中的数据 `admin_privilege`
-- 

INSERT INTO `admin_privilege` (`pk_privilege`, `fk_role`, `fk_menu`, `fk_operate`) VALUES 
(1, 1, 10, 65535),
(2, 4, 11, 1),
(3, 4, 1001, 3),
(4, 4, 1002, 7),
(5, 4, 1003, 7),
(6, 4, 1004, 7),
(7, 1, 1005, 65535),
(8, 1, 1006, 3),
(9, 1, 11, 65535);

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_role`
-- 

CREATE TABLE `admin_role` (
  `pk_role` int(11) NOT NULL auto_increment,
  `role_name` varchar(20) NOT NULL default '',
  `remark` tinytext NOT NULL,
  PRIMARY KEY  (`pk_role`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员角色' AUTO_INCREMENT=5 ;

-- 
-- 导出表中的数据 `admin_role`
-- 

INSERT INTO `admin_role` (`pk_role`, `role_name`, `remark`) VALUES 
(1, 'Administrator', ''),
(2, 'Author', ''),
(3, 'User', ''),
(4, '商店管理员', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_user`
-- 

CREATE TABLE `admin_user` (
  `pk_user` int(10) unsigned NOT NULL default '0',
  `user_name` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `enabled` enum('YES','NO') NOT NULL default 'NO',
  `email` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`pk_user`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员用户';

-- 
-- 导出表中的数据 `admin_user`
-- 

INSERT INTO `admin_user` (`pk_user`, `user_name`, `password`, `enabled`, `email`) VALUES 
(1, 'root', '1fd93b70ad6e785855c721dbb078913a', 'YES', '');

-- --------------------------------------------------------

-- 
-- 表的结构 `admin_user_role`
-- 

CREATE TABLE `admin_user_role` (
  `fk_user` int(11) NOT NULL default '0' COMMENT '用户编号',
  `fk_role` int(11) NOT NULL default '0' COMMENT '角色编号',
  UNIQUE KEY `uni_user_role` (`fk_user`,`fk_role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组';

-- 
-- 导出表中的数据 `admin_user_role`
-- 

INSERT INTO `admin_user_role` (`fk_user`, `fk_role`) VALUES 
(1, 1),
(1, 4);

-- --------------------------------------------------------

-- 
-- 表的结构 `auto_increment`
-- 

CREATE TABLE `auto_increment` (
  `name` varchar(20) NOT NULL default '' COMMENT '自增变量名',
  `value` int(10) unsigned NOT NULL default '0' COMMENT '值',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='多变量自增';

-- 
-- 导出表中的数据 `auto_increment`
-- 

INSERT INTO `auto_increment` (`name`, `value`) VALUES 
('admin_resource', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `t_passport`
-- 

CREATE TABLE `t_passport` (
  `GUID` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(50) NOT NULL default '',
  `password` varchar(16) NOT NULL default '',
  `region` varchar(2) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`GUID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `t_passport`
-- 

INSERT INTO `t_passport` (`GUID`, `email`, `password`, `region`, `ip`, `status`) VALUES 
(2, 'rsr_cn@hotmail.com', 'dac82374c5fad440', 'cn', '127.0.0.1', 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `t_passport_ext`
-- 

CREATE TABLE `t_passport_ext` (
  `GUID` int(10) unsigned NOT NULL default '0',
  `question` tinyint(3) unsigned NOT NULL default '0',
  `answer` varchar(30) NOT NULL default '',
  `email2` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`GUID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `t_passport_ext`
-- 

