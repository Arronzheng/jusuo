$(document).ready(function(){
    getData();
});

function getData(){
    layer.load(1)
    ajax_get(center_index_api_url,function(res){

        layer.closeAll("loading");

        if(res.status){
            if(res.data.cover){
                var html = template('cover-tpl', {data:res.data.cover});
                $('#cover').html(html)
            }

            if(res.data.data){
                var html = template('container-tpl', {data:res.data.data});
                $('#container').html(html)
            }
        }
        else if(res.status==0){
            m_go_login();
        }


    },function(){})
}