<?php
/**
 * 封装了es的请求
 * @author jiangpengfei 
 * @date   2017-12-25
 */
namespace App\Model;

class ESRequest{
    public $index;          //文档索引
    public $type;           //文档类型
    public $id;             //文档id
    public $searchStr;      //检索的语句
    public $source;         //文档的内容
    public $filter;         //过滤
    public $doc;            //更新文档的内容
    
    public function __construct($request = array()){
        $this->index = $request['index'] ?? '';
        $this->type = $request['type'] ?? '';
        $this->id = $request['id'] ?? '';
        $this->searchStr = $request['searchStr'] ?? '';
        $this->source = $request['source'] ?? '';
        $this->filter = $request['filter'] ?? array();
        $this->doc = $request['doc'] ?? '';
    }
}