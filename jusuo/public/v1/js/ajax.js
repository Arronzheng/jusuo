//页面加载所要进行的操作
var ajaxStatus = true;
$(function () {
    //设置ajax当前状态(是否可以发送);
    ajaxStatus = true;
});

// ajax提交(post方式提交)
function ajax_post(url, data, success,error, show_loading, alone) {
    ajax(url, data, success, error,show_loading , true, false, 'post','json');
}

function ajax_upload(url,data,success,error,show_loading){

    ajaxStatus = false;//禁用ajax请求

    $.ajax({
        url:url,
        type:"post",
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        'beforeSend': function () {
            if(show_loading){
                layer.msg('加载中', {  //通过layer插件来进行提示正在加载
                    icon: 16,
                    shade: 0.01
                });
            }

        },
        success: function(data) {
            /*console.log('请求成功');*/
            layer.closeAll('loading');
            setTimeout(function () {
                ajaxStatus = true;
            },500);

            //公共处理函数
            //...
            if(success){
                success(data);
            }
        },
        error:function(data) {
            /*console.error('请求成功失败');*/
            /*data.status;//错误状态吗*/
            layer.closeAll('loading');
            setTimeout(function () {
                /*if(data.status == 404){
                 layer.msg('请求失败，请求未找到');
                 }else if(data.status == 503){
                 layer.msg('请求失败，服务器内部错误');
                 }else {
                 layer.msg('请求失败,网络连接超时');
                 }*/
                ajaxStatus = true;
            },500);

            if(error){
                error(data)
            }
        }
    });
}

// ajax提交(get方式提交)
function ajax_get(url, success,error, show_loading, alone) {
    ajax(url, {}, success, error , show_loading , true, false, 'get','json');
}

// jsonp跨域请求(get方式提交)
function ajax_jsonp(url, success, error , show_loading, alone) {
    ajax(url, {}, success, error , show_loading, true, false, 'get','jsonp');
}

function set_url_query(url,query){
    if(query) {
        var queryArr = [];
        for (const key in query) {
            if (query.hasOwnProperty(key)) {
                queryArr.push(key+'='+query[key])
            }
        }
        if(url){
            if(url.indexOf('?') !== -1) {
                url = url+'&'+queryArr.join('&')
            } else {
                url = url+'?'+queryArr.join('&')
            }
        }else{
            url ='?'+queryArr.join('&')
        }

    }
    return url;
}


/**
 * 依赖layer弹框
 * @param url
 * @param data
 * @param success
 * @param cache
 * @param alone
 * @param async
 * @param type
 * @param dataType
 * @param error
 * @returns {boolean}
 */
function ajax(url, data, success,error, show_loading, alone, async, type, dataType) {
    var type = type || 'post';//请求类型
    var dataType = dataType || 'json';//接收数据类型
    var async = async || true;//异步请求
    var alone = alone || false;//独立提交（一次有效的提交）
    var cache = false;//浏览器历史缓存

    
    /*判断是否可以发送请求*/
    /*if(!ajaxStatus){
        return false;
    } */
    ajaxStatus = false;//禁用ajax请求
    /*正常情况下1秒后可以再次多个异步请求，为true时只可以有一次有效请求（例如添加数据）*/
    /*if(!alone){
        setTimeout(function () {
            ajaxStatus = true;
        },1000);
    }*/
   

    var headers = '';
    if(type=='post'){
        headers = {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }
    $.ajax({
        'headers':headers,
        'url': url,
        'data': data,
        'type': type,
        'dataType': dataType,
        'async': async,
        'cache': cache,
        'success': function (data) {
            /*console.log('请求成功');*/
            layer.closeAll('loading');
            setTimeout(function () {
                ajaxStatus = true;
            },500);

            //公共处理函数
            //...
            if(success){
                success(data);
            }

        },
        'error': function (data) {
            /*console.error('请求成功失败');*/
            /*data.status;//错误状态吗*/
            layer.closeAll('loading');
            setTimeout(function () {
                /*if(data.status == 404){
                 layer.msg('请求失败，请求未找到');
                 }else if(data.status == 503){
                 layer.msg('请求失败，服务器内部错误');
                 }else {
                 layer.msg('请求失败,网络连接超时');
                 }*/
                ajaxStatus = true;
            },500);

            if(error){
                error(data)
            }
        },
        'jsonpCallback': 'jsonp' + (new Date()).valueOf().toString().substr(-4),
        'beforeSend': function () {
            if(show_loading){
                layer.msg('加载中', {  //通过layer插件来进行提示正在加载
                    icon: 16,
                    shade: 0.01
                });
            }

        },
    });
}


