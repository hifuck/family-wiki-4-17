<?php

/**
 * http请求池
 * author:jiangpengfei
 * date:2017-12-11
 */

namespace App\Utils;

use Conf\Config;
use App\Utils\HttpClient;
use \SplQueue;          //php的spl标准库

class HttpPool{

    protected static $instance;
    private $pool;                          //请求池
    private $max = 100;                   //最大的请求数,因为swoole是多进程模式，可以在每个进程下都实例化这个对象
    private $total = 0;                     //创建的请求总数
    private $left = 0;                      //剩余的请求总数

    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct(){
        $this->pool = new SplQueue;
    }

    /**
     * 设置线程池的最大容量
     */
    public function setMax($max){
        $this->max = $max;
    }

    /**
     * 获取线程池的最大容量
     */
    public function getMax(){
        return $this->max;
    }

    /**
     * 从http连接池中出队列
     * @return object request对象
     */
    public function pop(){
        if($this->left > 0){
            //还有剩余的httpClient对象
            $httpClient =  $this->pool->pop();
            $this->left--;     //剩余的httpClient对象减少
            return $httpClient;

        }else if($this->total < $this->max){
            //创建的总数小于最大允许的连接数,创建新的连接
            //创建新的httpClient对象
            $config = Config::getInstance()->getConf("ES");
            $httpClient = new HttpClient($config['SERVER_URL'],$config['SERVER_PORT']);

            //创建的httpClient对象+1
            $this->total++;
            return $httpClient;
        }

        return null;
    }

    /**
     * 从http连接池中入队列
     * @param $httpClient  httpClient实例
     */
    public function push($httpClient){
        $this->pool->push($httpClient);
    }

}