<?php

/**
 * Created by PhpStorm.
 * User: kbyyd
 * Date: 2016/1/22
 * Time: 17:14
 */
class DBmongo
{
    const dbType = "mongodb";
    private $cfg;
    private $dbHost="";    //数据库地址
    private $dbUser="";    //用户
    private $dbPasswd="";  //密码
    private $dbName="";    //数据库名
    private $dbPort="";    //端口
    private $conn;		  //数据库连接
    private $db;
    private $collection;

    function __construct($path="")
    {
        $this->cfg=$path!=""?new Config(self::dbType):new Config(self::dbType, 1);
        $this->SetData($this->cfg->dbHost,$this->cfg->dbUser,$this->cfg->dbPasswd,$this->cfg->dbPort,$this->cfg->dbName);
        $this->connect();
    }
    private function SetData($host,$user,$passwd,$port=3306,$dbname="")
    {
        $this->dbHost=$host;
        $this->dbUser=$user;
        $this->dbPasswd=$passwd;
        $this->dbPort=$port;
        $this->dbName=$dbname;
    }
    private function connect() {
        if ("" == $this->dbUser) {
            $dsn = "mongodb://$this->dbHost:$this->dbPort";
        } else {
            $dsn = "mongodb://$this->dbUser:$this->dbPasswd@$this->dbHost:$this->dbPort";
        }
        try {
            $this->conn = new MongoClient($dsn, array("connect" => false));
            $this->selectDB();
        } catch (MongoException $me) {
            return false;
        }
    }

    public function selectDB() {
        try {
            $this->db = $this->conn->selectDB($this->dbName);
        } catch (MongoException $me) {
            return false;
        }
        return true;
    }

    public function selectCollection($colName = "") {
        if ("" == $colName) return false;
        try {
            $this->collection = $this->db->selectCollection($colName);
        } catch (MongoException $me) {
            return false;
        }
        return true;
    }

    public function select($query = "", $fields = "") {
        if (!is_a($this->collection, MongoCollection)) return false;
        if (!is_array($query)) return $this->collection->find();
        elseif (!is_array($fields)) return $this->collection->find($query);
        else return $this->collection->find($query, $fields);
    }

}