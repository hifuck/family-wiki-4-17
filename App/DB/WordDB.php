<?php
/**
 * 用户数据库类
 */

namespace App\DB;

use App\Model\Word;

class WordDB extends AbstractDB
{
    public $TABLE = 'wiki_word';
    public $TABLE_VERIFY = 'wiki_word_verify';
    public $TABLE_TYPE = 'wiki_word_type';
    public $TABLE_TEMPLATE = 'wiki_word_template';

    /**
     * 增加需审核的词条
     */
    public function addWordVerify(Word $word)
    {
        $sql = "INSERT INTO $this->TABLE_VERIFY 
        (word,content,type,template,version,isDelete,createTime,updateTime)
        VALUES(?,?,?,?,?,0,now(),now())";

        $params = array($word->word, $word->content, $word->type, $word->template, $word->version);

        return $this->insert($sql, $params);
    }

    /**
     * 编辑需审核的词条
     */
    public function editWordVerify(Word $word)
    {

    }

    /**
     * 删除需审核的词条
     */
    public function deleteWordVerify(int $wordId)
    {

    }

    /**
     * 获取需审核的词条
     */
    public function getWordVerify(int $wordId)
    {
        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime 
        FROM $this->TABLE_VERIFY WHERE id = '$wordId' ";

        $this->query($sql);
    }

    public function getWordVerifyPaging(int $pageIndex, int $pageSize)
    {

        $offset = ($pageIndex - 1) * $pageSize;

        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime 
        FROM $this->TABLE_VERIFY order by id desc limit $offset, $pageSize";

        $this->query($sql);
    }

    public function getWord(string $word)
    {
        $sql = "SELECT id,word,content,type,template,version,createTime,updateTime FROM $this->TABLE where word='$word' AND isDelete = '0' ";

        return $this->uniqueResult($sql);
    }

}