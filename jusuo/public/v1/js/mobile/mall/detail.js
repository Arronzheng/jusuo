var amount = 1;
var MAX_AMOUNT = 9;
var MIN_AMOUNT = 1;
var goodInfo = {}

$(function(){

    //获取商品信息
    getGoodDetail();

});

//兑换下单
function addOrder(){
    layer.confirm('确认兑换吗?', {icon: 3, title:'提示'}, function(index){
        layer.load(1);
        location.href = '/mobile/mall/confirm_order?g='+web_id_code+"&c="+amount;

    });
}

//获取商品详情信息
function getGoodDetail(){
    ajax_get("/mobile/mall/api/good_detail/"+web_id_code,function(res){

        if(res.status && res.data){

            goodInfo = res.data;
            var image_html = template('good-images-tpl', {data:res.data});
            var main_info_html = template('main-info-tpl', {data:res.data});
            $('#good-images').html(image_html)
            $('#main-info').html(main_info_html)

            $(".select-box .img-outer").mousemove(function(){
                $(this).siblings().removeClass('active');
                $(this).addClass('active');
                $('#sale-img').attr('style','background-image:url('+$(this).find('img').attr('src')+')');
            })

        }

    },function(){})
}

function setAmount(){
    $('.btn-add, .btn-reduce').removeClass('disabled');
    if(amount>=MAX_AMOUNT){
        amount = MAX_AMOUNT;
        $('.btn-add').addClass('disabled');
    }
    if(amount<=MIN_AMOUNT){
        amount = MIN_AMOUNT;
        $('.btn-reduce').addClass('disabled');
    }
    $('#buy-num').val(amount);
}

function addAmount(){
    amount++;
    setAmount();
}

function reduceAmount(){
    amount--;
    setAmount();
}




