<?php

namespace Mk3\Core;

class Routes{

    private static $_routes = null;

    public static function add($url, $controller, $action, $option = null){

        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        self::$_routes->scope("", true);

        return self::$_routes->addRoute("", $url, $controller, $action, $option);
    }

    public static function get($url, $controller, $action, $option = null){
        
        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        self::$_routes->scope("", true);

        return self::$_routes->get($url, $controller, $action, $option);
    }

    public static function post($url, $controller, $action, $option = null){

        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        self::$_routes->scope("", true);

        return self::$_routes->post($url, $controller, $action, $option);
    }

    public static function put($url, $controller, $action, $option = null){

        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        self::$_routes->scope("", true);

        return self::$_routes->put($url, $controller, $action, $option);
    }

    public static function delete($url, $controller, $action, $option = null){

        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        self::$_routes->scope("", true);

        return self::$_routes->delete($url, $controller, $action, $option);
    }

    public static function scope($url){

        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        return self::$_routes->scope($url, true);
    }

    public static function container($url, $container){

        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        return self::$_routes->container($url, $container);
    }

    public static function middleware($middleware){

        if(!self::$_routes){
            self::$_routes = new Routes2();
        }

        return self::$_routes->container($url, $container);
    }

    public static function out(){
        return self::$_routes->out();
    }
}

class Routes2{

    private $_buffer = [];

    private $_scope = null;

    public function addRoute($method, $url, $controller, $action, $option = null){

        $str = "controller:" . $controller."|action:" . $action;

        if($option){
            if($option["middleware"]){
                $str .= "|middleware:". join($option["middleware"]);
            }
        }

        if($this->_scope){
            if($url == "/"){
                $url = "";
            }
        }

        $urls = $this->_scope . $url;
        
        if($method == "get"){
            $urls = "get|" . $urls;
        }
        else if($method == "post"){
            $urls = "post|" . $urls;
        }
        else if($method == "put"){
            $urls = "put|" . $urls;
        }
        else if($method == "delete"){
            $urls = "delete|" . $urls;
        }

        $this->_buffer[$urls] = $str;

        return $this;
    }

    public function get($url, $controller, $action, $option = null){
        return $this->addRoute("get", $url, $controller, $action, $option);
    }

    public function post($url, $controller, $action, $option = null){
        return $this->addRoute("post", $url, $controller, $action, $option);
    }

    public function put($url, $controller, $action, $option = null){
        return $this->addRoute("put", $url, $controller, $action, $option);
    }

    public function delete($url, $controller, $action, $option = null){
        return $this->addRoute("delete", $url, $controller, $action, $option);
    }

    public function scope($url, $reset = false){

        if($reset){
            $this->_scope = $url;
        }
        else{
            $this->_scope .= $url;
        }

        return $this;
    }

    public function container($url, $container){

        $str = "container:". $container;

        if($this->_scope){
            if($url == "/"){
                $url = "";
            }
        }

        $urls = $this->_scope . $url;

        $this->_buffer[$urls] = $str;
        return;
    }

    public function out(){
        return $this->_buffer;
    }
}

// sample..

Routes::add("/", "main", "index");

Routes::get("/page_2", "page2", "index", [
    "middleware" => ["test1"],
]);

Routes::scope("/page_3")
    ->get("/","page3","index")
    ->get("/detail","page3","detail")
    ->scope("/sub2")
        ->get("/","page3","sub2")
        ->get("/detial","page3","sub2_detail")
;

Routes::scope("/page_4")
    ->get("/","page4","index")
    ->get("/detail","page4","detail")
;

Routes::post("/page_5", "page5", "index");

Routes::container("/yamada", "yamada");

Routes::middleware("aaaa")
    ->get("/page_6","page6","index")
    ->get("/page_6/detail","page6","detail")
;


Debug::out(Routes::out());

exit;