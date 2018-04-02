<?php
/**
 * 词条的模型类
 */

namespace App\Model\Word;

class Word {
    public $id;
    public $word;
    public $content;
    public $type;
    public $template;
    public $version;
    public $isDelete;
    public $createTime;
    public $updateTime;
}