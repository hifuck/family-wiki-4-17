<?php
/**
 * 异步任务类，封装了将词条异步投递到elasticsearch中
 * 在访问量大的时候，可能会出现任务堆积，堵塞缓冲区
 * author: jiangpengfei
 * date: 2018-04-03
 */

namespace App\Task;

use Core\Swoole\AsyncTaskManager;
use App\Model\Word;
use App\Utils\HttpPool;
use App\Model\Task;
use App\ES\WordES;
use App\Utils\Util;

class AsyncTask {
    
    /**
     * 添加词条搜索的任务
     * author: jiangpengfei
     * date:   2018-04-03
     */
    public static function addWordSearchTask(Word $word) {
        $asyncTaskManager = AsyncTaskManager::getInstance();
        echo "投递异步任务";
        $word->id = intval($word->id);
        $word->type = intval($word->type);
        $word->template = intval($word->template);

        var_dump($word);

        $asyncTaskManager->add(function() use($word) {
            $wordES = new WordES();

            $task = new Task();
            $task->type = 1;                    //任务类型,1是词条
            $task->modelId = $word->id;         //元数据id
            $task->action = 1;                  //操作类型，1是增加，2是更新，3是删除
            $task->source = json_encode($word);                   //任务内容

            $wordES->putWord($task);
        });
    }
}