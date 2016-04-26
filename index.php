<?php
session_start();
// require help file
        require("./source/functions.php");

// details of request
        define("URI", $_SERVER["REQUEST_URI"]);
	define("SHORTURI", ".".substr(URI,8));
        define("TIME",  $_SERVER["REQUEST_TIME"]);
        $method = $_SERVER["REQUEST_METHOD"];
        if ($method!="POST" && $method!="GET") die();
        define ("POST",$method=="POST");
	if (POST) $p=$_POST;

// connect to database
        require("./source/db.php");
        $db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

// initialize page
        $authLevel = 0;
        $country = 0; 
        $page_title = "TC interact";
        if (URI == "/monitor/logout" || !isset($_SESSION["authorization"]) || (int)$_SESSION["authorization"]<1){
                $_SESSION["authorization"]=0;
                $_SESSION["country"]=0;
        } else {
                $authLevel = (int)$_SESSION["authorization"];
		$country = (int)$_SESSION["country"];
        }
        if ($authLevel>1 && isset($p["change_country"]) && (int)$p["change_country"]){

                $country = (int)$p["change_country"];
                $_SESSION["country"]=$country;
        }

// output to browser 
        require("./html/head.php");
        require("./source/security.php");
        if ($authLevel>0 && $country>0) {
                require("./html/top_navigation.php");
        // router for authorized visitors
                switch (SHORTURI) {
                        case "./admintool": if ($authLevel<10) break;
                                require("./modules/admintool.php");
                                break;
                        case "./admintoolraw": if ($authLevel<10) break;
                                require("./modules/admintool_raw.php");
                                break;
                        case "./import": if ($authLevel<10) break;
                                require("./modules/import_from_file.php");
                                break;
                        case "./dbschema": if ($authLevel<10) break;
                                require("./modules/dbschema.php");
                                break;
                        case "./contacts":
                                require("./modules/updatecontacts.php");
                                break;
                        case "./status":
                                require("./modules/updatestatus.php");
                                break;
                        case "./fiches":
                                require("./modules/updatefiches.php");
                                break;
                        default:
                                echo get_text_block("homepage", $db);              
                } 
        }
        // end of restricted content

	require("./html/foot.php");

?>


