<?php

session_start();
// connect to database
        require("../source/db.php");
        $db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

// load countries and legal instruments
        $db->select('info_about');
        $res = $db->last_results;
        $current = [];
        foreach($res as $item) $current[]=$item['country'];

        $db->select("country");
        $db->sort_results();
        $countries = $db->get_results();
        $db->select("fiche_belge");
        $db->sort_results();
        $fiches = $db->get_results();
        foreach($countries as $cid=>$country){
                if(in_array("$cid", $current)) echo ". ";
                else {
                        $db->save_asset(
                                'info_about', 
                                0, 
                                ["title"=>$country['title'], "country"=>$cid], 
                                []);
                        echo "<br>";
                        echo $country['title'];
                        echo "  CREATED";
                        echo "<br>";
                }
        }
?>
