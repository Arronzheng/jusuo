<?php
$url = request()->fullUrl();
$basic_info_url = url($url_prefix.'admin/brand/basic_info');
$app_info_url = url($url_prefix.'admin/brand/app_info');
$news_url = url($url_prefix.'admin/brand/news');
$site_config_url = url($url_prefix.'admin/brand/site_config');
$banner_url = url($url_prefix.'admin/brand/banner');
$bind_wechat_url = url($url_prefix.'admin/brand/security_center/bind_wechat');
$modify_pwd_url = url($url_prefix.'admin/brand/security_center/modify_pwd');
?>
<div class="layui-tab layui-tab-brief" >
    <ul class="layui-tab-title">
        @can('info_manage.account_info.basic_info')
        <li @if($url==$basic_info_url)) class="layui-this" @endif onclick="location.href='{{$basic_info_url}}'" >基本信息</li>
        @endcan
        @can('info_manage.account_info.app_info')
        <li @if($url==$app_info_url) class="layui-this" @endif onclick="location.href='{{$app_info_url}}'" >应用信息</li>
        @endcan
        @can('site_config.news_index')
        <li @if($url==$news_url)) class="layui-this" @endif  onclick="location.href='{{$news_url}}'" >图文资讯</li>
        @endcan
        @can('info_manage.site_config')
        <li @if($url==$site_config_url)) class="layui-this" @endif onclick="location.href='{{$site_config_url}}'" >展示信息</li>
        @endcan
        @can('info_manage.banner_index')
        <li @if($url==$banner_url)) class="layui-this" @endif onclick="location.href='{{$banner_url}}'" >轮播图</li>
        @endcan
        @can('security_center.bind_wechat')
        <li @if($url==$bind_wechat_url)) class="layui-this" @endif onclick="location.href='{{$bind_wechat_url}}'" >绑定微信</li>
        @endcan
        @can('security_center.modify_pwd')
        <li @if($url==$modify_pwd_url)) class="layui-this" @endif onclick="location.href='{{$modify_pwd_url}}'">修改密码</li>
        @endcan
    </ul>
</div>