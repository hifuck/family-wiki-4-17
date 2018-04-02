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
                "HOST" => "120.26.103.174",
                "USER" => "family",
                "PASSWORD" => "Flare1111",
                "DB_NAME" => "family"
            ),
            'ALIPAY' => array(
                'alipay_public_key' => 'App/Sdk/Alipay/sandbox/alipay_public_key.txt',
                'app_private_key' => 'App/Sdk/Alipay/sandbox/app_private_key.pem',
                'gateway_url' => 'https://openapi.alipay.com/gateway.do',
                'appId' => '2016091200490439',
                'format' => 'json',
                'charset' => 'UTF-8',
                'signType' => 'RSA2'
            )
        );
    }

    private function userConf(){
        return array();
    }
}