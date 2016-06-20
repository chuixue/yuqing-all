<?php
//header("Content-Type:text/html;charset=utf8");
ini_set('display_errors','On');
include 'public.php';
include '../lib/demoData.php';
class OBJ{};

class RST{
    public $error=0;     	//是否出错
    public $message="OK";	//错误信息或其它
    public $data;		//结果，为JSON格式
    public $newID=-1;	//插入新记录的id，用于带有更新数据的请求
};
$key=@$_GET['key'];			//请求的关键字
$query=json_decode(@$_GET['data']);	//附带的数据
//$ID = @$_GET['id'];
$rst = new RST();			//待返回数据
$data = new OBJ();
//分类处理
$data = handle($rst, $data, $key, $query);
if (is_numeric($data)) $data = new OBJ();

$rst->data=$data;
echo urldecode(@$_GET['callback'].'('.json_encode($rst).')');
exit;

function handle($rst, $data, $key, $query) {
    if ($key=="event") {
        $pageNum = 4;
        $page = $query->page * $pageNum;
        $cls = $query->class;

        $sql = "select sortname as name, sortindex as id from sort";
        $data->eClass = getJsonBySql($sql);

        if (-1 == $cls)
            $sql = "select eventid as id, sort as cls, centertitle as title, tags as labels, heart as value, summary as content from event
                    ORDER BY heart DESC limit $page, $pageNum";
        elseif (isClassID($cls, $data->eClass))
            $sql = "select eventid as id, sort as cls, centertitle as title, tags as labels, heart as value, summary as content from event
                    WHERE sort = $cls ORDER BY heart DESC limit $page, $pageNum";
        else {
            $rst->error = 2;
            $rst->message = urlencode("错误的请求：服务器未受理！");
            return -1;
        }
        $data->events = getJsonBySql($sql);
        foreach ($data as $obj) {
            setStatus($obj);
        }
    } else if ($key == "eventInfo") {
        $eventID = $query->ID;
//    $eventID = "kdnet-8440154";
        if ($eventID != null) {
            $sql = "select eventid as id, sort as cls, centertitle as title, tags as labels, heart as value, summary as content, img from event WHERE eventid= '$eventID'";
            $data = getJsonBySql($sql);
            $data = $data[0];
            getMedias($data);
            getFirstDevelopments($data);
            getLikeEvents($data);

            setStatus($data);
            setSpread($data);
        } else {
            $rst->error=3;
            $rst->message=urlencode("错误的请求：服务器未受理！");
            return -2;
        }
    } elseif ("eventText" == $key) {
        $eventID = $query->ID;
        $date = $query->date;
        if ($eventID != null && $date != null) {
            $day = substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, -2);
            $sql = "SELECT title FROM eventskeleton WHERE eventid = '$eventID' AND wspdttime LIKE '$day%' ORDER BY wspdttime";
            $data = getArrayBySql($sql);
        } else {
            $rst->error = 4;
            $rst->message = urlencode("错误的请求：服务器未受理！");
            return -3;
        }
    } elseif ("eventSearch" == $key) {
        $pageNum = 4;
        $text = $query->text;
        $page = $query->page * $pageNum;
        $sql = "SELECT eventid as id, sort as cls, centertitle as title, tags as labels, heart as value, summary as content FROM event
                  WHERE centertitle LIKE '%$text%' OR '%$text' OR '$text%' OR '$text' ORDER BY heart DESC limit $page, $pageNum";
        @$data->events = getJsonBySql($sql);
        foreach ($data as $obj) {
            setStatus($obj);
        }
    } else {
        $rst->error=1;
        $rst->message=urlencode("错误的请求：服务器未受理！");
        retrun -4;
    }
    return $data;
}

//just work for test
function setStatus($data) {
        $data->status = urlencode("已结束");
}

//just work for test
function setSpread($data) {
    $e = new EventInfo();
        $demoData = $e->data[0]->spread;
        $all = array();
        foreach ($demoData as $item) {
            $p = new OBJ();
            foreach ($item as $key => $value) {
                if ("days" == $key) {
                    $arr = array();
                    $q = new OBJ();
                    foreach ($value as $k) {
                        foreach ($k as $i => $j) {
                            if ("value" == $i) {
                                $q->$i = $j;
//                                $p->$key->$i = $j;
                            } else {
                                $q->$i = urlencode($j);
//                                $p->$key->$i = urlencode($j);
                            }
                        }
                        array_push($arr, $q);
                    }
                    $p->$key = $arr;
                } else {
                    $p->$key = urlencode($value);
                }
            }
            array_push($all, $p);
        }
        $data->spread = $all;

}

function isClassID($classId, $classList) {
    foreach ($classList as $item) {
        if ($classId == $item->id) {
            return true;
        }
    }
    return false;
}

function getMedias($data) {
        $id = $data->id;
        $sql = "select ws from comevent where eventid = '$id'";
        $data->medias = getArrayBySql($sql);
}

function getFirstDevelopments($data) {
        $id = $data->id;
        $sql = "SELECT title FROM eventskeleton WHERE eventid = '$id' ORDER BY wspdttime";
        @$data->development->step = getArrayBySql($sql);
        $sql = "SELECT wspdttime FROM eventskeleton WHERE eventid = '$id' ORDER BY wspdttime limit 1";
        $date = getArrayBySql($sql);
        @$data->development->date = $date[0];
}

function getLikeEvents($data) {
        $cls = urldecode($data->cls);
        $id = urldecode($data->id);
        $sql = "SELECT count(*) as number FROM event WHERE heart >= (SELECT heart FROM event WHERE eventid = '$id')";
        $arr = getArrayBySql($sql);
        $rank = $arr[0];
        $min = $rank - 5;
        if ($min < 0) $min = 0;
        $max = $rank + 5;
        $num = rand($min, $max);
        $sql = "SELECT eventid as id, img as image, centertitle as title, heart as value FROM event
                  WHERE sort = $cls AND eventid <> '$id' ORDER BY heart DESC limit $num, 6";
        $data->like = getJsonBySql($sql);
}
