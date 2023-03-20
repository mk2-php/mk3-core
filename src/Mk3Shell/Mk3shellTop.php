<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * Mk3shellTop
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class Mk3shellTop extends Command{

    /**
     * __construct
     */
    public function __construct(){

        $this->cyan("===============================================");
        $this->text(" Mk2-Shell Command.");
        $this->text("Copylight : Nakajima Satoru.");
        $this->cyan("===============================================");
        $this->cyan("");
        $this->green(":Command List");
        $this->cyan("");
        $this->cyan(" command [...command]                          : Shell Script Command Run.");
        $this->text(" make controller [controllerName]              : Create a Controller class.");
        $this->text(" make model [modelName]                        : Create a Model class.");
        $this->text(" make middleware [middlewareName]              : Create a Middleware class.");
        $this->text(" make table [TableName]                        : Create a Table class.");
        $this->text(" make validator [validatorName]                : Create a Validator class.");
        $this->text(" make backpack [backpackrName]                 : Create a Backpack class.");
        $this->text(" make ui [uikrName]                            : Create a UI class.");
        $this->text(" make render [renderName]                      : Create a Render class.");
        $this->text(" make shell [shellName]                        : Create a Shell class.");
        $this->text(" config                                        : Make initial settings.");
        $this->text(" add routing [routeUrl] [controller] [action]  : Add routing, create required Controller and View at the same time.");
        $this->text(" add database [databaseConnectionName]         : Add database connection destination information.");
        $this->cyan("");
        $input=$this->input("Enter one of the above commands.");

        if($input){
            $input=explode(" ",$input);
            new Mk3Shell($input);
        }
    }
}