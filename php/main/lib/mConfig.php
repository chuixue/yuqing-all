<?php
	header('Content-Type:text/html; charset=utf8');
//mMysql.php
/*
 * Created on 2015-4-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
	class Config
	{
		private $xmlPath="";
		public $db_host="";    //数据库地址
 		public $db_user="";    //用户
 		public $db_passwd="";  //密码
 		public $db_name="";    //数据库名
		public $db_port="";    //端口
		public $conn;		  //数据库连接
		public $tbList;
		
		function __construct($path="") 
		{
			//echo "<pre>Get config xml file...</pre>";
			$this->xmlPath="config/database.xml";
       		if($path!="")$this->xmlPath="../".$this->xmlPath;
			//echo "<pre>Read Database config info...</pre>";
			$this->GetConfigDB();
			//echo "<pre>Read Tables config info...</pre>";
			//$this->GetConfigTB();
   		}
   		//获取数据库配置信息
		function GetConfigDB()
		{
			$dom = new DOMDocument(); 
			$dom->load($this->xmlPath); 
			$xpath = new DOMXPath($dom);
			$query = "//database/mysql";
			$value = $xpath->query($query)->item(0);
			$this->dbHost=$this->getValue($value,"dbhost");
			$this->dbUser=$this->getValue($value,"dbuser");
			$this->dbPasswd=$this->getValue($value,"dbpasswd");
			$this->dbName=$this->getValue($value,"dbname");
			$this->dbPort=$this->getValue($value,"dbport");
		}
		function getValue($node,$tagName)
		{
			return $node->getElementsByTagName($tagName)->item(0)->nodeValue;
		}
		//获取表配置信息
   		function GetConfigTB() 
		{
			$dom = new DOMDocument(); 
			$dom->load($this->xmlPath); 
			$xpath = new DOMXPath($dom);
			$query = "//database/table";
			$dt = $xpath->query($query);
			$this->tbList=array();
			$attrs=array('colName','type','length','primary','increment','default','null','charset');
			for($i=0; $i<$dt->length; $i++)
			{
				$xmltb=$dt->item($i);
				$tb=new Table();
				$tb->tbName=$xmltb->getAttribute('tbName');
				$xmlcol=$xmltb->getElementsByTagName('column');
				for($j=0; $j<$xmlcol->length; $j++)
				{
					$index=0;
					$col=new Column();
					$temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->colName=$temp;	$temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->type=$temp;	$temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->length=$temp==''?'-1':$temp;	$temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->primary=$temp==''?'0':$temp;	$temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->increment=$temp==''?'0':$temp;   $temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->dfValue=$temp;		$temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->canNull=$temp==''?'1':$temp;	$temp=$xmlcol->item($j)->getAttribute($attrs[$index++]);
					$col->charset=$temp;
					array_push($tb->colList,$col);
				}
				array_push($this->tbList,$tb);
   			}
		}
		function __destruct()
		{
       		$this->tbList=null;
   		}
	}
 //******************************************************************
	
 	class Column
	{
		public $colName="";
		public $type="";
		public $length="-1";
		public $primary="0";
		public $increment="0";
		public $dfValue="";
		public $canNull="1";
		public $charset="";
		
	}
	class Table
	{
		public $tbName="";
	 	public $colList=array();
	
		public function	GetSql()
		{
			$pkey=array();
			$sql='create table ';
			if($this->tbName=='')return '';
			$sql.=$this->tbName.'(';
			for($i=0; $i<count($this->colList); $i++)
			{
				$col=$this->colList[$i];
				if($col->colName=='' || $col->type=='')return '';
				$sql.=$col->colName.' '.$col->type;
				if($col->length!='-1')$sql.='('.$col->length.')';
				if($col->canNull=='0')$sql.=' not null';
				if($col->dfValue!='')$sql.=" default '".$col->dfValue ."'";
				if($col->increment=='1')$sql.=' auto_increment';
				if($col->primary=='1')array_push($pkey,$col->colName);
				if($i!=count($this->colList)-1)$sql.=',';
			}
			if(count($pkey)>0)
			{
				$sql.=',primary key(';
				for($i=0; $i<count($pkey); $i++)
				{	
					$sql.=$pkey[$i];
					if($i!=count($pkey)-1)$sql.=',';
				}
				$sql.=')';
			}
			$sql.=')ENGINE=InnoDB DEFAULT CHARSET utf8 COLLATE utf8_general_ci';
			return $sql;
		}

	}



 
 
 
 
 
?>
