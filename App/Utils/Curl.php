<?php
/**
 * curl对象的封装，实现了GET,POST,PUT,DELETE
 */
namespace App\Utils;

class Curl{
    
    public $curl = null;
    public function __construct(){
        $this->curl = curl_init();
    }

    public function get($url,$params = '',$header = false){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET'); 
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($this->curl);
        return $data;
    }

    public function put($url,$params = '',$header = false){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT'); 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        // curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($params))); 
        $data = curl_exec($this->curl);
        return $data;
    }

    public function post($url,$params = '',$header = false){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POST, true); 
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($this->curl);
        return $data;
    }

    public function delete($url,$params = '',$header = false){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec();
        return $data;
    }

    public function head($url,$params = '',$header = false){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'HEAD'); 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($this->curl);
        return $data;
    }

    public function close(){
        curl_close($this->curl);
    }
}
