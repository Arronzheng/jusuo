var seller_info = null;

$(document).ready(function(){
    //获取销售商基本信息
    get_seller_info();

    //获取设计团队
    list_designer();

    //获取设计方案
    list_album();

    //获取产品列表
    list_product();

    var mySwiper1 = new Swiper('.cover-swiper-outer,.section-swiper-outer',{
        pagination: {el:'.swiper-pagination',type:'fraction'},
    });

    $('#seller-profile').on('click', '#address-outer.available', function(){
        var lat = $(this).attr('lat');
        var lng = $(this).attr('lng');
        if(!lat||!lng)
            return;
        var x = bMapTransQQMap(lng,lat);
        wx.openLocation({
            latitude: parseFloat(x.lat),
            longitude: parseFloat(x.lng),
            name: $(this).attr('name'),
            address: $(this).attr('address'),
            scale: 14,
            infoUrl: window.location.href
        });
    });
    
});

function bMapTransQQMap(lng, lat) {
    let x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    let x = lng - 0.0065;
    let y = lat - 0.006;
    let z = Math.sqrt(x * x + y * y) - 0.00002 * Math.sin(y * x_pi);
    let theta = Math.atan2(y, x) - 0.000003 * Math.cos(x * x_pi);
    let lngs = z * Math.cos(theta);
    let lats = z * Math.sin(theta);
    return {
        lng: lngs,
        lat: lats
    }
}

//关注销售商
function fav_dealer(){
    ajax_post('/mobile/dealer/api/focus/'+seller_info.web_id_code,{}, function(res){
        if (res.status == 1) {
            if(res.data.faved){
                $('#focus-btn').html('已关注');
            }
            else{
                $('#focus-btn').html('关注');
            }
            $('#span-count-fav').html(res.data.count);
        }
        else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}

function get_seller_info(){

    ajax_get(seller_info_api_url, function (res) {


        if (res.status) {

            seller_info = res.data;

            document.title = seller_info.name+(seller_info.site_title?'-'+seller_info.site_title:'');

            var seller_profile_html = template('seller-profile-tpl', {seller: seller_info});
            var self_promotion_html = template('self-promotion-tpl', {seller: seller_info});

            $('#seller-profile').html(seller_profile_html)
            $('#cuxiao').html(self_promotion_html)
            $('.brand-logo').attr("src",seller_info.avatar)
            $('.brand-image').css("background-image","url('"+seller_info.brand_image+"')")

            wxShare(seller_info.name+(seller_info.site_title?'-'+seller_info.site_title:''),
                seller_info.introduction?seller_info.introduction:seller_info.self_address,
                seller_info.avatar
            );


        }else{
            if(result.code == 2001){
                m_go_login()
            }else{
                layer.msg(result.msg)
            }
        }


    }, function () {
    })
}

//获取设计师列表
function list_designer() {

    ajax_get(list_designer_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            $('#designer-count').html(datas.length>0?datas[0].total:0);

            if(datas.length<=0){
                $('#designer-list').hide();
            }else{
                var html = template('list-designer-tpl', {datas: datas});
                $('#designer-list .list-container').html(html)
            }

        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }


    }, function () {
    })
}

//获取方案列表
function list_album() {

    ajax_get(list_album_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

                $('#album-count').html(datas.total);

            if(datas.total<=0){
                $('#album-list').hide();
            }else{
                var html = template('list-album-tpl', {datas: datas.data});
                $('#album-list .album-container-outer').html(html)
            }

        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }


    }, function () {
    })
}

//获取产品列表
function list_product() {

    ajax_get(list_product_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            $('#product-count').html(datas.total);

            if(datas.total<=0){
                $('#product-list').hide();
            }else{
                var html = template('list-product-tpl', {datas: datas.data});
                $('#product-list .item-list').html(html)
            }

        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }


    }, function () {
    })
}