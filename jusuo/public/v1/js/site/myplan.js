var list=['我的方案','产品列表','我的统计','收藏关注','消息通知','个人中心','安全中心']
var navactive=0;

var fangan={"list":[{"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代","90㎡","90㎡"],
        "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"2019年12月12日 21:00", "status":"正在审核"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"2019年12月12日 21:00", "status":"已通过"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"2019年12月12日 21:00", "status":"已隐藏"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"2019年12月12日 21:00", "status":"不通过"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"2019年12月12日 21:00", "status":"已下架"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
        {"image":"images/designer_xq/8.png","title":"现代简约两居", "label":["90㎡","一厅两房","简约现代"],
            "like":135,"shoucang":68,"view":219,"fuzhi":35,"dofuzhi":false,"dolike":false,"doshoucang":false,"time":"3分钟前", "status":"编辑中"},
    ]};
//产品列表的表格
var tc_table={
    "colname":["名称","应用类别","工艺类别","色系","规格","产品状态","产品结构","缩略图","收藏"],
    "coldata":[{"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
        "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":true},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":true},]
}


//我的统计
var tongjinav=0
//top5方案浏览量
var top5view={
    "list":[{"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false}]
}
//top5方案收藏量
var top5view1={
    "list":[{"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false}]
}

//top5产品浏览量
var top5viewpro={
    "list":[{"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false}]
}
//top5产品收藏量
var top5viewpro1={
    "list":[{"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false},
        {"image":"images/cpk/7.png","name":"马桶马桶马桶马桶马桶","look":219,"collected":88, "collect":false}]
}

var noticenav=0;
//消息通知——系统消息
var notice={
    "list":[{"label":"系统通知","time":"15分钟前","content":["系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]},
        {"label":"方案通知","time":"40分钟前","content":["恭喜您！您的方案《保利简约设计》已通过审核，成功发布！"]},
        {"label":"账号通知","time":"40分钟前","content":["抱歉！您的方案《鳌园中式设计》未通过审核，请修改后再发布！","修改意见：XXXXXXXX！"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]}],
    "list1":[{"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false}],
    "list2":[{"name":"薛华少1","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少2","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少3","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少4","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少5","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少6","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少7","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少8","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少9","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"}]
}

var shoucangnav=0;
var project={
        "list":[
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,
                "identity":true,"hot":true,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
            {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        ],
        "list1":[
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"马桶马桶马桶马桶马桶麻烦玛法","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
            {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false}
        ],
    "list2":[
        {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":true,
    "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":true,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":true,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":false,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":false,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":false,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":false,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":true,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":true,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":false,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":false,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]},
    {"perimage":"images/designer/1.png","name":"周杰伦","experience":"金牌","position":"广东省佛山市","style":"简约现代、新中式","fans":42,"famgan":63,"guanzhu":false,
        "design":[{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"},{"images":"images/designer_xq/8.png","name":"碧桂园二居室设计"}]
    }]
};
//个人中心
var personnav=0;
//var persondetail={"name":"岑岑岑生","id":"abc1453224","image":"images/designer/1.png","sex":"男","identity":0} //identity:0时为未认证，1为审核中,2为已认证,3为不通过
var persondetail=[];
var realnameDetail = [];
//设计师认证
var userInfo = [];
var cities = [];
var districts = [];

var id_front=["images/designer_xq/8.png"]//身份证正面
var id_back=["images/designer_xq/8.png"]//身份证反面
var designidentity=0;//0为未认证，1为审核中
var educatenumber=0;//添加教育信息数量
var worknumber=0;//添加工作经验数量
var prizenumber=0;//添加奖项数量

//收藏关注
//方案
var fav_albums = [];
var fav_products = [];
var fav_designer = [];

//方案列表
var album_filter_types = [];
var album_page_list = [];

//产品列表
var product_filter_types = [];
var product_brand_data = [];
var product_page_list = [];

//通知
var comment = [];
var fav_notify = [];
var sys_notify = [];

//统计
var chart_top_product_visit = [];
var chart_top_product_collect = [];

//安全中心
var safenav=0;
var weixinid="allaaa14";

function showPersonTabs(){
    var str=""
    var a=["个人资料","实名认证","设计师认证"]
    for(var i=0;i<a.length;i++){
        if(personnav==i){
            str+="<div class='designtitle' id='f"+i+"' onclick='personchangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='f"+i+"' onclick='personchangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#persondaohang").append(str);


}

function showLeftTabs(){
    
    ajax_get('/center/user/is_realname',function(res){
        console.log(res);
        if(res.status){
            var is_realname = res.data.is_realname;
            if(is_realname){
                var str="";
                for(var i=0;i<list.length;i++){
                    if(navactive==i){
                        str+="<div class='nav_title1' id='navtitle"+i+"' onclick='changenav("+i+")'>"
                        str+="<span class='nav_text1' id='navtext"+i+"'>"+list[i]+"</span>"
                    }else{
                        str+="<div class='nav_title' onclick='changenav("+i+")' id='navtitle"+i+"'>"
                        str+="<span class='nav_text' id='navtext"+i+"'>"+list[i]+"</span>"
                    }
                    str+="</div>"
                }
                $("#daohangblock").append(str);
                showModule(0)
            }else{
                //未完成实名
                var str="";
                for(var i=0;i<list.length;i++){
                    if(navactive==i){
                        str+="<div class='nav_title1' id='navtitle"+i+"' onclick='changenav("+i+")' >"
                        str+="<span class='nav_text1' id='navtext"+i+"'>"+list[i]+"</span>"
                    }else{
                        str+="<div class='nav_title' onclick='changenav("+i+")' id='navtitle"+i+"'>"
                        str+="<span class='nav_text' id='navtext"+i+"'>"+list[i]+"</span>"
                    }
                    str+="</div>"
                }
                $("#daohangblock").append(str);
                $("#navtitle0").hide().removeClass('show');
                $("#navtext0").hide().removeClass('show');
                $("#navtitle1").hide().removeClass('show');
                $("#navtext1").hide().removeClass('show');
                $("#navtitle2").hide().removeClass('show');
                $("#navtext2").hide().removeClass('show');
                $("#navtitle3").hide().removeClass('show');
                $("#navtext3").hide().removeClass('show');


                //$("#navtitle5").trigger('click');
                showModule(5);

            }
        }
    });
}

function showSafeTabs(){
    var str=""
    var a=["修改密码","修改手机","绑定微信"]
    for(var i=0;i<a.length;i++){
        if(safenav==i){
            str+="<div class='designtitle' id='e"+i+"' onclick='safechangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='e"+i+"' onclick='safechangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#safedaohang").append(str);

    $.get('/center/get_user_phone',function(res){
        if(res.status == 1){
            //手机号码的显示
            var tel = document.getElementById('tel');
            var phone=res.data.phone;
            var hidephone=phone[0]+phone[1]+phone[2]+"****"+phone[9]+phone[10]
            tel.innerText = hidephone;

            $("#by_phone_phone").val(phone);
        }
    });
}

function getUserInfo(){
    $("#f0content").html('');
    $.get('/center/user_info/get',function(res){
        if(res.status == 1){
            persondetail = res.data;
            console.log(persondetail);
            //个人资料
            var str="";
            str+="<div class='pd_imageview'>"
            str+="<div class='pd_image' id='pd_image'>"+"</div>"
            str+="<div class='change_pdcon'>"
            str+="<input id='upload-input' style=' width:82px;height:24px; cursor: pointer;position: absolute; top: 0; left: 0;opacity: 0;' accept='image/*' type='file' onchange='uploadAvatar(this)'/>"
            str+="<input id='avatar_url' type='hidden' value='"+persondetail.url_avatar+"'/>"
            str+="<div class='change_pdimage'>"+"更改头像"+"</div>"
            str+="</div>"
            str+="</div>"
            str+="<div class='pddetail'>"+"用户账号"+"</div>"
            str+="<div class='pdid'>"+persondetail.login_username+"</div>"
            str+="<div class='pddetail'>"+"昵称"+"</div>"
            str+="<input type='text' name='nicheng' id='nickname' class='nc_input' placeholder='请输入昵称' value='"+persondetail.nickname+"'/>"
            str+="<div class='pddetail'>"+"性别"+"</div>"
            str+="<div class='danxuan'>"
            if(persondetail.gender==1){
                str+="<input type='radio' name='gender' id='paixu1' checked value='1'>"
                str+="<label for='paixu1' style='cursor:pointer;'>男</label>"
                str+="<input type='radio' name='gender' id='paixu2' value='2'>"
                str+="<label for='paixu2' style='cursor:pointer;'>女</label>"
            }else{
                str+="<input type='radio' name='gender' id='paixu1' value='1'>"
                str+="<label for='paixu1' style='cursor:pointer;'>男</label>"
                str+="<input type='radio' name='gender' id='paixu2' checked value='2'>"
                str+="<label for='paixu2' style='cursor:pointer;'>女</label>"
            }
            str+="</div>"
            str+="<div class='pddetail'>"+"实名认证"+"</div>"
            if(persondetail.approve_realname==1){
                str+="<div style='margin-top:10px;height:44px;'>"
                str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-top:2px;'>"+"</span>";
                str+="<span class='identext'>"+"已认证"+"</span>"
                str+="</div>"
            }else{
                str+="<div class='idbutton' onclick='personchangenav(1)'>"+"去认证"+"</div>"
            }
            str+="<div class='saveperson' id='submitUserInfo' onclick='bindSubmitUserInfo()'>"+"保存"+"</div>"
            $("#f0content").append(str);
            if(persondetail.url_avatar != null){
                $("#pd_image").css({"background-image":"url('"+persondetail.url_avatar+"')"});
            }

        }

    });
}

//实名认证界面
function getUserRealnameInfo(){
    $("#f1content").html('');
    //0时为未认证，1为审核中,2为已认证,3为不通过
    ///log_status 0未认证 -1已通过 1待审核 2不通过
    $.get('/center/realname/get_info',function(res){
        console.log(res);
        if(res.status == 1){
            realnameDetail = res.data;
            console.log(realnameDetail);
            //未实名认证
            var str1="";
            str1+="<div class='safelabel'>姓名</div>"
            if(realnameDetail.log_status == 2) {
                str1 += "<input type='text' name='name' id='legal_person_name'  class='tc_input' placeholder='请输入您的姓名' value='" + realnameDetail.log.content.legal_person_name + "'/>"
            }else{
                str1+="<input type='text' name='name' id='legal_person_name'  class='tc_input' placeholder='请输入您的姓名'/>"
            }
            str1+="<div class='safelabel'>身份证号</div>"
            if(realnameDetail.log_status == 2) {
                str1 += "<input type='text' name='id' id='code_idcard'  class='tc_input' placeholder='请输入您的身份证号' value='" + realnameDetail.log.content.code_idcard + "'/>"
            }else{
                str1 += "<input type='text' name='id' id='code_idcard'  class='tc_input' placeholder='请输入您的身份证号'/>"
            }
            str1+="<div class='safelabel'>上传身份证</div>"
            str1+="<div class='idcardimage'>"
            str1+="<div class='idcard_front'>"
            str1+="<div class='uploadimage'>"
            str1+="<div id='preview' class='uploadimage1' onmouseenter='showdel_idfront()'  onmouseleave='showdel_idfront1()'></div>"
            str1+="<div class='id_front'></div>"
            str1+="<div class='del_fengmian' style='display: none;' onclick='del_idfront()'>删除</div>"
            str1+="<input style='width:252px;height:156px; cursor: pointer;position: absolute; top: 0; left: 0;opacity: 0;' type='file' accept='image/*'  id='upload_idcard_front' onchange='uploadIdCardFront(this)'/>"
            str1+="<input type='hidden' id='url_idcard_front' value=''/>"
            str1+="</div>"
            str1+="<div class='id_tip'>上传身份证正面图片</div>"
            str1+="</div>"
            str1+="<div class='idcard_front' style='margin-left:16px;'>"
            str1+="<div class='uploadimage'>"
            str1+="<div id='preview1' class='uploadimage1' onmouseenter='showdel_idback()'  onmouseleave='showdel_idback1()'></div>"
            str1+="<div class='id_back'></div>"
            str1+="<div class='del_fengmian1' style='display: none;' onclick='del_idback()'>删除</div>"
            str1+="<input style='width:252px;height:156px; cursor: pointer;position: absolute; top: 0; left: 0;opacity: 0;' type='file' accept='image/*' id='upload_idcard_back' onchange='uploadIdCardBack(this)'/>"
            str1+="<input type='hidden' id='url_idcard_back' value=''/>"
            str1+="</div>"
            str1+="<div class='id_tip'>上传身份证背面图片</div>"
            str1+="</div>"
            str1+="</div>"
            str1+="<div class='confirmidentity' onclick='bindSubmitRealnameInfo()'>提交</div>"
            $("#f1content").append(str1);

            if(realnameDetail.log_status == 2){
                if(id_front.length>0){
                    $(".id_front").hide().removeClass('show')
                    $("#preview").css({"background-image":"url('"+realnameDetail.log.content.url_idcard_front+"')"});
                }
                if(id_back.length>0){
                    $(".id_back").hide().removeClass('show')
                    $("#preview1").css({"background-image":"url('"+realnameDetail.log.content.url_idcard_back+"')"});
                }
            }
            

            if(realnameDetail.log_status == -1){
                //实名认证通过
                var smname="";
                var smid="";
                var legal_person_name = realnameDetail.certification.legal_person_name;
                if(legal_person_name){
                    smname+=legal_person_name
                }else{
                    smname=smname+"*"
                }
                var code_idcard = realnameDetail.certification.code_idcard
                var id_part1 = code_idcard.substr(0,5);
                var id_part2 = '*********';
                var id_part3 = code_idcard.substr(13,5);
                smid=smid+id_part1+id_part2+id_part3

                $('#f1contname').html(smname)
                $('#f1contid').html(smid)
            }

        }
    })
}

function getAppInfo(){
    //认证界面
    //设计师认证
    $("#f2content").html('');
    $.get('/center/designerInfo/get',function(res){
        if(res.status == 1){
            userInfo = res.data;
            educatenumber = userInfo.designerDetail.self_education_data.length;
            worknumber = userInfo.designerDetail.self_work_data.length;
            prizenumber = userInfo.designerDetail.self_award_data.length;
            console.log(userInfo);

            var str2="";
            str2+="<div class='f2label'>用户账号</div>"
            str2+="<div class='f2text'>"+userInfo.designer.login_username+"</div>"
            str2+="<div class='f2label'>出生年月</div>"
            str2+="<div class='selectdesign' id='birth'>"
            str2+="<div class='select-menu' style='width:194px;margin-left:20px;margin-top:10px;'>"
            str2+="<div class='select-menu-div' style='width:194px;'>"
            str2+="<input readonly class='select-menu-input' placeholder='年份'/>"
            str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
            str2+="</div>"
            str2+="<ul class='select-menu-ul' style='width:194px;height: 200px; overflow:scroll;' id='year'>"
            str2+="</ul>"
            str2+="</div>"
            str2+="<div class='select-menu' style='width:194px;margin-left:10px;margin-top:10px;'>"
            str2+="<div class='select-menu-div' style='width:194px;'>"
            str2+="<input readonly class='select-menu-input' placeholder='月份'/>"
            str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
            str2+="</div>"
            str2+="<ul class='select-menu-ul' style='width:194px;height: 200px; overflow:scroll;' id='month'>"
            str2+="</ul>"
            str2+="</div>"
            str2+="<div class='select-menu' style='width:194px;margin-left:10px;margin-top:10px;'>"
            str2+="<div class='select-menu-div' style='width:194px;'>"
            str2+="<input readonly class='select-menu-input' placeholder='日'/>"
            str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
            str2+="</div>"
            str2+="<ul class='select-menu-ul' style='width:194px;height: 200px; overflow:scroll;' id='day'>"
            str2+="</ul>"
            str2+="</div>"
            str2+="</div>"
            str2+="<div class='f2label'>所属地</div>"
            str2+="<div class='selectdesign'  id='belong_area'>"
            str2+="<div class='select-menu' style='width:194px;margin-left:20px;margin-top:10px;'>"
            str2+="<div class='select-menu-div' style='width:194px;'>"
            str2+="<input readonly class='select-menu-input' placeholder='省'/>"
            str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
            str2+="</div>"
            str2+="<ul class='select-menu-ul' style='width:194px;height: 200px; overflow:scroll;' id='province'>"
            str2+="</ul>"
            str2+="</div>"
            str2+="<div class='select-menu' style='width:194px;margin-left:10px;margin-top:10px;'>"
            str2+="<div class='select-menu-div' style='width:194px;'>"
            str2+="<input readonly class='select-menu-input' placeholder='市'/>"
            str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
            str2+="</div>"
            str2+="<ul class='select-menu-ul' style='width:194px;height: 200px; overflow:scroll;' id='city_list'>"
            str2+="</ul>"
            str2+="</div>"
            str2+="<div class='select-menu' style='width:194px;margin-left:10px;margin-top:10px;'>"
            str2+="<div class='select-menu-div' style='width:194px;'>"
            str2+="<input readonly class='select-menu-input' placeholder='区' />"
            str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
            str2+="</div>"
            str2+="<ul class='select-menu-ul' style='width:194px;height: 200px; overflow:scroll;' id='area'>"
            str2+="</ul>"
            str2+="</div>"
            str2+="</div>"
            str2+="<div class='f2label'>教育信息</div>"
            str2+="<div id='education'>"+"</div>"
            str2+="<div class='addeducate' onclick='addedcation(educatenumber)'>"+"添加教育信息"+"</div>"
            str2+="<div class='f2label'>工作经历</div>"
            str2+="<div id='experience'>"+"</div>"
            str2+="<div class='addeducate' onclick='addexperience(worknumber)'>"+"添加工作经历"+"</div>"
            str2+="<div class='f2label'>证书与奖项</div>"
            str2+="<div id='prize'>"+"</div>"
            str2+="<div class='addeducate' onclick='addprize(prizenumber)'>"+"添加证书与奖项"+"</div>"
            str2+="<div class='f2label'>自我介绍</div>"
            str2+="<textarea  name='persondecription' class='titleinput1' placeholder='自我介绍，不超过200字' id='self_introduction'>"+userInfo.designerDetail.self_introduction+"</textarea>"
            str2+="<div class='desigbutton' onclick='bindSubmitAppInfo()'>"+"提交"+"</div>"
            $("#f2content").append(str2);

            //年月日
            var self_birth_time = userInfo.designerDetail.self_birth_time;
            console.log(self_birth_time);
            var self_birth_timeArr = new Array();
            self_birth_timeArr = self_birth_time.split(" ");
            var self_birth_time_date = self_birth_timeArr[0];
            var self_birth_time_date_arr = new Array();
            self_birth_time_date_arr = self_birth_time_date.split("-");
            var birth_year = self_birth_time_date_arr[0];
            var birth_month = self_birth_time_date_arr[1];
            var birth_day = self_birth_time_date_arr[2];
            //初始化年份
            selectMenu(11);
            years("year",birth_year);
            $(".select-menu-input").eq(11).val(birth_year);//把被点击的选项的值填入输入框中

            //初始化月
            selectMenu(12);
            months("month",birth_month);
            $(".select-menu-input").eq(12).val(birth_month);

            //初始化日
            selectMenu(13);
            days(birth_day);
            $(".select-menu-input").eq(13).val(birth_day);
            //设置年月日初始化变量
            // $('#year').val("");
            // $('#month').val("");
            // $('#day').val("");

            //改变year调用months和days函数，以下类同
            $(".select-menu-ul").eq(11).on("click","li",function(){
                months("month");
                days("day");
            })
            $(".select-menu-ul").eq(12).on("click","li",function(){
                months("month");
                days("day");
            })

            //省市区
            showPro(userInfo.designerDetail.area_belong_province);
            //省列表
            selectMenu(14);
            //市列表
            selectMenu(15);
            //区列表
            selectMenu(16);
            //如果用户已填写所属地区
            if(userInfo.designerDetail.area_belong_province != 0 && userInfo.designerDetail.area_belong_city != 0 && userInfo.designerDetail.area_belong_district != 0){
                cities = userInfo.cities;
                districts = userInfo.districts;

                //显示省
                showPro(userInfo.designerDetail.area_belong_province);
                for(var i = 0;i < userInfo.provinces.length;i++){
                    if(userInfo.provinces[i].id == userInfo.designerDetail.area_belong_province){
                        var belong_province_name = userInfo.provinces[i].name;
                        break;
                    }
                }
                $(".select-menu-input").eq(14).val(belong_province_name);

                //显示市
                showCity(userInfo.designerDetail.area_belong_city);
                for(var i = 0;i < userInfo.cities.length;i++){
                    if(userInfo.cities[i].id == userInfo.designerDetail.area_belong_city){
                        var belong_city_name = userInfo.cities[i].name;
                        break;
                    }
                }
                $(".select-menu-input").eq(15).val(belong_city_name);

                //显示区
                showArea(userInfo.designerDetail.area_belong_district);
                for(var i = 0;i < userInfo.districts.length;i++){
                    if(userInfo.districts[i].id == userInfo.designerDetail.area_belong_district){
                        var belong_district_name = userInfo.districts[i].name;
                        break;
                    }
                }
                $(".select-menu-input").eq(16).val(belong_district_name);
            }

            $(".select-menu-ul").eq(14).on("click","li",function(){
                var province_id = $(this).val();
                //清空市区显示
                $(".select-menu-input").eq(15).val("");
                $(".select-menu-input").eq(16).val("");
                //获取市数据
                $.get('/center/designerInfo/get_cities',{province_id: province_id},function(res){
                    if(res.status == 1){
                        cities = res.data;
                        showCity(userInfo.designerDetail.area_belong_city);
                    }
                });

            })
            $(".select-menu-ul").eq(15).on("click","li",function(){
                var city_id = $(this).val();
                $.get('/center/designerInfo/get_districts',{city_id: city_id},function(res){
                    if(res.status == 1){
                        districts = res.data;
                        showArea(userInfo.designerDetail.area_belong_district);
                    }
                });
            })

            //教育信息
            if(userInfo.designerDetail.self_education_data.length > 0){
                for(var i = 0;i < userInfo.designerDetail.self_education_data.length;i++){
                    addedcation(i,userInfo.designerDetail.self_education_data[i]);
                }
            }else{
                addedcation(0);
            }

            //工作经验
            if(userInfo.designerDetail.self_work_data.length > 0){
                for(var i = 0;i < userInfo.designerDetail.self_work_data.length;i++){
                    addexperience(i,userInfo.designerDetail.self_work_data[i]);
                }
            }else{
                addexperience(0);
            }

            //证书与奖项
            if(userInfo.designerDetail.self_award_data.length > 0){
                for(var i=0;i<userInfo.designerDetail.self_award_data.length;i++){
                    addprize(i,userInfo.designerDetail.self_award_data[i]);
                }
            }else{
                addprize(0);
            }

        }
    });

}

function get_designer_fav_albums(){
    $.get('/center/fav_album',function(res){
       console.log(res);
       if(res.status == 1){
           fav_albums = res.data;
           // xlPaging.js 收藏关注（收藏方案）使用方法
           var nowpage5 = $("#page5").paging({
               nowPage: 1, // 当前页码
               pageNum: Math.ceil(fav_albums.length / 6), // 总页码
               buttonNum: Math.ceil(fav_albums.length / 6), //要展示的页码数量
               canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
               showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
               callback: function (num) { //回调函数
                   console.log('sss'+num);
                   //更多产品
                   // $(function(e) {
                   $("#produ5").html("");
                   var txt="<div class='c0container' id='d0container'></div>"
                   $("#produ5").append(txt);
                   var total=Math.min(num*6,fav_albums.length)
                   console.log(num+'sss'+total)
                   scproject((num-1)*6,total);
               }
           });
           var endPage = 6;
           if(fav_albums.length < 6){
               endPage = fav_albums.length;
           }
           scproject(nowpage5.options.nowPage-1,endPage);
       }
    });
}

function get_designer_fav_product(){
    $.get('/center/fav_product',function(res){
        if(res.status == 1){
            fav_products = res.data;
            console.log(fav_products);

            // xlPaging.js 收藏关注（收藏产品）使用方法
            var nowpage6 = $("#page6").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(fav_products.length / 8), // 总页码
                buttonNum: Math.ceil(fav_products.length / 8), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    console.log('sss'+num);
                    //更多产品
                    // $(function(e) {
                    $("#produ6").html("");
                    var txt="<div class='c0container' id='d1container'></div>"
                    $("#produ6").append(txt);
                    var total=Math.min(num*8,fav_products.length)
                    console.log(num+'sss'+total)
                    scproduct((num-1)*8,total);
                }
            });

            var endPage = 8;
            if(fav_products.length < 8){
                endPage = fav_products.length;
            }
            scproduct(nowpage6.options.nowPage-1,endPage)
        }
    })
}

$("#fav_prodduct_org_1").on('click',function(){
    $(this).removeClass('scprobotton2');
    $(this).addClass('scprobotton1');

    $("#fav_prodduct_org_0").removeClass('scprobotton1');
    $("#fav_prodduct_org_0").addClass('scprobotton2');

    $.get('/center/fav_product',{org:'org'},function(res){
        if(res.status == 1){
            fav_products = res.data;
            console.log(fav_products);

            // xlPaging.js 收藏关注（收藏产品）使用方法
            var nowpage6 = $("#page6").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(fav_products.length / 8), // 总页码
                buttonNum: Math.ceil(fav_products.length / 8), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    console.log('sss'+num);
                    //更多产品
                    // $(function(e) {
                    $("#produ6").html("");
                    var txt="<div class='c0container' id='d1container'></div>"
                    $("#produ6").append(txt);
                    var total=Math.min(num*8,fav_products.length)
                    console.log(num+'sss'+total)
                    scproduct((num-1)*8,total);
                }
            });

            var endPage = 8;
            if(fav_products.length < 8){
                endPage = fav_products.length;
            }
            scproduct(nowpage6.options.nowPage-1,endPage)
        }
    })

});

$("#fav_prodduct_org_0").on('click',function(){
    $(this).removeClass('scprobotton2');
    $(this).addClass('scprobotton1');

    $("#fav_prodduct_org_1").removeClass('scprobotton1')
    $("#fav_prodduct_org_1").addClass('scprobotton2')

    $.get('/center/fav_product',{org:'other'},function(res){
        if(res.status == 1){
            fav_products = res.data;
            console.log(fav_products);

            // xlPaging.js 收藏关注（收藏产品）使用方法
            var nowpage6 = $("#page6").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(fav_products.length / 8), // 总页码
                buttonNum: Math.ceil(fav_products.length / 8), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    console.log('sss'+num);
                    //更多产品
                    // $(function(e) {
                    $("#produ6").html("");
                    var txt="<div class='c0container' id='d1container'></div>"
                    $("#produ6").append(txt);
                    var total=Math.min(num*8,fav_products.length)
                    console.log(num+'sss'+total)
                    scproduct((num-1)*8,total);
                }
            });

            var endPage = 8;
            if(fav_products.length < 8){
                endPage = fav_products.length;
            }
            scproduct(nowpage6.options.nowPage-1,endPage)
        }
    })
});

function get_designer_fav_designer(){
    $.get('/center/fav_designer',function(res){
        console.log(res);
        if(res.status == 1){
            fav_designer = res.data;
        }
        // xlPaging.js 收藏关注（收藏设计师）使用方法
        var nowpage7 = $("#page7").paging({
            nowPage: 1, // 当前页码
            pageNum: Math.ceil(fav_designer.length / 6), // 总页码
            buttonNum: Math.ceil(fav_designer.length / 6), //要展示的页码数量
            canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
            showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
            callback: function (num) { //回调函数
                console.log('sss'+num);
                //更多产品
                // $(function(e) {
                $("#produ7").html("");
                var txt="<div class='c0container' id='d2container'></div>"
                $("#produ7").append(txt);
                var total=Math.min(num*6,fav_designer.length)
                scdesigner((num-1)*6,total);
            }
        });
        var endPage = 6;
        if(fav_designer.length < 6){
            endPage = fav_designer.length;
        }
        scdesigner(nowpage7.options.nowPage-1,endPage)
    });
}

function get_ablum_list_filter_types(){

    var queryString = window.location.search.slice(1);
    queryString = queryString.split('#')[0];
    queryString = encodeURIComponent(queryString);

    $("#b0").show().addClass('show');

    //获取筛选类型数据
    ajax_get('/center/album_list/list_filter_types?query='+queryString,function(res){

        if(res.status && res.data.length>0){

            album_filter_types = res.data;
            console.log(album_filter_types);

            for(var i=0;i<album_filter_types.length;i++){
                if(album_filter_types[i].value == 'stl'){
                    $("#album_stl_ul").empty();
                    var type = 'stl';
                    var album_stl_li = '<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<album_filter_types[i].data.length;j++){
                        var value = album_filter_types[i].data[j].id;
                        album_stl_li+='<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ album_filter_types[i].data[j].name +'</li>'
                    }

                    $("#album_stl_ul").append(album_stl_li);
                }

                if(album_filter_types[i].value == 'ht'){
                    $("#album_ht_ul").empty();
                    var type = 'ht';
                    var album_ht_li = '<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<album_filter_types[i].data.length;j++){
                        var value = album_filter_types[i].data[j].id;
                        album_ht_li+='<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ album_filter_types[i].data[j].name +'</li>'
                    }

                    $("#album_ht_ul").append(album_ht_li);
                }

                if(album_filter_types[i].value == 'ca'){
                    $("#album_ca_ul").empty();
                    var type = 'ca';
                    var album_ca_li = '<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<album_filter_types[i].data.length;j++){
                        var value = album_filter_types[i].data[j].id;
                        album_ca_li+='<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ album_filter_types[i].data[j].name +'</li>'
                    }

                    $("#album_ca_ul").append(album_ca_li);
                }

                if(album_filter_types[i].value == 'status'){
                    $("#album_status_ul").empty();
                    var type = 'status';
                    var album_status_li = '<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<album_filter_types[i].data.length;j++){
                        var value = album_filter_types[i].data[j].id;
                        album_status_li+='<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ album_filter_types[i].data[j].name +'</li>'
                    }

                    $("#album_status_ul").append(album_status_li);
                }

                if(album_filter_types[i].value == 'time'){
                    $("#album_time_ul").empty();
                    var type = 'time';
                    var album_time_li = '<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<album_filter_types[i].data.length;j++){
                        var value = album_filter_types[i].data[j].id;
                        album_time_li+='<li onclick="change_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ album_filter_types[i].data[j].name +'</li>'
                    }

                    $("#album_time_ul").append(album_time_li);
                }
            }



            //var html = template('filter-type-tpl', {data:res.data});

            //$('#allnav').html(html)

        }

    },function(){})

    //获取设计方案列表数据
    //get_album_list()

}

//切换筛选类型的选择
function change_filter_type(type,value){
    var id = 'album_'+type+'_value';
    $("#"+id).val(value)
    //重设页数
    // $('#i_page').val(1)
    //get_album_list();
}

//获取设计方案列表数据
function get_album_list(scrollTop){
    var query_options = {};
    query_options.stl = $("#album_stl_value").val();
    query_options.ht = $("#album_ht_value").val();
    query_options.ca = $("#album_ca_value").val();
    query_options.status = $("#album_status_value").val()
    query_options.title =  $('#album_title').val();
    query_options.product_no = $("#album_product_no").val();
    query_options.time = $("#album_time_value").val();


    layer.load(1)
    $.get('/center/album_list/album_list',query_options,function(res){
        layer.closeAll("loading");

        if(res.status==1){
            album_page_list = res.data;
            // xlPaging.js 我的方案分页使用方法
            var nowpage = $("#page").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(album_page_list.length / 6), // 总页码
                buttonNum: Math.ceil(album_page_list.length / 6), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    //更多产品
                    // $(function(e) {
                    $("#produ").html("");
                    var txt="<div class='fangan_container' id='fangan'></div>"
                    $("#produ").append(txt);
                    var total=Math.min(num*6,album_page_list.length)
                    morefangan((num-1)*6,total);
                }
            });
            var endPage = 6;
            if(album_page_list.length < 6){
                endPage = album_page_list.length;
            }
            morefangan(nowpage.options.nowPage-1,endPage);

        }
    })

    if(scrollTop){
        //获取目标元素距离屏幕顶部的高度
        var target_roll_height = $('#project').offset().top;
        //滚动
        $("html,body").animate({scrollTop: target_roll_height}, 300);
    }
}

//获取产品筛选项
function get_product_list_filter_types(){
    var queryString = window.location.search.slice(1);
    queryString = queryString.split('#')[0];
    queryString = encodeURIComponent(queryString)

    ajax_get('/center/product_list/list_filter_types?query='+queryString,function(res){

        if(res.status){
            product_filter_types = res.data;
            console.log(product_filter_types);
            for(var i=0;i<product_filter_types.length;i++){
                //色系
                if(product_filter_types[i].value=='clr'){
                    $("#product_clr_ul").empty()
                    var type = 'clr';
                    var product_clr_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_clr_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_clr_ul").append(product_clr_li);
                }

                //工艺类别
                if(product_filter_types[i].value=='tc'){
                    $("#product_tc_ul").empty();
                    var type = 'tc';
                    var product_tc_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_tc_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_tc_ul").append(product_tc_li);
                }

                //应用类别
                if(product_filter_types[i].value=='ac'){
                    $("#product_ac_ul").empty()
                    var type = 'ac';
                    var product_ac_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_ac_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_ac_ul").append(product_ac_li)
                }

                //产品规格
                if(product_filter_types[i].value=='spec'){
                    $("#product_spec_ul").empty();
                    var type = 'spec';
                    var product_spec_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_spec_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_spec_ul").append(product_spec_li)
                }

                //状态
                if(product_filter_types[i].value=='status'){
                    $("#product_status_ul").empty();
                    var type = 'status';
                    var product_status_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_status_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_status_ul").append(product_status_li)
                }

                //产品结构
                if(product_filter_types[i].value=='str'){
                    $("product_str_ul").empty();
                    var type = 'str';
                    var product_str_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_str_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_str_ul").append(product_str_li)
                }
            }
        }
    });
}

function change_product_filter_type(type,value){
    var id = 'product_'+type+'_value';
    $("#"+id).val(value)
}

function get_user_brand(){
    ajax_get('/center/product_list/get_designer_brand',function(res){
        console.log(res.data);
        if(res.status){
            product_brand_data = res.data;
            $("#product_brand_id").html(product_brand_data.brand_name);

        }
    },
        function(){});
}

function get_product_list(){
    var query_options = {};

    query_options.name = $('#product_name_value').val();
    query_options.ac = $('#product_ac_value').val();
    query_options.tc = $("#product_tc_value").val();
    query_options.clr = $("#product_clr_value").val();
    query_options.spec = $("#product_spec_value").val();
    query_options.status = $("#product_status_value").val();
    query_options.str = $("#product_str_value").val();

    layer.load(1)
    $.get('/center/product_list/product_list',query_options,function(res){
        layer.closeAll("loading");
        console.log(res);
        if(res.status==1){
            product_page_list = res.data;
            console.log(product_page_list);

            // xlPaging.js 我的产品列表分页使用方法
            var nowpage1 = $("#page1").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(product_page_list.length / 6), // 总页码
                buttonNum: Math.ceil(product_page_list.length / 6), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    //更多产品
                    // $(function(e) {
                    $("#produ1").html("");
                    var txt="<table border='0' id='tc_table'></table>"
                    $("#produ1").append(txt);
                    var total=Math.min(num*6,product_page_list.length)
                    product((num-1)*6,total);
                }
            });
            var endPage = 6;
            if(product_page_list.length < 6){
                endPage =  product_page_list.length;
            }
            product(nowpage1.options.nowPage-1,endPage)
        }
    });
}

