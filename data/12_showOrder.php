<?php
    //设置响应头部
    header("Content-Type:application/json;charset=utf-8");
    //rcvId=1&price=123&payment=1&uname=lijun&productList=[{"proId":20,"count":3,"colorId":100101,"sizeId":100102},{"proId":23,"count":1,"colorId":100101,"sizeId":100102}];
    //接收并处理客户端提交的请求数据

    $uname=$_REQUEST['uname'];

    //加载数据库配置文件
    include("0_config.php");
    //连接数据库
    $link=mysqli_connect($db_host,$db_user,$db_upwd,$db_name,$db_port);

    //SQL1:设置编码方式
    $sql="SET NAMES UTF8";
    mysqli_query($link,$sql);

    //SQL2:根据用户名查询订单编号
    $sql = "SELECT nbaStore_rcvMessage.rcvName,nbaStore_order.oid,nbaStore_order.orderNum,nbaStore_order.shopName,nbaStore_order.price,nbaStore_order.payment,nbaStore_order.orderTime,nbaStore_order.status
    FROM nbaStore_order,nbaStore_rcvMessage WHERE nbaStore_order.userId IN(SELECT uid FROM nbaStore_user WHERE uname='$uname')
    AND nbaStore_rcvMessage.mid=nbaStore_order.rcvId";
    $result = mysqli_query($link,$sql);
    $orderList=mysqli_fetch_All($result,MYSQLI_ASSOC);

    //SQL3:根据订单列表中每个订单编号查询所有产品详情
    foreach($orderList as $i=>$v){
        $oid=$orderList[$i]['oid'];
        $sql="SELECT nbaStore_order_detail.did,nbaStore_order_detail.proId,nbaStore_order_detail.count,nbaStore_order_detail.colorId,nbaStore_order_detail.sizeId,nbaStore_product_photo.img_sm FROM nbaStore_product_photo,nbaStore_order_detail WHERE nbaStore_order_detail.orderId='$oid' AND nbaStore_product_photo.colorId=nbaStore_order_detail.colorId AND nbaStore_product_photo.isShowDefault='1'";
        $result = mysqli_query($link,$sql);
        $list=mysqli_fetch_All($result,MYSQLI_ASSOC);
        $orderList[$i]['productList']=$list;
    }

    //把数据编码为JSON字符串
    echo json_encode($orderList);
?>