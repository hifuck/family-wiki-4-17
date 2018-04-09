<?php

/**
 * Created by PhpStorm.
 * User: YF
 * Date: 16/8/25
 * Time: 上午12:05
 */
namespace Conf;

use Core\Component\Di;
use Core\Component\Spl\SplArray;
use Core\Component\Sys\SysConst;

class Config
{
    private static $instance;
    protected $conf;
    function __construct()
    {
        $conf = $this->sysConf()+$this->userConf();
        $this->conf = new SplArray($conf);
    }
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }
    function getConf($keyPath){
        return $this->conf->get($keyPath);
    }
    /*
     * 在server启动以后，无法动态的去添加，修改配置信息（进程数据独立）
     */
    function setConf($keyPath,$data){
        $this->conf->set($keyPath,$data);
    }

    private function sysConf(){
        return array(
            "SERVER"=>array(
                "LISTEN"=>"0.0.0.0",
                "SERVER_NAME"=>"",
                "PORT"=>9501,
                "WS_SUPPORT"=>false,
                "CONFIG"=>array(
                    'task_worker_num' => 8, //异步任务进程
                    "task_max_request"=>10,
                    'max_request'=>5000,//强烈建议设置此配置项
                    'worker_num'=>8,
                    "log_file"=>Di::getInstance()->get(SysConst::LOG_DIRECTORY)."/swoole.log",
                    'pid_file'=>Di::getInstance()->get(SysConst::LOG_DIRECTORY)."/pid.pid",
                ),
            ),
            "DEBUG"=>array(
                "LOG"=>1,
                "DISPLAY_ERROR"=>1,
                "ENABLE"=>false,
            ),
            "CONTROLLER_POOL"=>true,
            "MYSQL" => array(
                "HOST" => "47.104.149.49",
                "USER" => "family",
                "PASSWORD" => "Flare1111",
                "DB_NAME" => "family_wiki"
            ),
            "ES" => array(
                "SERVER_URL" => "http://192.168.3.61",
                "SERVER_PORT" => 9200,
                "WORD_TYPE" => "word"
            ),
            "SSO" => array(
                "SERVER_URL" => "http://www.xinhuotech.com",
                "SERVER_PORT" => 9090
            )
        );
    }

    private function userConf(){
        return array();
    }
}