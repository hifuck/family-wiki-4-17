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
use Conf\ErrorCode;

class Word extends Base
{

    function index()
    {
        // TODO: Implement index() method.
        $this->getWordVerifyReviewPaging();
        $this->fetch('Admin/Word/index.html');
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

        $SpageSize = $params['SpageSize'] ?? '';

        if ($SpageSize !== ''){
            $pageSize = $params['SpageSize'];
        }

        $result = $wordDb->getWordVerifyReviewPaging($pageIndex, $pageSize);
        $count = $wordDb->countWordVerifyReview();
        $total = ceil($count/$pageSize);
        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;
        $data['content'] = $result;
        $data['total'] = $total;

        if ($api !== null) {

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('next',ceil($total/$pageSize)>$pageIndex ? '' : 'disabled');
            $this->assign('pre',$pageIndex>1 ? '' : 'disabled');
            $this->assign('data', $data);
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

        // 审核通过 1
        if ($result > 0 && $isVerify == 1) {
            //审核通过
            $wordObj = $wordDb->getWordVerifyById($wordVerifyId);

            $oldWord = $wordDb->getWord($wordObj->word);

            //词条已经存在
            if ($oldWord !== null) {
                //创建对象 oldWordModel
                $oldWordModel = new \App\Model\Word();
                $oldWordModel->word = $oldWord['word'];
                $oldWordModel->content = $oldWord['content'];
                $oldWordModel->type = $oldWord['type'];
                $oldWordModel->template = $oldWord['template'];
                $oldWordModel->version = $oldWord['version'];
                $oldWordModel->author = $oldWord['author'];
                $oldWordModel->createTime = $oldWord['createTime'];
                $oldWordModel->updateTime = $oldWord['updateTime'];
                //存在的词条添加到历史记录中
                $wordHistoryId = $wordDb->addWordToHistory($oldWordModel);
                if ($wordHistoryId > 0) {
                    $wordObj->version = $oldWord['version'] + 1;
                    $updateRow = $wordDb->updateWord($wordObj, $oldWord['id']);
                    if ($updateRow > 0) {
                        $wordId = $oldWord['id'];
                    }
                }

            } else {
                $wordId = $wordDb->addWord($wordObj);
            }

            // 截取内容
            $content = $wordObj->content;
            $len = mb_strlen($content);
            if ($len > 250) {
                $subContent = mb_substr($content, 0, 250);
            } else {
                $subContent = $content;
            }

            if ($wordId > 0) {
                //word 模型
                $word = new \App\Model\Word();
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
            // 不通过
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
        $arr = ['getWordVerifyReviewPaging','wordIsVerified','index'];
        if (in_array($actionName,$arr)){
            $this->checkLoginStatus();
        }
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