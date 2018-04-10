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
use PhpParser\Error;

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
            $this->response()->write($e->errorMessage());
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

        try{
            $isVerify = Check::checkInteger($params['isVerify'] ?? null);
            $wordVerifyId = Check::checkInteger($params['wordVerifyId'] ?? null);
        }catch (CheckException $e){
            $this->response()->write($e->errorMessage());
            return;
        }

        $wordDb = new WordDB();

        if ($api !== null) {
            if ($wordVerifyId == null || $isVerify == null) {
                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');
                return;
            }

            if (!$wordDb->wordVerifyIdIsExist($wordVerifyId)) {
                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_WRONG, '待审核的词条不存在');
                return;
            }
            // 审核通过 1 、 不通过 -1
            $result = $wordDb->wordIsVerified($isVerify, $wordVerifyId);

            if ($result > 0 && $isVerify == 1) {
                //审核通过
                $word = $wordDb->getWordVerifyById($wordVerifyId);
                $wordId = $wordDb->addWord($word);

                if ($wordId > 0) {
                    //投递异步任务
                    $word->id = $wordId;
                    $word->isDelete = 0;
                    $word->createTime = Util::getStandardCurrentTime();
                    $word->updateTime = $word->createTime;
                    AsyncTask::addWordSearchTask($word);
                }

                $data['updateRow'] = $result;
                $data['wordId'] = $wordId;
                Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
            } else {
                $data['updateRow'] = $result;
                Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
            }
        } else {
            if ($wordVerifyId == null || $isVerify == null) {
                $this->assign('error', '缺少参数');
                return;
            }

            if (!$wordDb->wordVerifyIdIsExist($wordVerifyId)) {
                $this->assign('isExist', '待审核的词条不存在');
                return;
            }

            $result = $wordDb->wordIsVerified($isVerify, $wordVerifyId);

            if ($isVerify == 1 && $result > 0) {
                $word = $wordDb->getWordVerifyById($wordVerifyId);
                $wordId = $wordDb->addWord($word);

                if ($wordId > 0) {
                    //投递异步任务
                    $word->id = $wordId;
                    $word->isDelete = 0;
                    $word->createTime = Util::getStandardCurrentTime();
                    $word->updateTime = $word->createTime;
                    AsyncTask::addWordSearchTask($word);
                }

                $data['updateRow'] = $result;
                $data['wordId'] = $wordId;
                $this->assign('data', $data);
            } else {
                $this->assign('updateRow', $result);
            }
        }
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.


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