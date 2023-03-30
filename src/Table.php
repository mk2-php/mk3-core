<?php

namespace Reald\Core;

use Reald\Orm\OrmTrait;

class Table{

    use OrmTrait;
    
    public function __construct(){

        if(!$this->existDriver()){
            $getDrive = Config::get("config.database.". $this->drive);
            $this->setDatabase($this->drive, $getDrive);
        }
    }
}