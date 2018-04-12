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
    public $TABLE_HISTORY = 'wiki_history';

    /**
     * 增加需审核的词条
     */
    public function addWordVerify(Word $word)
    {
        $sql = "INSERT INTO $this->TABLE_VERIFY 
        (word,content,type,template,version,author,authorName,isDelete,createTime,updateTime)
        VALUES(?,?,?,?,?,?,?,0,now(),now())";

        $params = array($word->word, $word->content, $word->type, $word->template, $word->version, $word->author, $word->authorName);

        return $this->insert($sql, $params);
    }

    /**
     * 检查用户是否有修改权限（编辑和删除）
     * @param $userId 用户id
     * @param $wordId 词条id
     */
    public function hasUpdatePermission($userId, $wordId) {
        $word = $this->getWordVerifyById($wordId);
        if ($word != null && $word->author == $userId) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 编辑需审核的词条
     */
    public function editWordVerify(Word $word)
    {
        $sql = "UPDATE $this->TABLE_VERIFY SET 
        word = ?,content=?,type=?,template=?,
        version=version+1,author=?,authorName=?,
        updateTime=now() WHERE id = ? and isDelete = 0";

        $params = array($word->word, $word->content, $word->type, $word->template, $word->author, $word->authorName, $word->id);

        return $this->update($sql, $params);
    }

    /**
     * 删除需审核的词条
     * @param $wordId
     * @return int 删除的记录数
     */
    public function deleteWordVerify(int $wordId)
    {
        $sql = "UPDATE $this->TABLE_VERIFY SET isDelete = 1 WHERE id = '$wordId' ";
        return $this->update($sql);
    }

    /**
     * 根据Id获取词条内容
     * @param int $wordId
     * @return Word
     */
    public function getWordVerifyById(int $wordId)
    {
        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime,author,authorName 
        FROM $this->TABLE_VERIFY WHERE id = '$wordId' ";

        $result = $this->uniqueResult($sql);

        if ($result != null) {
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
            $word->author = $result['author'];
            $word->authorName = $result['authorName'];
            return $word;
        } else {
            return null;
        }

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

        $sql = "SELECT id,word,content,type,template,version,isDelete,createTime,updateTime,author,authorName 
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
        $sql = "INSERT INTO $this->TABLE(word,content,type,template,version,isDelete,createTime,updateTime,author,authorName) 
          VALUES (
          '$word->word',
          '$word->content',
          '$word->type',
          '$word->template',
          '$word->version',
          '$word->isDelete',
          '$word->createTime',
          '$word->updateTime',
          '$word->author',
          '$word->authorName'
          )
        ";
        return $this->insert($sql);
    }

    /**
     * 待审核的词条是否存在
     * @param $wordVerifyId
     * @return null
     */
    public function wordVerifyIdIsExist(int $wordVerifyId)
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

    /**
     * 根据词条名字获取词条内容
     * @param string $word
     * @return null
     */
    public function getWord(string $word)
    {
        $sql = "SELECT id,word,content,type,template,version,createTime,updateTime,author,authorName FROM $this->TABLE where word='$word' AND isDelete = '0' ";

        return $this->uniqueResult($sql);
    }

    /**
     * 更新词条  版本+1
     * @param $word
     * @param $id
     * @return bool
     */
    public function updateWord($word, $id)
    {
        $sql = "UPDATE $this->TABLE SET 
          word='$word->word',
          content='$word->content',
          type='$word->type',
          template='$word->template',
          version='$word->version',
          isDelete='$word->isDelete',
          createTime='$word->createTime',
          updateTime= '$word->updateTime',
          author = '$word->author',
          authorName = '$word->authorName'
        WHERE id=? AND isDelete = 0";
        $params = [$id];
        return $this->update($sql, $params);
    }

    /**
     * 已经存在的词条 添加词条记录
     * @param $word
     * @return bool
     */
    public function addWordToHistory($word)
    {
        $sql = "INSERT INTO $this->TABLE_HISTORY(word,content,type,template,version,author,createTime,updateTime) 
          VALUES (
          '$word->word',
          '$word->content',
          '$word->type',
          '$word->template',
          '$word->version',
          '$word->author',
          '$word->createTime',
          '$word->updateTime'
          )";
        return $this->insert($sql);
    }

    /**
     * 分页获取用户词条
     */
    public function getUserWordsPaging($pageIndex, $pageSize, $userId) {
        $offset = ($pageIndex - 1) * $pageSize;

        $sql = "SELECT id,word,content,type,template,version,author,authorName,isVerify,createTime,updateTime FROM
                 $this->TABLE_VERIFY WHERE author = '$userId' AND isDelete = 0 ORDER BY id DESC limit $offset,$pageSize ";
        
        return $this->query($sql);
    }

    /**
     * 获取用户词条总数
     */
    public function getUserWordsCount($userId) {
        $sql = "SELECT count(id) FROM $this->TABLE_VERIFY WHERE author = '$userId' AND isDelete = 0 ";
        $result = $this->uniqueResult($sql);

        return $result['count(id)'];
    }
}