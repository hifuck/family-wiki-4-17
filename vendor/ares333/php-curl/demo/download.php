<?php
require '_inc.php';
use Ares333\Curl\Toolkit;
$curl = (new Toolkit())->getCurl();
$curl->onInfo = null;
$url = 'http://www.baidu.com/img/bd_logo1.png';
$file = __DIR__ . '/output/download.png';
// $fp is closed automatically on download finished.
$fp = fopen($file, 'w');
$curl->add(
    array(
        'opt' => array(
            CURLOPT_URL => $url,
            CURLOPT_FILE => $fp,
            CURLOPT_HEADER => false
        ),
        'args' => array(
            'file' => $file
        )
    ),
    function ($r, $args) {
        echo "download finished successfully, file=$args[file]\n";
    })->start();