function get_comment_list(){
    $.get('/center/notify/comment_notify',function(res){
        if(res.status){
            comment = res.data;
            // xlPaging.js 消息通知——评论分页使用方法
            var nowpage4 = $("#page4").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(comment.length / 4), // 总页码
                buttonNum: Math.ceil(comment.length / 4), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    //更多产品
                    // $(function(e) {
                    $("#produ4").html("");
                    var txt="<div class='c0container' id='c2container'></div>"
                    $("#produ4").append(txt);
                    var total=Math.min(num*4,comment.length)
                    noticepinglun((num-1)*4,total);

                }
            });
            var endPage = 4;
            if(comment.length < 4){
                endPage =  comment.length;
            }
            noticepinglun(nowpage4.options.nowPage-1,endPage);
        }
    });
}

function get_fav_list(){
    $.get('/center/notify/favList',function(res){
        console.log(res);
        if(res.status){
            fav_notify = res.data;

            // xlPaging.js 消息通知——互动消息分页使用方法
            var nowpage3 = $("#page3").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(fav_notify.length / 4), // 总页码
                buttonNum: Math.ceil(fav_notify.length / 4), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    //更多产品
                    // $(function(e) {
                    $("#produ3").html("");
                    var txt="<div class='c0container' id='c1container'></div>"
                    $("#produ3").append(txt);
                    var total=Math.min(num*4,fav_notify.length);
                    acticenotice((num-1)*4,total);
                }
            });
            var endPage = 4;
            if(fav_notify.length < 4){
                endPage =  fav_notify.length;
            }

            acticenotice(nowpage3.options.nowPage-1,endPage);

        }

    })
}

