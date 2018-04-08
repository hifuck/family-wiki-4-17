<?php

namespace App\DB;

use Core\Component\Di;
use Core\Component\Sys\SysConst;
use Conf\Config;
use PDO;

class AbstractDB{

    protected $pdo = null;

    public function __construct(){
        if( ($this->pdo = Di::getInstance()->get(SysConst::PDO)) == null){
            //如果pdo对象不存在,则构建数据库连接
            $this->connect();
        }
        
    }

    private function connect(){
        $conf = Config::getInstance()->getConf('MYSQL');
        $this->pdo = new PDO("mysql:host=".$conf['HOST'].";dbname=".$conf['DB_NAME'],$conf['USER'],$conf['PASSWORD'], array(PDO::ATTR_PERSISTENT=>true));
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("set names utf8mb4");
        Di::getInstance()->set(SysConst::PDO,$this->pdo);
    }

    private function stmtExcute($sql,$params){
        try{
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
        }catch(\Exception $e){
            if($e->getCode() == "HY000"){
                $this->connect();
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute($params);
            }else{
                throw $e;
            }
        }

        return $stmt;
    }

    /**
     * 只取一个结果
     */
    public function uniqueResult($sql,$params = array()){
        $result = $this->query($sql,$params);
        if(count($result) > 0){
            return $result[0];
        }else{
            return null;
        }
    }

    /**
     * 查询
     */
    public function query($sql,$params = array()){
        
        if($stmt = $this->stmtExcute($sql,$params)){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }

    /**
     * 插入
     */
    public function insert($sql,$params = array()){
        if($this->stmtExcute($sql,$params)){
            return $this->pdo->lastinsertid();
        }else{
            return false;
        }
    }

    /**
     * 更新或删除
     */
    public function update($sql,$params = array()){
        if($stmt = $this->stmtExcute($sql,$params)){
            return $stmt->rowCount();
        }else{
            return false;
        }
    }

}