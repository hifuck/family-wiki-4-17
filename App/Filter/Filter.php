<?php
/**
 * 用户身份验证过滤器
 */
namespace App\Filter;

use App\DB\UserDB;
use Core\Component\Di;

class Filter{

    protected static $filter = null;

    public static function getInstance(){
        if(self::$filter == null){
            self::$filter = new Filter();
        }

        return self::$filter;
    }

    private function __construct(){

    }


    /**
     * 验证用户身份
     * @param $idToken id_token
     * @param $deviceCode 设备码
     * @return bool true身份正确，false身份错误
     */
    public function validateUser($idToken,$deviceCode){
        $arrRes = explode("|",$idToken);

        if(count($arrRes) != 3){
            return false;
        }

        
        $userId =  $arrRes[0];
        $token = $arrRes[1];
        $device = $arrRes[2];		

        $userDB = Di::getInstance()->get('UserDB');
        if($userDB == null){
            $userDB = new UserDB();
            Di::getInstance()->set('UserDB',$userDB);
        }
        if($userDB->validateUser($userId,$token,$device,$deviceCode)){
            return $userId;
        }else{
            return false;
        }
    }
}