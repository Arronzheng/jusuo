-- ----------------------------
-- Table structure for albums
-- ----------------------------
DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `designer_id` bigint(20) unsigned NOT NULL COMMENT '设计师id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '类别（0.高清图.1.酷家乐方案源）',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '方案标题（不超过50字）',
  `photo_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '封面图地址',
  `address_province_id` int(10) unsigned NOT NULL COMMENT '所在省id',
  `address_city_id` int(10) unsigned NOT NULL COMMENT '所在市id',
  `address_area_id` int(10) unsigned NOT NULL COMMENT '所在区id',
  `address_street` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所在镇或街道',
  `address_residential_quarter` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所在小区',
  `address_building` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所在楼栋',
  `address_layout_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所在户型号',
  `count_area` float unsigned NOT NULL DEFAULT 0 COMMENT '建筑面积',
  `description_design` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '设计说明（200字以内）',
  `description_layout` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '户型说明（200字以内）',
  `coin_use` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '使用所需积分',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `count_section` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '章节数',
  `count_visit` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '访问数',
  `count_praise` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '点赞数',
  `count_fav` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '收藏数',
  `count_use` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '使用数（高清图指下载数，方案源指复制数）',
  `point_focus` float unsigned NOT NULL DEFAULT 0 COMMENT '关注度',
  `top_status_platform` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '平台置顶状态',
  `top_status_brand` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '品牌置顶状态',
  `top_status_dealer` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '销售商置顶状态',
  `top_status_designer` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '设计师置顶状态',
  `top_expired_at_platform` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '平台置顶状态失效时间',
  `top_expired_at_brand` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '品牌置顶状态失效时间',
  `top_expired_at_dealer` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '销售商置顶状态失效时间',
  `top_expired_at_designer` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '设计师置顶状态失效时间',
  `weight_sort` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '排序权重',
  `is_representative work` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '是否代表作（0.不是.非0.大者靠前）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.不通过.1.正在审核.2.已通过.3.显示.4.下架）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计方案（一经创建，不可删除）';

