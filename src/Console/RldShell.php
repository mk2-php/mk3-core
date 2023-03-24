<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * RldShell
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

class RldShell{

    /**
     * __construct
     * @param $argv
     */
    public function __construct($argv){

        $main=$argv[0];

        if($main=="top"){
            require_once "RldShellTop.php";
            new RldShellTop();
        }
        else if($main=="make"){

            array_shift($argv);
            $type=$argv[0];
            array_shift($argv);

            if($type=="controller"){
                require_once "RldshellMakeController.php";
                new RldshellMakeController($argv);
            }
            /*
            else if($type=="model"){
                require_once "RldshellMakeModel.php";
                new RldshellMakeModel($argv);
            }
            else if($type=="middleware"){
                require_once "RldshellMakeMiddleware.php";
                new RldshellMakeMiddleware($argv);
            }
            else if($type=="table"){
                require_once "RldshellMakeTable.php";
                new RldshellMakeTable($argv);
            }
            else if($type=="validator"){
                require_once "RldshellMakeValidator.php";
                new RldshellMakeValidator($argv);
            }
            else if($type=="backpack"){
                require_once "RldshellMakeBackpack.php";
                new RldshellMakeBackpack($argv);
            }
            else if($type=="ui"){
                require_once "RldshellMakeUI.php";
                new RldshellMakeUI($argv);
            }
            else if($type=="render"){
                require_once "RldshellMakeRender.php";
                new RldshellMakeRender($argv);
            }
            else if($type=="shell"){
                require_once "RldshellMakeShell.php";
                new RldshellMakeShell($argv);
            }
            */
        }
        else if($main=="add"){

            array_shift($argv);
            $type=$argv[0];
            array_shift($argv);

            if($type=="routing"){
                require_once "RldshellAddRouting.php";
                new RldshellAddRouting($argv);
            }
            else if($type=="ddatabase"){
                require_once "RldshellAddDatabase.php";
                new RldshellAddDatabase($argv);
            }

        }
        else if($main=="config"){

            array_shift($argv);

            require_once "RldshellConfig.php";
            new RldshellConfig($argv);
        }
    }
}