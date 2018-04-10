<?php
/**
 * 人物搜索
 */

namespace App\ES;

use App\ES\AbstractES;
use Conf\Config;
use App\Model\ESRequest;
use App\Model\Task;

class WordES extends AbstractES{
    
    private $config = null;

    public function init(){
        $this->config = Config::getInstance()->getConf('ES');
    }

    public function searchWord($searchStr){
        $esRequest = new ESRequest();
        $esRequest->index = $this->config['WORD_TYPE'];
        $esRequest->type = $this->config['WORD_TYPE'];
        $esRequest->searchStr = $searchStr;
        return $this->search($esRequest);
    }

    /**
     * 删除词条
     */
    public function deleteWord(Task $task){
        $esRequest = new ESRequest();
        $esRequest->index = $this->config['WORD_TYPE'];
        $esRequest->type = $this->config['WORD_TYPE'];
        $esRequest->id = $task->modelId;
        return $this->delete($esRequest);
    }

    /**
     * 创建词条
     */
    public function putWord(Task $task){

        $esRequest = new ESRequest();
        $esRequest->index = $this->config['WORD_TYPE'];
        $esRequest->type = $this->config['WORD_TYPE'];
        $esRequest->id = $task->modelId;
        $esRequest->source = $task->source;

        return $this->put($esRequest);
    }


     /**
     * 更新词条
     */
    public function updateWord(Task $task){
        $esRequest = new ESRequest();
        $esRequest->index = $this->config['WORD_TYPE'];
        $esRequest->type = $this->config['WORD_TYPE'];
        $esRequest->id = $task->modelId;
        $doc['doc'] = $task->source;
        $esRequest->doc = $doc;
        return $this->update($esRequest);
    }
}