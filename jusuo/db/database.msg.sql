-- ----------------------------
-- Table structure for msg_system_brands
-- ----------------------------
DROP TABLE IF EXISTS `msg_system_brands`;
CREATE TABLE `msg_system_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（品牌）系统通知';

-- ----------------------------
-- Table structure for msg_system_dealers
-- ----------------------------
DROP TABLE IF EXISTS `msg_system_dealers`;
CREATE TABLE `msg_system_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '销售商id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（销售商）系统通知';

-- ----------------------------
-- Table structure for msg_system_designers
-- ----------------------------
DROP TABLE IF EXISTS `msg_system_designers`;
CREATE TABLE `msg_system_designers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（设计师）系统通知';

-- ----------------------------
-- Table structure for msg_account_brands
-- ----------------------------
DROP TABLE IF EXISTS `msg_account_brands`;
CREATE TABLE `msg_account_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（品牌）账号通知';

-- ----------------------------
-- Table structure for msg_account_dealers
-- ----------------------------
DROP TABLE IF EXISTS `msg_account_dealers`;
CREATE TABLE `msg_account_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '销售商id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（销售商）账号通知';

-- ----------------------------
-- Table structure for msg_account_designers
-- ----------------------------
DROP TABLE IF EXISTS `msg_account_designers`;
CREATE TABLE `msg_account_designers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（设计师）账号通知';

-- ----------------------------
-- Table structure for msg_album_brands
-- ----------------------------
DROP TABLE IF EXISTS `msg_album_brands`;
CREATE TABLE `msg_album_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（品牌）方案通知';

-- ----------------------------
-- Table structure for msg_album_dealers
-- ----------------------------
DROP TABLE IF EXISTS `msg_album_dealers`;
CREATE TABLE `msg_album_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '销售商id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（销售商）方案通知';

-- ----------------------------
-- Table structure for msg_album_designers
-- ----------------------------
DROP TABLE IF EXISTS `msg_album_designers`;
CREATE TABLE `msg_album_designers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（设计师）方案通知';

-- ----------------------------
-- Table structure for msg_product_ceramic_brands
-- ----------------------------
DROP TABLE IF EXISTS `msg_product_ceramic_brands`;
CREATE TABLE `msg_product_ceramic_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（品牌）产品通知';

-- ----------------------------
-- Table structure for msg_product_ceramic_dealers
-- ----------------------------
DROP TABLE IF EXISTS `msg_product_ceramic_dealers`;
CREATE TABLE `msg_product_ceramic_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '销售商id',
  `type` tinyint(3) unsigned DEFAULT 0 COMMENT '类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='（销售商）产品通知';
