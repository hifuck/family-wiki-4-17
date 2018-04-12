<?php
/**
 * 词条的控制器类
 * author: jiangpengfei
 * Date: 2018/4/2
 */

namespace App\Controller;


use App\Utils\CheckException;
use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use App\ViewController;
use App\DB\WordDB;
use App\Utils\Util;
use App\Utils\Check;
use App\Model\Word;
use Conf\ErrorCode;
use Conf\Constant;
use App\Task\AsyncTask;
use App\Model\ESQuery;

class WordCtl extends ViewController
{

    function index()
    {

    }

    /**
     * 词条详情页
     */
    function word() {
        $params = $this->request()->getRequestParam();

        $word = $params['word'] ?? null;

        if ($word == null) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING,'参数缺失','Word/word.html',$this->api);
            return;
        }

        $wordDB = Util::buildInstance('\App\DB\WordDB');
        $word = $wordDB->getWord($word);

        if ($word == null) {
            Util::printError($this,ErrorCode::ERROR_SQL_QUERY,'该词条不存在','Word/word.html',$this->api);
            return;
        }

         if ($this->api == null) {
            $this->assignMap($word);
            $this->fetch('Word/word.html');
            return;
         } else {
            $data['word'] = $word;
            Util::printResult($this->response(),ErrorCode::ERROR_SUCCESS,$data);
            return;
         }
    }

    function viewAdd() {
        // 渲染页面直接输出
        $this->fetch('Word/add.html');
    }

    //TODO 编辑词条的权限判断
    function viewEdit() {
        $params = $this->request()->getRequestParam();
        $wordId = Check::checkInteger($params['wordId'] ?? '');

        $wordDB = Util::buildInstance('App\DB\WordDB');
        $word = $wordDB->getWordVerifyById($wordId);

        if ($word == null) {
            Util::printError($this,ErrorCode::ERROR_SQL_QUERY,'该词条不存在','Word/edit.html',null);
            return;
        }

        $this->assign('word',$word);
        $this->fetch('Word/edit.html');
    }

    /**
     * 添加词条
     */
    function add() {

        $params = $this->request()->getRequestParam();

        try{
            $word = new Word();
            $word->word = trim($params['word'] ?? '');
            $word->content = $params['content'] ?? '';
            $word->type = Check::checkInteger($params['type'] ?? '');
            $word->template = Check::checkInteger($params['template'] ?? '');
            $word->author = $this->user['userId'];
            $word->authorName = $this->user['username'];
            $word->version = 1;
        }catch (CheckException $e){
            Util::printError($this,$e->getCode(),$e->getMessage(),'Word/add.html',$this->api);
            return;
        }
        // 检查参数是否齐全
        if (in_array('', array($word->word, $word->content), true)) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING,'缺少参数','Word/add.html',$this->api);
            return;
        }

        $wordDB = Util::buildInstance('\App\DB\WordDB');
        $wordId = $wordDB->addWordVerify($word);

        $data['wordId'] = $wordId;

        if ($this->api !== null) {
            // 返回接口数据
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assignMap($data);
            $this->assign('word',$word);
            $this->fetch('Word/add.html');
        }
    }

    /**
     * 编辑词条
     * 
     */
    function edit() {
        $params = $this->request()->getRequestParam();

        try{
            $word = new Word();
            $word->id = Check::checkInteger($params['wordId'] ?? '');
            $word->word = trim($params['word'] ?? '');
            $word->content = $params['content'] ?? '';
            $word->type = Check::checkInteger($params['type'] ?? '');
            $word->template = Check::checkInteger($params['template'] ?? '');
            $word->author = $this->user['userId'];
            $word->authorName = $this->user['username'];
            $word->version = 1;
        }catch (CheckException $e){
            Util::printError($this,$e->getCode(),$e->getMessage(),'Word/edit.html',$this->api);
            return;
        }
        // 检查参数是否齐全
        if (in_array('', array($word->word, $word->content), true)) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING,'缺少参数','Word/edit.html',$this->api);
            return;
        }

        $wordDB = Util::buildInstance('\App\DB\WordDB');

        // 检查编辑权限
        if (!$wordDB->hasUpdatePermission($this->user['userId'],$word->id)) {
            Util::printError($this,ErrorCode::ERROR_PERMISSION,'无编辑权限','Word/edit.html',$this->api);
            return;
        }

        $update = $wordDB->editWordVerify($word);

        $data['update'] = $update;

        if ($this->api !== null) {
            // 返回接口数据
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assignMap($data);
            $this->assign('word',$word);
            $this->fetch('Word/edit.html');
        }
    }

    /**
     * 删除词条
     * 
     */
    function delete() {
        $params = $this->request()->getRequestParam();

        try{
            $wordId = Check::checkInteger($params['wordId'] ?? '');
        }catch (CheckException $e){
            Util::printError($this,$e->getCode(),$e->getMessage(),'Word/edit.html',$this->api);
            return;
        }

        $wordDB = Util::buildInstance('\App\DB\WordDB');

        // 检查删除权限
        if (!$wordDB->hasUpdatePermission($this->user['userId'],$wordId)) {
            Util::printError($this,ErrorCode::ERROR_PERMISSION,'无删除权限','Word/delete.html',$this->api);
            return;
        }

        $update = $wordDB->deleteWordVerify($wordId);

        $data['delete'] = $update;

        if ($this->api !== null) {
            // 返回接口数据
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assignMap($data);
            $this->fetch('Word/delete.html');
        }
    }

    /**
     * 搜索词条
     */
    function search() {
        $params = $this->request()->getRequestParam();
        $word = $params['word'] ?? null;

        if ($word == null) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING,'缺少参数','Word/search.html',$this->api);
            return;
        }

        $esQuery = new ESQuery();
        $esQuery->setPaging(1,10);
        $esQuery->setMultiMatch(array("word","content"),$word);
        $esQuery->setSourceExclude(array('content'));

        $wordES = Util::buildInstance('\App\ES\WordES');
        $result = $wordES->searchWord($esQuery->toString());

        if ($result == null) {
            //搜索服务器出现问题
            Util::printError($this,ErrorCode::ERROR_SQL_QUERY,'搜索服务器出现故障','Word/search.html',$this->api);
            return;
        }

        $hits = $result->hits;          //获取命中的条目

        if ($this->api != null) {
            $data['hits'] = $hits;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
            return;
        } else {
            $this->assign('hits',$hits);
            $this->fetch("Word/search.html");
            return;
        }
    }

    function onRequest($actionName)
    {
        parent::onRequest($actionName);
        $checkAction = array('viewAdd','add','edit','delete');

        if (in_array($actionName,$checkAction)) {
            $this->checkLogin();
        }
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {

    }

}