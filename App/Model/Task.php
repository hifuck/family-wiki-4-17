<?php
/**
 * 任务模型
 */
namespace App\Model;

class Task{
    public $type;       //任务类型,1是词条
    public $modelId;    //元数据id
    public $action;     //操作类型，1是增加，2是更新，3是删除
    public $source;     //任务内容

    public function __construct($result = null){
        if ($result != null) {
            $result = \json_decode($result,true);
            $this->type = $result['type'] ?? '';
            $this->modelId = $result['modelId'] ?? '';
            $this->action = $result['action'] ?? '';
            $this->source = $result['source'] ?? '';
        }
    }
}