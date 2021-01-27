-- ----------------------------
-- Table structure for privilege_platforms
-- ----------------------------
DROP TABLE IF EXISTS `privilege_platforms`;
CREATE TABLE `privilege_platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父权限id',
  `level` int(10) unsigned DEFAULT 0 COMMENT '层级',
  `display_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '识别字符串',
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '权限描述',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `shown` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '是否显示（0.否.1.是）',
  `is_super_admin` tinyint(3) unsigned DEFAULT 0 COMMENT '是否超级管理员专用（0.否.1.是）',
  `is_menu` tinyint(3) unsigned DEFAULT 0 COMMENT '是否显示在菜单中',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '后台页面路径，即url中/admin/之后的路径',
  `method` tinyint(3) unsigned DEFAULT 0 COMMENT '提交方式（0.GET.1.POST），用于鉴权',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `privilege_platforms_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='平台后台管理权限';

-- ----------------------------
-- Table structure for role_platforms
-- ----------------------------
DROP TABLE IF EXISTS `role_platforms`;
CREATE TABLE `role_platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '展示名称',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '识别字符串',
  `description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色说明',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '最后修改时间',
  `is_super_admin` int(10) unsigned DEFAULT 0 COMMENT '是否超级管理员角色（0.否.1.是，只能有一个）',
  `created_by_administrator_id` int(10) unsigned DEFAULT 0 COMMENT '创建此角色的管理员id（超级管理员此值为0，表示系统创建）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_platforms_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='平台后台管理角色（普通管理员也可创建角色，必须选择其权限树内权限才能保存成功）';

-- ----------------------------
-- Table structure for role_privilege_platforms
-- ----------------------------
DROP TABLE IF EXISTS `role_privilege_platforms`;
CREATE TABLE `role_privilege_platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `privilege_id` int(11) NOT NULL COMMENT '权限id（最多只能选择创建者角色范围内的权限）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='平台后台管理角色权限';

-- ----------------------------
-- Table structure for privilege_brands
-- ----------------------------
DROP TABLE IF EXISTS `privilege_brands`;
CREATE TABLE `privilege_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父权限id',
  `level` int(10) unsigned DEFAULT 0 COMMENT '层级',
  `display_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '识别字符串',
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '权限描述',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `shown` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '是否显示（0.否.1.是）',
  `is_super_admin` tinyint(3) unsigned DEFAULT 0 COMMENT '是否超级管理员专用（0.否.1.是）',
  `is_menu` tinyint(3) unsigned DEFAULT 0 COMMENT '是否显示在菜单中',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '后台页面路径，即url中/admin/之后的路径',
  `method` tinyint(3) unsigned DEFAULT 0 COMMENT '提交方式（0.GET.1.POST），用于鉴权',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `privilege_brands_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌后台管理权限';

-- ----------------------------
-- Table structure for role_brands
-- ----------------------------
DROP TABLE IF EXISTS `role_brands`;
CREATE TABLE `role_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '品牌id',
  `display_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '展示名称',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '识别字符串（须增加品牌id作为前缀）',
  `description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色说明',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '最后修改时间',
  `is_super_admin` int(10) unsigned DEFAULT 0 COMMENT '是否超级管理员角色（0.否.1.是，只能有一个）',
  `created_by_administrator_id` int(10) unsigned DEFAULT 0 COMMENT '创建此角色的管理员id（超级管理员此值为0，表示系统创建）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_brands_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌后台管理角色（普通管理员也可创建角色，必须选择其权限树内权限才能保存成功）';

-- ----------------------------
-- Table structure for role_privilege_brands
-- ----------------------------
DROP TABLE IF EXISTS `role_privilege_brands`;
CREATE TABLE `role_privilege_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `privilege_id` int(11) NOT NULL COMMENT '权限id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌后台管理角色权限';