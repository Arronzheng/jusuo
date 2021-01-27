$(document).ready(function(){
    $('.layui-side.layui-bg-black').mousemove(function () {
        $('.layui-side.layui-bg-black').addClass('active');
    }).mouseout(function () {
        $('.layui-side.layui-bg-black').removeClass('active');
    });
});