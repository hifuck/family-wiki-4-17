<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-3
 * Time: 下午5:20
 */

namespace App\Controller\Admin;


use App\DB\WordTypeDB;
use App\Utils\Check;
use App\Utils\CheckException;
use App\Utils\Util;
use App\ViewController;
use Conf\ErrorCode;

class WordType extends ViewController
{

    function index()
    {
        // TODO: Implement index() method.
        $this->fetch('Admin/WordType/index.html');
    }

    function viewAdd()
    {
        $this->fetch('Admin/WordType/add.html');
    }

    /**
     * 添加词条分类
     */
    function add()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $type = Check::check($params['type'] ?? null);
            $parentId = Check::checkInteger($params['parentId'] ?? 0); // 0 代表根分类
            $depth = Check::checkInteger($params['depth'] ?? 1); //  1 代表一级分类 2 代表二级分类
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/WordType/add.html', $api);
            return;
        }

        if ($type == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', 'Admin/WordType/add.html', $api);
            return;
        }

        $wordTypeDb = new WordTypeDB();

        if ($wordTypeDb->wordTypeIsExist($type, $parentId)) {
            Util::printError($this, ErrorCode::ERROR_PARAM_WRONG, '分类名字重复', 'Admin/WordType/add.html', $api);
            return;
        }

        $result = $wordTypeDb->addWordType($type, $parentId, $depth);

        if ($api !== null) {
            $data['typeId'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('typeId', $result);
            $this->fetch('Admin/WordType/add.html');
        }
    }

    /**
     * 编辑词条分类
     */
    function edit()
    {

        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $type = Check::check($params['type'] ?? null);
            $parentId = Check::checkInteger($params['parentId'] ?? null);
            $depth = Check::checkInteger($params['depth'] ?? null);
            $typeId = Check::checkInteger($params['typeId'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/WordType/edit.html', $api);
            return;
        }

        if ($typeId == null || $type == null || $parentId == null || $depth == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', 'Admin/WordType/edit.html', $api);
            return;
        }

        $wordTypeDb = new WordTypeDB();

        $result = $wordTypeDb->editWordType($type, $parentId, $depth, $typeId);

        if ($api !== null) {
            $data['updateRow'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('updateRow', $result);
            $this->fetch('Admin/WordType/edit.html');
        }
    }

    /**
     * 删除词条分类
     */
    function del()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $typeId = Check::checkInteger($params['typeId'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), '', $api);
            return;
        }

        if ($typeId == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', '', $api);
            return;
        }

        $wordTypeDb = new WordTypeDB();

        $result = $wordTypeDb->delWordType($typeId);

        if ($api !== null) {
            $data['delRow'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('delRowArr', $result);
            $this->fetch('Admin/WordType/del.html');
        }
    }

    /**
     * 分页获取词条分类
     */
    function getWordTypePaging()
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

        $wordTypeDb = new WordTypeDB();

        $result = $wordTypeDb->getWordTypePaging($pageIndex, $pageSize);

        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;
        $data['content'] = $result;
        $data['total'] = $wordTypeDb->countWordType();

        if ($api !== null) {
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('data', $data);
        }
    }

    /**
     * 分页获取一级词条分类
     */
    function getTopWordTypePaging()
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

        $wordTypeDb = new WordTypeDB();

        $result = $wordTypeDb->getTopWordTypePaging($pageIndex, $pageSize);

        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;
        $data['content'] = $result;
        $data['total'] = $wordTypeDb->countTopWordType();

        if ($api !== null) {
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('data', $data);
        }
    }

    /**
     * 根据分类Id获取子分类列表
     */
    function getChildWordTypeListById()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $typeId = Check::checkInteger($params['typeId'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), '', $api);
            return;
        }

        if ($typeId == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', '', $api);
            return;
        }

        $wordTypeDb = new WordTypeDB();

        $result = $wordTypeDb->getChildWordTypeListById($typeId);

        if ($api !== null) {
            $data['childWordTypeList'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $result = $wordTypeDb->getChildWordTypeListById($typeId);
            $this->assign('childWordTypeList', $result);
        }
    }

    /**
     * 根据id获取该分类的详细内容
     */
    function getWordTypeById()
    {

        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $typeId = Check::checkInteger($params['typeId'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), '', $api);
            return;
        }

        if ($typeId == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', '', $api);
            return;
        }

        $wordTypeDb = new WordTypeDB();

        $result = $wordTypeDb->getWordTypeById($typeId);

        if ($api !== null) {
            $data['wordTypeContent'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $result = $wordTypeDb->getWordTypeById($typeId);
            $this->assign('wordTypeContent', $result);
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