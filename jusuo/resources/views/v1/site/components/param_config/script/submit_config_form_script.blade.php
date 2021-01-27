<script>
    function submit_config_form(obj,submit_type){
        var form_no_name = $(obj).parents('.layui-form-item').find('form :not("input[name=\'param_name\']")');
        var form = $(obj).parents('.layui-form-item').find('form');
        var param_name = form.find('input[name="param_name"]').val();
        var data = form_no_name.serializeArray();
        ajax_post('{!! url($url_prefix.'admin/api/param_config/update') !!}', {
            param_value:data,
            param_name:param_name,
            submit_type:submit_type,
        }, function(result){
            layer.msg(result.msg);
        });
    }




</script>