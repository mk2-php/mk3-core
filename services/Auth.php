<?php

namespace Reald\Services;

use Reald\Services\Session;

class _Auth{

    public $sessionClass = "Reald\Services\Session";

    public const MODE_MANUAL = "manual";
    public const MODE_DBTABLE = "dbtable";
    public const MODE_TOKEN = "token";

    private $session;

    public $verifyMode = self::MODE_MANUAL;

    public $algolizum = "sha256";
    public $salt = "59112***********************";
    public $stretch = 5;
    
    public function __construct($option = null){
        $this->session = new $this->sessionClass;

        if($option){
            foreach($option as $key => $value){
                $this->{$key} = $value;
            }
        }
    }

    public function verify($post = null){

        if($post){

            if(gettype($post) == "object"){
                $post = $post->toArray();
            }

            if($this->verifyMode == self::MODE_MANUAL){
                return $this->_verify_normal($post);
            }
            else if($this->verifyMode == self::MODE_DBTABLE){
                return $this->_verify_dbtable($post);
            }
            else if($this->verifyMode == self::MODE_TOKEN){
                return $this->_verify_token($post);
            }

        }
        else{

            $authData = $this->session->read("auth");

            if(!$authData){
                return false;
            }
    
            return true;
        }
    }
    
    private function _verify_normal($post){

        $name = $this->name;
        $user = $this->user;
        $pass = $this->pass;

        if(!($post["user"] == $user && $post["pass"] == $pass)){
            return false;
        }

        $this->session->write("auth",[
            "user" => $user,
            "name" => $name,
        ]);

        return true;
    }

    private function _verify_dbtable($post){

        $tableName = $this->table;
        $user = $this->user;
        $passHash = $this->passHash;

        $table = new $tableName;

        $std = $table->select()
            ->where($user, "=", $post["user"])
            ->where($passHash, "=", $this->getPassHash($post["pass"]))
        ;

        if(isset($this->role)){
            foreach($this->role as $r_){
                $std->where($r_[0], $r_[1], $r_[2]);
            }
        }

        $get = $std->first();

        if(!$get->toArray()){
            return false;
        }

        $get = $get->toArray();

        unset($get[$passHash]);

        $this->session->write("auth",$get);

        return true;
    }

    private function _verify_token($post){


    }

    public function logout(){
        $this->session->delete("auth");
    }

    public function get($name = null){
        if($name){
            $authData = $this->session->read("auth");

            if(!empty($authData[$name])){
                return $authData[$name];
            }
        }
        else{
            return  $this->session->read("auth");
        }
    }

    public function getPassHash($pass){
        $hash = $pass;
        for($v = 0 ; $v < $this->stretch ; $v++){
            $hash = hash($this->algolizum, $hash . $this->salt);
        }
        return $hash;
    }


}