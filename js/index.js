/**
 * Created by bjwsl-001 on 2016/11/9.
 */
var app=angular.module("NBA",['ng','ngRoute']);
//设置post请求的响应头部
app.run(function($http){
  $http.defaults.headers.post={"Content-Type":"application/x-www-form-urlencoded"};
})
//设置根控制器
app.controller("rootCtrl",["$scope","$rootScope","$location","$routeParams","$http",function($scope,$rootScope,$location,$routeParams,$http){
  //跳转到该路由时先判断屏幕宽度；
  $rootScope.userId=1;//用来保存用户的id;
  $rootScope.userName="lijun";//用户名；后续功能完善后在删除
  $rootScope.pclass="";//查询商品类别；
  $rootScope.searchMsg={};//查询信息
  $rootScope.searchMsg.pageNum=1;
  $rootScope.num=[];//分页数字数组
  $rootScope.len=8;//每页记录数
  $rootScope.proList=[];//保存商品列表数据
  $rootScope.isMore=true;//是否有后一页
  $rootScope.isPrev=false;//是否有前一页
  $rootScope.exit=function(){
    $rootScope.userId="";
    $rootScope.userName="";
  }
  $rootScope.jump=function(url){
    $location.path(url);
  }
  $rootScope.$watch("searchMsg.pageNum",function(){
    //判断分页按钮状态
    $rootScope.isPrev=$rootScope.searchMsg.pageNum>1?true:false;
    $rootScope.isMore=$rootScope.searchMsg.pageNum>=$rootScope.pageCount?false:true;
  })
  $rootScope.loadMore=function(n,url) {
    //接收要跳转到的页面
    $rootScope.searchMsg.pageNum=n;
    $http.get(url+"?"+$.param($rootScope.searchMsg)).success(function (obj) {
      $rootScope.pageCount=obj.pageCount;
      $rootScope.len = obj.data.length;
      if (innerWidth > 450) {//页面宽度不是手机页面时清空列表实现分页加载商品详情
        $rootScope.proList = [];
        $rootScope.num=[];
        for(var i=1;i<=obj.pageCount;i++){
          $rootScope.num.push(i);
          $rootScope.isPageShow=true;
        }
      }else{
        $rootScope.isPageShow=false;
        if($rootScope.len<8){
          $rootScope.searchMsg.pageNum++;
        }
      }
      for (var i = 0; i < $rootScope.len; i++) {
        var img=obj.data[i].img_sm;
        obj.data[i].img_sm=img.slice(0,img.length-9)+"sm.jpg";
        $rootScope.proList.push(obj.data[i]);
      }
    });
  };
  $rootScope.goToUserCenter=function(){
    if($rootScope.userName){
      $location.path('/mall_userCenter/1');
    }else{
      //TODO 弹出提示框；
      alert("请登录");
    }
  }
}]);
//配置路由
app.config(function($routeProvider){
  $routeProvider
    .when("/APP_start",{
      templateUrl:"tpl/APP_start.html"
    })
    .when("/mall_main",{
      templateUrl:"tpl/mall_main.html",
      controller:"mallMainCtrl"
    })
    .when("/mall_search/:id",{
      templateUrl:"tpl/mall_search.html",
      controller:"mallSearchCtrl"
    })
    .when("/mall_proList/:id",{
      templateUrl:"tpl/mall_proList.html",
      controller:"mallProListCtrl"
    })
    .when("/mall_proListbyteam/:id",{
      templateUrl:"tpl/mall_proListbyteam.html",
      controller:"mallProListByTeamCtrl"
    })
    .when("/mall_detail/:id",{
      templateUrl:"tpl/mall_detail.html",
      controller:"mallDetailCtrl"
    })
    .when("/mall_lottery",{
      templateUrl:"tpl/mall_lottery.html",
      controller:"mallLotteryCtrl"
    })
    .when("/mall_userCenter/:id",{
      templateUrl:"tpl/mall_userCenter.html",
      controller:"mallUserCenterCtrl"
    })
    .otherwise({redirectTo:"/APP_start"})
});
app.controller("mallMainCtrl",["$scope",function($scope){

}]);
app.controller("mallDetailCtrl",["$scope","$routeParams","$http","$rootScope",function($scope,$routeParams,$http,$rootScope){
  //接收路由传递的参数，向服务器端请求商品详情
  $scope.order={}
  $http.get("data/7_showProductDetails.php?proId="+$routeParams.id).success(function(obj){
    $scope.proDetail=obj;
    $scope.order.count=1;
    $scope.order.proId=$routeParams.id;
    $scope.colorList=obj.colorList;
    $scope.order.colorId=$scope.colorList[0].colorId;//颜色id;
    $scope.photoList=$scope.colorList[0].photoList;//颜色对应的图片列表
    $scope.sizeList=$scope.colorList[0].sizeList;//颜色对应的尺寸列表
    $scope.Img={};
    $scope.Img.s=$scope.colorList[0].photoList[0].img_sm;
    $scope.Img.m=$scope.Img.s.substring(0,$scope.Img.s.length-6)+"md.jpg";
    $scope.order.sizeId=$scope.colorList[0].sizeList[0].sizeId;
    //商品详情数组
    $scope.pinfo=obj.pinfo.split("_");
    $scope.$watch("Img.s",function(){
      $scope.Img.m=$scope.Img.s.substring(0,$scope.Img.s.length-6)+"md.jpg";
    })
    $scope.$watch("order.colorId",function(){
      for(var i=0;i<$scope.colorList.length;i++){
        if($scope.order.colorId==$scope.colorList[i].colorId){
          $scope.photoList=$scope.colorList[i].photoList;
          $scope.Img.s=$scope.colorList[i].photoList[0].img_sm;
          $scope.Img.m=$scope.Img.s.substring(0,$scope.Img.s.length-6)+"md.jpg";
          $scope.sizeList=$scope.colorList[i].sizeList;
          $scope.order.sizeId=$scope.colorList[i].sizeList[0].sizeId;
        }
      }
    })
  })
  $scope.reduce=function(){
    if($scope.order.count>1){
      $scope.order.count--;
    }
  }
  $scope.add=function(){
    $scope.order.count++;
  }
  //加入购物车
  $scope.addToCart=function(){
    if($rootScope.userName){
      $scope.order.uname=$rootScope.userName;
      //发送请求提交数据
      if($scope.order.proId!==undefined
        && $scope.order.count!==undefined
        && $scope.order.colorId!==undefined
        && $scope.order.sizeId!==undefined){
        $http.post("data/8_cartAdd.php", $.param($scope.order)).success(function(txt){
          if(txt=="ok"){
            alert("商品添加购物车成功,您可以去到我的购物车进行结算")
          }else{
            alert("添加失败")
          }
        })
      }
    }else{
      //TODO 弹出提示框，提醒用户登录
      alert("您还未登录，请登录后在使用此功能")
    }
  }
}]);
app.controller("mallLotteryCtrl",["$scope",function($scope){

}]);
app.controller("mallSearchCtrl",["$scope","$rootScope","$routeParams","$http",function($scope,$rootScope,$routeParams,$http){
  $rootScope.searchMsg={};
  $rootScope.searchMsg.kw=$routeParams.id;
  $rootScope.proList=[];
  $rootScope.loadMore(1,"data/4_showProductByKw.php");
}]);
app.controller("mallProListCtrl",["$scope","$rootScope","$routeParams","$http",function($scope,$rootScope,$routeParams,$http){
  $rootScope.isPageShow=innerWidth>450?true:false;
  $rootScope.searchMsg={};
  $rootScope.searchMsg.pclass=$routeParams.id;
  $rootScope.num=[];
  $rootScope.proList=[];
  $rootScope.loadMore(1,"data/5_showProductByPclass.php");
  $scope.show=function(n){
    $rootScope.loadMore(n+1,"data/5_showProductByPclass.php");
  }
  $scope.showNext=function(){
    $rootScope.searchMsg.pageNum++;
    $rootScope.loadMore($rootScope.searchMsg.pageNum,"data/5_showProductByPclass.php");
  }
  $scope.prev=function(){
    $rootScope.searchMsg.pageNum--;
    $rootScope.loadMore($rootScope.searchMsg.pageNum,"data/5_showProductByPclass.php");
  }
  $scope.add=function(){
    $rootScope.searchMsg.pageNum++;
    $rootScope.loadMore($rootScope.searchMsg.pageNum,"data/5_showProductByPclass.php");
  }
}]);
app.controller("mallProListByTeamCtrl",["$scope","$rootScope","$routeParams","$http",function($scope,$rootScope,$routeParams,$http){
  $rootScope.searchMsg={};
  $rootScope.searchMsg.team=$routeParams.id;
  $rootScope.proList=[];
  $rootScope.loadMore(1,"data/6_showProductByTeam.php");
}]);
app.controller("mallUserCenterCtrl",["$scope","$rootScope","$routeParams","$http",function($scope,$rootScope,$routeParams,$http){
  if($routeParams.id==1){
    $scope.isMyCart=true;
    $scope.isMyOrder=false;
    $http.get("data/9_cartShow.php?uname="+$rootScope.userName).success(function(data){
      $scope.productList=data;
      for(var i= 0,sum=0;i<$scope.productList.length;i++){
        var total=($scope.productList[i].price*$scope.productList[i].count).toFixed(2);
        $scope.productList[i].totalPrice=total;
        sum+=Number(total);
      }
      $scope.total=sum;
    })
    $scope.removePro=function(did){
      $scope.did=did;
      $http.get("data/10_cartRemove.php?did="+did).success(function(txt){
        if(txt=="ok"){
          for(var i=0;i<$scope.productList.length;i++){
            if($scope.productList[i].did==$scope.did){
              $scope.total-=$scope.productList[i].totalPrice;
              $scope.productList.splice(i,1);
              break;
            }
          }
        }else{
          alert("删除失败了")
        }
      })
    }
    $scope.submitOrder=function(){
      $scope.data={};
      $scope.data.rcvId=1;
      $scope.data.price=$scope.total;
      $scope.data.payment=1;
      $scope.data.uname=$rootScope.userName;
      $scope.data.productList=JSON.stringify($scope.productList);
      $http.post("data/11_addOrder.php", $.param($scope.data)).success(function(data){
        if(data.msg=="succ"){
          alert("订单提交成功，您的订单编号为"+data.orderNum+"; 您可以在我的订单中查看订单状态");
          $scope.productList=[];
          $scope.total=0;
        }else{
          alert("订单提交失败");
        }
      })
    }
  }else{
    $scope.isMyCart=false;
    $scope.isMyOrder=true;
    $scope.orderList=null;
    $http.get("data/12_showOrder.php?uname="+$rootScope.userName).success(function(data){
      $scope.orderList=data;
      for(var i=0;i<$scope.orderList.length;i++){
        var date=new Date(Number($scope.orderList[i].orderTime));
        $scope.orderList[i].orderTime=$scope.changeTime(date);
        var status=$scope.orderList[i].status;
        $scope.orderList[i].status=$scope.judgeStatus(status);
      }
    })
    //转换日期格式
    $scope.changeTime=function(date){
      var year=date.getFullYear();
      var mouth=date.getMonth();
      mouth=mouth<10?("0"+mouth):mouth;
      var day=date.getDate();
      day=day<10?("0"+day):day;
      var hour=date.getHours();
      hour=hour<10?("0"+hour):hour;
      var minues=date.getMinutes();
      minues=minues<10?("0"+minues):minues;
      var second=date.getSeconds();
      second=second<10?("0"+second):second;
      return year+'-'+mouth+'-'+day+'\n'+hour+":"+minues+":"+second;
    }
    //判断订单状态
    $scope.judgeStatus=function(status){
      switch(status){
        case "1":
          return "等待付款";
          break;
        case "2":
          return "等待配货";
          break;
        case "3":
          return "运输中";
          break;
        case "4":
          return "已收货";
          break;
      }
    }
  }
}]);

function chose(obj){
  $(obj).addClass("color-box-active").parent().siblings("label").children(".color-box-active").removeClass("color-box-active");
}
