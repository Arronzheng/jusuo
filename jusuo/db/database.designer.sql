-- ----------------------------
-- Table structure for designers
-- ----------------------------
DROP TABLE IF EXISTS `designers`;
CREATE TABLE `designers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `login_username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录用户名（可随时修改，但必须全站唯一）',
  `login_password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录密码，使用bcrypt',
  `login_telephone` char(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录手机号（可随时修改，但须先进行密码验证，且全站唯一）',
  `login_wx_openid` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '微信openid（用于PC端扫码登录/移动端微信授权登录）',
  `login_token_type` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '登录凭证（0.用户名.1.手机号.2.微信openid，指该记录是经由什么途径创建的，其中0、1目前不分开）',
  `secret_question` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密保问题（用于修改或重置密码，50字以内）',
  `secret_answer` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密保问题答案（用于修改或重置密码，25字以内）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `last_active_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后活跃时间',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designer_account` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '设计账号ID（数字+字母组合，全部设计师唯一）',
  `designer_account_type_id` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '设计师账号类别',
  `organization_type` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '归属单位的类型（0.无.1.品牌.2.经销商.3.装饰公司，用于分别查找不同的表完成关联）',
  `organization_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '归属单位的id（开通账号时记录）',
  `status` int(5) unsigned NOT NULL DEFAULT 200 COMMENT '状态（200正常，100禁用）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `designers_login_username` (`login_username`) USING BTREE,
  UNIQUE KEY `designers_designer_account` (`designer_account`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师（一经创建，只可禁用，不可删除）';

-- ----------------------------
-- Table structure for log_designer_certifications
-- ----------------------------
DROP TABLE IF EXISTS `log_designer_certifications`;
CREATE TABLE `log_designer_certifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_designer_id` int(10) unsigned NOT NULL COMMENT '被审核设计师id',
  `content` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '待审核信息的serialize字符串（数组，在字段值前加上字段名）',
  `approve_administrator_id` smallint(5) unsigned NOT NULL COMMENT '审核平台管理员id',
  `is_approved` tinyint(3) NOT NULL DEFAULT 0 COMMENT '审核结果（-1.驳回.0.待审核.1.通过）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '审核备注（驳回理由等）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师认证记录（与设计师一对多关联）';

-- ----------------------------
-- Table structure for certification_designers
-- ----------------------------
DROP TABLE IF EXISTS `certification_designers`;
CREATE TABLE `certification_designers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `designer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `legal_person_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '真实姓名',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '性别（0.未知.1.男.2.女）',
  `code_idcard` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '身份证号',
  `expired_at_idcard` timestamp NOT NULL COMMENT '身份证到期日期（精确至日，显示某年某月某日00:00:00）',
  `url_idcard_front` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证正面的完整的公网访问地址',
  `url_idcard_back` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证背面的完整的公网访问地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师认证详情（与设计师一对一关联）';

-- ----------------------------
-- Table structure for designer_details
-- ----------------------------
DROP TABLE IF EXISTS `designer_details`;
CREATE TABLE `designer_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `designer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `nickname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称（可与登录用户名不同）',
  `url_avatar` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像的完整公网访问地址',
  `point_experience` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '经验值',
  `point_money` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '积分',
  `privilege_show_im_designer` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '是否打开“我是设计师”相关内容',
  `privilege_account_lock` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '账号是否被锁定（0.否.1.是）',
  `approve_realname` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '是否已实名认证（0.否.1.是）',
  `approve_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '认证时间（冗余字段，与log表挂钩）',
  `self_designer_type` tinyint(3) unsigned NOT NULL COMMENT '设计师类型（0.未选择1.应用设计师.1.空间设计师）',
  `self_designer_level` tinyint(4) NOT NULL DEFAULT -1 COMMENT '用户等级（-1.临时账号.0-10.设计师.注意需先实名认证通过才更新为0以上）',
  `code_idcard` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '身份证号',
  `url_idcard_front` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '身份证正面的完整的公网访问地址',
  `url_idcard_back` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '身份证背面的完整的公网访问地址',
  `self_birth_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '出生时间（精确至日，显示某日00:00:00）',
  `contact_name` bigint(20) unsigned NOT NULL COMMENT '联系人姓名',
  `contact_telephone` bigint(20) unsigned NOT NULL COMMENT '联系电话（仅管理员可见）',
  `contact_address` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '联系地址',
  `contact_postcode` int(10) unsigned NOT NULL COMMENT '联系地址邮政编码',
  `self_organization` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所在单位（自由设计师随意填写，可为空，组织设计师创建时自动填写）',
  `self_position` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '担任职位',
  `area_belong_id` int(10) unsigned NOT NULL COMMENT '所属地区id',
  `area_working_id` int(10) unsigned NOT NULL COMMENT '工作地区id',
  `self_working_telephone` bigint(20) unsigned NOT NULL COMMENT '工作联系电话（全网公开）',
  `self_working_address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '工作地址',
  `area_serving_id` int(10) unsigned NOT NULL COMMENT '服务地区id',
  `privilege_area_serving` tinyint(3) unsigned NOT NULL COMMENT '服务地区范围权限（0.本市.1.本省.2.全国）',
  `self_introduction` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '自我介绍（不超过250字）',
  `self_expert` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '服务专长（不超过250字）',
  `self_education` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '教育信息的serialize字符串（包含学校、专业、学历、起止年月的数组）',
  `self_work` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '工作信息的serialize字符串（包含公司、职位、工作描述、起止年月的数组）',
  `self_award` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '奖项信息的serialize字符串（包含证书名称、获奖年月、证书照片的完整公网访问路径的数组）',
  `last_modify_level` tinyint(3) unsigned NOT NULL COMMENT '上次修改时的等级（每升一级可修改资料一次）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `last_modify_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上次修改时间',
  `last_active_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后活跃时间',
  `point_focus` float unsigned DEFAULT 0 COMMENT '设计师关注度',
  `point_experience` float unsigned DEFAULT 0 COMMENT '设计师经验值',
  `count_visit` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '访问数',
  `count_praise` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '点赞数',
  `count_fav` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '收藏数',
  `top_status` tinyint(3) unsigned DEFAULT 0 COMMENT '置顶状态（0.否.1.是）',
  `top_expired_at` timestamp NULL DEFAULT NULL COMMENT '置顶时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师详情表';

-- ----------------------------
-- Table structure for log_designer_details
-- ----------------------------
DROP TABLE IF EXISTS `log_designer_details`;
CREATE TABLE `log_designer_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_designer_id` int(10) unsigned NOT NULL COMMENT '被审核设计师id',
  `content` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '待审核信息的serialize字符串（数组，在字段值前加上字段名）',
  `approve_administrator_id` smallint(5) unsigned NOT NULL COMMENT '审核平台管理员id',
  `is_approved` tinyint(3) NOT NULL DEFAULT 0 COMMENT '审核结果（-1.驳回.0.待审核.1.通过）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '审核备注（驳回理由等）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师资料修改记录（与设计师一对多关联）';

-- ----------------------------
-- Table structure for designer_spaces
-- ----------------------------
DROP TABLE IF EXISTS `designer_spaces`;
CREATE TABLE `designer_spaces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `designer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `space_id` mediumint(6) unsigned NOT NULL COMMENT '擅长空间id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师擅长空间表（一对多）';

-- ----------------------------
-- Table structure for designer_styles
-- ----------------------------
DROP TABLE IF EXISTS `designer_styles`;
CREATE TABLE `designer_styles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `designer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `style_id` mediumint(6) unsigned NOT NULL COMMENT '擅长风格id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师擅长风格表（一对多）';
