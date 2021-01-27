<script>
    $(function () {
        layui.config({
            base: '{{$js_url}}'
        }).extend({
            authtree: 'authtree',
        });
        layui.use(['jquery', 'authtree', 'form', 'layer'], function () {
            var $ = layui.jquery;
            var authtree = layui.authtree;
            var form = layui.form;
            var layer = layui.layer;
            // 一般来说，权限数据是异步传递过来的
            $.ajax({
                url: '{{$api_url}}',
                dataType: 'json',
                data: {rid:'{{isset($role_id)?$role_id:0}}'},
                success: function(res){
                    var trees = authtree.listConvert(res.data.list, {
                        primaryKey: 'id'
                        ,startPid: 0
                        ,parentKey: 'parent_id'
                        ,nameKey: 'display_name'
                        ,valueKey: 'id'
                        ,checkedKey: res.data.checkedId
                    });
                    // 如果后台返回的不是树结构，请使用 authtree.listConvert 转换
                    authtree.render('#LAY-auth-tree-index', trees, {
                        inputname: 'privileges[]',
                        layfilter: 'lay-check-auth',
                        autowidth: true,
                        openall  : true
                    });
                }
            });
        });
    });
</script>