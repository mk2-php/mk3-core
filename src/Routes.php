<?php

namespace Mk3\Core;

class Routes{

    private const ROUTE_PAGES = "pages";
    private const ROUTE_SHELL = "shell";
    private const TYPE_RELEASE = "release";
    private const TYPE_ERROR = "error";

    private static $_routes = [];
    private static $_scope = [];
    private static $_middleware = [];

    private static $_routeMode = self::ROUTE_PAGES;
    private static $_routeType = self::TYPE_RELEASE;

    /**
     * pages
     */
    public static function pages(){
        self::$_routeMode = self::ROUTE_PAGES;
        if(empty(self::$_routes[self::$_routeMode])){
            self::$_routes[self::$_routeMode] = [];
        }
    }

    /**
     * get
     * @param $url
     * @param $controller
     * @param action
     */
    public static function get($url, $controller, $action, $noReset = false){

        if(!$noReset){
            self::$_scope = [];
            self::$_middleware = [];
        }

        if(empty(self::$_routes[self::$_routeMode][self::$_routeType])){
            self::$_routes[self::$_routeMode][self::$_routeType] = [];
        }

        $str = "controller:". $controller ."|action:". $action;

        if(self::$_middleware){
            $str .= "|middleware:".self::$_middleware;
        }

        $scope = join(self::$_scope);
        
        self::$_routes[self::$_routeMode][self::$_routeType][$scope. $url] = $str;
    }
    
    public static function container($url, $container, $noReset = false){

        if(!$noReset){
            self::$_scope = [];
            self::$_middleware = [];
        }

        $str = "container=". $container;

        if(self::$_middleware){
            $str .= "|middleware:".self::$_middleware;
        }

        self::$_routes[self::$_routeMode][self::$_routeType][$url] = $str;

        return new RoutesAdd;
    }

    public static function middleware($middleware){
        self::$_middleware[] = $middleware;
    }

    public static function scope($url){
        self::$_scope[] = $url;
        return new RoutesAdd;
    }
    
    public static function out(){
        return self::$_routes;
    }
    
}

class RoutesAdd{

    public function get($url, $controller, $action){
        Routes::get($url, $controller, $action, true);
        return $this;
    }

    public function middleware($middleware){
        Routes::middleware($middleware);
        return $this;
    }

    public function container($url, $container){
        Routes::container($url, $container, true);
        return $this;
    }

}