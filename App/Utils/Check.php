<?php

namespace App\Utils;

use App\Utils\Util;          //使用Util命名空间的Util类
use App\Utils\FileUtil;
use Conf\ErrorCode;
use Conf\Constant;
use Core\Http\Request;
use Core\Http\Response;

/**
 * Check类，用来检测用户输入的合法性
 * author         : jiangpengfei
 * date           : 2017-01-09
 * create date    : 2017-01-09
 * update date    : 2017-01-09
 * update author  : jiangpengfei
 * update version : 1.0
 * update reason  : 初始化
 */
class Check
{

    /**
     * 所有的用户传进来的字符串都需要处理
     * @param $param 传进来的参数
     * @param $minLen  字符串的最短长度
     * @param $maxLen  字符串的最大长度
     * @return string 经过html预处理和mysql预处理的字符串
     */
    public static function check($param, $minLen = 0, $maxLen = -1)
    {
        if ($param === null) {
            return null;
        }
        if ($maxLen != -1) {
            //检查字符串的长度
            $len = mb_strlen($param, 'utf-8');
            if ($len > $maxLen || $len < $minLen) {
                throw new CheckException('输入长度不正确',ErrorCode::ERROR_INPUT_FORMAT);
            }
        }
        $param = htmlspecialchars(addslashes($param));
        return $param;
    }

    /**
     * 检查性别
     * @param $gender 性别
     * @param $isAssert 如果不符合是否直接终止程序运行，默认是true
     * @return 性别或者false
     */
    public static function checkGender($gender, $isAssert = true)
    {
        if (in_array($gender, array(Constant::GENDER_FEMALE, Constant::GENDER_MALE, Constant::GENDER_UNKNOWN))) {
            return $gender;
        } else {
            if ($isAssert) {
                throw new CheckException('性别错误',ErrorCode::ERROR_INPUT_FORMAT);
            } else {
                return false;
            }
        }
    }

    /**
     * 检查日期格式,只支持xxxx-xx-xx
     * @param $str 传入的字符串
     * @param $isAssert 如果不符合是否直接终止程序运行，默认是true
     * @return 日期或者false
     */
    public static function checkDate($str, $isAssert = true)
    {
        $regex = "@^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$@AD";      //@是定界符，php的正则是支持在正则表达式后输入模式的,类似与2012-00-00这种输入是错误的
        $min = 10;
        $max = 10;
        $msg = "日期格式错误，请重新输入";
        return self::checkInput($str, $regex, $min, $max, $msg, $isAssert);
    }

    /**
     * 检查日期时间格式,只支持xxxx-xx-xx xx:xx:xx
     * @param $str 传入的字符串
     * @param $isAssert 如果不符合是否直接终止程序运行，默认是true
     * @return 日期或者false
     */
    public static function checkDateTime($str, $isAssert = true)
    {
        $regex = "@^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$@AD";
        $min = 19;
        $max = 19;
        $msg = "日期格式错误，请重新输入";
        return self::checkInput($str, $regex, $min, $max, $msg, $isAssert);
    }

    /**
     * 检查邮箱格式
     * @param $email 邮箱地址
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回或者返回false
     */
    public static function checkEmail($email, $isAssert = true)
    {
        $regex = "|^[a-zA-Z0-9_\-.]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z]+$|AD";
        $min = 5;
        $max = 50;
        $msg = "邮箱格式错误，请检查后重新填入";
        return self::checkInput(self::check($email), $regex, $min, $max, $msg, $isAssert);
    }

    /**
     * 检查用户名是否合法,用户名中不能是数字开头，只能包含字母数字下划线
     * @param $username 用户名
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回或者返回false
     */
    public static function checkUserName($username, $isAssert = true)
    {
        $regex = "/^[^0-9]((?!@)\S)*$/AD";
        $min = 1;
        $max = 45;
        $msg = "用户名不合法，请检查后重新填入";
        return self::checkInput(self::check($username), $regex, $min, $max, $msg, $isAssert);
    }

    /**
     * 检查电话号码,目前只考虑了国内的电话号码长度
     * @param $phoneNumber 电话号码
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回或者返回false
     */
    public static function checkPhoneNumber($phoneNumber, $isAssert = true)
    {
        $regex = "@^\d{8,15}$@AD";
        $min = 11;
        $max = 11;
        $msg = "手机号码格式错误，请检查后重新填入";
        return self::checkInput(self::check($phoneNumber), $regex, $min, $max, $msg, $isAssert);
    }