function get_sysNotify(){
    $.get('/center/notify/sysNotify',function(res){
        console.log(res);
        if(res.status){
            sys_notify = res.data;

            // xlPaging.js 消息通知——系统通知分页使用方法
            var nowpage2 = $("#page2").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(sys_notify.length / 4), // 总页码
                buttonNum: Math.ceil(sys_notify.length / 4), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    console.log('sss'+num);
                    //更多产品
                    // $(function(e) {
                    $("#produ2").html("");
                    var txt="<div class='c0container' id='c0container'></div>"
                    $("#produ2").append(txt);
                    var total=Math.min(num*4,sys_notify.length)
                    console.log(num+'sss'+total)
                    systemnotice((num-1)*4,total);
                }
            });

            var endPage = 4;
            if(sys_notify.length < 4){
                endPage =  sys_notify.length;
            }

            systemnotice(nowpage2.options.nowPage-1,endPage);
        }
    });
}

function get_album_chart(){
    $.get('/center/chart/album_visit',function(res){
        console.log(res);
        if(res.status){
            var data = res.data;
            //方案统计折线图
            $('#chart_album_yes_visit_num').html(data.yes_num);
            $("#chart_album_month_visit_num").html(data.month_num);
            var lineChartData = {
                //labels: ["12/12", "12/13", "12/14", "12/15", "12/16", "12/17", "12/18"],
                labels: data.date,
                datasets: [
                    {
                        // label: "My First dataset",
                        fill: false,
                        lineTension: 0.1,
                        scaleGridLineColor : "rgba(248,248,248,1)",
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "#1582FF",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "#1582FF",
                        pointBackgroundColor: "#1582FF",
                        pointBorderWidth: 4,
                        pointHoverRadius: 2,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 4,
                        pointRadius: 2,
                        pointHitRadius: 10,
                        //data: [30, 50, 30, 81, 74, 70, 52],
                        data: data.num,
                        spanGaps: false,
                        label: '浏览量',
                    }
                ],
            };
            var ctx = document.getElementById("lines-graph").getContext("2d");
            var LineChart = new Chart(ctx, {
                type: 'line',
                data: lineChartData,
                responsive: true,
                bezierCurve : false,
                scaleGridLineColor: "rgba(0,0,0,.05)",
            });
        }

    });

    $.get('/center/chart/album_collect',function(res){
        if(res.status){
            var data = res.data;
            $('#chart_album_yes_collect_num').html(data.yes_num);
            $("#chart_album_month_collect_num").html(data.month_num);
            var lineChartData = {
                //labels: ["12/12", "12/13", "12/14", "12/15", "12/16", "12/17", "12/18"],
                labels: data.date,
                datasets: [
                    {
                        // label: "My First dataset",
                        fill: false,
                        lineTension: 0.1,
                        scaleGridLineColor : "rgba(248,248,248,1)",
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "#1582FF",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "#1582FF",
                        pointBackgroundColor: "#1582FF",
                        pointBorderWidth: 4,
                        pointHoverRadius: 2,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 4,
                        pointRadius: 2,
                        pointHitRadius: 10,
                        //data: [30, 50, 30, 81, 74, 70, 52],
                        data: data.num,
                        spanGaps: false,
                        label: '收藏量',
                    }
                ],
            };
            var ctx1 = document.getElementById("lines-graph1").getContext("2d");
            var LineChart = new Chart(ctx1, {
                type: 'line',
                data: lineChartData,
                responsive: true,
                bezierCurve : false,
                scaleGridLineColor: "rgba(0,0,0,.05)",
            });
        }
    });

    $.get('/center/chart/album_top',function(res){
        console.log(res);
        var data = res.data;

        //收藏量
        $("#top5view1").html('');
        var str="";
        for(var i=0;i<data.collect.length;i++){
            str+="<div class='topitem'>"
            //str+="<div class='topimage' id='top1image"+i+"' >"+"</div>"
            str+='<div class="topimage" id="top1image'+i+'" onclick="click_album(&quot;'+data.collect[i].web_id_code+'&quot;)"/>'
            str+="<div class='topname'>"+data.collect[i].title+"</div>"
            str+="<div class='d_detail1f'>";
            str+="<span class='iconfont icon-chakan'  style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str+="<span class='looknumber'>"+data.collect[i].count_visit+"</span>";
            str+="<span class='iconfont icon-shoucang2' id='topf1collected_"+i+"' style='color:#B7B7B7;margin-left:20px;'>"+"</span>";
            str+="<span class='looknumber' id='topf1collectednumber_"+i+"'>"+data.collect[i].count_fav+"</span>";
            str+="</div>";
            str+="</div>"
        }
        $("#top5view1").append(str);
        for(var i=0;i<data.collect.length;i++){
            $("#top1image"+i).css({"background-image":"url('"+data.collect[i].photo_cover+"')"});
        }

        //浏览量
        $("#top5view").html("");
        var str1="";
        for(var i=0;i<data.visit.length;i++){
            str1+="<div class='topitem'>"
            str1+='<div class="topimage" id="topimage'+i+'" onclick="click_album(&quot;'+data.visit[i].web_id_code+'&quot;)"/>'
            str1+="<div class='topname'>"+data.visit[i].title+"</div>"
            str1+="<div class='d_detail1f'>";
            str1+="<span class='iconfont icon-chakan' style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str1+="<span class='looknumber'>"+data.visit[i].count_visit+"</span>";
            str1+="<span class='iconfont icon-shoucang2' id='topfcollected_"+i+"' style='color:#B7B7B7;margin-left:20px;'>"+"</span>";
            str1+="<span class='looknumber' id='topfcollectednumber_"+i+"' >"+data.visit[i].count_fav+"</span>";
            str1+="</div>";
            str1+="</div>"
        }
        $("#top5view").append(str1);
        for(var i=0;i<data.visit.length;i++){
            $("#topimage"+i).css({"background-image":"url('"+data.visit[i].photo_cover+"')"});
        }

    });
}

function get_product_chart(){
    $.get('/center/chart/product_top',function(res){
        var data = res.data;
        chart_top_product_visit = res.data.visit;
        chart_top_product_collect = res.data.collect;

        //浏览
        $("#top5viewpro").html("");
        var str="";
        for(var i=0;i<chart_top_product_visit.length;i++){
            str+="<div class='topitem1'>"
            //str+="<div class='topimage1' id='topproimage"+i+"'>"+"</div>"
            str+='<div class="topimage1" id="topproimage'+i+'" onclick="go_detail(&quot;'+chart_top_product_visit[i].web_id_code+'&quot;)"/>'
            str+="<div class='topname'>"+chart_top_product_visit[i].productTitle+"</div>"
            str+="<div class='d_detail1f'>";
            str+="<span class='iconfont icon-chakan'  style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str+="<span class='looknumber'>"+chart_top_product_visit[i].count_visit+"</span>";
            if(chart_top_product_visit[i].collected){
                str+="<span class='iconfont icon-buoumaotubiao44' id='toppcollected_"+i+"' style='color:#1582FF;margin-left:20px;' onclick='toppcollected("+i+")'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-shoucang2' id='toppcollected_"+i+"' style='color:#B7B7B7;margin-left:20px;' onclick='toppcollected("+i+")'>"+"</span>";
            }
            str+="<span class='looknumber' id='toppcollectednumber_"+i+"' >"+chart_top_product_visit[i].count_fav+"</span>";
            str+="</div>";
            str+="</div>"
        }
        $("#top5viewpro").append(str);
        for(var i=0;i<chart_top_product_visit[i].length;i++){
            $("#topproimage"+i).css({"background-image":"url('"+chart_top_product_visit[i].cover+"')"});
        }

        //收藏
        $("#top5viewpro1").html('');
        var str="";
        for(var i=0;i<chart_top_product_collect.length;i++){
            str+="<div class='topitem1'>"
            //str+="<div class='topimage1' id='topimage1"+i+"'>"+"</div>"
            str+='<div class="topimage1" id="topimage1'+i+'" onclick="go_detail(&quot;'+chart_top_product_collect[i].web_id_code+'&quot;)"/>'
            str+="<div class='topname'>"+chart_top_product_collect[i].productTitle+"</div>"
            str+="<div class='d_detail1f'>";
            str+="<span class='iconfont icon-chakan'  style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str+="<span class='looknumber'>"+chart_top_product_collect[i].count_visit+"</span>";
            if(chart_top_product_collect[i].collected){
                str+="<span class='iconfont icon-buoumaotubiao44' id='topp1collected_"+i+"' style='color:#1582FF;margin-left:20px;' onclick='topp1collected("+i+")'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-shoucang2' id='topp1collected_"+i+"' style='color:#B7B7B7;margin-left:20px;' onclick='topp1collected("+i+")'>"+"</span>";
            }
            str+="<span class='looknumber' id='topp1collectednumber_"+i+"' )'>"+chart_top_product_collect[i].count_fav+"</span>";
            str+="</div>";
            str+="</div>"
        }
        $("#top5viewpro1").append(str);
        for(var i=0;i<chart_top_product_collect.length;i++){
            $("#toppro1image"+i).css({"background-image":"url('"+chart_top_product_collect[i].cover+"')"});
        }

    })
}

