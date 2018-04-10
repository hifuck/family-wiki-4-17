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
     * 根据Id获取词条内容
     * @param int $wordId
     * @return Word
     */
    public function getWordVerifyById(int $wordId)
    {
        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime 
        FROM $this->TABLE_VERIFY WHERE id = '$wordId' ";

        $result = $this->uniqueResult($sql);
        $word = new Word();
        $word->id = $result['id'];
        $word->word = $result['word'];
        $word->content = $result['content'];
        $word->type = $result['type'];
        $word->template = $result['template'];
        $word->version = $result['version'];
        $word->isDelete = $result['isDelete'];
        $word->createTime = $result['createTime'];
        $word->updateTime = $result['updateTime'];
        return $word;

    }

    /**
     * 分页获取待审核的词条
     * @param int $pageIndex
     * @param int $pageSize
     * @return bool
     */
    public function getWordVerifyReviewPaging(int $pageIndex, int $pageSize)
    {
        $offset = ($pageIndex - 1) * $pageSize;

        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime 
        FROM $this->TABLE_VERIFY WHERE isVerify = 0 AND isDelete = 0 ORDER BY id DESC LIMIT $offset, $pageSize";

        return $this->query($sql);
    }

    /**
     * 待审核的词条数
     * @return mixed
     */
    public function countWordVerifyReview()
    {
        $sql = "SELECT count('id') FROM $this->TABLE_VERIFY WHERE isVerify=0 AND isDelete = 0";
        $result = $this->uniqueResult($sql);
        return $result["count('id')"];
    }

    /**
     * 审核通过 1、不通过 -1
     * @param int $isVerify
     * @param int $wordId
     * @return bool
     */
    public function wordIsVerified(int $isVerify, int $wordVerifyId)
    {
        $sql = "UPDATE $this->TABLE_VERIFY SET isVerify = ? WHERE id = ? AND isVerify = 0 AND isDelete = 0";
        $params = [$isVerify, $wordVerifyId];
        return $this->update($sql, $params);
    }

    /**
     * 审核通过的词条，将记录同步到wiki_word表中
     * @param $word
     * @return bool
     */
    public function addWord($word)
    {
        $sql = "INSERT INTO $this->TABLE(word,content,type,template,version,isDelete,createTime,updateTime) 
          VALUES (
          '$word->word',
          '$word->content',
          '$word->type',
          '$word->template',
          '$word->version',
          '$word->isDelete',
          '$word->createTime',
          '$word->updateTime'
          )
        ";
        return $this->insert($sql);
    }

    /**
     * 待审核的词条是否存在
     * @param $wordVerifyId
     * @return null
     */
    public function wordVerifyIdIsExist($wordVerifyId)
    {
        $sql = "SELECT count('id') FROM $this->TABLE_VERIFY WHERE id=? AND isDelete = 0 AND isVerify = 0";
        $params = [$wordVerifyId];
        $count = $this->uniqueResult($sql, $params);
        if ($count["count('id')"] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getWord(string $word)
    {
        $sql = "SELECT id,word,content,type,template,version,createTime,updateTime FROM $this->TABLE where word='$word' AND isDelete = '0' ";

        return $this->uniqueResult($sql);
    }

}