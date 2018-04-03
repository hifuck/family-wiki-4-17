<?php
namespace App\Utils;

use Core\Component\Di;

class Util{

    /**
     * 获取用户id
     */
    public static function getUserId($idToken){
        $arrRes = explode("|",$idToken);

        if(count($arrRes) != 3){
            return false;
        }

        
        $userId =  $arrRes[0];
        return $userId;
    }

    /**
    * 获取当前时间
    */
    public static function getCurrentTime(){
        return date("Y-m-d H:i:s");
    }

    /**
    * 获取当前日期
    */
    public static function getCurrentDate(){
        return date("Y-m-d");
    }


    /**
    * 输出结果的函数
    * @param success 是否成功 true是成功，false是失败
    */
    public static function printResult($writer,$errorCode,$data){
        $result['errorCode']=$errorCode;
        if(is_array($data)){
            $result['msg'] = "";
            $result['data'] = $data;
        }else{
            $result['msg'] = $data;
            $result['data'] = new \stdClass();
        }
        $writer->write(json_encode($result));
    }

    /**
     * 将数组转换为逗号分割的字符串
     */
    public static function getArrayString($array){
        $result = "";
        $first = true;
        foreach($array as $item){
            if($first){
                $result .= $item;
                $first = false;
            }else{
                $result .= ",".$item;
            }
        }

        return $result;
    }

    /**
     * 随机数字串
     */
    public static function randomNumString(int $len){
        $str = '';
        for($i = 0; $i < $len; $i++){
            $num = rand(0,9);
            $str .= $num;
        }

        return $str;
    }

    public static function buildInstance($className){
        $instance = Di::getInstance()->get($className);
        if($instance === null){
            $instance = new $className();
            Di::getInstance()->set($className,$instance);
        }

        return $instance;
    }
}