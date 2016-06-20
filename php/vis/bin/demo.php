<?php
	header('Content-Type:text/html; charset=utf8');
	include "public.php";
	
	class OBJ{};		//空对象
	//通用响应格式
	class RST{
		public $error=0;     	//是否出错
		public $message="OK";	//错误信息或其它
		public $data;		//结果，为JSON格式
		public $newID=-1;	//插入新记录的id，用于带有更新数据的请求
	};
	
	$key=@$_GET['key'];			//请求的关键字
	$query=json_decode(@$_GET['data']);	//附带的数据
	$rst = new RST();			//待返回数据
	$data = new OBJ();

	//分类处理	
	if($key=="userInfo")
	{
		$id=$query->id;		//取出附加数据
		$id=1;				//测试用
		$sql="select * from examcategory where ID=$id";
		$id="cdqq-216886";
		$sql="select * from emotion where eventid='$id' and 'emationword'<>\"\" limit 10";
		echo $sql;
		$data=getJsonBySql($sql);

	}
	//......else if(key==)
	else
	{
		$rst->error=1;
		$rst->message="错误的请求：服务器未受理！";
	}
	

	$rst->data=$data;
	echo urldecode(@$_GET['callback'].'('.json_encode($rst).')');
	exit;
	
	
	

	
?>


