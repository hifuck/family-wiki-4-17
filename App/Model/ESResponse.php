<?php
/**
 * 封装了es回应的消息体 
 */
namespace App\Model;

class ESResponse{
    public $took;
    public $timed_out;
    public $_shards;
    public $hits;
    public $error;

    public function __construct($response){
        $obj = json_decode($response,true);
        $this->took = $obj['took'] ?? '';
        $this->timed_out = $obj['timed_out'] ?? '';
        $this->_shards = $obj['_shards'] ?? '';
        $this->hits = $obj['hits'] ?? '';
        $this->error = $obj['error'] ?? '';
        if($this->error != ''){
            var_dump($response);
        }
    }
}