<?php

/**
 * http请求池
 * author:jiangpengfei
 * date:2017-12-11
 */

namespace App\Utils;

use App\Utils\Curl;
use \SplQueue;          //php的spl标准库

class CurlPool{

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
            //还有剩余的curl对象
            $curl =  $this->pool->pop();
            $this->left--;     //剩余的curl对象减少
            return $curl;

        }else if($this->total < $this->max){
            //创建的总数小于最大允许的连接数,创建新的连接
            //创建新的curl对象
            $curl = new Curl();

            //创建的curl对象+1
            $this->total++;
            return $curl;
        }

        return null;
    }

    /**
     * 从http连接池中入队列
     * @param $curl  curl实例
     */
    public function push($curl){
        $this->pool->push($curl);
    }

}