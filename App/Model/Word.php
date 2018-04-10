<?php
/**
 * 词条的模型类
 */

namespace App\Model;

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
    public $subContent;
    public $author;
    public $authorName;
}