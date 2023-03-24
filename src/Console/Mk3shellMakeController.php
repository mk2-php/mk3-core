<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * Mk2shellMakeController
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

class Mk3shellMakeController extends Command{

    /**
     * __construct
     * @param $argv
     */
    public function __construct($argv){

        $input = [];

        $this->green("Create a new Controller file.");
        $this->text("");

        if(!empty($argv[0])){
            $input["name"] = $argv[0];
        }
        else{
            $buff = "";
            for(;;){
                $buff = $this->input("\t- Enter the name of the controller to create.");
                if($buff){
                    break;
                }
                $this->red("\t  [ERROR] The Controller name has not been entered.");
            }
            $input["name"] = $buff;
        }

        $input["extends"] = $this->input("\t- If there is an inheritance source Controller name, enter it.");

        $juge = strtolower($this->input("\t- Do you want to add an action?[Y/n]"));

        if($juge != "y"){
            $juge = "n"; 
        }

        $input["actions"] = [];

        if($juge == "y"){

            $looped=false;
            for(;;){
    
                $buff=[];

                for(;;){
                    $name = $this->input("\t\t- Please enter the action name.");
                    if($name){
                        $buff["name"] = $name;
                        break;
                    }
                    $this->red("\t\t  [ERROR] The action name has not been entered.");
                }

                $buff["aregment"] = $this->input("\t\t- If there is an argument name, enter it with.(\",\" Separation)");
                
                $juge = strtolower($this->input("\t\t- Do you want to continue adding actions?[Y/n]"));
                if($juge != "y"){ $juge = "n"; }
    
                $input["actions"][] = $buff;

                if($juge == "n"){
                    break;
                }
            }
        }

        $juge = strtolower($this->input("\t- Do you want to set options?[Y/n]"));

        if($juge != "y"){
            $juge = "n";
        }

        $buff=[];

        if($juge == "y"){

            $buff["template"] = $this->input("\t\t- Enter template name if available.");

            $juge=strtolower($this->input("\t\t- Do you want to apply autoRender?[y/n]"));
            if($juge!="y"){ $juge="n"; }
            $buff["autoRender"] = $juge;
            
            $juge=$this->input("\t\t- Do you want to set up a handleBefore?[y/n]");
            if($juge!="y"){ $juge="n"; }
            $buff["onHandleBefore"] = $juge;

            $juge=$this->input("\t\t- Do you want to set up a handleAfter?[y/n]");
            if($juge!="y"){ $juge="n"; }
            $buff["onHandleAfter"] = $juge;

            $buff["comment"] = $this->input("\t\t- Enter any comment text.");
        }

        $input["option"]=$buff;

        $this->text("\t===========================================================================");

        $this->text("");
        $juge=strtolower($this->input("\t- Create a Controller file based on the entered information. Is it OK?[Y/n]"));
        
        if($juge=="n"){
            $this->text("");
            $this->text("");
            $this->text("Controller creation has been canceled,");
            return;
        }

        $juge=$this->_make($input);

        if(!$juge){
            $this->text("");
            $this->text("");
            $this->text("Controller creation has been canceled,");
            return;
        }

        $this->text("");
        $this->text("");
        $this->green("Controller creation completed.");
        
    }

