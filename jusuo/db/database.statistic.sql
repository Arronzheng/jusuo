-- ----------------------------
-- Table structure for statistic_account_brands
-- ----------------------------
DROP TABLE IF EXISTS `statistic_account_brands`;
CREATE TABLE `statistic_account_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '品牌id',
  `count_product` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '产品数',
  `count_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案数',
  `count_designer` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '直属设计师数',
  `count_designer_lv1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '一级销售商设计师数',
  `count_designer_lv2` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '二级销售商设计师数',
  `count_dealer_lv1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '一级销售商数',
  `count_dealer_lv2` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '二级销售商数',
  `count_product_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增产品数',
  `count_album_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增方案数',
  `count_designer_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新开通直属设计师数',
  `count_designer_lv1_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增一级销售商设计师数',
  `count_designer_lv2_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增二级销售商设计师数',
  `count_dealer_lv1_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增一级销售商数',
  `count_dealer_lv2_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增二级销售商数',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(3) unsigned DEFAULT 200 COMMENT '状态（1正常，0失效）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌信息统计（一对多，每天统计一次）';

-- ----------------------------
-- Table structure for statistic_account_dealers
-- ----------------------------
DROP TABLE IF EXISTS `statistic_account_dealers`;
CREATE TABLE `statistic_account_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '销售商id',
  `count_product` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上线产品数',
  `count_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案数',
  `count_designer` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '直属设计师数',
  `count_product_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增上线产品数',
  `count_album_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增方案数',
  `count_designer_increase` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新开通直属设计师数',
  `count_product_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上线产品数',
  `count_album_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案数',
  `count_designer_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '直属设计师数',
  `count_product_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上线产品数',
  `count_album_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案数',
  `count_designer_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '直属设计师数',
  `count_product_increase_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增上线产品数',
  `count_album_increase_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增方案数',
  `count_designer_increase_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新开通直属设计师数',
  `count_product_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上线产品数',
  `count_album_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案数',
  `count_designer_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '直属设计师数',
  `count_product_increase_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增上线产品数',
  `count_album_increase_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新增方案数',
  `count_designer_increase_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '新开通直属设计师数',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(3) unsigned DEFAULT 200 COMMENT '状态（1正常，0失效）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='销售商信息统计（一对多，每天统计一次）';

-- ----------------------------
-- Table structure for statistic_designers
-- ----------------------------
DROP TABLE IF EXISTS `statistic_designers`;
CREATE TABLE `statistic_designers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '设计师id',
  `belong_brand_id` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '所属品牌id',
  `belong_dealer_id` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '所属销售商id',
  `count_upload_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上传的方案数',
  `count_top_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案被置顶次数',
  `count_fav_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的方案数',
  `count_praise_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '点赞的方案数',
  `count_download_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '下载的方案数',
  `count_copy_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '复制的方案数',
  `count_fav_designer` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的设计师数',
  `count_upload_album_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上传的方案数',
  `count_top_album_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案被置顶次数',
  `count_fav_album_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的方案数',
  `count_praise_album_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '点赞的方案数',
  `count_download_album_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '下载的方案数',
  `count_copy_album_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '复制的方案数',
  `count_fav_designer_day_1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的设计师数',
  `count_upload_album_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上传的方案数',
  `count_top_album_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案被置顶次数',
  `count_fav_album_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的方案数',
  `count_praise_album_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '点赞的方案数',
  `count_download_album_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '下载的方案数',
  `count_copy_album_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '复制的方案数',
  `count_fav_designer_day_7` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的设计师数',
  `count_upload_album_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '上传的方案数',
  `count_top_album_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案被置顶次数',
  `count_fav_album_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的方案数',
  `count_praise_album_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '点赞的方案数',
  `count_download_album_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '下载的方案数',
  `count_copy_album_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '复制的方案数',
  `count_fav_designer_day_30` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '关注的设计师数',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(3) unsigned DEFAULT 200 COMMENT '状态（1正常，0失效）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师信息统计（一对多，每天统计一次）';

-- ----------------------------
-- Table structure for statistic_albums
-- ----------------------------
DROP TABLE IF EXISTS `statistic_albums`;
CREATE TABLE `statistic_albums` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '设计师id',
  `belong_brand_id` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '所属品牌id',
  `belong_dealer_id` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '所属销售商id',
  `count_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案数',
  `count_album_top` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案置顶次数',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(3) unsigned DEFAULT 200 COMMENT '状态（1正常，0失效）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师信息统计（一对多，每天统计一次）';

-- ----------------------------
-- Table structure for statistic_product__ceramics
-- ----------------------------
DROP TABLE IF EXISTS `statistic_product__ceramics`;
CREATE TABLE `statistic_product__ceramics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '产品id',
  `count_album` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案数',
  `count_album_top` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '方案置顶次数',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(3) unsigned DEFAULT 200 COMMENT '状态（1正常，0失效）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师信息统计（一对多，每天统计一次）';
