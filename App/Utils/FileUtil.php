<?php
/**
 * 一些文件操作方法的封装
 * @author jiangpengfei
 * @date 2017-11-07
 */

namespace App\Utils;

use PHPUnit\DbUnit\DataSet\Specification\IFactory;

class FileUtil
{

    const FILE_TYPE_EXE = 1;
    const FILE_TYPE_MIDI = 2;
    const FILE_TYPE_RAR = 3;
    const FILE_TYPE_ZIP = 4;
    const FILE_TYPE_JPG = 5;
    const FILE_TYPE_GIF = 6;
    const FILE_TYPE_BMP = 7;
    const FILE_TYPE_PNG = 8;
    const FILE_TYPE_DOC = 9;
    const FILE_TYPE_PDF = 10;
    const FILE_TYPE_MP3 = 11;
    const FILE_TYPE_VIDEO = 12;
    const FILE_TYPE_AVI = 13;
    const FILE_TYPE_MKV = 14;
    const FILE_TYPE_FLV = 15;
    const FILE_TYPE_SWF = 16;
    const FILE_TYPE_WMA = 17;
    const FILE_TYPE_RMVB = 18;
    const FILE_TYPE_APE = 19;
    const FILE_TYPE_FLAC = 20;
    const FILE_TYPE_AAC = 21;
    const FILE_TYPE_AC3 = 22;
    const FILE_TYPE_MMF = 23;
    const FILE_TYPE_AMR = 24;
    const FILE_TYPE_OGG = 25;
    const FILE_TYPE_TXT = 26;
    const FILE_TYPE_UNKNOWN = -1;

    /**
     * 获取文件类型
     * @param array $file
     * @return int 文件类型
     */
    public static function getFileType(array $file): int
    {
        $fileArr = fopen($file['tmp_name'], "rb");
        $bin = fread($fileArr, 2); //只读2字节
        fclose($fileArr);
        $strInfo = @unpack("C2chars", $bin);
        $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
        $fileType = '';
        switch ($typeCode) {
            case 7790:
                $fileType = self::FILE_TYPE_EXE;
                break;
            case 7784:
                $fileType = self::FILE_TYPE_MIDI;
                break;
            case 8297:
                $fileType = self::FILE_TYPE_RAR;
                break;
            case 8075:
                $fileType = self::FILE_TYPE_ZIP;
                break;
            case 255216:
                $fileType = self::FILE_TYPE_JPG;
                break;
            case 7173:
                $fileType = self::FILE_TYPE_GIF;
                break;
            case 6677:
                $fileType = self::FILE_TYPE_BMP;
                break;
            case 13780:
                $fileType = self::FILE_TYPE_PNG;
                break;
            case 208207:
                $fileType = self::FILE_TYPE_DOC;
                break;
            case 3780:
                $fileType = self::FILE_TYPE_PDF;
                break;
            case 7368:
                $fileType = self::FILE_TYPE_MP3;
                break;
            case 4838:
                $fileType = self::FILE_TYPE_WMA;
                break;
            case 7765:
                $fileType = self::FILE_TYPE_APE;
                break;
            case 10276:
                $fileType = self::FILE_TYPE_FLAC;
                break;
            case 255241:
                $fileType = self::FILE_TYPE_AAC;
                break;
            case 11119:
                $fileType = self::FILE_TYPE_AC3;
                break;
            case 7777:
                $fileType = self::FILE_TYPE_MMF;
                break;
            case 3533:
                $fileType = self::FILE_TYPE_AMR;
                break;
            case 79103:
                $fileType = self::FILE_TYPE_OGG;
                break;
            case 0:
                $fileType = self::FILE_TYPE_VIDEO;
                break;
            case 8273:
                $fileType = self::FILE_TYPE_AVI;
                break;
            case 4682:
                $fileType = self::FILE_TYPE_RMVB;
                break;
            case 2669:
                $fileType = self::FILE_TYPE_MKV;
                break;
            case 7076:
                $fileType = self::FILE_TYPE_FLV;
                break;
            case 7087:
                $fileType = self::FILE_TYPE_SWF;
                break;
            default:
                $name = $file['name'];
                $p = mb_strripos($name,'.');
                $len = mb_strlen($name);
                $extType = mb_substr($name, $p + 1, $len);
                $extType = mb_strtolower($extType);
                if ($extType == 'txt') {
                    $fileType = 26;
                } else {
                    $fileType = self::FILE_TYPE_UNKNOWN;
                }
        }

        //Fix  
        if ($strInfo['chars1'] == '-1' AND $strInfo['chars2'] == '-40') return self::FILE_TYPE_JPG;
        if ($strInfo['chars1'] == '-119' AND $strInfo['chars2'] == '80') return self::FILE_TYPE_PNG;

        return $fileType;
    }

}

;