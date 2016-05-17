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
        $db->select("fiche_belge");
        $db->sort_results();
        $fiches = $db->get_results();
        foreach($countries as $cid=>$country){
                foreach($fiches as $fid=>$fiche){
                        echo $cid.' ('.$country['title'].') - '.$fid.' ('.$fiche['number'].')<br>';
                        $db->save_asset(
                                'status_fiche', 
                                0, 
                                ["title"=>$country['title'].' / '.$fiche['number'] , "country"=>$cid, "fiche"=>$fid, "status"=>"confirmed"], 
                                []);
                }
        }
?>
