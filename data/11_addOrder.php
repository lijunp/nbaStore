<?php
    //设置响应头部
    header("Content-Type:application/json;charset=utf-8");
    //rcvId=1&price=123&payment=1&uname=lijun&productList=[{"proId":20,"count":3,"colorId":100101,"sizeId":100102},{"proId":23,"count":1,"colorId":100101,"sizeId":100102}];
    //接收并处理客户端提交的请求数据
    $orderNum=rand(1000000000,10000000000);
    $shopName='nba官方旗舰店';
    $rcvId=$_REQUEST['rcvId'];
    $price=$_REQUEST['price'];
    $payment=$_REQUEST['payment'];
    $orderTime = time()*1000;
    $status=1;
    $uname=$_REQUEST['uname'];
    $productList=$_REQUEST['productList'];
    $productList=json_decode($productList);

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //SQL1:设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);


    //SQL2:根据用户名查询用户编号
    $sql = "SELECT uid FROM nbaStore_user WHERE uname='$uname'";
    $result = mysqli_query($link,$sql);
    $userId=mysqli_fetch_assoc($result)['uid'];

    //SQL3:向订单表插入一条记录，得到自增的订单编号
    $sql = "INSERT INTO nbaStore_order VALUES(NULL,'$orderNum','$shopName','$rcvId','$price','$payment','$orderTime','$status','$userId')";
    $result = mysqli_query($link,$sql);
    $orderId=mysqli_insert_id($link);

    //SQL4:循环执行：向订单详情表中插入记录
    foreach($productList as $v){
        $proId=$v->proId;
        $count=$v->count;
        $colorId=$v->colorId;
        $sizeId=$v->sizeId;
        $sql = "INSERT INTO nbaStore_order_detail VALUES(NULL,'$orderId','$proId','$count','$colorId','$sizeId')";
        $result = mysqli_query($link,$sql);
    }

    //创建要输出给客户端的数据
    $output = [];
    if($orderId){    //执行成功
        $output['msg'] = 'succ';
        $output['oid'] = $orderId;
        $output['orderNum']= $orderNum;
        //删除购物车中的商品；
        $sql="DELETE FROM nbaStore_cart_detail WHERE cartId IN(SELECT cid FROM nbaStore_cart WHERE userId='$userId')";
        $res=mysqli_query($link,$sql);
    }else {         //执行失败
        $output['msg'] = 'err';
    }

    //把数据编码为JSON字符串
    echo json_encode($output);
?>