function get_product_chart_use(){
    $.get('/center/chart/product_use',function(res){
        console.log(res);
        if(res.status){
            var data = res.data;
            //产品条形图
            var ctx2 = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx2, {
                type: 'horizontalBar',
                data: {
                    labels: data.name,
                    datasets: [{
                        label: '百分比',
                        data: data.time,
                        borderColor:'#1582FF',
                        backgroundColor:'#1582FF',
                        borderWidth: 1,
                    }]
                }
            });
        }

    })
}

//页面初始显示部分
function showModule(i){
    if(i != navactive){
        document.getElementById("navtitle"+i).className = "nav_title1";
        document.getElementById("navtext"+i).className = "nav_text1";
        document.getElementById("navtitle"+navactive).className = "nav_title";
        document.getElementById("navtext"+navactive).className = "nav_text";
        $('#b'+navactive).hide().removeClass('show')
        navactive=i;
        $('#b'+navactive).show().addClass('show');
    }


    if(i == 0){
        //我的方案
        get_ablum_list_filter_types();
        get_album_list();
    }else if(i == 1){
        //产品列表
        get_product_list_filter_types();
        get_user_brand();
        get_product_list();
    }else if(i == 2){
        //我的统计
        get_album_chart();
        get_product_chart();
        get_product_chart_use();
    }else if(i == 3){
        //收藏关注
        get_designer_fav_albums();
        get_designer_fav_product();
        get_designer_fav_designer();
    }else if(i == 4){
        //消息通知
        get_comment_list();
        get_fav_list();
        get_sysNotify();
    }else if(i == 5){
        //个人中心
        //个人资料
        getUserInfo();
        //实名认证
        getUserRealnameInfo();
        //设计师认证
        getAppInfo();

    }else if(i == 6){
        //安全中心
    }else{

    }
}





$(document).ready(function(){

    showLeftTabs();
    showPersonTabs();
    showSafeTabs();






});

//去方案详情
function click_album(id_code){
    window.open("/album/s/"+id_code+"?__bs="+__cache_brand)
}

//去产品详情
function go_detail(web_id_code){
    window.open('/product/s/'+web_id_code+"?__bs="+__cache_brand);
}

//设计师详情
function go_designer_detail(id_code){
    window.open('/designer/s/'+id_code+"?__bs="+__cache_brand)
}

//更改头像
function uploadAvatar(input) {


    var file = $('#upload-input')[0].files[0];
    var formData = new FormData();
    console.log(file);
    formData.append('avatar',file);
    $.ajax({
        url:'/center/user_info/save_avatar',
        dataType:'json',
        type:'POST',
        async: false,
        data: formData,
        processData : false, // 使数据不做处理
        contentType : false, // 不要设置Content-Type请求头
        success: function(res){
            console.log(res)
            var url = res.data.storage_path;
            $("#pd_image").css({"background-image":"url('"+url+"')"});
            $("#avatar_url").val(url);
            layer.msg(res.msg)
        },
        error:function(response){
            console.log(response);
        }
    });
}

