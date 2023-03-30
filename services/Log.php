<?php

namespace Reald\Services;

use Reald\Core\Routings;
use Reald\Core\RequestCollectionStatic;

class Log{

    public $temporary = RLD_PATH_TEMPORARY;

    public $path = "logs";
    public $fileName = "{Y}{m}{d}.log";
    public $errorFileName = "error-{Y}{m}{d}.log";

    public $header = "{datetime} {request.method} {request.root} {response.code} {request.query} {request.remoteip} {response.message}";

    /**
     * out
     * 
     * @param String $message
     * @param $option = null
     */
    public function out($message, $option=null){
        $this->_out($message,$option);
        return $this;
    }

    /**
     * _out
     * 
     * @param String $message
     * @param Array $option
     */
    private function _out($message, $option){

        $temporary = $this->temporary;
        if(!empty($option["temporary"])){
            $temporary = $option["temporary"];
        }

        $path = $this->path;
        if(!empty($option["path"])){
            $path = $option["path"];
        }

        $filePath = $temporary."/".$path;

        if(!is_dir($filePath)){
            mkdir($filePath, 0777, true);
        }

        $fileName=$this->fileName;
        if(!empty($option["fileName"])){
            $fileName = $option["fileName"];
        }

        $fileName = $this->_convertFileName($fileName);
        
        $header=$this->header;
        if(!empty($option["header"])){
            $header = $option["header"];
        }

        error_log($this->_convert($message,$header), 3, $filePath . "/" . $fileName);
    }

    /**
     * error
     * 
     * @param String $exception
     * @param Array $option = null
     */
    public function error($exception, $option = null){
        $option["fileName"] = $this->errorFileName;
        return $this->out($exception,$option);
    }

    /**
     * _convertFileName
     * 
     * @param String $fileName
     */
    private function _convertFileName($fileName){

        $fileName = str_replace("{Y}",date("Y"),$fileName);
        $fileName = str_replace("{m}",date("m"),$fileName);
        $fileName = str_replace("{d}",date("d"),$fileName);
        $fileName = str_replace("{h}",date("h"),$fileName);
        $fileName = str_replace("{i}",date("i"),$fileName);
        $fileName = str_replace("{s}",date("s"),$fileName);

        return $fileName;
    }

    /**
     * _convert
     * @param String $message
     * @param String $headers
     */
    private function _convert($message, $headers){
        
        $params = Routings::$_data;

        $headers = str_replace("{datetime}",date_format(date_create("now"),"Y/m/d H:i:s"),$headers);
        $headers = str_replace("{request.method}",$params["method"],$headers);
        $headers = str_replace("{request.root}",$params["root"],$headers);
        $headers = str_replace("{request.remoteip}",$params["remoteIp"],$headers);
        $headers = str_replace("{request.port}",$params["port"],$headers);
        $headers = str_replace("{request.url}",$params["url"],$headers);
        $headers = str_replace("{request.host}",$params["host"],$headers);
        $headers = str_replace("{request.controller}",$params["controller"],$headers);
        $headers = str_replace("{request.action}",$params["action"],$headers);
        $headers = str_replace("{request.path}",$params["path"],$headers);
        $headers = str_replace("{request.protocol}",$params["protocol"],$headers);

        if($params["method"] == "GET"){

            $query = RequestCollectionStatic::get(RequestCollectionStatic::METHOD_QUERY);

            if($query){
                $headers = str_replace("{request.query}", json_encode($query, JSON_UNESCAPED_UNICODE), $headers);
            }
            else{
                $headers = str_replace("{request.query}", "", $headers);                
            }

            $headers = str_replace("{request.body}", "", $headers);
        }
        else{

            $headers = str_replace("{request.query}", "", $headers);

            if($params["method"] == "POST"){

                $post = RequestCollectionStatic::get(RequestCollectionStatic::METHOD_POST);

                if($post){
                    $headers = str_replace("{request.body}", json_encode($post,JSON_UNESCAPED_UNICODE), $headers);
                }
            }
            else if($params["method"] == "PUT"){

                $post = RequestCollectionStatic::get(RequestCollectionStatic::METHOD_PUT);

                if($put){
                    $headers = str_replace("{request.body}", json_encode($put,JSON_UNESCAPED_UNICODE), $headers);
                }
            }
            else if($params["method"] == "DELETE"){

                $delete = RequestCollectionStatic::get(RequestCollectionStatic::METHOD_DELETE);

                if($delete){
                    $headers = str_replace("{request.body}", json_encode($delete,JSON_UNESCAPED_UNICODE), $headers);
                }
            }
        }


        $headers = str_replace("{response.code}", http_response_code(), $headers);

        $headers = str_replace("{response.message}", $message , $headers);

        return $headers."\n";
    }
}