<?php
    //设置响应头部
    header("Content-Type:application/json;charset=utf-8");

    //接收请求参数
    $uname=$_REQUEST['uname'];
    $upwd=$_REQUEST['upwd'];

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);

    //执行SQL语句
    $sql="INSERT INTO nbaStore_user VALUES(NULL,'$uname','$upwd')";
    $res=mysqli_query($link,$sql);
    $uid=mysqli_insert_id($link);

    //定义输出对象
    $output=[];
    if($uid){
        $output['msg']='succ';
        $output['userId']=$uid;
    }else{
        $output['msg']='fail';
        $output['userId']="";
    }

    //向客户端输出结果
    echo json_encode($output);
?>