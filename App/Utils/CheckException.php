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
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message,$code,$previous);
        $this->code = (string) $code;
    }
//    public function errorMessage()
//    {
//        $code = $this->getCode();
//        $data = $this->getMessage();
//        $result['errorCode'] = "$code";
//        if (is_array($data)) {
//            $result['msg'] = '';
//            $result['data'] = $data;
//        } else {
//            $result['msg'] = $data;
//            $result['data'] = new \stdClass();
//        }
//        return json_encode($result);
//    }
}