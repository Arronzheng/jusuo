$(document).ready(function(){
    $('.main-nav .nav-search-btn').click(function(){
        $('.main-nav').addClass('search-stat');
        $('#nav-search-input').focus();
    });
    $('.main-nav .close-btn').click(function(){
        $('.main-nav').removeClass('search-stat');
    });
    $('.nav-search-cont .sub-row').click(function(){
        $('#nav-search-type-text').html($(this).attr('data-text'));
        $('#nav-search-type-text').attr('data-attr',$(this).attr('data-value'));
    });
    $('.nav-search-btn').click(function(){
        navSearch($('#nav-search-type-text').attr('data-attr'),$('#nav-search-input').val());
        e.stopPropagation();
    });

    //登录注册
    $('.login-reg-modal .btn-cont-list .iconfont').click(function(){
        $(this).parent().parent().parent().find('.login-cont-outer').addClass('hidden');
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $('#'+$(this).attr('data-attr')).removeClass('hidden');
    });
    $('#btn-login-reg-close').click(function(){
        $('#login-reg-model').addClass('hidden');
    });
    $('#login, #login-to-center, #login-to-create').click(function(){
        showLoginReg(true);
    });
    $('#register').click(function(){
        showLoginReg(false);
    });
    $('.login-reg-tab .tab-item').click(function(){
        $('.modal-cont').addClass('hidden');
        $('.login-reg-tab .tab-item').removeClass('active');
        $(this).addClass('active');
        $('#'+$(this).attr('data-attr')+'-cont').removeClass('hidden');
        $('.tab-bar.moving').attr('style','left:'+($(this).position().left+20)+'px');
    });
    //getCity();
    getOrganization();
});

function navSearch(type,keyword){
    if(type&&keyword!='') {
        switch (type){
            case '0':
                //方案
                window.location.href = '/album?__bs='+__cache_brand+'&k='+keyword;
                break;
            case '2':
                //设计师
                window.location.href = '/designer?__bs='+__cache_brand+'&k='+keyword;
                break;
            case '3':
                //产品
                window.location.href = '/product?__bs='+__cache_brand+'&k='+keyword;
                break;
            default:
                break;
        }
    }
}

function getCity(){
    $.get('/common/location_get_city',function(res){
        if(res.status==1)
            $('#city').html(res.data);
    });
}

function getOrganization(){
    $.get('/index/location_get_organization',function(res){
        if(res.status==1)
            $('#city').html(res.data);
    });
}

function showLoginReg(isLogin){
    $('#login-reg-model').removeClass('hidden');
    $('.modal-cont').addClass('hidden');
    $('.login-reg-tab .tab-item').removeClass('active');
    if(isLogin){
        $('#tab-item-login').addClass('active');
        $('#login-cont').removeClass('hidden');
        $('.tab-bar.moving').attr('style','left:160px');
    }
    else{
        $('#tab-item-reg').addClass('active');
        $('#reg-cont').removeClass('hidden');
        $('.tab-bar.moving').attr('style','left:212px');
    }
}

function goToDesigner(code){
    window.location.href = '/designer/s/'+code;//+"?__bs="+__cache_brand;

}

function goToAlbum(code){
    window.location.href = '/album/s/'+code;//+"?__bs="+__cache_brand;

}

function goToDealer(code){
    window.location.href = '/dealer/s/'+code;//+"?__bs="+__cache_brand;

}

function goToBrand(code){
    window.location.href = '/brand/'+code;//+"?__bs="+__cache_brand;
}

function goToProduct(code){
    window.location.href = '/product/s/'+code;//+"?__bs="+__cache_brand;

}