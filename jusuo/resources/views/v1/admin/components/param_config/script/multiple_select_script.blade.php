<script>
    function add_multiple_select_item(tpl_id,target_id,type){
        layer.prompt({title: '请输入值',},function(value, index, elem){
            ajax_post('{!! url($url_prefix.'admin/api/param_config/add_multiple_option') !!}', {
                option:value,
                type:type,
            }, function(result){
                if(result.status==1){
                    var tpl = $('#'+tpl_id).html();
                    var dom_elem = $(tpl)
                    dom_elem.find('input[name=\'option\']').val(value)
                    dom_elem.find('input[name=\'id\']').val(result.data.id)
                    dom_elem.find('input[name=\'type\']').val(type)
                    $('#'+target_id).append(dom_elem);
                    layer.close(index);
                    layer.msg('添加成功');
                    location.reload();
                }else{
                    layer.msg(result.msg);
                }

            });

        });

    }


    function delete_multiple_select_item(obj){
        var id = $(obj).siblings('input[name=\'id\']').val()
        var type = $(obj).siblings('input[name=\'type\']').val()

        layer.confirm('确定删除此选项?', {icon: 3, title:'提示'}, function(index){
            ajax_post('{!! url($url_prefix.'admin/api/param_config/delete_multiple_option') !!}', {
                id:id,
                type:type,
            }, function(result){
                if(result.status==1){
                    layer.close(index);
                    layer.msg('删除成功');
                    $(obj).parents('.multiple-select-item').remove();
                }else{
                    layer.msg(result.msg);
                }

            });
        });
    }

    function modify_multiple_select_item(obj){
        var id = $(obj).siblings('input[name=\'id\']').val()
        var type = $(obj).siblings('input[name=\'type\']').val()
        var value = $(obj).siblings('input[name=\'option\']').val()

        layer.confirm('确定修改此选项?', {icon: 3, title:'提示'}, function(index){
            ajax_post('{!! url($url_prefix.'admin/api/param_config/modify_multiple_option') !!}', {
                id:id,
                type:type,
                value:value,
            }, function(result){
                if(result.status==1){
                    layer.close(index);
                    layer.msg('修改成功');
                    location.reload();
                }else{
                    layer.msg(result.msg);
                }

            });
        });
    }
</script>