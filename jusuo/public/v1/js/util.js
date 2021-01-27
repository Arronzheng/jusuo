function objectToArray(object){
    var  arr=[];
    for(var i in object){
        arr.push(object[i]);
    }

    return arr;
}

//获取地区
function get_area_global(area_id,next_elem,next_text){
    var options='<option value="">请选择'+next_text+'</option>';
    ajax_get('/common/get_area_children?pi='+area_id,function(res){
        if(res.status && res.data.length>0){
            $.each(res.data,function(k,v){
                options+='<option value="'+ v.id+'">'+ v.name+'</option>';
            });
            $('#'+next_elem).html(options);
            form.render('select');
        }
    });
}