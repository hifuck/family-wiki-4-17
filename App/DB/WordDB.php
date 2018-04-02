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

    }

    public function editWordVerify(Word $word) {

    }

    public function deleteWordVerify(int $wordId) {

    }

    public function getWordVerify(int $wordId) {

    }

    public function getWord(string $word) {
        
    }

}