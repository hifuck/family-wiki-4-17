<?php
/**
 * 词条的控制器类
 * author: jiangpengfei
 * Date: 2018/4/2
 */

namespace App\Controller;


use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use App\ViewController;
use App\DB\WordDB;
use App\Utils\Util;
use App\Utils\Check;
use App\Model\Word;
use Conf\ErrorCode;
use App\Task\AsyncTask;

class WordCtl extends ViewController
{

    function index()
    {

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

        $word = new Word();
        $word->word = $params['word'] ?? null;
        $word->content = $params['content'] ?? null;
        $word->type = Check::checkInteger($params['type'] ?? null);
        $word->template = Check::checkInteger($params['template'] ?? null);
        $word->version = 1;

        // 检查参数是否齐全
        if (in_array(null, array($word->word, $word->content), true)) {
            Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');
            return;
        }


        $wordDB = Util::buildInstance('\App\DB\WordDB');
        $wordId = $wordDB->addWordVerify($word);

        if ($wordId > 0) {
            //投递异步任务
            $word->id = $wordId;
            $word->isDelete = 0;
            $word->createTime = Util::getStandardCurrentTime();
            $word->updateTime = $word->createTime;
            AsyncTask::addWordSearchTask($word);
        }

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

    function onRequest($actionName)
    {
        $this->response()->withHeader('Access-Control-Allow-Origin','*');

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