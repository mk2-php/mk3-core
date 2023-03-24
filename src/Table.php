<?php

namespace Reald\Core;

use Reald\Orm\OrmTrait;

class Table extends CoreBlock{
    use OrmTrait;
    
    public function __construct(){
        parent::__construct();

        if(!$this->existDriver()){
            $getDrive = Config::get("config.database.". $this->drive);
            $this->setDatabase($this->drive, $getDrive);
        }
    }
}