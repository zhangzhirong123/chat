/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50723
Source Host           : localhost:3306
Source Database       : demo

Target Server Type    : MYSQL
Target Server Version : 50723
File Encoding         : 65001

Date: 2018-08-27 17:13:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for db_chat
-- ----------------------------
DROP TABLE IF EXISTS `db_chat`;
CREATE TABLE `db_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `send_id` int(11) DEFAULT NULL COMMENT '发送者',
  `content` text COMMENT '内容',
  `time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `accept_id` int(11) DEFAULT NULL COMMENT '接受id',
  `status` tinyint(1) DEFAULT '0' COMMENT '0 未读 1 已读',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_chat
-- ----------------------------

-- ----------------------------
-- Table structure for db_file
-- ----------------------------
DROP TABLE IF EXISTS `db_file`;
CREATE TABLE `db_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_url` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `status` varchar(255) DEFAULT '0' COMMENT '0  所有人  1单人',
  `chat_id` int(11) DEFAULT NULL COMMENT '聊天id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_file
-- ----------------------------

-- ----------------------------
-- Table structure for db_groud_member
-- ----------------------------
DROP TABLE IF EXISTS `db_groud_member`;
CREATE TABLE `db_groud_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groud_members` varchar(255) DEFAULT NULL COMMENT '组成员',
  `groud_name` varchar(255) DEFAULT NULL COMMENT '群名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_groud_member
-- ----------------------------
INSERT INTO `db_groud_member` VALUES ('1', '1,2,3,4,5', null);

-- ----------------------------
-- Table structure for db_group
-- ----------------------------
DROP TABLE IF EXISTS `db_group`;
CREATE TABLE `db_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_id` int(11) DEFAULT NULL,
  `group_desc` text COMMENT '内容',
  `group_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  `group_status` tinyint(1) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_group
-- ----------------------------

-- ----------------------------
-- Table structure for db_member
-- ----------------------------
DROP TABLE IF EXISTS `db_member`;
CREATE TABLE `db_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `name` varchar(30) DEFAULT NULL,
  `nickname` char(30) DEFAULT '' COMMENT '昵称',
  `path_url` varchar(255) DEFAULT NULL COMMENT '用户图像地址',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='会员信息表';

-- ----------------------------
-- Records of db_member
-- ----------------------------
INSERT INTO `db_member` VALUES ('1', '超级管理员', 'admin', '1.jpg');
INSERT INTO `db_member` VALUES ('2', '王浩', 'wanghao', '2.jpg');
INSERT INTO `db_member` VALUES ('3', '黄刚', 'huanggang', '3.jpg');
INSERT INTO `db_member` VALUES ('4', '张三', 'zhangsan', '4.jpg');
INSERT INTO `db_member` VALUES ('5', '张红', 'zhanghong', '5.jpg');

-- ----------------------------
-- Table structure for db_rel
-- ----------------------------
DROP TABLE IF EXISTS `db_rel`;
CREATE TABLE `db_rel` (
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `sid` int(11) DEFAULT NULL COMMENT '用户关联',
  `status` tinyint(1) DEFAULT '1' COMMENT '0 关系删除  1 好友关系 2确认关系'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of db_rel
-- ----------------------------
INSERT INTO `db_rel` VALUES ('3', '1', '1');
INSERT INTO `db_rel` VALUES ('3', '2', '1');
INSERT INTO `db_rel` VALUES ('3', '4', '1');
INSERT INTO `db_rel` VALUES ('3', '5', '1');
INSERT INTO `db_rel` VALUES ('2', '3', '1');
INSERT INTO `db_rel` VALUES ('5', '2', '1');
INSERT INTO `db_rel` VALUES ('2', '5', '1');
SET FOREIGN_KEY_CHECKS=1;
