-- ----------------------------
-- Table structure for administrator_platforms
-- ----------------------------
DROP TABLE IF EXISTS `administrator_platforms`;
CREATE TABLE `administrator_platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_account` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '管理员账号（系统自动生成，不可修改）',
  `login_username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录用户名（可随时修改，但必须唯一）',
  `login_password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录密码，使用bcrypt',
  `login_mobile` bigint(20) unsigned NOT NULL COMMENT '登录手机号',
  `login_wx_openid` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '微信openid（用于PC端扫码登录/移动端微信授权登录）',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色id',
  `created_by_administrator_id` int(10) unsigned DEFAULT 0 COMMENT '创建此角色的管理员id（超级管理员此值为0，表示系统创建）',
  `last_active_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '最后活跃时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `realname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '真实姓名',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '性别（0.未知.1.男.2.女）',
  `self_department` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '所属部门',
  `self_position` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '担任职位',
  `status` int(5) unsigned DEFAULT 200 COMMENT '状态（200正常，100禁用）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `administrator_platforms_login_account` (`login_account`) USING BTREE,
  UNIQUE KEY `administrator_platforms_login_username` (`login_username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='平台后台管理员（创建后不能删除只能禁用）';

-- ----------------------------
-- Table structure for administrator_brands
-- ----------------------------
DROP TABLE IF EXISTS `administrator_brands`;
CREATE TABLE `administrator_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_account` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '管理员账号（系统自动生成，不可修改）',
  `login_username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录用户名（可随时修改，但必须唯一）',
  `login_password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录密码，使用bcrypt',
  `login_mobile` bigint(20) unsigned NOT NULL COMMENT '登录手机号',
  `login_wx_openid` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '微信openid（用于PC端扫码登录/移动端微信授权登录）',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色id',
  `created_by_administrator_id` int(10) unsigned DEFAULT 0 COMMENT '创建此角色的管理员id（超级管理员此值为0，表示系统创建）',
  `last_active_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '最后活跃时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `realname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '真实姓名',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '性别（0.未知.1.男.2.女）',
  `self_department` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '所属部门',
  `self_position` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '担任职位',
  `status` int(5) unsigned DEFAULT 200 COMMENT '状态（200正常，100禁用）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `administrator_brands_login_account` (`login_account`) USING BTREE,
  UNIQUE KEY `administrator_brands_login_username` (`login_username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌后台管理员（创建后不能删除只能禁用）';