    /**
     * 检查输入是否是整型
     * @param $input 输入
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回,如果是浮点数则会被转换成整数返回或者返回false
     */
    public static function checkInteger($input, $isAssert = true)
    {

        if (!is_numeric($input) || (intval($input) != floatval($input))) {
            if ($isAssert) {
                throw new CheckException('不是整型输入', ErrorCode::ERROR_INPUT_FORMAT);
            } else {
                return false;
            }
        }

        return $input;

    }

    /**
     * 检查输入是否是数组
     * @param $input 输入
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回或者返回false
     */
    public static function checkArray($input, $isAssert = true)
    {
        if (!is_array($input)) {
            if ($isAssert) {
                throw new CheckException('不是数组输入', ErrorCode::ERROR_INPUT_FORMAT);
            } else {
                return false;
            }
        }

        return $input;
    }

    /**
     * 检查输入是否是boolean
     * @param $input 输入
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回或者返回false
     */
    public static function checkBool($input, $isAssert = true)
    {
        if (!is_bool($input)) {
            if ($isAssert) {
                throw new CheckException('不是bool输入', ErrorCode::ERROR_INPUT_FORMAT);
            } else {
                return false;
            }
        }

        return $input;
    }

    /**
     * 检查字符串代表的数组格式是否正确,比如   1,1,1,1,1,1,1,1
     * @param $input 字符串
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回或者返回false
     */
    public static function checkIdArray($input, $isAssert = true)
    {
        $regex = "@^(\d+,)*\d+$@";
        $min = 1;
        $max = -1;
        $msg = "消息数组格式不正确，请确认后重试";
        return self::checkInput($input, $regex, $min, $max, $msg, $isAssert);
    }

    /**
     * 检查url是否合法
     * @param $url url地址
     * @return boolean true代表合法,false代表不合法
     */
    public static function checkUrl($url, $isAssert = true)
    {
        $regex = "@^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$@";
        $min = 3;
        $max = 255;
        $msg = "url地址不合法";
        return self::checkInput($url, $regex, $min, $max, $msg, $isAssert);
    }

    /**
     * 检查url是否属于合法域名(这里会预填合法的域名)
     * @param $url url地址
     * @return boolean true代表合法，false代表不合法
     */
    public static function checkUrlWithLimit($url)
    {
        $urls = Constant::VALID_URL;
        foreach ($urls as $validUrl) {
            if ($validUrl === $url) {
                return $url;
            }
        }

        return false;
    }

    /**
     * 检查图片类型是否支持
     * @param $file 文件
     * @param $isAssert 是否断言,true的话条件出错会直接终止程序运行，默认是true
     * @return bool true是，false不是
     */
    public static function checkImageType($file, $isAssert = true)
    {

        if ($file == '' || $file == null) {
            return false;
        }

        $fileType = FileUtil::getFileType($file);
        if ($fileType === FileUtil::FILE_TYPE_PNG
            || $fileType === FileUtil::FILE_TYPE_JPG
            || $fileType === FileUtil::FILE_TYPE_GIF) {
            return $file;
        } else {
            if ($isAssert) {
                throw new CheckException('图片格式错误', ErrorCode::ERROR_FILE_FORMAT);
            } else {
                return false;
            }
        }
    }

    /**
     * 通用的输入检测函数
     * @param $input 输入数据
     * @param $regex 检测正则
     * @param $min 最小输入长度
     * @param $max 最大输入长度,-1代表不限制
     * @param $msg 出错信息
     * @param $isAssert 是否进行断言，true的话条件出错会直接终止程序运行，默认是true
     * @return mix 原样返回输入内容
     */
    public static function checkInput($input, $regex, $min, $max, $msg, $isAssert = true)
    {
        $len = mb_strlen($input);

        //先检查长度
        if (($len > $max && $max != -1) || $len < $min) {
            if ($isAssert) {
                Util::printResult(Response::getInstance(), ErrorCode::ERROR_INPUT_FORMAT, $msg);
                Response::getInstance()->end();
            } else {
                return false;
            }
        }

        if (preg_match($regex, $input) == 0) {
            if ($isAssert) {
                throw new CheckException($msg, ErrorCode::ERROR_INPUT_FORMAT);
            } else {
                return false;
            }
        }
        return $input;
    }
}

;