-- ----------------------------
-- Table structure for album_spaces
-- ----------------------------
DROP TABLE IF EXISTS `album_spaces`;
CREATE TABLE `album_spaces` (
  `album_id` bigint(20) unsigned NOT NULL COMMENT '设计方案id',
  `space_id` mediumint(6) unsigned NOT NULL COMMENT '空间id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计方案空间表（一对多）';

-- ----------------------------
-- Table structure for album_styles
-- ----------------------------
DROP TABLE IF EXISTS `album_styles`;
CREATE TABLE `album_styles` (
  `album_id` bigint(20) unsigned NOT NULL COMMENT '设计方案id',
  `style_id` mediumint(6) unsigned NOT NULL COMMENT '风格id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师擅长风格表（一对多）';

-- ----------------------------
-- Table structure for album_sections
-- ----------------------------
DROP TABLE IF EXISTS `album_sections`;
CREATE TABLE `album_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` bigint(20) unsigned NOT NULL COMMENT '设计方案id',
  `title` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题（不超过10字）',
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述（不超过500字）',
  `count_area` float unsigned NOT NULL DEFAULT 0 COMMENT '面积',
  `photos` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配图的serialize字符串（photo_url/type/link，后两者可为空）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计方案章节';

-- ----------------------------
-- Table structure for album_section_product_ceramics
-- ----------------------------
DROP TABLE IF EXISTS `album_section_product_ceramics`;
CREATE TABLE `album_section_product_ceramics` (
  `album_id` bigint(20) unsigned NOT NULL COMMENT '设计方案id（冗余字段）',
  `album_section_id` bigint(20) unsigned NOT NULL COMMENT '设计方案章节id',
  `product_ceramic_id` bigint(20) unsigned NOT NULL COMMENT '瓷砖产品id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计方案关联产品';

-- ----------------------------
-- Table structure for album_comments
-- ----------------------------
DROP TABLE IF EXISTS `album_comments`;
CREATE TABLE `album_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '设计方案id',
  `designer_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '发表评论的设计师id',
  `target_comment_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '被跟的评论的id（如0表示非跟评）',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '二维码图片地址',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '状态（0.屏蔽.1.显示）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计方案评论';

-- ----------------------------
-- Table structure for fav_albums
-- ----------------------------
DROP TABLE IF EXISTS `fav_albums`;
CREATE TABLE `fav_albums` (
  `album_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '设计方案id',
  `designer_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '收藏该方案的设计师id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计方案收藏';

-- ----------------------------
-- Table structure for fav_products
-- ----------------------------
DROP TABLE IF EXISTS `fav_products`;
CREATE TABLE `fav_products` (
  `product_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '产品id',
  `designer_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '收藏该产品的设计师id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='产品收藏';

-- ----------------------------
-- Table structure for fav_designers
-- ----------------------------
DROP TABLE IF EXISTS `fav_designers`;
CREATE TABLE `fav_designers` (
  `target_designer_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '设计师id',
  `designer_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '关注该设计师的设计师id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设计师关注';

-- ----------------------------
-- Table structure for product_ceramics
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramics`;
CREATE TABLE `product_ceramics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(10) unsigned NOT NULL COMMENT '所属品牌id',
  `series_id` int(10) unsigned NOT NULL COMMENT '所属系列id',
  `spec_id` int(10) unsigned NOT NULL COMMENT '所属规格id',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '编码（按规则自动生成）',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `photo_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '封面图地址',
  `photo_product` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '产品图的serialize字符串',
  `photo_practicality` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '实物图的serialize字符串',
  `key_technology` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '核心工艺',
  `price_way` tinyint(3) unsigned NOT NULL COMMENT '默认定价方式（0.统一.1.浮动.2.渠道.3.不允许定价）',
  `physical_chemical_property` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '理化性能的serialize字符串（性能+优势，多项）',
  `function_feature` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '功能特征的serialize字符串（性能+优势，多项）',
  `customer_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '顾客价值（250字）',
  `photo_video` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '产品视频的serialize字符串',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `count_visit` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '访问数',
  `count_fav` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '收藏数',
  `point_focus` float unsigned NOT NULL DEFAULT 0 COMMENT '关注度',
  `weight_sort` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '排序权重',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.不通过.1.正在审核.2.已通过.3.显示.4.下架）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖（一经创建，不可删除）';

-- ----------------------------
-- Table structure for log_product_ceramics
-- ----------------------------
DROP TABLE IF EXISTS `log_product_ceramics`;
CREATE TABLE `log_product_ceramics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_product_id` int(10) unsigned NOT NULL COMMENT '被审核产品id',
  `content` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '待审核信息的serialize字符串（数组，在字段值前加上字段名）',
  `approve_administrator_id` smallint(5) unsigned NOT NULL COMMENT '审核平台管理员id',
  `is_approved` tinyint(3) NOT NULL DEFAULT 0 COMMENT '审核结果（-1.驳回.0.待审核.1.通过）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '审核备注（驳回理由等）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖产品资料修改记录（与设计师一对多关联）';

-- ----------------------------
-- Table structure for product_ceramic_series
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_series`;
CREATE TABLE `product_ceramic_series` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题（不超过10字）',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述（不超过200字）',
  `photos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片网址',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖系列';

-- ----------------------------
-- Table structure for product_ceramic_apply_categories
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_apply_categories`;
CREATE TABLE `product_ceramic_apply_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `apply_category_id` mediumint(10) unsigned NOT NULL COMMENT '应用类别id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖应用类别（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_technology_categories
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_technology_categories`;
CREATE TABLE `product_ceramic_technology_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `technology_category_id` mediumint(10) unsigned NOT NULL COMMENT '工艺类别id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖工艺类别（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_surface_features
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_surface_features`;
CREATE TABLE `product_ceramic_surface_features` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `surface_feature_id` mediumint(10) unsigned NOT NULL COMMENT '表面特征id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖表面特征（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_styles
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_styles`;
CREATE TABLE `product_ceramic_styles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `style_id` mediumint(10) unsigned NOT NULL COMMENT '风格id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖适用风格（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_colors
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_colors`;
CREATE TABLE `product_ceramic_colors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `color_id` mediumint(10) unsigned NOT NULL COMMENT '色系id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖色系（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_accessories
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_accessories`;
CREATE TABLE `product_ceramic_accessories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '编码（不超过25字）',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称（不超过25字）',
  `photo` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配件图的serialize字符串',
  `spec_length` int(10) unsigned NOT NULL COMMENT '规格长度（毫米）',
  `spec_width` int(10) unsigned NOT NULL COMMENT '规格宽度（毫米）',
  `technology` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '加工工艺（不超过250字）',
  `application_note` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '应用说明（不超过250字）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖配件（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_collocations
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_collocations`;
CREATE TABLE `product_ceramic_collocations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `collocation_id` bigint(20) unsigned NOT NULL COMMENT '搭配产品id',
  `photo` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '搭配效果图的serialize字符串',
  `note` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '搭配说明（不超过250字）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖搭配产品（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_spaces
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_spaces`;
CREATE TABLE `product_ceramic_spaces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题（不超过50字）',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '效果图地址',
  `note` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '搭配说明（不超过250字）',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（0.禁用.1.正常）',
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖空间应用（一对多）';

-- ----------------------------
-- Table structure for product_ceramic_authorizations
-- ----------------------------
DROP TABLE IF EXISTS `product_ceramic_authorizations`;
CREATE TABLE `product_ceramic_authorizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL COMMENT '产品id',
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '销售商id',
  `life_phase_id` mediumint(10) unsigned NOT NULL COMMENT '产品生命周期id',
  `price_way` tinyint(3) unsigned NOT NULL COMMENT '定价方式（0.统一.1.浮动.2.渠道.3.不允许定价）',
  `price` float unsigned NOT NULL DEFAULT 0 COMMENT '价格',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` tinyint(3) unsigned NOT NULL DEFAULT 200 COMMENT '状态（000.下架.100.正常.200.新授权）',
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='瓷砖产品授权（不同一级销售商分别授权）';

-- ----------------------------
-- Table structure for product_qas
-- ----------------------------
DROP TABLE IF EXISTS `product_qas`;
CREATE TABLE `product_qas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '产品id',
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '问题',
  `question_designer_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '提问的设计师id',
  `answer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回答',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '状态（0.屏蔽.1.显示）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `answered_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '回答时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='产品问答';
