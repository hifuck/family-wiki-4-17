<?php
/**
 * es 的搜索语句封装
 */
namespace App\Model;

class ESQuery{
    public $query;

    public function __construct(){
        $this->query = array();
    }

    public function setPaging($index,$size){
        $this->query['from'] = ($index  - 1) * $size;
        $this->query['size'] = $size;
    }

    /**
     * bool查询必须条件
     */
    public function setBoolMustMatch($key,$value){
        if(!isset($this->query['query']['bool']['must'])){
            $this->query['query']['bool']['must'] = array();
        }
        $match['match'][$key] = $value;
        $this->query['query']['bool']['must'][] = $match;
    }

    public function setBoolMustMultiMatch($fields,$value){
        if(!isset($this->query['query']['bool']['must'])){
            $this->query['query']['bool']['must'] = array();
        }
        $fields['multi_match']["fields"] = $fields;
        $query['multi_match']["query"] = $value;

        $this->query['query']['bool']['must'][] = $fields;
        $this->query['query']['bool']['must'][] = $value;
    }

    /**
     * bool查询should条件
     */
    public function setBoolShouldMatch($key,$value){
        if(!isset($this->query['query']['bool']['should'])){
            $this->query['query']['bool']['should'] = array();
        }

        $should['match'][$key] = $value;
        $this->query['query']['bool']['should'][] = $should;
    }

    public function setBoolShouldRange($key,$value){
        if(!isset($this->query['query']['bool']['should'])){
            $this->query['query']['bool']['should'] = array();
        }

        $range['range'][$key] = $value;
        $this->query['query']['bool']['should'][] = $range;
    }

    /**
     * bool查询过滤器
     */
    public function setBoolFilterTerms($key,$value){
        $this->query['query']['bool']['filter']['terms'][$key] = $value;
    }

    /**
     * 单个查询
     */
    public function setMatch($key,$value){
        $this->query['query']['match'][$key] = $value;
    }

    /**
     * ES的multi_match查询，在多个字段上执行相同的match查询
     */
    public function setMultiMatch($fields,$value){
        $this->query['query']['multi_match']["fields"] = $fields;
        $this->query['query']['multi_match']["query"] = $value;
    }

    /**
     * term精确查询
     */
    public function setTerm($key,$value){
        $this->query['query']['term'][$key] = $value;
    }

    /**
     * range查询
     */
    public function setRange($key,$op,$value){
        $this->query['query']['range'][$key][$op] = $value;
    }

    /**
     * 多term查询
     */
    public function setTerms($key,$fields){
        $this->query['query']['terms'][$key] = $fields;
    }

    public function toString(){
        return json_encode($this->query);
    }
}