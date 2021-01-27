-- ----------------------------
-- Table structure for organization_brands
-- ----------------------------
DROP TABLE IF EXISTS `organization_brands`;
CREATE TABLE `organization_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `short_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '简称',
  `organization_account` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号（数字+字母组合，所有组织内唯一）',
  `product_category` tinyint(3) unsigned NOT NULL COMMENT '经营产品类别',
  `quota_dealer_lv1` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '一级经销商开通数量限额',
  `quota_dealer_lv2` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '二级经销商开通数量限额',
  `quota_designer_brand` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '品牌设计师开通数量限额',
  `quota_designer_dealer` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '经销商设计师开通数量限额',
  `quota_dealer_lv1_used` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '一级经销商已开通数量',
  `quota_dealer_lv2_used` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '二级经销商已开通数量',
  `quota_designer_brand_used` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '品牌设计师已开通数量',
  `quota_designer_dealer_used` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '经销商设计师已开通数量',
  `expired_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '账号有效期',
  `contact_name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `contact_telephone` bigint(20) unsigned NOT NULL COMMENT '联系电话',
  `contact_address` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '联系地址',
  `contact_zip_code` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮政编码',
  `create_administrator_id` smallint(5) unsigned NOT NULL COMMENT '开通账号的平台管理员id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` int(5) unsigned DEFAULT 200 COMMENT '状态（000未审核，200正常，100禁用）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌商';

-- ----------------------------
-- Table structure for log_brand_certifications
-- ----------------------------
DROP TABLE IF EXISTS `log_brand_certifications`;
CREATE TABLE `log_brand_certifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_brand_id` int(10) unsigned NOT NULL COMMENT '被审核品牌id',
  `content` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '待审核信息的serialize字符串（数组，在字段值前加上字段名）',
  `approve_administrator_id` smallint(5) unsigned NOT NULL COMMENT '审核平台管理员id',
  `is_approved` tinyint(3) NOT NULL DEFAULT 0 COMMENT '审核结果（-1.驳回.0.待审核.1.通过）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '审核备注（驳回理由等）',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌商认证记录（与品牌商一对多关联）';

-- ----------------------------
-- Table structure for certification_brands
-- ----------------------------
DROP TABLE IF EXISTS `certification_brands`;
CREATE TABLE `certification_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `legal_person_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人姓名',
  `code_idcard` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证号',
  `expired_at_idcard` timestamp NOT NULL COMMENT '法人身份证到期日期（精确至日，显示某年某月某日00:00:00）',
  `code_license` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '统一社会信用代码',
  `url_idcard_front` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证正面的完整的公网访问地址',
  `url_idcard_back` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证背面的完整的公网访问地址',
  `url_license` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '营业执照的完整的公网访问地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌商认证详情（与品牌商一对一关联）';

-- ----------------------------
-- Table structure for log_detail_brands
-- ----------------------------
DROP TABLE IF EXISTS `log_detail_brands`;
CREATE TABLE `log_detail_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_brand_id` int(10) unsigned NOT NULL COMMENT '被审核品牌id',
  `content` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '待审核信息的serialize字符串（数组，在字段值前加上字段名）',
  `approve_administrator_id` smallint(5) unsigned NOT NULL COMMENT '审核平台管理员id',
  `is_approved` tinyint(3) NOT NULL DEFAULT 0 COMMENT '审核结果（-1.驳回.0.待审核.1.通过）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '审核备注（驳回理由等）',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌商资料审核记录（与品牌商一对多关联）';

-- ----------------------------
-- Table structure for detail_brands
-- ----------------------------
DROP TABLE IF EXISTS `detail_brands`;
CREATE TABLE `detail_brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `url_avatar` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像（LOGO）的完整公网访问地址',
  `brand_domain` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号子域名（字母+数字组合）',
  `product_category` tinyint(3) unsigned NOT NULL COMMENT '经营产品类别',
  `area_belong_id` int(10) unsigned NOT NULL COMMENT '所在地区id',
  `area_serving_id` int(10) unsigned NOT NULL COMMENT '服务地区id',
  `privilege_area_serving` tinyint(3) unsigned NOT NULL COMMENT '服务地区范围权限（0.本市.1.本省.2.全国）',
  `self_establish_time` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '建立时间（精确至日，显示某年某月某日00:00:00）',
  `self_staff_scale` int(10) unsigned NOT NULL COMMENT '人员规模',
  `self_capital_scale` int(10) unsigned NOT NULL COMMENT '注册资金规模',
  `self_capital_address` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '总部地址',
  `self_longitude` double(10,6) NOT NULL COMMENT '总部的经度位置（配合联系地址导航用）',
  `self_latitude` double(10,6) NOT NULL COMMENT '总部的经度位置（配合联系地址导航用）',
  `self_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '总部地址',
  `self_address_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '总部地址的坐标点标题（配合联系地址导航用）',
  `self_address_content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '总部地址的坐标点内容（配合联系地址导航用）',
  `self_introduction` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌介绍（不超过500字）',
  `self_introduction_scale` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司规模（不超过200字）',
  `self_introduction_brand` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌理念（不超过200字）',
  `self_introduction_product` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '产品理念（不超过200字）',
  `self_introduction_service` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '服务理念（不超过200字）',
  `self_introduction_plan` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌规划（不超过200字）',
  `self_award` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌荣誉的serialize字符串（包含多张照片的完整公网访问路径的数组）',
  `self_staff` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '团队建设的serialize字符串（包含多张照片的完整公网访问路径的数组）',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `point_focus` float unsigned DEFAULT 0 COMMENT '关注度',
  `is_top` tinyint(3) unsigned DEFAULT 0 COMMENT '是否置顶',
  `is_top_time` timestamp NULL DEFAULT NULL COMMENT '置顶时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='品牌商详情（与品牌商一对一关联）';

