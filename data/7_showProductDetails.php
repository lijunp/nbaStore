<?php
    //设置响应头部
    //header("Content-Type:application/json;charset=utf-8");

    //接收请求参数
    $proId=$_REQUEST['proId'];

    //定义输出
    $output=[
        "proId" => $proId,
        "colorList" =>null
    ];

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);

    //执行SQL语句;
    $sql="SELECT nbaStore_product.proId,price,pname,pinfo FROM nbaStore_product WHERE proId='$proId'";
    $res=mysqli_query($link,$sql);
    $list=mysqli_fetch_assoc($res);
    $output['pname']=$list['pname'];
    $output['price']=$list['price'];
    $output['pinfo']=$list['pinfo'];

    //获取颜色
    $sql="SELECT colorName,colorId FROM nbaStore_product_color WHERE proId='$proId'";
    $res=mysqli_query($link,$sql);
    $list=mysqli_fetch_all($res,MYSQLI_ASSOC);
    $colorList=[];
    for($i=0;$i<count($list);$i++){
        $value=$list[$i]['colorId'];
        $name=$list[$i]['colorName'];
        $arr=[];

        //找到颜色对应的图片列表压入数组中
        $sql="SELECT img_sm,isShowDefault FROM nbaStore_product_photo WHERE colorId='$value'";
        $res=mysqli_query($link,$sql);
        $potoList=mysqli_fetch_all($res,MYSQLI_ASSOC);

        //找到颜色对应的尺寸列表压入数组中
        $sql="SELECT sizeId,psize,isShowDefault FROM nbaStore_product_size WHERE colorId='$value'";
        $res=mysqli_query($link,$sql);
        $sizeList=mysqli_fetch_all($res,MYSQLI_ASSOC);

        $arr["colorId"]=$value;
        $arr["colorName"]=$name;
        $arr["photoList"]=$potoList;
        $arr["sizeList"]=$sizeList;
        $colorList[]=$arr;
    };
    $output['colorList']=$colorList;

    echo json_encode($output);
?>