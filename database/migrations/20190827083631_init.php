<?php

class Init extends \Phinx\Migration\AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `auth_rule` (
                `id` int NOT NULL COMMENT 'id',
                `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
                `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文名称',
                `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '',
                `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
                `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
                `pId` int NOT NULL DEFAULT '0' COMMENT '',
                `icon` varchar(200) NOT NULL DEFAULT '' COMMENT '图标',
                `condition` char(100) NULL DEFAULT NULL COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证', 
                `createdTime` INT NOT NULL DEFAULT '0' COMMENT '创建时间' , 
                `updatedTime` INT NOT NULL DEFAULT '0' COMMENT '修改时间' ,
                PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
            CREATE TABLE `auth_group` (
                `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
                `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
                `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
                `rules` varchar(255) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则\",\"隔开',
                `createdTime` INT NOT NULL DEFAULT '0' COMMENT '创建时间' , 
                `updatedTime` INT NOT NULL DEFAULT '0' COMMENT '修改时间' ,
                PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
            CREATE TABLE `user` ( 
                `id` INT NOT NULL AUTO_INCREMENT , 
                `guId` VARCHAR(200) NOT NULL COMMENT 'guId' , 
                `groupId` INT(10) NOT NULL COMMENT '用户组id' , 
                `name` VARCHAR(200) NOT NULL COMMENT '用户名' , 
                `password` VARCHAR(200) NOT NULL COMMENT '密码' , 
                `salt` VARCHAR(200) NOT NULL COMMENT '密码盐' , 
                `status` INT NOT NULL DEFAULT '0' COMMENT '状态 0 禁止 1 允许' , 
                `createdTime` INT NOT NULL DEFAULT '0' COMMENT '创建时间' , 
                `updatedTime` INT NOT NULL DEFAULT '0' COMMENT '修改时间' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
            INSERT INTO `user` (`id`, `guId`, `groupId`, `name`, `password`, `salt`, `status`, `createdTime`, `updatedTime`) VALUES (1, '7964d140d1b45b0b0af0c11f858fb86f', 1, 'admin', '1414e8240fafdb12d79bc0eb7c498c5ea2289459', '3b055159671648270ba5a0568265894b', '1', '0', '0');
            INSERT INTO `auth_group` (`id`, `title` ,`status`, `rules`) VALUES (1,'超级管理组',1,'1,2,3,4,5,6,7');
            ");
    }

    public function down()
    {
        $this->execute('
            DROP TABLE `auth_rule`;
            DROP TABLE `auth_group`;
            DROP TABLE `user`;
        ');
    }
}
