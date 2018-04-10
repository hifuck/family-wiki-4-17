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

        $api = $params['api'] ?? null;
        $word = $params['word'] ?? null;

        if ($word == null) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING,'参数缺失','Word/word.html',$api);
            return;
        }

        $wordDB = Util::buildInstance('\App\DB\WordDB');
        $word = $wordDB->getWord($word);

        if ($word == null) {
            Util::printError($this,ErrorCode::ERROR_SQL_QUERY,'该词条不存在','Word/word.html',$api);
            return;
        }

         if ($api == null) {
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
        $this->assign('user',"xinhuo");
        $this->fetch('Word/add.html');
    }

    /**
     * 添加词条
     */
    function add() {
        $params = $this->request()->getRequestParam();

        $api = $params['api'] ?? null;

        try{
            $word = new Word();
            $word->word = trim($params['word'] ?? null);
            $word->content = $params['content'] ?? null;
            $word->type = Check::checkInteger($params['type'] ?? null);
            $word->template = Check::checkInteger($params['template'] ?? null);
            $word->version = 1;
        }catch (CheckException $e){
            Util::printError($this,$e->getCode(),$e->getMessage(),'Word/add.html',$api);
            return;
        }
        // 检查参数是否齐全
        if (in_array(null, array($word->word, $word->content), true)) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING,'缺少参数','Word/add.html',$api);
            return;
        }

        $wordDB = Util::buildInstance('\App\DB\WordDB');
        $wordId = $wordDB->addWordVerify($word);

        $data['wordId'] = $wordId;

        if ($api !== null) {
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
     */
    function edit() {

    }

    /**
     * 删除词条
     */
    function delete() {

    }

    /**
     * 搜索词条
     */
    function search() {
        $params = $this->request()->getRequestParam();

        $api = $params['api'] ?? null;
        $word = $params['word'] ?? null;

        if ($word == null) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING,'缺少参数','Word/search.html',$api);
            return;
        }

        $esQuery = new ESQuery();
        $esQuery->setPaging(1,10);
        $esQuery->setMultiMatch(array("word","content"),$word);
        $esQuery->setSourceExclude(array('content'));

        $wordES = Util::buildInstance('\App\ES\WordES');
        $result = $wordES->searchWord($esQuery->toString());

        $hits = $result->hits;          //获取命中的条目

        if ($api != null) {
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
        // $params = $this->request()->getRequestParam();
        // $idToken = $params['id_token'] ?? null;
        // $deviceCode = $params['deviceCode'] ?? '';
        // if($idToken == null){
        //     Util::printResult($this->response(),"-20000001","未登录");
        //     $this->response()->end();
        //     return;
        // }else{
        //     $filter = Filter::getInstance();
        //     $userId = $filter->validateUser($idToken, $deviceCode);
        //     if(!$userId){
        //         Util::printResult($this->response(),"-20000001","未登录");
        //         $this->response()->end();
        //         return;
        //     }else{
        //         $this->userId = $userId;
        //     }
        // }
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {

    }

}