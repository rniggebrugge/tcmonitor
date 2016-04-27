<?php
// connect to database
require("./source/db.php");
$db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

print_r($db->find_latest("contact_point"));







?>