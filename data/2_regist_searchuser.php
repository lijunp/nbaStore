<?php
    //设置响应头部
    header("Content-Type:text/plain;charset=utf-8");

    //接受请求参数
    $uname=$_REQUEST['uname'];

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);

    //执行SQL语句
    $sql="SELECT uid FROM nbaStore_user WHERE uname='$uname'";
    $res=mysqli_query($link,$sql);
    $row=mysqli_fetch_assoc($res);

    //向客户端输出结果
    if($row){
        echo "exist";
    }else{
        echo "non-exist";
    }
?>