//提交基本信息
function bindSubmitUserInfo(){
    var data = {
        avatar_url: $("#avatar_url").val(),
        nick_name: $("#nickname").val(),
        gender: $("input[name='gender']:checked").val(),
    };

    console.log(data);

    ajax_post('/center/user/base_info/update',data,function(res){
        console.log(res);
        if(res.status == 1){
            persondetail.url_avatar = res.data.url_avatar;
            persondetail.nickname = res.data.nickname;
            persondetail.gender = res.data.gender;
            layer.msg(res.msg)
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}

//上传身份证正面
function uploadIdCardFront(input) {

    var file = $("#upload_idcard_front")[0].files[0];
    var formData = new FormData();
    console.log(file);
    formData.append('file',file);
    $.ajax({
        url:'/center/upload_id_card',
        dataType:'json',
        type:'POST',
        async: false,
        data: formData,
        processData : false, // 使数据不做处理
        contentType : false, // 不要设置Content-Type请求头
        success: function(res){
            console.log(res)
            var url = res.data.storage_path;
            $(".id_front").hide().removeClass('show')
            $("#preview").css({"background-image":"url('"+url+"')"});
            $("#url_idcard_front").val(url);
            id_front = [url];
            layer.msg(res.msg)
        },
        error:function(response){
            console.log(response);
        }
    });

    // var file = input.files[0];
    // var url = window.URL.createObjectURL(file)
    // console.log(url)
    // id_front.push(url)
    // $(".id_front").hide().removeClass('show')
    // $("#preview").css({"background-image":"url('"+url+"')"});
}

function uploadIdCardBack(input){
    var file = $("#upload_idcard_back")[0].files[0];
    var formData = new FormData();
    console.log(file);
    formData.append('file',file);
    $.ajax({
        url:'/center/upload_id_card',
        dataType:'json',
        type:'POST',
        async: false,
        data: formData,
        processData : false, // 使数据不做处理
        contentType : false, // 不要设置Content-Type请求头
        success: function(res){
            console.log(res)
            var url = res.data.storage_path;
            $(".id_back").hide().removeClass('show')
            $("#preview1").css({"background-image":"url('"+url+"')"});
            $("#url_idcard_back").val(url);
            id_back = [url];
            layer.msg(res.msg)
        },
        error:function(response){
            console.log(response);
        }
    });
}

//实名认证
function bindSubmitRealnameInfo(){
    layer.load(1);
    var legal_person_name = $("#legal_person_name").val();
    var code_idcard = $("#code_idcard").val();
    var url_idcard_front = $("#url_idcard_front").val();
    var url_idcard_back = $("#url_idcard_back").val();

    var data = {
        legal_person_name: legal_person_name,
        code_idcard: code_idcard,
        url_idcard_front: url_idcard_front,
        url_idcard_back: url_idcard_back,
    }

    ajax_post('/center/realname/update_info',data,function(res){
        console.log(res);
        if(res.status == 1){
            //persondetail.identity=1
            realnameDetail.log_status = 1;
            console.log(realnameDetail);
            $('#f1content2').hide().removeClass('show');
            $('#f1content').hide().removeClass('show');
            $('#f1content1').show().addClass('show');
            $('#f1content3').hide().removeClass('show');

            layer.msg(res.msg)
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}

function bindSubmitAppInfo(){
    layer.load(1);
    //获取生日
    var birth_year = $('#birth').find('input').eq(0).val();
    var birth_month = $('#birth').find('input').eq(1).val();
    var brth_day = $('#birth').find('input').eq(2).val();

    //获取所属省id
    var belong_province_id = '';
    var belong_province_li = $('#province li');
    for(var i=0;i<belong_province_li.length;i++){
        if(belong_province_li[i].className == 'select-this'){
            belong_province_id = belong_province_li[i].value;
        }
    }

    //获取所属市id
    var belong_city_id = '';
    var belong_city_li = $('#city_list li');
    for(var i=0;i<belong_city_li.length;i++){
        if(belong_city_li[i].className == 'select-this'){
            belong_city_id = belong_city_li[i].value;
        }
    }

    //获取所属区id
    var belong_district_id = ''
    var belong_district_li = $("#area li");
    for(var i=0;i<belong_district_li.length;i++){
        if(belong_district_li[i].className == 'select-this'){
            belong_district_id = belong_district_li[i].value;
        }
    }

    //循环教育信息
    var education_arr = [];
    var edu_arr_index = 0;
    for(var i=0;i<educatenumber;i++){

        if($("#education"+i).css('display') != 'none'){
            var school = $("#education"+i).find('input').eq(0).val();
            var education = $("#education"+i).find('input').eq(1).val();
            var profession = $("#education"+i).find('input').eq(2).val();
            var graduate_year = $("#education"+i).find('input').eq(3).val();
            var graduate_month = $("#education"+i).find('input').eq(4).val();

            education_arr[edu_arr_index] = {
                school: school,
                education:education,
                profession:profession,
                graduate_year:graduate_year,
                graduate_month:graduate_month,
            }

            edu_arr_index++;
        }
    }

    //循环工作信息
    var work_arr = [];
    var work_arr_index = 0;

    for(var i=0;i<worknumber;i++){

        if($("#work"+i).css('display') != 'none'){
            var company = $("#work"+i).find('input').eq(0).val();
            var position = $("#work"+i).find('input').eq(1).val();
            var start_year = $("#work"+i).find('input').eq(2).val();
            var start_month = $("#work"+i).find('input').eq(3).val();
            var end_year = $("#work"+i).find('input').eq(4).val();
            var end_month = $("#work"+i).find('input').eq(3).val();
            var work_description = $("#work"+i).find('textarea').val();

            if(work_description==null || work_description==undefined){
                work_description = '';
            }

            work_arr[work_arr_index] = {
                company:company,
                position:position,
                start_year:start_year,
                start_month:start_month,
                end_year:end_year,
                end_month:end_month,
                work_description:work_description,
            }
            work_arr_index++;
        }
    }

    //循环奖项
    var award_arr = [];
    var award_arr_index = 0;

    for(var i=0;i<prizenumber;i++){
       if($("#prize"+i).css('display') != 'none'){
           var award_name = $("#prize"+i).find('input').eq(0).val();
           var award_year = $("#prize"+i).find('input').eq(1).val();
           var award_month = $("#prize"+i).find('input').eq(2).val();

           award_arr[award_arr_index] = {
               award_name:award_name,
               award_year:award_year,
               award_month:award_month,
           }
           award_arr_index++;
       }
    }

    //自我介绍
    var self_introduction = $("#self_introduction").val();

    var data = {
        birth_year: birth_year,
        birth_month: birth_month,
        brth_day: brth_day,
        area_belong_province: belong_province_id,
        area_belong_city: belong_city_id,
        area_belong_district: belong_district_id,
        school: education_arr,
        work_company: work_arr,
        award_name: award_arr,
        self_introduction: self_introduction,
    }
    console.log(data);


    ajax_post('/center/designerInfo/update',data,function(res){
        console.log(res);
        if(res.status){
            layer.msg(res.msg);
        }else{
            layer.msg(res.msg);
        }
        layer.closeAll("loading");
    })




}

//修改密码通过密码修改
function bindSubmitChangePwdByPwd(){
    var oldpassword = $("#by_pwd_original_pwd").val();
    var newpassword = $("#by_pwd_new_pwd").val();
    var confirmpassword = $("#by_pwd_confirm_pwd").val();

    if(newpassword !== confirmpassword){
        layer.msg('两次输入的')
        return false
    }

    var data = {
        oldpassword: oldpassword,
        newpassword: newpassword,
    }

    ajax_post('/center/reset_by_pwd',data,function(res){
        console.log(res);
        if(res.status == 1){
            layer.msg(res.msg)
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}

//获取验证码
function bindChangePwdSendCode(){
    var phone = $("#by_phone_phone").val();

    var data = {
        login_mobile: phone,
    }

    $.get('/center/getResetSmsCode',data,function(res){
        console.log(res);
        if(res.status == 1){
            layer.msg(res.msg);
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}

//修改密码通过手机修改
function bindSubmitChangePwdByPhone(){

    var newpassword = $("#by_phone_newpassword").val();
    var phone = $("#by_phone_phone").val();
    var verification_code = $("#by_phone_smscode").val();

    var data = {
        newpassword: newpassword,
        phone: phone,
        verification_code: verification_code,
    }

    ajax_post('/center/reset_by_phone',data,function(res){
        if(res.status == 1){
            layer.msg(res.msg);
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });

}

//修改手机 发送验证码
function bindChangePhoneSendCode(){
    var phone = $("#change_phone_new_phone").val();

    var data = {
        new_phone: phone,
    }

    ajax_post('/center/getResetPhoneCode',data,function(res){
        if(res.status == 1){
            layer.msg(res.msg);
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });


}

//修改手机
function bindSubmitChangePhone(){
    var new_phone = $("#change_phone_new_phone").val();
    var verification_code = $("#change_phone_code").val();
    var password = $("#change_phone_pwd").val();

    var data = {
        new_phone: new_phone,
        verification_code: verification_code,
        password: password,
    }

    ajax_post('/center/resetUserPhone',data,function(res){
        if(res.status == 1){
            layer.msg(res.msg);
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}









//下拉列表
$(function(){
    selectMenu(0);
    selectMenu(1);
    selectMenu(2);
    selectMenu(3);
    selectMenu(4);
    selectMenu(5);
    selectMenu(6);
    selectMenu(7);
    selectMenu(8);
    selectMenu(9);
    selectMenu(10);

})
function selectMenu(index){
    console.log(index)
    $(".select-menu-input").eq(index).val($(".select-this").eq(index).html());//在输入框中自动填充第一个选项的值
    $(".select-menu-div").eq(index).on("click",function(e){
        e.stopPropagation();
        if($(".select-menu-ul").eq(index).css("display")==="block"){
            $(".select-menu-ul").eq(index).hide();
            $(".select-menu-div").eq(index).find("i").removeClass("select-menu-i");
            $(".select-menu-ul").eq(index).animate({marginTop:"50px",opacity:"0"},"fast");
        }else{
            $(".select-menu-ul").eq(index).show();
            $(".select-menu-div").eq(index).find("i").addClass("select-menu-i");
            $(".select-menu-ul").eq(index).animate({marginTop:"2px",opacity:"1"},"fast");
        }
        for(var i=0;i<$(".select-menu-ul").length;i++){
            if(i!==index&& $(".select-menu-ul").eq(i).css("display")==="block"){
                $(".select-menu-ul").eq(i).hide();
                $(".select-menu-div").eq(i).find("i").removeClass("select-menu-i");
                $(".select-menu-ul").eq(i).animate({marginTop:"50px",opacity:"0"},"fast");
            }
        }

    });
    $(".select-menu-ul").eq(index).on("click","li",function(){//给下拉选项绑定点击事件
        $(".select-menu-input").eq(index).val($(this).html());//把被点击的选项的值填入输入框中
        $(".select-menu-div").eq(index).click();
        $(this).siblings(".select-this").removeClass("select-this");
        $(this).addClass("select-this");
    });
    $("body").on("click",function(event){
        event.stopPropagation();
        if($(".select-menu-ul").eq(index).css("display")==="block"){
            console.log(1);
            $(".select-menu-ul").eq(index).hide();
            $(".select-menu-div").eq(index).find("i").removeClass("select-menu-i");
            $(".select-menu-ul").eq(index).animate({marginTop:"50px",opacity:"0"},"fast");

        }
    });
}
function selectMenu1(a,index){
    console.log(index)
    //$(".select-menu-input1").eq(index).val($(".select-this").eq(index).html());//在输入框中自动填充第一个选项的值
    $(".select-menu-div"+a).eq(index).on("click",function(e){
        e.stopPropagation();
        if($(".select-menu-ul"+a).eq(index).css("display")==="block"){
            $(".select-menu-ul"+a).eq(index).hide();
            $(".select-menu-div"+a).eq(index).find("i").removeClass("select-menu-i");
            $(".select-menu-ul"+a).eq(index).animate({marginTop:"50px",opacity:"0"},"fast");
        }else{
            $(".select-menu-ul"+a).eq(index).show();
            $(".select-menu-div"+a).eq(index).find("i").addClass("select-menu-i");
            $(".select-menu-ul"+a).eq(index).animate({marginTop:"2px",opacity:"1"},"fast");
        }
        for(var i=0;i<$(".select-menu-ul"+a).length;i++){
            if(i!==index&& $(".select-menu-ul"+a).eq(i).css("display")==="block"){
                $(".select-menu-ul"+a).eq(i).hide();
                $(".select-menu-div"+a).eq(i).find("i").removeClass("select-menu-i");
                $(".select-menu-ul"+a).eq(i).animate({marginTop:"50px",opacity:"0"},"fast");
            }
        }

    });
    $(".select-menu-ul"+a).eq(index).on("click","li",function(){//给下拉选项绑定点击事件
        $(".select-menu-input"+a).eq(index).val($(this).html());//把被点击的选项的值填入输入框中
        $(".select-menu-div"+a).eq(index).click();
        $(this).siblings(".select-this").removeClass("select-this");
        $(this).addClass("select-this");
    });
    $("body").on("click",function(event){
        event.stopPropagation();
        if($(".select-menu-ul"+a).eq(index).css("display")==="block"){
            console.log(1);
            $(".select-menu-ul"+a).eq(index).hide();
            $(".select-menu-div"+a).eq(index).find("i").removeClass("select-menu-i");
            $(".select-menu-ul"+a).eq(index).animate({marginTop:"50px",opacity:"0"},"fast");

        }
    });
}

//我的统计
$(function(e) {
    var str=""
    var a=["方案统计","产品统计"]
    for(var i=0;i<a.length;i++){
        if(tongjinav==i){
            str+="<div class='designtitle' id='g"+i+"' onclick='tjchangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='g"+i+"' onclick='tjchangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#tongjidaohang").append(str);

})



//收藏关注
$(function(e) {
    var str=""
    var a=["收藏的方案","收藏的产品","关注的设计师"]
    for(var i=0;i<a.length;i++){
        if(shoucangnav==i){
            str+="<div class='designtitle' id='d"+i+"' onclick='scchangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='d"+i+"' onclick='scchangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#shoucangdaohang").append(str);



})

//消息通知
$(function(e) {
    var str=""
    var a=["系统消息"/*,"互动消息","评论"*/]
    for(var i=0;i<a.length;i++){
        if(noticenav==i){
            str+="<div class='designtitle' id='c"+i+"' onclick='noticechangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='c"+i+"' onclick='noticechangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#noticedaohang").append(str);
    // systemnotice(nowpage2.options.nowPage-1,nowpage2.options.nowPage*4)
    //acticenotice(nowpage3.options.nowPage-1,nowpage3.options.nowPage*4)
    //noticepinglun(nowpage4.options.nowPage-1,nowpage4.options.nowPage*4)
})





//安全中心——绑定微信
$(function() {
    var str = '';
    var hideweixin=weixinid[0]+weixinid[1]+weixinid[2]+"**"+weixinid[weixinid.length-2]+weixinid[weixinid.length-1]
    if(weixinid==""){
        str+="<div class='weixin' onclick='closefriend()'>"+"绑定微信"+"</div>"
        str+="<div id='jiebang' style='display: none;'>"
        str+="<div class='weixinname'>"+"已绑定微信："+hideweixin+"</div>"
        str+="<div class='weixin1'  onclick='jiebang()'>"+"解绑微信"+"</div>"
        str+="</div>"
    }else{
        str+="<div class='weixin' style='display: none;' onclick='closefriend()'>"+"绑定微信"+"</div>"
        str+="<div id='jiebang'>"
        str+="<div class='weixinname'>"+"已绑定微信："+hideweixin+"</div>"
        str+="<div class='weixin1' onclick='jiebang()'>"+"解绑微信"+"</div>"
        str+="</div>"
    }
    str+="<div class='jbcontent' style='display: none;'>"
    str+="<div class='safelabel'>密码</div>"
    str+="<input type='password' name='psw'  class='tc_input' placeholder='请输入密码' />"
    str+="<div class='weixin2' onclick='comjiebang()'>"+"确认解绑"+"</div>"
    str+="</div>"
    $("#weixin").append(str);
})

//我的方案的方案列表
function morefangan(begin,end){
    $("#fangan").html("");
    //status 状态（000.不通过 .050.未审核 100.提交审核 .200.已通过 020已隐藏）
    //visible_status 100.显示. 200.下架
    //period_status 阶段状态，000编辑阶段100审核阶段200完成阶段
    var str="";
    for(var i=begin;i<end;i++){
        str+="<div class='fanganitem' id='fanganitem_"+i+"'>"

        str+="<div class='moreview' id='more_"+i+"' >"

        str+="<div class='morechoose' id='morechoose_"+i+"' >"
        //编辑状态下才能编辑
        if(album_page_list[i].period_status =='000'){
            str+="<div class='edit' onclick='edit("+i+")'>"
            str+="<label class='editlable'>"+"编辑"+"</label>"
            str+="</div>"
        }
        //方案已完成才能另存
        /*if(album_page_list[i].period_status=='200'){
         str+="<div class='edit' onclick='save("+i+")'>"
         str+="<label class='editlable'>"+"另存方案"+"</label>"
         str+="</div>"
         }
         //方案已完成才能上下架
         if(album_page_list[i].period_status=='200'){

         if(album_page_list[i].visible_status == '100'){
         //状态为上架
         str+="<div class='edit1' style='cursor: not-allowed;'>"
         str+="<label class='editlable1'>"+"隐藏方案"+"</label>"
         str+="</div>"
         }else{
         //状态为下架
         str+="<div class='edit' onclick='look("+i+")'>"
         str+="<label class='editlable'>"+"显示方案"+"</label>"
         str+="</div>"
         }
         }*/
        str+="<div class='edit' onclick='del("+i+")'>"
        str+="<label class='editlable'>"+"删除"+"</label>"
        str+="</div>"

        str+="</div>"
        str+="</div>"

        str+='<div class="fangan_image" id="fangan_image'+i+'" onclick="click_album(&quot;'+album_page_list[i].web_id_code+'&quot;)">'+'</div>'
        str+="<div class='fangan_title'>"+album_page_list[i].title+"</div>"
        str+="<div class='label_con'>"

        // var totalwidth=0;
        // for(var j=0;j<fangan.list[i].label.length;j++){
        //     var width=fangan.list[i].label[j].length*14+20;
        //     console.log('width'+width)
        //     totalwidth=totalwidth+width;
        //     console.log(totalwidth)
        //     if(totalwidth<=250){
        //         str+="<div class='labelitem'>"+fangan.list[i].label[j]+"</div>"
        //     }
        // }
        var totalwidth=0;
        var area = album_page_list[i].count_area+'㎡'
        totalwidth = area.length*14+20 + album_page_list[i].style_text.length*14+20 + album_page_list[i].house_type_text.length*14+20;
        // if(totalwidth<=250){
            str+="<div class='labelitem'>"+area+"</div>"
        if(album_page_list[i].style_text){
            str+="<div class='labelitem'>"+album_page_list[i].style_text+"</div>"
        }
        if(album_page_list[i].house_type_text){
            str+="<div class='labelitem'>"+album_page_list[i].house_type_text+"</div>"
        }

        // }

        str+="</div>"
        str+="<div class='d_detail'>";
        str+="<span class='iconfont icon-chakan' id='look' style='color:#B7B7B7;'>"+"</span>";
        str+="<span class='looknumber'>"+album_page_list[i].count_visit+"</span>";
        // if(fangan.list[i].dolike==false){
        //     str+="<span class='iconfont icon-dianzan2' id='like_"+i+"' style='color:#B7B7B7;margin-left:20px;' onclick='like("+i+")'>"+"</span>";
        // }
        // else{
        //     str+="<span class='iconfont icon-dianzan2' id='like_"+i+"' style='color:#3CA4FF;margin-left:20px;' onclick='like("+i+")'>"+"</span>";
        // }
        str+="<span class='iconfont icon-dianzan2' id='like_"+i+"' style='color:#B7B7B7;margin-left:20px;'>"+"</span>";
        str+="<span class='looknumber' id='likenumber_"+i+"' >"+album_page_list[i].count_praise+"</span>";
        // if(fangan.list[i].doshoucang==false){
        //     str+="<span class='iconfont icon-shoucang2' id='collected_"+i+"' style='color:#B7B7B7;margin-left:20px;' onclick='collected("+i+")'>"+"</span>";
        // }else{
        //     str+="<span class='iconfont icon-shoucang2' id='collected_"+i+"' style='color:#3CA4FF;margin-left:20px;' onclick='collected("+i+")'>"+"</span>";
        // }
        str+="<span class='iconfont icon-shoucang2' id='collected_"+i+"' style='color:#B7B7B7;margin-left:20px;'>"+"</span>";
        str+="<span class='looknumber' id='collectednumber_"+i+"' >"+album_page_list[i].count_fav+"</span>";
        str+="<div class='fuzhi'>"+"</div>";
        str+="<span class='looknumber' id='fuzhi"+i+"' >"+album_page_list[i].count_use+"</span>";
        str+="</div>";
        str+="<div class='fline'>"+"</div>"
        str+="<div class='fbottom'>"
        str+="<div class='ftime'>"+album_page_list[i].created_at+"</div>"
        if(album_page_list[i].period_status=='000'|| album_page_list[i].status=='100'){
            str+="<div class='fstatus'>"+"▪&nbsp;"+album_page_list[i].statusText+"</div>"
        }else if(album_page_list[i].status=='200'|| album_page_list[i].status=='020'){
            str+="<div class='fstatus1'>"+"▪&nbsp;"+album_page_list[i].statusText+"</div>"
        }else if(album_page_list[i].status=='000'){
            str+="<div class='fstatus2'>"+"▪&nbsp;"+album_page_list[i].statusText+"</div>"
        }else{
            str+="<div class='fstatus3'>"+"▪&nbsp;"+album_page_list[i].statusText+"</div>"
        }
        str+="</div>"
        str+="</div>"
    }
    $("#fangan").append(str);
    for(var i=begin;i<end;i++){
        $("#fangan_image"+i).css({"background-image":"url('"+album_page_list[i].photo_cover+"')"});
    }
}

//我的产品列表html
function product(begin,end){
    console.log(begin)
    console.log(end)
    $("#tc_table").html('');
    var str = '';
    str+="<tr>"
    for (var i = 0; i < tc_table.colname.length; i++) {
        var j=i+1;
        str+="<th class='c1'>"+tc_table.colname[i]+"</th>"
    }
    str+="</tr>"
    for(var i=begin;i<end;i++){
        str+="<tr>"
        str+="<th class='d1'>"+product_page_list[i].productTitle+"</th>"
        str+="<th class='d1'>"+product_page_list[i].ac_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].tc_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].colors_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].spec_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].status_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].str_text+"</th>"
        str+="<th class='d1'>"
        if(product_page_list[i].cover!=""){
            str+="<div class='data_image' id='data_image"+i+"'>"+"</div>"
        }
        str+="</th>"
        str+="<th class='d1'>"
        if(product_page_list[i].collected==true){
            str+="<div class='scbotton' onclick='proshoucang("+i+")' id='proshoucang"+i+"'>"+"取消收藏"+"</div>"
        }else{
            str+="<div class='scbotton1' onclick='proshoucang("+i+")' id='proshoucang"+i+"'>"+"收藏"+"</div>"
        }
        str+="</th>"

        str+="</tr>"
    }

    $("#tc_table").append(str);
    for(var i=begin;i<end;i++) {
        $("#data_image"+i).css({"background-image":"url('"+product_page_list[i].cover+"')"});
    }
}

//收藏的方案html
function scproject(begin,end){
    $("#d0container").html('');
    var str = '';
    for (var i = begin; i < end; i++) {
        str+="<div class='projectitem'>";
        //str+="<div class='designimage' id='image_"+i+"'/>"
        str+='<div class="designimage" id="image_'+i+'" onclick="click_album(&quot;'+fav_albums[i].search_albums.web_id_code+'&quot;)" />'
        if(fav_albums[i].panorama == true){
            str+="<div class='wholeview'>"+"全景图"+"</div>"
        }
        str+="<div class='designer_text'>";
        str+="<span class='d_area'>"+fav_albums[i].count_area+"㎡"+"&nbsp;|"+"</span>";
        str+="<span class='d_title'>"+"&nbsp;"+fav_albums[i].title+"</span>";
        str+="</div>";
        str+="<div class='d_detail1'>";
        str+="<span class='iconfont icon-chakan' id='look' style='color:#B7B7B7;margin-left:16px;'>"+"</span>";
        str+="<span class='sclooknumber'>"+fav_albums[i].count_visit+"</span>";
        str+="<span class='iconfont icon-dianzan2' id='sclike_"+i+"' style='color:#B7B7B7;margin-left:42px;' onclick='sclike("+i+")'>"+"</span>";
        str+="<span class='sclooknumber' id='sclikenumber_"+i+"' onclick='sclike("+i+")'>"+fav_albums[i].count_praise+"</span>";
        str+="<span class='iconfont icon-shoucang2' id='sccollected_"+i+"' style='color:#B7B7B7;margin-left:42px;' onclick='sccollected("+i+")'>"+"</span>";
        str+="<span class='sclooknumber' id='sccollectednumber_"+i+"' onclick='sccollected("+i+")'>"+fav_albums[i].count_fav+"</span>";
        str+="</div>";
        str+="<div class='d_line'>"+"</div>"
        str+="<div class='d_person1'>";
        str+="<div class='d_personimage1' id='d_personimage"+i+"'>"+"</div>"
        str+="<span class='d_personname'>"+fav_albums[i].designer_detail.nickname+"</span>";
        if(fav_albums[i].designer_detail.approve_realname)
        {
            str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-left:10px;margin-top:4px;'>"+"</span>";
        }else{
            str+="<span class='iconfont icon-shimingrenzheng' style='color:#D2D1D1;font-size:16px;margin-left:10px;margin-top:4px;'>"+"</span>";
        }
        if(project.list[i].hot==true) {
            str += "<span class='iconfont icon-renqiwang' id='hot' style='color:#FFE115;margin-left:10px;margin-top:4px;'>" + "</span>"
        }
        str+="</div>";
        str+="</div>";;
        str+="</div>";
    }
    $("#d0container").append(str);
    for(var i = begin; i < end; i++){
        $("#image_"+i).css({"background-image":"url('"+ fav_albums[i].photo_cover +"')"});
        $("#d_personimage"+i).css({"background-image":"url('"+fav_albums[i].designer_detail.url_avatar+"')"});
        // document.getElementById("image_"+i).style.backgroundImage="url('"+"../image/8.png"+"')";
        if(fav_albums[i].fav){
            $("#sccollectednumber_"+i).css({"color":"#1582FF"});
            document.getElementById("sccollected_"+i).className = "iconfont icon-buoumaotubiao44";
            $("#sccollected_"+i).css({"color":"#1582FF"});
        }else{
            $("#sccollectednumber_"+i).css({"color":"#B7B7B7"});
            document.getElementById("sccollected_"+i).className = "iconfont icon-shoucang2";
            $("#sccollected_"+i).css({"color":"#B7B7B7"})
        }

        if(fav_albums[i].liked){
            $("#sclikenumber_"+i).css({"color":"#1582FF"});
            document.getElementById("sclike_"+i).className = "iconfont icon-dianzan";
            $("#sclike_"+i).css({"color":"#1582FF","font-size":"16px"});
        }else{
            $("#sclikenumber_"+i).css({"color":"#B7B7B7"});
            document.getElementById("sclike_"+i).className = "iconfont icon-dianzan2";
            $("#sclike_"+i).css({"color":"#B7B7B7","font-size":"16px"})
        }

    }
}
//收藏的产品html
function scproduct(begin,end){
    $("#d1container").html("");
    var str1 = '';
    for (var i = begin; i < end; i++) {
        str1+="<div class='productview'>"
        //str1+="<div class='pimage' id='primage_"+i+"'>"+"</div>";
        str1+='<div class="pimage" id="primage_'+i+'" onclick="go_detail(&quot;'+fav_products[i].web_id_code+'&quot;)"/>'
        str1+="<div class='productname'>"+fav_products[i].name+"</div>";
        str1+="<div class='priceview'>";
        if(fav_products[i].guide_price>0)
            str1+="<span class='pricetxt'>"+"¥"+fav_products[i].guide_price+"</span>";
        else
            str1+="<span class='pricetxt'>价格面议</span>";
        str1+="<div class='lookview'>";
        str1+="<span class='iconfont icon-shoucang2' id='scprocollected_"+i+"' style='color:#B7B7B7;' onclick='scprocollected("+i+")'>"+"</span>";
        str1+="<span class='scprolooknumber' id='scprocollectednumber_"+i+"' onclick='scprocollected("+i+")'>"+fav_products[i].count_fav+"</span>";
        str1+="</div>";
        str1+="</div>";
        str1+="<div class='details'>";
        str1+="<span class='companytext'>"+fav_products[i].brand.brand_name+"</span>"
        str1+="<div class='lookview'>";
        str1+="<div class='dingwei'>"+"</div>";
        //地址
        str1+="<span class='areatxt'></span>";
        str1+="</div>";
        str1+="</div>"
        str1+="</div>";
    }
    $("#d1container").append(str1);
    for (var i = begin; i < end; i++) {
        $("#primage_"+i).css({"background-image":"url('"+fav_products[i].cover+"')"});
        if(fav_products[i].collected){
            $("#scprocollectednumber_"+i).css({"color":"#1582FF"});
            document.getElementById("scprocollected_"+i).className = "iconfont icon-buoumaotubiao44";
            $("#scprocollected_"+i).css({"color":"#1582FF"});
        }else{
            $("#scprocollectednumber_"+i).css({"color":"#B7B7B7"});
            document.getElementById("scprocollected_"+i).className = "iconfont icon-shoucang2";
            $("#scprocollected_"+i).css({"color":"#B7B7B7"})
        }
    }
}

// 消息通知——系统消息
function systemnotice(begin,end){
    $("#c0container").html("");
    var str="";
    for(var i=begin;i<end;i++){
        str+="<div class='systemitem'>"
        str+="<div class='systemhead'>"
        str+="<div class='systemlabel'>"+"【"+sys_notify[i].type_text+"】"+"</div>"
        str+="<div class='systemtime'>"+sys_notify[i].time+"</div>"
        str+="</div>"
        str+="<div class='systemcontent'>"+ sys_notify[i].content +"</div>"
        str+="</div>"
    }
    $("#c0container").append(str);
}
// 消息通知——互动消息
function acticenotice(begin,end){
    $("#c1container").html('');
    var str="";
    for(var i=begin;i<end;i++){
        str+="<div class='systemitem1'>"
        str+="<div class='noticeimage' id='noticeimage"+i+"'>"+"</div>"
        str+="<div class='noticemiddle'>"
        str+="<div class='noticedetail'>"
        str+="<label class='noticename'>"+fav_notify[i].sender_name+"</label>"
        str+="<label class='noticetime'>"+fav_notify[i].time+"</label>"
        str+="</div>"
        if(fav_notify[i].notify_type == 0){
            //关注
            str+="<div class='noticedetail1'>" + "关注了你" + "</div>"
        }
        if(fav_notify[i].notify_type == 1){
            str+="<div class='noticedetail1'>" + "点赞了你的作品《" + fav_notify[i].album_title + "》" + "</div>"
        }
        if(fav_notify[i].notify_type == 2){
            str+="<div class='noticedetail1'>" + "收藏了你的作品《" + fav_notify[i].album_title + "》" + "</div>"
        }
        //str+="<div class='noticedetail1'>"+notice.list1[i].content+"</div>"
        str+="</div>"
        if(fav_notify[i].fav==false){
            str+="<div class='noticeguanzhu' id='noticeguanzhu"+i+"' onclick='noticeguanzhu("+i+")'>"+"关注"+"</div>"
        }else{
            str+="<div class='noticeguanzhu' id='noticeguanzhu"+i+"' onclick='noticeguanzhu("+i+")'>"+"已关注"+"</div>"
        }
        str+="</div>"
    }
    $("#c1container").append(str);
    for(var i=begin;i<end;i++){
        $("#noticeimage"+i).css({"background-image":"url('"+fav_notify[i].sender_avatar+"')"});
    }
}
// 消息通知——评论
function noticepinglun(begin,end){
    console.log(end);
    $("#c2container").html('');
    var str="";
    for(var i=begin;i<end;i++){
        str+="<div class='systemitem2' id='noticepinglun"+i+"'>"
        str+="<div class='noticeimage' id='noticepimage"+i+"'>"+"</div>"
        str+="<div class='noticemiddle'>"
        str+="<div class='noticedetail'>"
        str+="<label class='noticename'>"+comment[i].sender_name+"</label>"
        str+="<label class='noticetime1'>"+comment[i].time+"</label>"
        str+="</div>"
        str+="<div class='noticedetail2'>"
        if(comment[i].target_comment_id != 0){
            str+="<label class='pinlunno'>"+"回复"+notice.list2[i].reviewperson+"评论您的"+notice.list2[i].article+"："+"</label>"
        }else{
            str+="<label class='pinlunno'>"+"评论您的《"+comment[i].album_title+"》："+"</label>"
        }
        str+=comment[i].content
        str+="</div>"
        str+="<div class='review' id='review"+i+"'onclick='review("+i+")'>"+"回复"+"</div>"
        str+="<div id='noticepl"+i+"' class='noticepl' style='display: none;'>"
        str+="<div class='review1' id='review1"+i+"'onclick='review("+i+")'>"+"回复"+"</div>"
        str+="<div class='pinglunblock' id='ping"+i+"' contentEditable='true' onkeyup='checkContent("+i+")'>"+"</div>"
        str+="<div class='pl_placeholder'>"+"写下您的评论..."+"</div>"
        str+="<div class='button2' type='submit' onclick='commit1("+i+")' id='btnButton"+i+"'>"+"回复"+"</div>";
        str+="</div>"
        str+="</div>"
        str+="</div>"
    }
    $("#c2container").append(str);
    for(var i=begin;i<end;i++){
        $("#noticepimage"+i).css({"background-image":"url('"+comment[i].sender_avatar+"')"});
    }
}
function commit1(i){
    console.log('f'+i)
    var cont=document.getElementById("ping"+i);
    var content=cont.innerText;
    console.log('i'+i)
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var valuestr = content.trim();
    var time=getCurrentDate(2);
    var revaue = content.replace(/^\s*|(\s*$)/g,"");
    if(revaue<= 0){
        // layer.msg('评论内容不能为空或全为空格！', {icon: 2});
        return;
    } else{
        var personname="Tracy"
        var image="images/designer/1.png"
        var a={"name":personname,"image":image,"reviewperson":notice.list2[i].name,"time":time,"content":content,"article":notice.list2[i].article};
        notice.list2.unshift(a);
        console.log(notice.list2)
        $("#c2container").html("")
        var nowpage4 = $("#page4").paging({
            nowPage: 1, // 当前页码
            pageNum: Math.ceil(notice.list2.length / 4), // 总页码
            buttonNum: Math.ceil(notice.list2.length / 4), //要展示的页码数量
            canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
            showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
            callback: function (num) { //回调函数
                console.log('sss'+num);
                //更多产品
                // $(function(e) {
                $("#produ4").html("");
                var txt="<div class='c0container' id='c2container'></div>"
                $("#produ4").append(txt);
                var total=Math.min(num*4,notice.list2.length)
                console.log(num+'sss'+total)
                noticepinglun((num-1)*4,total);
            }
        });
        noticepinglun(nowpage4.options.nowPage-1,nowpage4.options.nowPage*4)
    }
}
//获取现在时间
function getCurrentDate(format) {
    var now = new Date();
    var year = now.getFullYear(); //得到年份
    var month = now.getMonth();//得到月份
    var date = now.getDate();//得到日期
    var day = now.getDay();//得到周几
    var hour = now.getHours();//得到小时
    var minu = now.getMinutes();//得到分钟
    var sec = now.getSeconds();//得到秒
    month = month + 1;
    if (month < 10) month = "0" + month;
    if (date < 10) date = "0" + date;
    if (hour < 10) hour = "0" + hour;
    if (minu < 10) minu = "0" + minu;
    if (sec < 10) sec = "0" + sec;
    var time = "";
    //精确到天
    if(format==1){
        time = year + "-" + month + "-" + date;
    }
    //精确到分
    else if(format==2){
        time = year + "-" + month + "-" + date+ " " + hour + ":" + minu + ":" + sec;
    }
    return time;
}

function changenav(i){
    if(i==navactive){
        return;
    }else{
        document.getElementById("navtitle"+i).className = "nav_title1";
        document.getElementById("navtext"+i).className = "nav_text1";
        document.getElementById("navtitle"+navactive).className = "nav_title";
        document.getElementById("navtext"+navactive).className = "nav_text";
        $('#b'+navactive).hide().removeClass('show')
        navactive=i;
        $('#b'+navactive).show().addClass('show')
    }

    if(i == 0){
        //我的方案
    }else if(i == 1){
        //产品列表
        get_product_list_filter_types();
        get_user_brand();
        get_product_list();
    }else if(i == 2){
        //我的统计
        get_album_chart();
        get_product_chart();
        get_product_chart_use();
    }else if(i == 3){
        //收藏关注
        get_designer_fav_albums();
        get_designer_fav_product();
        get_designer_fav_designer();
    }else if(i == 4){
        //消息通知
        get_comment_list();
        get_fav_list();
        get_sysNotify();
    }else if(i == 5){
        //个人中心
        //个人资料
        getUserInfo();
        //实名认证
        getUserRealnameInfo();
        //设计师认证
        getAppInfo();

    }else if(i == 6){
        //安全中心
    }else{

    }
}

function tjchangenav(i){
    if(tongjinav==i){
        return;
    }else{
        console.log(i)
        for(var a=0;a<2;a++){
            if(a!=i){
                document.getElementById("g"+a).className = "designtitle1";
                $('#g'+a+'content').hide().removeClass('show')
            }else{
                tongjinav=i;
                document.getElementById("g"+a).className = "designtitle";
                $('#g'+a+'content').show().addClass('show')
            }

        }
    }
}

function noticechangenav(i){
    if(noticenav==i){
        return;
    }else{
        document.getElementById("c"+noticenav).className = "designtitle1";
        $('#c'+noticenav+'content').hide().removeClass('show')
        noticenav=i;
        document.getElementById("c"+noticenav).className = "designtitle";
        $('#c'+noticenav+'content').show().addClass('show')
    }

}
function scchangenav(i){
    console.log('ssssss')

    if(shoucangnav==i){
        console.log('s'+i)
        return;
    }else{
        console.log(i)
        for(var a=0;a<3;a++){
            if(a!=i){
                document.getElementById("d"+a).className = "designtitle1";
                $('#d'+a+'content').hide().removeClass('show')
            }else{
                shoucangnav=i;
                document.getElementById("d"+a).className = "designtitle";
                $('#d'+a+'content').show().addClass('show')
            }

        }
    }
}
//个人中心导航
function personchangenav(i){
    console.log(i)

    if(personnav==i) {
        console.log('s' + i)
        return;
    }else if(i==1 && personnav!=i){
        document.getElementById("f0").className = "designtitle1";
        $('#f0content').hide().removeClass('show')
        document.getElementById("f2").className = "designtitle1";
        $('#f2content').hide().removeClass('show')
        personnav=i;
        document.getElementById("f"+i).className = "designtitle";

        console.log(realnameDetail.log_status);
        if(realnameDetail.log_status==0){
            //未认证
            $('#f1content2').hide().removeClass('show')
            $('#f1content1').hide().removeClass('show')
            $('#f1content').show().addClass('show')
        }else if(realnameDetail.log_status==2){
            //不通过
            $('#f1content2').hide().removeClass('show')
            $('#f1content1').hide().removeClass('show')
            $('#f1content').show().addClass('show')
            $('#f1content3').show().addClass('show')
        }else if(realnameDetail.log_status==1){
            //待审核
            $('#f2content1').show().addClass('show');
        }else if(realnameDetail.log_status == -1){
            //已通过
            $("#f1content2").show().addClass('show');
        }
        else{
            for(var b=0;b<4;b++){
                if(b==persondetail.identity){
                    $('#f1content'+b).show().addClass('show')
                }else{
                    $('#f1content'+b).hide().removeClass('show')
                }
            }
        }
    }else if(i==2 && personnav!=i){
        document.getElementById("f0").className = "designtitle1";
        $('#f0content').hide().removeClass('show')
        document.getElementById("f1").className = "designtitle1";
        $('#f2content').hide().removeClass('show')
        personnav=i;
        document.getElementById("f"+i).className = "designtitle";

        if(designidentity==0){
            $('#f2content').show().addClass('show')
            $('#f2content1').hide().removeClass('show')
            $('#f0content').hide().removeClass('show')
            $('#f1content').hide().removeClass('show')
            $('#f1content2').hide().removeClass('show')
            $('#f1content1').hide().removeClass('show')
            $('#f1content3').hide().removeClass('show')
        }else{
            $('#f2content1').show().addClass('show')
            $('#f2content').hide().removeClass('show')
            $('#f0content').hide().removeClass('show')
            $('#f1content').hide().removeClass('show')
            $('#f1content2').hide().removeClass('show')
            $('#f1content3').hide().removeClass('show')
        }
    }
    else{
        for(var a=0;a<3;a++){
            if(a!=i){
                document.getElementById("f"+a).className = "designtitle1";
                $('#f'+a+'content').hide().removeClass('show')
            }else{
                personnav=i;
                document.getElementById("f"+a).className = "designtitle";

                $('#f'+a+'content').show().addClass('show')
                $("#f2content1").hide().removeClass('show');
                $("#f1content").hide().removeClass('show');
                $("#f1content1").hide().removeClass('show');
                $("#f1content2").hide().removeClass('show');
                $("#f1content3").hide().removeClass('show');
            }

        }
    }
}

//安全中心导航
function safechangenav(i){
    console.log('ssssss')

    if(safenav==i){
        console.log('s'+i)
        return;
    }else{
        console.log(i)
        for(var a=0;a<3;a++){
            if(a!=i){
                document.getElementById("e"+a).className = "designtitle1";
                $('#e'+a+'content').hide().removeClass('show')
            }else{
                safenav=i;
                document.getElementById("e"+a).className = "designtitle";
                $('#e'+a+'content').show().addClass('show')
            }

        }
        //如果是绑定微信
        if(i==2){
            showQrcode();
        }
    }
}

//方案的点赞
function like(i){
    console.log('sss'+i)
    if(fangan.list[i].dolike==false){
        var a=fangan.list[i].like+1;
        fangan.list[i].like=a;
        $("#likenumber_"+i).html(fangan.list[i].like);
        $("#likenumber_"+i).css({"color":"#1582FF"});
        document.getElementById("like_"+i).className = "iconfont icon-dianzan";
        $("#like_"+i).css({"color":"#1582FF","font-size":"16px"});
        fangan.list[i].dolike=true;
    }else{
        var a=fangan.list[i].like-1;
        fangan.list[i].like=a;
        $("#likenumber_"+i).html(a);
        $("#likenumber_"+i).css({"color":"#B7B7B7"});
        document.getElementById("like_"+i).className = "iconfont icon-dianzan2";
        $("#like_"+i).css({"color":"#B7B7B7","font-size":"16px"})
        fangan.list[i].dolike=false;
    }
}
//方案的收藏
function collected(i){
    console.log('sss'+i)
    if(fangan.list[i].doshoucang==false){
        var a=fangan.list[i].shoucang+1;
        fangan.list[i].shoucang=a;
        $("#collectednumber_"+i).html(fangan.list[i].shoucang);

        $("#collectednumber_"+i).css({"color":"#1582FF"});
        document.getElementById("collected_"+i).className = "iconfont icon-buoumaotubiao44";
        $("#collected_"+i).css({"color":"#1582FF"});
        fangan.list[i].doshoucang=true;
    }else{
        var a=fangan.list[i].shoucang-1;
        fangan.list[i].shoucang=a;
        $("#collectednumber_"+i).html(a);
        $("#collectednumber_"+i).css({"color":"#B7B7B7"});
        document.getElementById("collected_"+i).className = "iconfont icon-shoucang2";
        $("#collected_"+i).css({"color":"#B7B7B7"})
        fangan.list[i].doshoucang=false;
    }
}
//方案的复制
function fuzhi(i){
    console.log('sss'+i)
    var a=fangan.list[i].fuzhi+1;
    fangan.list[i].fuzhi=a;
    $("#fuzhi"+i).html(fangan.list[i].fuzhi);
}

//点击某个方案的更多选择按钮
function choosemore(i){
    $("#morechoose_"+i).show().addClass('show');
}
function edit(i){
    var album_id = album_page_list[i].web_id_code;
    location.href= '/center/album/'+album_id+'/edit';
}
function save(i){
    $("#morechoose_"+i).hide().removeClass('show');
    $(".main").show().addClass('show');
}
function look(i){
    $("#morechoose_"+i).hide().removeClass('show');
}
function del(i){
    var album_id = album_page_list[i].web_id_code
    layer.load(1);
    layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
        ajax_post("/center/album/api/delete",{id:album_id}, function (res) {
            layer.closeAll('loading');
            if (res.status == 1) {
                layer.msg('操作成功！');
                get_album_list();
            } else {
                layer.msg(res.msg);
            }
        });
        layer.close(index);
    },function(){
        layer.closeAll('loading');
    });

}
function cancel(){
    $(".main").hide().removeClass('show');
}
function proshoucang(i){

    var album_id = product_page_list[i].web_id_code;
    if(product_page_list[i].collected == false){
        //点击时未收藏
        ajax_post('/product/api/collect',{op:1,aid:album_id},function(res){

            if(res.status){
                $("#proshoucang"+i).html("取消");
                document.getElementById("proshoucang"+i).className = "scbotton";
                product_page_list[i].collected = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }else{
        ajax_post('/product/api/collect',{op:2,aid:album_id},function(res){

            if(res.status){
                $("#proshoucang"+i).html("收藏");
                document.getElementById("proshoucang"+i).className = "scbotton1";
                product_page_list[i].collected = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
}
function noticeguanzhu(i){
    layer.load(1);
    var designer_id = fav_notify[i].sender_web_id_code;
    if(fav_notify[i].fav==false){
        ajax_post('/designer/api/focus',{op:1,aid:designer_id},function(res){

            if(res.status){
                $('#noticeguanzhu'+i).html("已关注")
                fav_notify[i].fav=true;
            }else{
                layer.msg(res.code)
            }
            layer.closeAll("loading");

        },function(){})
    }else{
        ajax_post('/designer/api/focus',{op:2,aid:designer_id},function(res){

            if(res.status){
                $('#noticeguanzhu'+i).html("关注")
                fav_notify[i].fav=false;
            }else{
                layer.msg(res.code)
            }
            layer.closeAll("loading");

        },function(){})
    }
}
function checkContent(i) {
    console.log('sssss')
    var cont=document.getElementById("ping"+i);
    var content=cont.innerText
    console.log('sss'+cont.innerText+'aaaa');
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var valuestr = content.trim();
    console.log('contentlength'+content.length)
    var revaue = content.replace(/^\s*|(\s*$)/g,"");

    if(revaue<= 0){
        if(content.length<=0){
            $('.pl_placeholder').show().addClass('show');
        }else{
            $('.pl_placeholder').hide().removeClass('show');
        }
        $('#btnButton'+i).attr('disabled', true);
        $('#btnButton'+i).css({"background-color":"#D9D9D9","cursor":"not-allowed"})
    } else {
        $('.pl_placeholder').hide().removeClass('show');
        $('#btnButton'+i).attr('disabled', false);
        $('#btnButton'+i).css({"background-color":"#1582FF","cursor":"pointer"})

    }
}
function review(i){
    $("#review"+i).toggle()
    $("#noticepl"+i).toggle()
}
//收藏的方案的点赞
function sclike(i){
    layer.load(1);
    var album_id = fav_albums[i].search_albums.web_id_code;

    if(fav_albums[i].liked==false){

        ajax_post('/album/api/like',{op:1,aid:album_id},function(res){

            if(res.status){
                var a = fav_albums[i].count_praise+1;
                fav_albums[i].count_praise=a;
                $("#sclikenumber_"+i).html(a);
                $("#sclikenumber_"+i).css({"color":"#1582FF"});
                document.getElementById("sclike_"+i).className = "iconfont icon-dianzan";
                $("#sclike_"+i).css({"color":"#1582FF","font-size":"16px"});
                fav_albums[i].liked=true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})

    }else{

        ajax_post('/album/api/like',{op:2,aid:album_id},function(res){

            if(res.status){
                var a = fav_albums[i].count_praise-1;
                fav_albums[i].count_praise=a;
                $("#sclikenumber_"+i).html(a);
                $("#sclikenumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("sclike_"+i).className = "iconfont icon-dianzan2";
                $("#sclike_"+i).css({"color":"#B7B7B7","font-size":"16px"})

                fav_albums[i].liked=false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
}
//收藏的方案的收藏
function sccollected(i){
    layer.load(1);
    var album_id = fav_albums[i].search_albums.web_id_code;
    if(fav_albums[i].fav == false){
        //点击时未收藏
        ajax_post('/album/api/collect',{op:1,aid:album_id},function(res){

            if(res.status){
                var a = fav_albums[i].count_fav+1;
                fav_albums[i].count_fav = a;
                $("#sccollectednumber_"+i).html(a);

                $("#sccollectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("sccollected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#sccollected_"+i).css({"color":"#1582FF"});
                fav_albums[i].fav = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }else{
        ajax_post('/album/api/collect',{op:2,aid:album_id},function(res){

            if(res.status){
                var a = fav_albums[i].count_fav - 1;
                console.log(a);
                fav_albums[i].count_fav = a;
                $("#sccollectednumber_"+i).html(a);

                $("#sccollectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("sccollected_"+i).className = "iconfont icon-shoucang2";
                $("#sccollected_"+i).css({"color":"#B7B7B7"})
                fav_albums[i].fav = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
}


//收藏关注（收藏的产品）的收藏
function scprocollected(i){
    layer.load(1);
    var product_id = fav_products[i].web_id_code;

    if(fav_products[i].collected==false){

        ajax_post('/product/api/collect',{op:1,aid:product_id},function(res){

            if(res.status){
                var a = fav_products[i].count_fav + 1;
                fav_products[i].count_fav = a;
                $("#scprocollectednumber_"+i).html(a);

                $("#scprocollectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("scprocollected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#scprocollected_"+i).css({"color":"#1582FF"});
                fav_products[i].collected = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }else{

        ajax_post('/product/api/collect',{op:2,aid:product_id},function(res){

            if(res.status){
                var a = fav_products[i].count_fav - 1;
                fav_products[i].count_fav = a;
                $("#scprocollectednumber_"+i).html(a);

                $("#scprocollectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("scprocollected_"+i).className = "iconfont icon-shoucang2";
                $("#scprocollected_"+i).css({"color":"#B7B7B7"})
                fav_products[i].collected = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
}


//我的统计浏览量产品的收藏
function toppcollected(i){
    layer.load(1);
    var product_id = chart_top_product_visit[i].web_id_code;

    if(chart_top_product_visit[i].collected==false){


        ajax_post('/product/api/collect',{op:1,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_visit[i].count_fav + 1;
                chart_top_product_visit[i].count_fav = a;
                $("#toppcollectednumber_"+i).html(a);

                $("#toppcollectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("toppcollected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#toppcollected_"+i).css({"color":"#1582FF"});

                chart_top_product_visit[i].collected = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})

    }else{
        ajax_post('/product/api/collect',{op:2,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_visit[i].count_fav - 1;
                chart_top_product_visit[i].count_fav = a;
                $("#toppcollectednumber_"+i).html(a);
                $("#toppcollectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("toppcollected_"+i).className = "iconfont icon-shoucang2";
                $("#toppcollected_"+i).css({"color":"#B7B7B7"})

                chart_top_product_visit[i].collected = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
    console.log(chart_top_product_visit[i].collected);

}

//我的统计收藏量产品的收藏
function topp1collected(i){

    layer.load(1);
    var product_id = chart_top_product_collect[i].web_id_code;

    if(chart_top_product_collect[i].collected==false){


        ajax_post('/product/api/collect',{op:1,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_collect[i].count_fav + 1;
                chart_top_product_collect[i].count_fav = a;
                $("#topp1collectednumber_"+i).html(a);

                $("#topp1collectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("topp1collected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#topp1collected_"+i).css({"color":"#1582FF"});

                chart_top_product_collect[i].collected = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})

    }else{
        ajax_post('/product/api/collect',{op:2,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_collect[i].count_fav - 1;
                chart_top_product_collect[i].count_fav = a;
                $("#topp1collectednumber_"+i).html(a);

                $("#topp1collectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("topp1collected_"+i).className = "iconfont icon-shoucang2";
                $("#topp1collected_"+i).css({"color":"#B7B7B7"})

                chart_top_product_collect[i].collected = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
}

//设计师方案html
function scdesigner(begin,end){
    console.log(begin);
    console.log(end);
    $("#d2container").html("");
    var str = '';
    for(var i=begin;i<end;i++){
        str+="<div class='fangan_item'>";
        //str+="<div class='d_perimage' id='d_perimage"+i+"'>"+"</div>"
        str+='<div class="d_perimage" id="d_perimage'+i+'" onclick="go_designer_detail(&quot;'+fav_designer[i].web_id_code+'&quot;)"/>'
        str+="<div class='d_middle'>";
        str+="<div class='dm_head'>";
        str+="<div class='df_name'>"+fav_designer[i].detail.nickname+"</div>";
        if(fav_designer[i].level=="金牌"){
            str+="<div class='dm_jinpai'>"+"</div>"
        }else if(fav_designer[i].level=="资深"){
            str+="<div class='dm_zishen'>"+"</div>"
        }else if(fav_designer[i].level=="新手"){
            str+="<div class='dm_xinshou'>"+"</div>"
        }else if(fav_designer[i].level=="见习"){
            str+="<div class='dm_jianxi'>"+"</div>"
        }else if(fav_designer[i].level=="专业"){
            str+="<div class='dm_zhuanye'>"+"</div>"
        }
        if(fav_designer[i].focused==false){
            str+="<div class='design_guanzhu' onclick='de_guanzhu("+i+")' id='desi_guanzhu"+i+"'>"+"关注"+"</div>"
        }else{
            str+="<div class='design_guanzhu1' onclick='de_guanzhu("+i+")' id='desi_guanzhu"+i+"'>"+"已关注"+"</div>"
        }
        str+="</div>"
        str+="<div class='de_posi'>";
        str+="<div class='position_icon'>"+"</div>";
        str+="<div class='de_position'>"+fav_designer[i].area_text+"</div>"
        str+="</div>"
        str+="<div class='de_style'>"+"<span>"+"擅长风格："+"</span>"+"<span>"+fav_designer[i].styles_text+"</span>"+"</div>";
        str+="<div class='de_detail'>"
        str+="<div class='dm_fan'>";
        str+="<div class='dm_fanspan'>"+fav_designer[i].fans+"</div>"
        str+="<div class='dm_fanspan1'>"+"粉丝数"+"</div>"
        str+="</div>";
        str+="<div class='dm_line'>"+"</div>"
        str+="<div class='dm_fan1'>";
        str+="<div class='dm_fanspan'>"+fav_designer[i].count_upload_album+"</div>"
        str+="<div class='dm_fanspan1'>"+"设计方案数"+"</div>"
        str+="</div>";
        str+="</div>";
        str+="</div>";
        str+="<div class='de_right'>";
        if( fav_designer[i].limit_albums.length>0){
            for(var j=0;j<fav_designer[i].limit_albums.length;j++){
                str+="<div class='dr_container'>"
                //str+="<div class='dr_image' id='dr_image"+i+"_"+j+"'>"+"</div>";
                str+='<div class="dr_image" id="dr_image'+i+'_'+j+'" onclick="click_album(&quot;'+fav_designer[i].limit_albums[j].web_id_code+'&quot;)">'+'</div>'
                str+="<div class='dr_text'>"+fav_designer[i].limit_albums[j].name+"</div>"
                str+="</div>"
            }
        }
        str+="</div>"
        str+="</div>"
    }

    $("#d2container").append(str);
    for (var i = begin; i < end; i++) {
        $("#d_perimage"+i).css({"background-image":"url('"+fav_designer[i].detail.url_avatar+"')"});
        if(fav_designer[i].limit_albums.length>0){
            for(var j=0;j<fav_designer[i].limit_albums.length;j++){
                $("#dr_image"+i+"_"+j).css({"background-image":"url('"+fav_designer[i].limit_albums[j].photo_cover+"')"});
            }
        }
    }
};
//收藏的设计师的关注
function de_guanzhu(i){
    layer.load(1);
    var designer_id = fav_designer[i].web_id_code;

    if(fav_designer[i].focused==false){
        ajax_post('/designer/api/focus',{op:1,aid:designer_id},function(res){

            if(res.status){
                document.getElementById("desi_guanzhu"+i).className = "design_guanzhu1";
                $("#desi_guanzhu"+i).html("已关注")
                fav_designer[i].focused=true;
            }else{
                layer.msg(res.code)
            }
            layer.closeAll("loading");

        },function(){})
    }else{
        ajax_post('/designer/api/focus',{op:2,aid:designer_id},function(res){

            if(res.status){
                document.getElementById("desi_guanzhu"+i).className = "design_guanzhu";
                $("#desi_guanzhu"+i).html("关注");
                fav_designer[i].focused=false;

            }else{
                layer.msg(res.code)
            }
            layer.closeAll("loading");

        },function(){})
    }
}

//安全中心——修改密码
function changepsw() {
    $("#bypsw").toggle();
    $("#byphone").toggle();
}
function jiebang(){
    $("#jiebang").hide().removeClass('show');
    $(".jbcontent").show().addClass('show');
}
function closefriend1(){
    $("#qrcode1").hide().removeClass('show');
}
function closefriend(){
    $("#qrcode1").show().addClass('show');
}
function comjiebang(){
    $("#jiebang").hide().removeClass('show');
    $(".jbcontent").hide().removeClass('show');
    $(".weixin").show().addClass('show');
}
function identity(){
    $('#f0content').hide().removeClass('show')
    $('#f1content').show().addClass('show')
    $('#f2content').hide().removeClass('show')
}



function del_idfront(){
    console.log('ss')
    id_front.splice(0,1);
    $(".del_fengmian").hide().removeClass('show');
    $(".id_front").show().addClass('show')
    $("#preview").css({"background-image":"url()"});
}
function showdel_idfront(){
    if(id_front.length>0){
        console.log('sss1')
        $(".del_fengmian").show().addClass('show');
        $("#upload-inputf").css({"height":"134px"})
    }else{
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-inputf").css({"height":"156px"})
    }
}
function showdel_idfront1(){
    if(id_front.length>0){
        console.log('sss2')
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-inputf").css({"height":"134px"})
    }else{
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-inputf").css({"height":"156px"})
    }
}


function del_idback(){
    console.log('ss')
    id_back.splice(0,1);
    $(".del_fengmian1").hide().removeClass('show');
    $(".id_back").show().addClass('show')
    $("#preview1").css({"background-image":"url()"});
}
function showdel_idback(){
    if(id_back.length>0){
        $(".del_fengmian1").show().addClass('show');
        $("#upload-input1").css({"height":"134px"})
    }else{
        $(".del_fengmian1").hide().removeClass('show');
        $("#upload-input1").css({"height":"156px"})
    }
}
function showdel_idback1(){
    if(id_back.length>0){
        $(".del_fengmian1").hide().removeClass('show');
        $("#upload-input1").css({"height":"134px"})
    }else{
        $(".del_fengmian1").hide().removeClass('show');
        $("#upload-input1").css({"height":"156px"})
    }
}
function closef1content3(){
    persondetail.identity=0
    $('#f1content3').hide().removeClass('show')
}
//实名动作
// function smcommit() {
//     persondetail.identity=1
//     $('#f1content2').hide().removeClass('show')
//     $('#f1content').hide().removeClass('show')
//     $('#f1content1').show().addClass('show')
//     $('#f1content3').hide().removeClass('show')
// }

//年月日三级联动
function years(id,selectValue) {
    console.log('date:'+id)
    var dates = new Date();
    var nowYear = dates.getFullYear();
    for (i = nowYear; i >= 1968; i--) {
        //用于打印输出年的范围，以下类同
        if(selectValue != null && i == selectValue){
            var str = "<li value=\"" + i + "\" class=\"select-this\">" + i + "</li>";
        }else{
            var str = "<li value=\"" + i + "\">" + i + "</li>";
        }

        $('#'+id).append(str);
    }
}

function months(id,selectValue) {
    $('#'+id).empty();
    for (i = 1; i <= 12; i ++) {

        if(selectValue != null && i == selectValue){
            var str = "<li value=\"" + i + "\" class=\"select-this\">" + i + "</li>";
        }else{
            var str = "<li value=\"" + i + "\">" + i + "</li>";
        }
        $('#'+id).append(str);
    }
}
function days(selectValue) {
    $('#day').empty();
    //判定润年平年进行设置 2月份
    var dayTime = 0;
    if (parseInt($(".select-menu-input").eq(11).val()) == 1 || parseInt($(".select-menu-input").eq(11).val()) == 3 || parseInt($(".select-menu-input").eq(11).val()) == 5 || parseInt($(".select-menu-input").eq(11).val()) == 7 || parseInt($(".select-menu-input").eq(11).val()) == 8 || parseInt($(".select-menu-input").eq(11).val()) == 10 || parseInt($(".select-menu-input").eq(11).val()) == 12) {
        dayTime = 31;
    } else if (parseInt($(".select-menu-input").eq(11).val()) == 4 || parseInt($(".select-menu-input").eq(11).val()) == 6 || parseInt($(".select-menu-input").eq(11).val()) == 9 || parseInt($(".select-menu-input").eq(11).val()) == 11) {
        days = 30;
    } else {
        if (parseInt($(".select-menu-input").eq(10).val()) % 400 == 0 || (parseInt($(".select-menu-input").eq(10).val()) % 4 == 0 && parseInt($(".select-menu-input").eq(10).val()) % 100 != 0)) {
            dayTime = 29;
        } else {
            dayTime = 28;
        }
    }
    for(i = 1; i<=dayTime;i++){

        if(selectValue != null && i == selectValue){
            var str = "<li value=\"" + i + "\" class=\"select-this\">" + i + "</li>";
        }else{
            var str = "<li value=\"" + i + "\">" + i + "</li>";
        }
        $('#day').append(str);
    }
}


function showPro(selectValue){
    var len = userInfo.provinces.length;
    for(var i = 0; i < len; i++) {
        if(userInfo.provinces[i].id == selectValue){
            var str = "<li value=\"" + userInfo.provinces[i].id + "\" class=\"select-this\">" + userInfo.provinces[i].name+ "</li>";
        }else{
            var str = "<li value=\"" + userInfo.provinces[i].id + "\">" + userInfo.provinces[i].name+ "</li>";
        }

        $('#province').append(str);
    }
}
function showCity(selectValue) {
    $('#city_list').empty();

    var cityLen=cities.length;
    var str = '';
    for(var j = 0; j < cityLen; j++) {
        if(cities[j].id == selectValue){
            str += "<li value=\"" + cities[j].id + "\"class=\"select-this\">" + cities[j].name+ "</li>";
        }else{
            str += "<li value=\"" + cities[j].id + "\">" + cities[j].name + "</li>";
        }
    }

    $('#city_list').append(str);

}
function showArea(selectValue) {
    $('#area').empty();

    var arealen = districts.length;
    var str = '';
    for(var z = 0; z < arealen; z++) {
        if(districts[z].id == selectValue){
            str += "<li value=\"" + districts[z].id + "\"class=\"select-this\">" + districts[z].name+ "</li>";
        }else{
            str += "<li value=\"" + districts[z].id + "\">" + districts[z].name + "</li>";
        }
    }

    $('#area').append(str);
}

function addedcation(i,data){
    if(data){
        var school = data.school;
        var education = data.education;
        var profession = data.profession;
        var graduate_year = data.graduate_year;
        var graduate_month = data.graduate_month;
        var showDel = i==0?'none':'show';
    }else{
        educatenumber = educatenumber + 1;

        var school = '';
        var education = '';
        var profession = '';
        var graduate_year = '';
        var graduate_month = '';
        var showDel = i==0?'none':'show';
    }

    var str2="";
    str2+="<div id='education"+i+"'>"
    str2+="<div>"
    str2+="<input type='text' name='placename'  class='titleinput' style='width:286px;margin-left:20px;margin-top:10px;' placeholder='就读学校' value='"+ school +"' />"
    str2+="<input type='text' name='placename'  class='titleinput' style='width:286px;margin-top:10px;' placeholder='学历' value='"+ education +"'/>"
    str2+="<label class='deletework' onclick='deleteeducation("+i+")' style='display: "+ showDel +";'>"+"删除本条教育经历"+"</label>"
    str2+="</div>"
    str2+="<div class='selectdesign' style='margin-bottom:20px;'>"
    str2+="<input type='text' name='placename'  class='titleinput' style='width:184px;margin-left:20px;margin-top:10px;' placeholder='专业' value='"+ profession +"'/>"
    str2+="<div class='select-menu1' style='width:194px;margin-left:10px;margin-top:10px;'>"
    str2+="<div class='select-menu-div1' style='width:194px;'>"
    str2+="<input readonly class='select-menu-input1' placeholder='毕业年份' value='"+ graduate_year +"'/>"
    str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str2+="</div>"
    str2+="<ul class='select-menu-ul1' style='width:194px;height: 200px; overflow:scroll;' id='byyear"+i+"'>"
    str2+="</ul>"
    str2+="</div>"
    str2+="<div class='select-menu1' style='width:194px;margin-left:10px;margin-top:10px;'>"
    str2+="<div class='select-menu-div1' style='width:194px;'>"
    str2+="<input readonly class='select-menu-input1' placeholder='毕业月份' value='"+ graduate_month +"'/>"
    str2+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str2+="</div>"
    str2+="<ul class='select-menu-ul1' style='width:194px;height: 200px; overflow:scroll;' id='bymonth"+i+"'>"
    str2+="</ul>"
    str2+="</div>"

    str2+="</div>"
    str2+="</div>"
    $('#education').append(str2);
    var b=(i+1)*2
    selectMenu1(1, b-2);
    selectMenu1(1, b-1);


    years("byyear" + i,graduate_year);
    months("bymonth" + i,graduate_month);
    //设置年月日初始化变量
    // $('#byyear' + i).val("");
    // $('#bymonth' + i).val("");
    $(".select-menu-ul1").eq(b-2).on("click", "li", function () {
        months("bymonth" + i);
    })


}

function addexperience(i,data){

    if(data){
        var company = data.company;
        var position = data.position;
        var start_year = data.start_year;
        var start_month = data.start_month;
        var end_year = data.end_year;
        var end_month = data.end_month;
        var work_description = data.work_description;
    }else{
        worknumber = worknumber + 1;

        var company = '';
        var position = '';
        var start_year = '';
        var start_month = '';
        var end_year = '';
        var end_month = '';
        var work_description = '';
    }

    work_description = work_description?work_description:''

    var str="";
    str+="<div id='work"+i+"'>"
    str+="<div>"
    str+="<input type='text' name='companyname'  class='titleinput' style='width:286px;margin-left:20px;margin-top:10px;' placeholder='公司名称' value='"+ company +"' />"
    str+="<input type='text' name='companyposition'  class='titleinput' style='width:286px;margin-top:10px;' placeholder='担任职位' value='"+ position +"' />"
    str+="<label class='deletework' onclick='deletework("+i+")'>"+"删除本条工作经历"+"</label>"
    str+="</div>"
    str+="<div class='selectdesign'>"

    str+="<div class='select-menu2' style='width:194px;margin-left:20px;margin-top:10px;'>"
    str+="<div class='select-menu-div2' style='width:194px;'>"
    str+="<input readonly class='select-menu-input2' placeholder='开始年份' value='"+ start_year +"'/>"
    str+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str+="</div>"
    str+="<ul class='select-menu-ul2' style='width:194px;height: 200px; overflow:scroll;' id='startyear"+i+"'>"
    str+="</ul>"
    str+="</div>"
    str+="<div class='select-menu2' style='width:194px;margin-left:10px;margin-top:10px;'>"
    str+="<div class='select-menu-div2' style='width:194px;'>"
    str+="<input readonly class='select-menu-input2' placeholder='开始月份' value='"+ start_month +"'/>"
    str+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str+="</div>"
    str+="<ul class='select-menu-ul2' style='width:194px;height: 200px; overflow:scroll;' id='startmonth"+i+"'>"
    str+="</ul>"
    str+="</div>"
    str+="<div class='link'>"+"至"+"</div>"
    str+="<div class='select-menu2' style='width:194px;margin-left:20px;margin-top:10px;'>"
    str+="<div class='select-menu-div2' style='width:194px;'>"
    str+="<input readonly class='select-menu-input2' placeholder='结束年份' value='"+ end_year +"'/>"
    str+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str+="</div>"
    str+="<ul class='select-menu-ul2' style='width:194px;height: 200px; overflow:scroll;' id='endyear"+i+"'>"
    str+="</ul>"
    str+="</div>"
    str+="<div class='select-menu2' style='width:194px;margin-left:10px;margin-top:10px;'>"
    str+="<div class='select-menu-div2' style='width:194px;'>"
    str+="<input readonly class='select-menu-input2' placeholder='结束月份' value='"+ end_month +"'/>"
    str+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str+="</div>"
    str+="<ul class='select-menu-ul2' style='width:194px;height: 200px; overflow:scroll;' id='endmonth"+i+"'>"
    str+="</ul>"
    str+="</div>"

    str+="</div>"
    str+="<textarea  name='styledecription' class='titleinput1' placeholder='工作描述，不超过200字' style='margin-bottom:20px;'>"+work_description+"</textarea>"
    str+="</div>"
    $('#experience').append(str);


    var b = (i + 1) * 4;
    selectMenu1(2,b-4);
    selectMenu1(2,b-3);
    years("startyear"+i,start_year);
    months("startmonth"+i,start_month);
    // //设置年月日初始化变量
    // $('#startyear'+i).val("");
    // $('#startmonth'+i).val("");
    $(".select-menu-ul2").eq(b-4).on("click","li",function(){
        months("startmonth"+i);
    })
    selectMenu1(2,b-2);
    selectMenu1(2,b-1);
    years("endyear"+i,end_year);
    months("endmonth"+i,end_month);
    // //设置年月日初始化变量
    // $('#endyear'+i).val("");
    // $('#endmonth'+i).val("");
    $(".select-menu-ul2").eq(b-2).on("click","li",function(){
        months("endmonth"+i);
    })
}
function addprize(i,data){
    if(data){
        var award_name = data.award_name;
        var award_year = data.award_year;
        var award_month = data.award_month;
        var showDel = i==0?'none':'show';
    }else{
        var award_name = '';
        var award_year = '';
        var award_month = '';
        var showDel = i==0?'none':'show';
        prizenumber = prizenumber + 1;
    }
    var str="";
    str+="<div id='prize"+i+"'>"
    str+="<div class='selectdesign' style='margin-bottom:20px;'>"
    str+="<input type='text' name='prizename'  class='titleinput' style='width:184px;margin-left:20px;margin-top:10px;' placeholder='证书名称' value='"+ award_name +"' />"
    str+="<div class='select-menu3' style='width:194px;margin-left:10px;margin-top:10px;'>"
    str+="<div class='select-menu-div3' style='width:194px;'>"
    str+="<input readonly class='select-menu-input3' placeholder='获利年' value='"+ award_year +"'/>"
    str+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str+="</div>"
    str+="<ul class='select-menu-ul3' style='width:194px;height: 200px; overflow:scroll;' id='getyear"+i+"'>"
    str+="</ul>"
    str+="</div>"
    str+="<div class='select-menu3' style='width:194px;margin-left:10px;margin-top:10px;'>"
    str+="<div class='select-menu-div3' style='width:194px;'>"
    str+="<input readonly class='select-menu-input3' placeholder='获利月' value='"+ award_month +"'/>"
    str+="<i class='fa fa-angle-down' style='font-size:22px;color:#B7B7B7;'></i>"
    str+="</div>"
    str+="<ul class='select-menu-ul3' style='width:194px;height: 200px; overflow:scroll;' id='getmonth"+i+"'>"
    str+="</ul>"
    str+="</div>"
    str+="<label class='deletework' onclick='deleteprize("+i+")' style='display: "+ showDel+";margin-top:20px;'>"+"删除本条证书与奖项"+"</label>"
    str+="</div>"

    str+="</div>"
    $('#prize').append(str);


    var b=(i+1)*2
    selectMenu1(3,b-2);
    selectMenu1(3,b-1);
    years("getyear"+i,award_year);
    months("getmonth"+i,award_month);
    // //设置年月日初始化变量
    // $('#getyear'+i).val("");
    // $('#getmonth'+i).val("");
    $(".select-menu-ul3").eq(b-2).on("click","li",function(){
        months("getmonth"+i);
    })
}

function deletework(i){
    $("#work"+i).hide().removeClass('show');
}
function deleteeducation(i){
    $("#education"+i).hide().removeClass('show');
}
function deleteprize(i){
    $("#prize"+i).hide().removeClass('show');
}