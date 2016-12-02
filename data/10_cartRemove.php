<?php
    //设置响应头部
    //header("Content-Type:text/plain;charset=utf-8");

    //接收请求参数
    $did=$_REQUEST['did'];

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);

    $sql="DELETE FROM nbaStore_cart_detail where did='$did'";
    $res=mysqli_query($link,$sql);

    if($res){
        echo "ok";
    }else{
        echo "fail";
    }
?>