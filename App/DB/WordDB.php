<?php
/**
 * 用户数据库类
 */
namespace App\DB;

use Model\Word;

class WordDB extends AbstractDB {
    public $TABLE = 'wiki_word';
    public $TABLE_VERIFY = 'wiki_word_verify';

    public function addWordVerify(Word $word) {
        $sql = "INSERT INTO $TABLE_VERIFY 
        (word,content,type,template,version,isDelete,createTime,updateTime)
        VALUES(?,?,?,?,?,0,now(),now())";

        $params = array($word->word, $word->content, $word->type, $word->template, $word->version);

        $this->insert($sql, $params);
    }

    public function editWordVerify(Word $word) {

    }

    public function deleteWordVerify(int $wordId) {

    }

    public function getWordVerify(int $wordId) {
        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime 
        FROM $this->TABLE_VERIFY WHERE id = '$wordId' ";

        $this->query($sql);
    }

    public function getWordVerifyPaging(int $pageIndex, int $pageSize) {

        $offset = ($pageIndex - 1) * $pageSize;

        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime 
        FROM $this->TABLE_VERIFY order by id desc limit $offset, $pageSize";

        $this->query($sql);
    }

    public function getWord(string $word) {

    }

}