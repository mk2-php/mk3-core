<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * Mk3shell
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class Mk3shell{

    /**
     * __construct
     * @param $argv
     */
    public function __construct($argv){

        $main=$argv[0];

        if($main=="top"){
            require_once "Mk3shellTop.php";
            new Mk3shellTop();
        }
        else if($main=="make"){

            array_shift($argv);
            $type=$argv[0];
            array_shift($argv);

            if($type=="controller"){
                require_once "Mk3shellMakeController.php";
                new Mk3shellMakeController($argv);
            }
            /*
            else if($type=="model"){
                require_once "Mk3shellMakeModel.php";
                new Mk3shellMakeModel($argv);
            }
            else if($type=="middleware"){
                require_once "Mk3shellMakeMiddleware.php";
                new Mk3shellMakeMiddleware($argv);
            }
            else if($type=="table"){
                require_once "Mk3shellMakeTable.php";
                new Mk3shellMakeTable($argv);
            }
            else if($type=="validator"){
                require_once "Mk3shellMakeValidator.php";
                new Mk3shellMakeValidator($argv);
            }
            else if($type=="backpack"){
                require_once "Mk3shellMakeBackpack.php";
                new Mk3shellMakeBackpack($argv);
            }
            else if($type=="ui"){
                require_once "Mk3shellMakeUI.php";
                new Mk3shellMakeUI($argv);
            }
            else if($type=="render"){
                require_once "Mk3shellMakeRender.php";
                new Mk3shellMakeRender($argv);
            }
            else if($type=="shell"){
                require_once "Mk3shellMakeShell.php";
                new Mk3shellMakeShell($argv);
            }
            */
        }
        else if($main=="add"){

            array_shift($argv);
            $type=$argv[0];
            array_shift($argv);

            if($type=="routing"){
                require_once "Mk3shellAddRouting.php";
                new Mk3shellAddRouting($argv);
            }
            else if($type=="ddatabase"){
                require_once "Mk3shellAddDatabase.php";
                new Mk3shellAddDatabase($argv);
            }

        }
        else if($main=="config"){

            array_shift($argv);

            require_once "Mk3shellConfig.php";
            new Mk3shellConfig($argv);
        }
    }
}