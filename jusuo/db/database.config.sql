-- ----------------------------
-- Table structure for param_configs
-- 系统信息 --
-- 设计师/公式：积分、经验值、关注度、星级
-- 组织/公式：关注度、星级
-- 基本信息 --
-- 全局：空间、风格、户型、经营品类（需代码）
-- 设计师：账号类型
-- 装饰公司：服务项目
-- ？设计图：户型类别？
-- 应用信息（部分需分级设置） --
-- 设计师/项目数量：教育经历、工作经历、证书奖项
-- 设计师/字数限制：昵称、工作地址、教育经历、工作经历、证书奖项、自我介绍
-- 设计师/必填：头像、昵称、性别、出生年月日、省市区、工作地址、教育经历、工作经历、证书奖项、自我介绍、联系手机
-- 装饰公司/项目数量：设计案例
-- 装饰公司/字数限制：公司名称、联系人、联系地址、公司地址、公司介绍、店面地址、公司资质、证书奖项、设计案例、服务承诺
-- 装饰公司/必填：公司名称、省市区、联系人、联系电话、联系地址、邮政编码、LOGO、公司地址、公司介绍、店面地址、公司资质、证书奖项、设计案例、服务承诺
-- 销售商/项目数量：店面形象照、近期促销
-- 销售商/字数限制：公司名称、联系人、联系地址、公司地址、商家介绍、服务承诺、店面地址、主页路径
-- 销售商/必填：公司名称、省市区、联系人、联系电话、联系地址、邮政编码、LOGO、公司地址、商家介绍、服务承诺、店面地址、店面形象照、主页路径、近期促销
-- 品牌/项目数量：品牌荣誉、团队建设
-- 品牌/字数限制：公司名称、联系人、联系地址、公司地址、公司规模、品牌理念、品牌荣誉、产品理念、服务理念、团队建设、品牌规划、主页路径
-- 品牌/必填：公司名称、省市区、联系人、联系电话、联系地址、邮政编码、LOGO、公司地址、公司规模、品牌理念、品牌荣誉、产品理念、服务理念、团队建设、品牌规划、主页路径
-- ----------------------------
DROP TABLE IF EXISTS `param_configs`;
CREATE TABLE `param_configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '适用的品牌id（通过平台进行设置的，此值为0）',
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '展示名称',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '识别字符串',
  `value_type` int(10) unsigned DEFAULT 0 COMMENT '值类型',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '内容（可以是serialize字符串）',
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '参数说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `param_configs_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统参数配置（如枚举选项、计算公式中的参数）';
