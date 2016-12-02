<?php
    //设置响应头部
    //header("Content-Type:text/plain;charset=utf-8");

    //接收请求参数
    $uname=$_REQUEST['uname'];
    $proId=$_REQUEST['proId'];
    $colorId=$_REQUEST['colorId'];
    $sizeId=$_REQUEST['sizeId'];
    $count=$_REQUEST['count'];

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);

    $sql="SELECT * FROM nbaStore_user where uname='$uname'";
    $res=mysqli_query($link,$sql);
    $list=mysqli_fetch_assoc($res);
    $userId=$list['uid'];

    $sql="SELECT * FROM nbaStore_cart WHERE userId=$userId";
    $res=mysqli_query($link,$sql);
    $list=mysqli_fetch_assoc($res);

    if(!count($list)){
        $sql="INSERT INTO nbaStore_cart VALUES(null,$userId)";
        mysqli_query($link,$sql);
        $sql="SELECT * FROM nbaStore_cart WHERE userId=$userId";
        $res=mysqli_query($link,$sql);
        $list=mysqli_fetch_assoc($res);
    }

    $cartId=$list['cid'];
    $sql="SELECT * FROM nbaStore_cart_detail WHERE cartId='$cartId' AND proId='$proId' AND sizeId='$sizeId' AND colorId='$colorId'";
    $res=mysqli_query($link,$sql);
    $list=mysqli_fetch_assoc($res);

    if(count($list)){
       $count+=$list['count'];
       $sql="UPDATE nbaStore_cart_detail SET count='$count' WHERE cartId='$cartId' AND proId='$proId' AND sizeId='$sizeId' AND colorId='$colorId'";
       $res=mysqli_query($link,$sql);
       if($res){
           echo 'ok';
       }
    }else if(count($list)==0){
       $sql="INSERT INTO nbaStore_cart_detail VALUES(null,'$cartId','$proId','$colorId','$sizeId','$count')";
       $res=mysqli_query($link,$sql);
       $id=mysqli_insert_id($link);
       if($id){
          echo 'ok';
       }
    }else{
       echo 'fail';
    }
?>