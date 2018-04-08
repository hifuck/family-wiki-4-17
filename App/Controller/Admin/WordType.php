<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-3
 * Time: 下午5:20
 */

namespace App\Controller\Admin;


use App\DB\WordTypeDB;
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

        $type = $params['type'] ?? null;

        $parentId = $params['parentId'] ?? 0; // 0 代表根分类

        $depth = $params['depth'] ?? 1; //  1 代表一级分类 2 代表二级分类

        $api = $params['api'] ?? null;

        $wordTypeDb = new WordTypeDB();

        if ($api !== null) {

            if ($type == null) {

                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');
                return;

            }

            if ($wordTypeDb->wordTypeIsExist($type, $parentId)) {

                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_WRONG, '分类名字重复');
                return;
            }

            $result = $wordTypeDb->addWordType($type, $parentId, $depth);

            $data['typeId'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);


        } else {

            if ($type == null) {

                $this->assign('error', '缺少参数');

                $this->fetch('Admin/WordType/add.html');
                return;

            }

            if ($wordTypeDb->wordTypeIsExist($type, $parentId)) {

                $this->assign('exist', '同级词条分类名字已经存在');

                $this->fetch('Admin/WordType/add.html');
                return;
            }

            $result = $wordTypeDb->addWordType($type, $parentId, $depth);

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

        $type = $params['type'] ?? null;

        $parentId = $params['parentId'] ?? null;

        $depth = $params['depth'] ?? null;

        $typeId = $params['typeId'] ?? null;

        $api = $params['api'] ?? null;

        $wordTypeDb = new WordTypeDB();

        if ($api !== null) {

            if ($typeId == null || $type == null || $parentId == null || $depth == null) {

                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');
                return;

            }

            $result = $wordTypeDb->editWordType($type, $parentId, $depth, $typeId);

            $data['updateRow'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);

        } else {

            if ($typeId == null || $type == null || $parentId == null || $depth == null) {

                $this->assign('error', '缺少参数');

                $this->fetch('Admin/WordType/edit.html');
                return;

            }

            $result = $wordTypeDb->editWordType($type, $parentId, $depth, $typeId);

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

        $typeId = $params['typeId'] ?? null;

        $api = $params['api'] ?? null;

        $wordTypeDb = new WordTypeDB();

        if ($api !== null) {

            if ($typeId == null) {

                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');
                return;

            }

            $result = $wordTypeDb->delWordType($typeId);

            $data['delRow'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);

        } else {

            if ($typeId == null) {

                $this->assign('error', '缺少参数');

                $this->fetch('Admin/WordType/del.html');
                return;

            }

            $result = $wordTypeDb->delWordType($typeId);

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

        $pageIndex = $params['pageIndex'] ?? 1;

        $pageSize = $params['pageSize'] ?? 10;

        $api = $params['api'] ?? null;

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

        $pageIndex = $params['pageIndex'] ?? 1;

        $pageSize = $params['pageSize'] ?? 10;

        $api = $params['api'] ?? null;

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

        $typeId = $params['typeId'] ?? null;

        $api = $params['api'] ?? null ;

        $wordTypeDb = new WordTypeDB();

        $result = $wordTypeDb->getChildWordTypeListById($typeId);

        if ($api !== null ){

            if ($typeId == null){

                Util::printResult($this->response(),ErrorCode::ERROR_PARAM_MISSING,'缺少参数');
                return;

            }

            $data['childWordTypeList'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);

        }else{

            $this->assign('childWordTypeList',$result);

        }
    }


    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
        $this->response()->withHeader('Access-Control-Allow-Origin', '*');
        $this->wordTypeDB = Util::buildInstance('\App\DB\WordTypeDB');
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