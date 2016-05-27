<?php

session_start();
// connect to database
        require("../source/db.php");
        $db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

// load countries and legal instruments
        $db->select('status_fiche');
        $res = $db->last_results;
        $current = [];
        foreach($res as $item) $current[]=$item['country'].'/'.$item['fiche'];

        $db->select("country");
        $db->sort_results();
        $countries = $db->get_results();
        $db->select("fiche_belge");
        $db->sort_results();
        $fiches = $db->get_results();
        foreach($countries as $cid=>$country){
                foreach($fiches as $fid=>$fiche){
                        if(in_array("$cid/$fid", $current)) echo ". ";
                        else {
                                $db->save_asset(
                                        'status_fiche', 
                                        0, 
                                        ["title"=>$country['title'].' / '.$fiche['number'] , "country"=>$cid, "fiche"=>$fid, "status"=>"confirmed"], 
                                        []);
                                echo "<br>";
                                echo $country['title'].' - '.$fiche['number'];
                                echo "  CREATED";
                                echo "<br>";
                        }
                }
        }
?>
