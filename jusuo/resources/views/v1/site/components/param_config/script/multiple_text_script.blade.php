<script>
    function add_multiple_block(tpl_id,target_id){
        layer.prompt({title: '请输入值',},function(value, index, elem){
            var tpl = $('#'+tpl_id).html();
            var dom_elem = $(tpl)
            dom_elem.find('input').val(value)
            $('#'+target_id).append(dom_elem);
            layer.close(index);
        });
    }

    function delete_multiple_block(obj){
        $(obj).parents('.multiple-text-block').remove();
    }

</script>