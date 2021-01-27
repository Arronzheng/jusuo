-- ----------------------------
-- Table structure for banners
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '适用的品牌id（0表示平台）',
  `position` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '显示位置（0.首页）',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片地址',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '跳转地址',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序（大者靠前）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '状态（0.隐藏.1.显示）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='banner（平台/品牌）';

-- ----------------------------
-- Table structure for search_place_holders
-- ----------------------------
DROP TABLE IF EXISTS `search_place_holders`;
CREATE TABLE `search_place_holders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '适用的品牌id（0表示平台）',
  `position` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '显示位置（0.方案.1.设计师.2.装饰公司.3.产品）',
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文字',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='搜索栏提示文字（平台/品牌）';

-- ----------------------------
-- Table structure for footer_ad_cards
-- ----------------------------
DROP TABLE IF EXISTS `footer_ad_cards`;
CREATE TABLE `footer_ad_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '适用的品牌id（0表示平台）',
  `position` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '显示位置（0.首页）',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片地址',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '跳转地址',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序（大者靠前）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '状态（0.隐藏.1.显示）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='页脚广告卡片（平台/品牌）';

-- ----------------------------
-- Table structure for link_friends
-- ----------------------------
DROP TABLE IF EXISTS `link_friends`;
CREATE TABLE `link_friends` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '适用的品牌id（0表示平台）',
  `text` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '显示文字（不超过25字）',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '跳转地址',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序（大者靠前）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '状态（0.隐藏.1.显示）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='页脚友情链接（平台/品牌）';

-- ----------------------------
-- Table structure for qr_codes
-- ----------------------------
DROP TABLE IF EXISTS `qr_codes`;
CREATE TABLE `qr_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '适用的品牌id（0表示平台）',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '二维码图片地址',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '状态（0.隐藏.1.显示）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='页脚二维码（平台/品牌）';
