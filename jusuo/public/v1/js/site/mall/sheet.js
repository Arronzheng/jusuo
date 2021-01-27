var goodInfo = {}
var modalIndex = null
var addressList = []
var selectedAddressId = 0
var editType = 'add'
var form = layui.form
    ,layer = layui.layer;

layui.element.init();

$(function(){

    //获取商品信息
    getGoodInfo()

    //获取收货地址list
    getAddressList()

});

//提交兑换订单
function submitOrder(){
    //组装数据：
    //g  商品id
    //a  收货地址id
    //c  商品数量
    //r  备注
    layer.load(1)

    var goodId = web_id_code;
    var addressId = selectedAddressId;
    var remark = $('#i-remark').val();
    var apiUrl = '/mall/api/submit_order'
    ajax_post(apiUrl,{
        g:goodId,
        a:addressId,
        c:goodCount,
        r:remark,
    },function(result){
        layer.closeAll('loading')
        if(result.status){
            layer.msg(result.msg, {
                time: 1500
            }, function(){
                //跳转去我的兑换列表
                location.href='/center/integral'
            });
        }else{
            layer.closeAll('load')
            layer.msg(result.msg)
        }
    });
}

//设为默认地址
function setDefaultAddress(e,id){
    e.stopPropagation();
    e.preventDefault()
    var apiUrl = '/mall/api/address/default/'+id;
    layer.load(1);
    ajax_post(apiUrl,{
        '_method':'PATCH',
        'id':id,
    },function(result){
        layer.closeAll('loading')
        if(result.status){
            layer.msg('设置成功！')
            getAddressList()
        }else{
            layer.msg(result.msg)
        }
    });
}

//删除地址
function deleteAddress(e,id){
    e.stopPropagation();
    e.preventDefault()
    var apiUrl = '/mall/api/address/'+id;
    layer.confirm('确定删除该收货地址吗?', {icon: 3, title:'提示'}, function(index){
        ajax_post(apiUrl,{
            '_method':'DELETE',
            'id':id,
        },function(result){
            if(result.status){
                layer.msg('删除成功！')
                getAddressList()
            }else{
                layer.msg(result.msg)
            }
        });

        layer.close(index);
    });
}

//打开编辑框
function editAddress(e,type,id){
    e.stopPropagation();
    e.preventDefault()
    var page = '/mall/address/create';
    if(type=='edit'){
        page = "/mall/address/"+id+"/edit";
    }
    layer.open({
        type: 2,
        title:type=='add'?'新增收货地址':'编辑收货地址',
        area:['700px', '500px'],
        resize:true,
        maxmin:true,
        //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
        content:page,
        success:function(){

        }
    });
}


//获取收货地址list
function getAddressList(){
    layer.load(1);
    ajax_get("/mall/api/address",function(res){
        layer.closeAll('loading')
        if(res.status && res.data){

            addressList = res.data;
            //默认选择第一个
            selectAddress(addressList[0].id)

            var html = template('address-list-tpl', {data:res.data});
            $('#address-list').html(html)


        }

    },function(){})
}

//获取商品信息
function getGoodInfo(){
    ajax_get("/mall/api/confirm_good_info/"+web_id_code,function(res){

        if(res.status && res.data){

            goodInfo = res.data;

            $('#sale-name').text(goodInfo.name)
            $('#sale-image').text(goodInfo.cover)
            $('#sale-integral').text(goodInfo.integral)
            $('#sale-count').text(goodCount)

            //总积分
            var total = goodCount * goodInfo.integral
            $('#total-amount').text(total)

        }

    },function(){})
}

//选择地址处理
function selectAddress(id){
    var address_info = null;
    selectedAddressId = id;
    console.log(selectedAddressId)
    for(var i=0;i<addressList.length;i++){
        if(addressList[i].id==id){
            address_info = addressList[i];
        }
    }
    if(!address_info){return false;}
    $('#confirm-address .province').text(address_info.province_name)
    $('#confirm-address .city').text(address_info.city_name)
    $('#confirm-address .district').text(address_info.area_name)
    $('#confirm-address .address').text(address_info.receiver_address)
    $('#confirm-address .receiver').text(address_info.receiver_name)
    $('#confirm-address .telephone').text(address_info.receiver_tel)
}


$(document).on('click','.address-outer',function(){
    selectedAddressId = $(this).attr('data-id');
    selectAddress(selectedAddressId)
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
});

$(document).on('click','#address-add-btn',function(e){
    editAddress(e,'add',$(this).parents('.address-outer').attr('data-id'));
});

function closeModal(){
    layer.close(modalIndex)

}


