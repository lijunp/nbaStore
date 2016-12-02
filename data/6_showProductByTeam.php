<?php
    //设置响应头部
    header("Content-Type:application/json;charset=utf-8");

    //接收请求参数
    $team=$_REQUEST['team'];
    $pageNum=$_REQUEST['pageNum'];

    //设置分页数组对象
    $pager=[
        "recordCount" => 0,
        "pageSize" => 8,
        "pageNum" => intval($pageNum),
        "data" => null
    ];

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);

    //执行SQL语句获取总记录数;
    $sql="SELECT COUNT(*) FROM nbaStore_product_color WHERE colorId LIKE '%$team'";
    $res=mysqli_query($link,$sql);
    $rows=mysqli_fetch_assoc($res);
    $pager['recordCount']=$rows['COUNT(*)'];
    $pager['pageCount']=ceil($pager['recordCount']/$pager['pageSize']);

    //执行SQL语句获取当前记录数;
    $start=($pageNum-1)*$pager['pageSize'];
    $count=$pager['pageSize'];
    $sql="SELECT nbaStore_product.proId,img_sm,price,pname,sales FROM
    nbaStore_product,nbaStore_product_color,nbaStore_product_size,nbaStore_product_photo WHERE nbaStore_product_photo.isShowDefault='1'
    AND nbaStore_product_size.isShowDefault='1'
    AND nbaStore_product_color.colorId LIKE '%$team'
    AND nbaStore_product_color.proId=nbaStore_product.proId
    AND nbaStore_product_color.colorId=nbaStore_product_size.colorId
    AND nbaStore_product_color.colorId=nbaStore_product_photo.colorId LIMIT $start,$count";
    $res=mysqli_query($link,$sql);
    $list=mysqli_fetch_all($res,MYSQLI_ASSOC);
    $pager['data']=$list;

    echo json_encode($pager);
?>