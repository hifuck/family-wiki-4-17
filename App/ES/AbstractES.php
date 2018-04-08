<?php

namespace App\ES;

use Conf\Config;
use App\Utils\HttpPool;
use App\Model\ESRequest;
use App\Model\ESResponse;


abstract class AbstractES{
    
    protected $pool = null;
    protected $httpClient = null;
    protected $server = null;

    public function __construct(){
        //如果连接池对象不存在，则新建连接池对象
        $this->pool = HttpPool::getInstance();
        $config = Config::getInstance()->getConf('ES');
        $this->server = "{$config['SERVER_URL']}:{$config['SERVER_PORT']}";
        $this->init();
    }

    protected abstract function init();

    /**
     * 搜索文档
     */
    protected function search($esRequest){
        $this->httpClient = $this->pool->pop();
        $url = '/'.$esRequest->index.'/'.$esRequest->type.'/_search';
        $result = $this->httpClient->get($url,$esRequest->searchStr);
        $this->retrieve();
        return new ESResponse($result);
    }

    /**
     * 获取指定文档
     */
    protected function get($esRequest){
        $this->httpClient = $this->pool->pop();
        $result = $this->httpClient->get('/'.$esRequest->index.'/'.$esRequest->type.'/'.$esRequest->id);
        $this->retrieve();
        return new ESResponse($result);
    }

    /**
     * 插入文档
     */
    protected function put($esRequest){
        $this->httpClient = $this->pool->pop();
        $url = '/'.$esRequest->index.'/'.$esRequest->type.'/'.$esRequest->id;

        $result = $this->httpClient->post($url,$esRequest->source);


        $this->retrieve();
        return new ESResponse($result);
    }

    /**
     * 更新文档
     */
    protected function update($esRequest){
        $this->httpClient = $this->pool->pop();
        $url = '/'.$esRequest->index.'/'.$esRequest->type.'/'.$esRequest->id.'/_update';

        $result = $this->httpClient->post($url,$esRequest->doc);


        $this->retrieve();
        return new ESResponse($result);
    }

    
    /**
     * 删除文档
     */
    protected function delete($esRequest){
        $this->httpClient = $this->pool->pop();
        $result = $this->httpClient->delete('/'.$esRequest->index.'/'.$esRequest->type.'/'.$esRequest->id);
        $this->retrieve();
        return new ESResponse($result);
    }

    /**
     * 回收curl，在不使用的时候，一定要记得回收
     */
    protected function retrieve(){
        $this->pool->push($this->httpClient);
    }
}