<?php
/**
 * curl对象的封装，实现了GET,POST,PUT,DELETE
 */
namespace App\Utils;

class HttpClient{
    
    public $curl = null;
    public $server = null;
    public $port = null;
    public function __construct($ip,$port){
        $this->server = $ip;
        $this->port = $port;
        $this->curl = curl_init();
    }

    public function get($url,string $params = '',$header = false){
        $url = "http://$this->server:$this->port".$url;
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET'); 
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($this->curl);
        return $data;
    }

    public function put($url,array $params,$header = false){
        $url = "http://$this->server:$this->port".$url;
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT'); 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        // curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($params))); 
        $data = curl_exec($this->curl);
        return $data;
    }

    public function post($url,array $params,$header = false){
        $url = "http://$this->server:$this->port".$url;
        var_dump($url);
        var_dump($params);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST'); 
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        $data = curl_exec($this->curl);
        var_dump($data);
        return $data;
    }

    public function delete($url,array $params = array(),$header = false){
        $url = "http://$this->server:$this->port".$url;
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        $data = curl_exec($this->curl);
        return $data;
    }

    public function head($url,array $params,$header = false){
        $url = "http://$this->server:$this->port".$url;
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'HEAD'); 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('content-type: application/json;charset=UTF-8')); 
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        $data = curl_exec($this->curl);
        return $data;
    }

    public function close(){
        curl_close($this->curl);
    }
}
