<?php
    //设置响应头部
    //header("Content-Type:application/json;charset=utf-8");

    //接收请求参数
    $uname=$_REQUEST['uname'];

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
    $sql="SELECT nbaStore_cart_detail.did,nbaStore_cart_detail.proId,nbaStore_cart_detail.count,nbaStore_product_color.colorName,nbaStore_product_size.psize,nbaStore_product.pname,nbaStore_product.price,nbaStore_product_photo.img_sm,nbaStore_product_color.colorId,nbaStore_product_size.sizeId FROM nbaStore_cart_detail,nbaStore_product,nbaStore_product_color,nbaStore_product_size,nbaStore_product_photo WHERE cartId='$cartId'
    AND nbaStore_cart_detail.colorId=nbaStore_product_color.colorId
    AND nbaStore_cart_detail.colorId=nbaStore_product_size.colorId
    AND nbaStore_cart_detail.sizeId=nbaStore_product_size.sizeId
    AND nbaStore_cart_detail.proId=nbaStore_product.proId
    AND nbaStore_cart_detail.colorId=nbaStore_product_photo.colorId
    AND nbaStore_product_photo.isShowDefault='1'";
    $res=mysqli_query($link,$sql);
    $list=mysqli_fetch_all($res,MYSQLI_ASSOC);

    echo json_encode($list);
?>