-- ----------------------------
-- Table structure for organization_dealers
-- ----------------------------
DROP TABLE IF EXISTS `organization_dealers`;
CREATE TABLE `organization_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `p_brand_id` bigint(20) unsigned NOT NULL COMMENT '所属品牌id',
  `area_belong_id` int(10) unsigned NOT NULL COMMENT '所在地区id',
  `p_dealer_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '所属上级经销商id（如0表示无上级经销商）',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `short_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '简称',
  `organization_account` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号ID（数字+字母组合，所有组织内唯一）',
  `quota_designer` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '设计师开通数量限额',
  `quota_designer_used` mediumint(6) unsigned NOT NULL DEFAULT 0 COMMENT '设计师已开通数量',
  `expired_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '账号有效期',
  `contact_name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `contact_telephone` bigint(20) unsigned NOT NULL COMMENT '联系电话',
  `contact_address` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '联系地址',
  `contact_zip_code` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮政编码',
  `create_administrator_id` smallint(5) unsigned NOT NULL COMMENT '开通账号的品牌管理员id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `last_active_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后活跃时间',
  `status` int(5) unsigned DEFAULT 200 COMMENT '状态（000未审核，200正常，100禁用）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='经销商';

-- ----------------------------
-- Table structure for log_brand_certifications
-- ----------------------------
DROP TABLE IF EXISTS `log_dealer_certifications`;
CREATE TABLE `log_dealer_certifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_brand_id` int(10) unsigned NOT NULL COMMENT '被审核品牌id',
  `content` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '待审核信息的serialize字符串（数组，在字段值前加上字段名）',
  `approve_administrator_id` smallint(5) unsigned NOT NULL COMMENT '审核平台管理员id',
  `is_approved` tinyint(3) NOT NULL DEFAULT 0 COMMENT '审核结果（-1.驳回.0.待审核.1.通过）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '审核备注（驳回理由等）',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='销售商认证记录（与品牌商一对多关联）';

-- ----------------------------
-- Table structure for certification_dealers
-- ----------------------------
DROP TABLE IF EXISTS `certification_dealers`;
CREATE TABLE `certification_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL COMMENT '品牌id',
  `legal_person_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人姓名',
  `code_idcard` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证号',
  `expired_at_idcard` timestamp NOT NULL COMMENT '法人身份证到期日期（精确至日，显示某年某月某日00:00:00）',
  `code_license` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '统一社会信用代码',
  `url_idcard_front` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证正面的完整的公网访问地址',
  `url_idcard_back` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '法人身份证背面的完整的公网访问地址',
  `url_license` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '营业执照的完整的公网访问地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='销售商认证详情（与品牌商一对一关联）';

-- ----------------------------
-- Table structure for log_detail_dealers
-- ----------------------------
DROP TABLE IF EXISTS `log_detail_dealers`;
CREATE TABLE `log_detail_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_dealer_id` int(10) unsigned NOT NULL COMMENT '被审核经销商id',
  `content` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '待审核信息的serialize字符串（数组，在字段值前加上字段名）',
  `approve_administrator_id` smallint(5) unsigned NOT NULL COMMENT '审核品牌管理员id',
  `is_approved` tinyint(3) NOT NULL DEFAULT 0 COMMENT '审核结果（-1.驳回.0.待审核.1.通过）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后修改时间',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '审核备注（驳回理由等）',
  `is_read` tinyint(3) unsigned DEFAULT 0 COMMENT '是否已读（0.否.1.是）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='经销商资料认证记录（与经销商一对多关联）';

-- ----------------------------
-- Table structure for detail_dealers
-- ----------------------------
DROP TABLE IF EXISTS `detail_dealers`;
CREATE TABLE `detail_dealers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint(20) unsigned NOT NULL COMMENT '经销商id',
  `url_avatar` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像（LOGO）的完整公网访问地址',
  `dealer_domain` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '账号子域名（字母+数字组合，经销商）',
  `area_serving_id` int(10) unsigned NOT NULL COMMENT '服务地区id',
  `privilege_area_serving` tinyint(3) unsigned NOT NULL COMMENT '服务地区范围权限（0.本市.1.本省.2.全国）',
  `self_longitude` double(10,6) NOT NULL COMMENT '店面的经度位置（配合联系地址导航用）',
  `self_latitude` double(10,6) NOT NULL COMMENT '店面的经度位置（配合联系地址导航用）',
  `self_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '店面地址',
  `self_address_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '店面地址的坐标点标题（配合联系地址导航用）',
  `self_address_content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '店面地址的坐标点内容（配合联系地址导航用）',
  `self_introduction` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商家介绍（不超过500字）',
  `self_photo` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '店面形象照的serialize字符串（包含多张照片的完整公网访问路径的数组）',
  `self_promise` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '服务承诺（不超过500字）',
  `self_promotion` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '最近促销',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `point_focus` float unsigned DEFAULT 0 COMMENT '关注度',
  `is_top` tinyint(3) unsigned DEFAULT 0 COMMENT '是否置顶',
  `is_top_time` timestamp NULL DEFAULT NULL COMMENT '置顶时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='经销商详情（与经销商一对一关联）';
