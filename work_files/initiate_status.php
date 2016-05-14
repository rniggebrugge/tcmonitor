<?php
die("DO NOT USE UNLESS NEW STATUSSES MUST BE INTRODUCED!");

session_start();
// connect to database
        require("../source/db.php");
        $db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

// load countries and legal instruments
        $db->select("country");
        $db->sort_results();
        $countries = $db->get_results();
        $db->select("legal_instrument");
        $db->sort_results();
        $legals = $db->get_results();
        foreach($countries as $cid=>$country){
                foreach($legals as $lid=>$legal){
                        echo $cid.' ('.$country['title'].') - '.$lid.' ('.$legal['reference'].')<br>';
                        $db->save_asset(
                                'status_implementation', 
                                0, 
                                ["title"=>$country['title'].' / '.$legal['reference'] , "country"=>$cid, "legal_instrument"=>$lid], 
                                []);
                }
        }
?>