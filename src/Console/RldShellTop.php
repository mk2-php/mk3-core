<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * RldShellTop
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

class RldShellTop extends Command{

    /**
     * __construct
     */
    public function __construct(){

        $this->cyan("===============================================");
        $this->text(" Reald Console Command List.");
        $this->text("Copylight : Nakatsuji Masato.");
        $this->cyan("===============================================");
        $this->cyan("");
        $this->cyan("");
        $this->cyan(" command [...command]                          : Shell Script Command Run.");
        $this->text(" make Controller [controllerName]              : Create a Controller class.");
        $this->text(" make Model [modelName]                        : Create a Model class.");
        $this->text(" make Middleware [middlewareName]              : Create a Middleware class.");
        $this->text(" make Pack [packName]                          : Create a pack class.");
        $this->text(" make Render [renderName]                      : Create a Render class.");
        $this->text(" make Shell [shellName]                        : Create a Shell class.");
        $this->text(" config                                        : Make initial settings.");
        $this->text(" add routing [routeUrl] [controller] [action]  : Add routing, create required Controller and View at the same time.");
        $this->text(" add database [databaseConnectionName]         : Add database connection destination information.");
        $this->cyan("");
        $input=$this->input("Enter one of the above commands.");

        if($input){
            $input=explode(" ",$input);
            new RldShell($input);
        }
    }
}