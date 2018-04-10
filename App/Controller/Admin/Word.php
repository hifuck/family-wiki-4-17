<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-9
 * Time: 下午4:16
 */

namespace App\Controller\Admin;


use App\DB\WordDB;
use App\Task\AsyncTask;
use App\Utils\Check;
use App\Utils\CheckException;
use App\Utils\Util;
use App\ViewController;
use Conf\ErrorCode;

class Word extends ViewController
{

    function index()
    {
        // TODO: Implement index() method.
    }

    /**
     * 分页获取待审核的词条
     */
    function getWordVerifyReviewPaging()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $pageIndex = Check::checkInteger($params['pageIndex'] ?? 1);
            $pageSize = Check::checkInteger($params['pageSize'] ?? 10);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), '', $api);
            return;
        }

        $wordDb = new WordDB();
        $result = $wordDb->getWordVerifyReviewPaging($pageIndex, $pageSize);

        if ($api !== null) {
            $data['pageIndex'] = $pageIndex;
            $data['pageSize'] = $pageSize;
            $data['content'] = $result;
            $data['total'] = $wordDb->countWordVerifyReview();
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('data', $result);
        }
    }

    /**
     * 词条审核通过 1 、不通过 -1
     * 审核通过 同步到word表中
     */
    function wordIsVerified()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $isVerify = Check::checkInteger($params['isVerify'] ?? null);
            $wordVerifyId = Check::checkInteger($params['wordVerifyId'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/Word/index.html', $api);
            return;
        }

        if ($wordVerifyId == null || $isVerify == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', 'Admin/Word/index.html', $api);
            return;
        }

        $wordDb = new WordDB();

        if (!$wordDb->wordVerifyIdIsExist($wordVerifyId)) {
            Util::printError($this, ErrorCode::ERROR_PARAM_WRONG, '待审核的词条不存在', 'Admin/Word/index.html', $api);
            return;
        }

        $result = $wordDb->wordIsVerified($isVerify, $wordVerifyId);

        // 审核通过 1 、 不通过 -1
        if ($result > 0 && $isVerify == 1) {
            //审核通过
            $word = $wordDb->getWordVerifyById($wordVerifyId);
            $wordId = $wordDb->addWord($word);

            // 截取内容
            $content = $word->content;
            $len = mb_strlen($content);
            if ($len > 250) {
                $subContent = mb_substr($content, 0, 250);
            } else {
                $subContent = $content;
            }

            if ($wordId > 0) {
                //投递异步任务
                $word->id = $wordId;
                $word->subContent = $subContent;
                $word->isDelete = 0;
                $word->createTime = Util::getStandardCurrentTime();
                $word->updateTime = $word->createTime;
                AsyncTask::addWordSearchTask($word);
            }

            $data['updateRow'] = $result;
            $data['wordId'] = $wordId;
        } else {
            $data['updateRow'] = $result;
        }

        if ($api !== null) {
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('data', $data);
        }
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
        parent::onRequest($actionName);
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFound() method.
    }

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }
}