<?php

namespace Reald\Core;

class Env{

    private static $_env;

    public static function get($name){

        if(!self::$_env){

            $getEnvBuff = file_get_contents(RLD_ROOT . "/.env");

            $getEnvBuff = explode("\n", $getEnvBuff);

            $getEnv = [];
            foreach($getEnvBuff as $ind => $g_){
                if(substr($g_, 0, 1) == "#" || trim($g_) == ""){
                    continue;
                }

                $g_ = explode("=",$g_);
                $g_[0] = trim($g_[0]);
                $g_[1] = trim($g_[1]);
               
                if($g_[1] == "true"){
                    $g_[1] = true;
                }
                if($g_[1] == "false"){
                    $g_[1] = false;
                }
                
                $getEnv[$g_[0]] = $g_[1];
            }

            self::$_env = $getEnv;
        }

        if(!empty(self::$_env[$name])){
            return self::$_env[$name];
        }
    }

}