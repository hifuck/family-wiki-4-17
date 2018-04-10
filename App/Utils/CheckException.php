<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-10
 * Time: 上午9:52
 */

namespace App\Utils;


class CheckException extends \Exception
{
    public function errorMessage()
    {
        $code = $this->getCode();
        $data = $this->getMessage();
        $result['errorCode'] = "$code";
        if (is_array($data)) {
            $result['msg'] = '';
            $result['data'] = $data;
        } else {
            $result['msg'] = $data;
            $result['data'] = new \stdClass();
        }
        return json_encode($result);
    }
}