var phone,contact,link,qrcode,relate,i,j,str,tag;

function getFooter(){
    layer.load(1);
    $.get('/index/get_footer',{}, function(res) {
        layer.closeAll('loading')
        if (res.status == 1) {
            if(!res.data){
                return false;
            }
            phone = res.data.phone;
            logo = res.data.logo;
            contact = res.data.contact;
            link = res.data.link;
            qrcode = res.data.qrcode;
            relate = res.data.relate;
            site_title = res.data.site_title;
            document.title = site_title;
            $('#phone').html(phone);
            for (i = 0; i < contact.length; i++)
                $('#contact-' + i).html(contact[i]);
            for (i = 0; i < link.length; i++){
                str = '';
                for (j = 0; j < link[i].length; j++) {
                    if(j>0)
                        tag = '1';
                    else
                        tag = '';
                    if(link[i][j].text){
                        str += '<div class="m_text'+tag+'" id="link-'+i+'-'+j+'" data-link="'+link[i][j].link+'">'+link[i][j].text+'</div>';

                    }
                }
                $('#bh-middle-'+i).html(str);
            }
            for (i = 0; i < qrcode.length; i++){
                $('#qrcode-'+i).attr('src',qrcode[i].image);
                $('#qrcode-'+i+'-text').html(qrcode[i].text);
            }
            str = '';
            for(i= 0; i < relate.length; i++){
                if(relate[i].text && relate[i].link){
                    if(i>0 )
                        str += "<div class='qmhline'></div>";
                    str += "<div class='qmh' data-link='"+relate[i].link+"'>"+relate[i].text+"</div>";
                }

            }
            $("#bottom_relate").html(str);
            //设置logo
            if(logo){
                $(".nav-row .logo").css('background-image','url('+logo+')');
            }else{
                $(".nav-row .logo").css('background-image','url(/v1/images/site/logo.png)');

            }

            bindClickFooter();
        }
    });
}

function bindClickFooter(){
    $('#footer').on('click', '.m_text1, .qmh', function(){
        window.location.href = $(this).attr('data-link');
    });
}

$(document).ready(function(){
    getFooter();
});