    /**
     * _make
     * @param $data
     */
    private function _make($data){

        $str="";

        $str.="<?php \n";
        $str.="\n";
        $str.="/** \n";
        $str.=" * ============================================\n";
        $str.=" * \n";
        $str.=" * PHP Fraemwork - Mark2 \n";
        $str.=" * ".ucfirst($data["name"]). "Controller \n";
        $str.=" * \n";
        if(!empty($data["option"]["comment"])){
            $str.=" * ".$data["option"]["comment"]."\n";
        }
        $str.=" * created : ".date("Y/m/d")."\n";
        $str.=" * \n";
        $str.=" * ============================================\n";
        $str.=" */ \n";
        $str.="namespace App\Controller;\n";
        $str.="\n";
        if(!$data["extends"]){
            $str.="use Mk2\Libraries\Controller;\n";
            $data["extends"]="";
            $str.="\n";
        }
        $str.="class ".ucfirst($data["name"])."Controller extends ".ucfirst($data["extends"])."Controller\n";
        $str.="{\n";
        
        if($data["option"]){

            $str.="\n";
            $opt=$data["option"];

            if($opt["template"]){
                $str.="\t// Set Template Name.\n";
                $str.="\tpublic \$Template = '".$opt["template"]."';\n\n";
            }

            if($opt["autoRender"]=="y"){
                $str.="\t// auto Render Enable.\n";
                $str.="\tpublic \$autoRender = true;\n\n";
            }

            if($opt["onHandleBefore"]=="y"){
                $str.="\t/**\n";
                $str.="\t * handleBefore\n";
                $str.="\t */\n";
                $str.="\tpublic function handleBefore()\n";
                $str.="\t{\n";
                $str.="\n";

                if($opt["loadModel"]){
                    $models=explode(",",$opt["loadModel"]);

                    $str.="\t\t// load Model\n";
                    $str.="\t\t\$this->Model->load([\n";
                    foreach($models as $m_){
                        $str.="\t\t\t\"".ucfirst($m_)."\",\n";
                    }
                    $str.="\t\t]);\n\n";
                }

                if($opt["loadBackpack"]){
                    $backpacks=explode(",",$opt["loadBackpack"]);
                    $str.="\t\t// load Backpack\n";
                    $str.="\t\t\$this->Backpack->load([\n";
                    foreach($backpacks as $b_){
                        $str.="\t\t\t\"".ucfirst($b_)."\",\n";
                    }
                    $str.="\t\t]);\n\n";
                }

                if($opt["loadUI"]){
                    $uis=explode(",",$opt["loadUI"]);
                    $str.="\t\t// load UI\n";
                    $str.="\t\t\$this->UI->load([\n";
                    foreach($uis as $u_){
                        $str.="\t\t\t\"".ucfirst($u_)."\",\n";
                    }
                    $str.="\t\t]);\n\n";
                }



                $str.="\t}\n\n";
            }

            if($opt["onHandleAfter"]=="y"){
                $str.="\t/**\n";
                $str.="\t * handleAfter\n";
                $str.="\t * @param \$input \n";                
                $str.="\t */\n";
                $str.="\tpublic function handleAfter(\$input)\n";
                $str.="\t{\n";
                $str.="\n";
                $str.="\t}\n\n";
            }

        }

        if($data["actions"]){
            foreach($data["actions"] as $a_){
                
                $argStr="";
                $argComment="";
                if($a_["aregment"]){
                    $aregments=explode(",",$a_["aregment"]);
                    foreach($aregments as $ind=>$ag_){
                        if($ind>0){
                            $argStr.=", ";
                        }
                        $argStr.="$".$ag_;
                        $argComment.="\t * @param ".$ag_."\n";
                    }
                }
                

                $str.="\n";
                $str.="\t/**\n";
                $str.="\t * ".$a_["name"]."\n";
                $str.=$argComment;
                $str.="\t */\n";
                $str.="\tpublic function ".$a_["name"]."(".$argStr.")\n";
                $str.="\t{\n";
                $str.="\t\n";
                $str.="\t}\n";
            }
        }

        $str.="\n";
        $str.="}";

        $fileName=MK2_ROOT."/".MK2_DEFNS_CONTROLLER."/".ucfirst($data["name"])."Controller.php";
        $fileName=str_replace("\\","/",$fileName);

        if(file_exists($fileName)){
            $juge=strtolower($this->input("\tThe same Controller already exists, do you want to overwrite it as it is?[y/n]"));
            if($juge!="y"){ $juge="n"; }

            if($juge=="n"){
                return false;
            }
        }

        file_put_contents($fileName,$str);

        return true;
    }
}