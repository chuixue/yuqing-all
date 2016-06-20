<?php
	header('Content-Type:text/html; charset=utf8');
//mMysql.php
/*
 * Created on 2015-4-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
error_reporting(0);
	class EventInfo
	{
		private $xmlPath="";
		public $data=array();
		
		function __construct($path="") 
		{
			//echo "<pre>Get config xml file...</pre>";
       		$this->xmlPath=$path==""?"event.xml":"event.xml";
			$this->GetEvent();
   		}
   		//获取事件数据信息
		function GetEvent()
		{
			$dom = new DOMDocument(); 
			$dom->load($this->xmlPath); 
			$xpath = new DOMXPath($dom);
			$query = "//data/event";
			$dt = $xpath->query($query);
			for($i=0; $i<$dt->length; $i++)
			{
				$event=$dt->item($i);
				$e=new Event();
				$e->id=$event->getAttribute("id");
				$e->cls=$this->getValue($event,"class");
				$e->title=$this->getValue($event,"title");
				$e->status=$this->getValue($event,"status");
				$e->value=$this->getValue($event,"value");
				$e->content=$this->getValue($event,"content");
				$e->image=$this->getValue($event,"image");
				$labels=$event->getElementsByTagName('label')
					->item(0)->getElementsByTagName('name');
				for($j=0; $j<$labels->length; $j++)
				{
					$tp=$labels->item($j)->nodeValue;
					array_push($e->labels,$tp);
				}
				$medias=$event->getElementsByTagName('media')
					->item(0)->getElementsByTagName('name');
				for($j=0; $j<$medias->length; $j++)
				{
					$tp=$medias->item($j)->nodeValue;
					array_push($e->medias,$tp);
				}
				$likes=$event->getElementsByTagName('like')
					->item(0)->getElementsByTagName('event');
				for($j=0; $j<$likes->length; $j++)
				{
					$tp=$likes->item($j);
					$like=new Like();
					$like->title=$tp->getAttribute("title");
					$like->id=$tp->getAttribute("id");
					$like->value=$tp->getAttribute("value");
					$like->image=$tp->getAttribute("image");
					array_push($e->like,$like);
				}
				$spread=$event->getElementsByTagName('spread')
					->item(0)->getElementsByTagName('src');
				for($j=0; $j<$spread->length; $j++)
				{
					$tp=$spread->item($j);
					$sp=new Spread();
					$sp->src=$tp->getAttribute("key");					
					$sp->id=$tp->getAttribute("id");
					$days=$tp->getElementsByTagName('day');
					for($k=0; $k<$days->length; $k++)
					{
						$d=$days->item($k);
						$item=new Item();						
						$item->date=$d->getAttribute("date");
						$v=str_replace("\"","",$d->nodeValue);
						$item->value=explode(",",$v);
						array_push($sp->days,$item);
					}
					array_push($e->spread,$sp);
				}
				$deve=$event->getElementsByTagName('spread')
					->item(0)->getElementsByTagName('development')->item(0);
				$e->development=new Development();
				$e->development->date=$deve->getAttribute("date");
				$steps=$deve->getElementsByTagName('step');
				for($j=0; $j<$steps->length; $j++)
					array_push($e->development->step,$steps->item($j)->nodeValue);
			}
			array_push($this->data,$e);
		}
		function getValue($node,$tagName)
		{
			return $node->getElementsByTagName($tagName)->item(0)->nodeValue;
		}

		function __destruct()
		{
       		$this->tbList=null;
   		}
	}
 //******************************************************************
	
 	class Event
	{
		public $id=-1;
		public $cls="";
		public $title="";
		public $status="";
		public $labels=array();
		public $medias=array();
		public $value="";
		public $content="";
		public $image="";
		public $like=array();
		public $spread=array();
		public $development;
	} 
 	class Like
	{
		public $id=-1;
		public $image="";
		public $title="";
		public $value="";
	}
	class Spread
	{
		public $src="";
		public $id=-1;
		public $days=array();
	}
	class Item
	{
		public $date="";
		public $value=array();
	}
 	class Development
	{
		public $date="";
		public $step=array();
	}
 	
//	$e=new EventInfo();
//	var_dump($e->data[0]->spread);
?>
