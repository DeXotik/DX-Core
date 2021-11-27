<?php
    chdir(dirname(__FILE__));

    include "../include/database.php";
    require_once "../include/functions.php";
    $f = new Functions();
    require_once "../config/settings.php";

    $f->checkBanIP();

    // Data - version 1.3
    $accountID = isset($_POST["udid"]) ? $f->checkString($_POST["udid"]) : "";
    $userName = isset($_POST["userName"]) ? $f->checkDefaultString($_POST["userName"]) : "";
    $stars = isset($_POST["stars"]) ? $f->checkNum($_POST["stars"]) : "";
    $demons = isset($_POST["demons"]) ? $f->checkNum($_POST["demons"]) : "";
    $icon = isset($_POST["icon"]) ? $f->checkNum($_POST["icon"]) : "";
    $color1 = isset($_POST["color1"]) ? $f->checkNum($_POST["color1"]) : "";
    $color2 = isset($_POST["color2"]) ? $f->checkNum($_POST["color2"]) : "";

    // Check data
    if($accountID === "" OR is_numeric($accountID)) exit("-1");
    if($userName === "") exit("-1");
    if($stars === "") exit("-1");
    if($demons === "") exit("-1");
    if($icon === "") exit("-1");
    if($color1 === "") exit("-1");
    if($color2 === "") exit("-1");

    if($_POST["secret"] === "Wmfd2893gb7"){
        // Score limit
        if($scoreLimit === true){
            // Additional limit for stars
            $query = $db->prepare("SELECT SUM(stars) FROM levels");
            $query->execute();
            $maxStars = $scoreLimitStars + $query->fetchColumn();
            // Additional limit for demons
            $query = $db->prepare("SELECT SUM(demon) FROM levels");
            $query->execute();
            $maxDemons = $scoreLimitDemons + $query->fetchColumn();

            if($stars > $maxStars) exit("-1");
            if($demons > $maxDemons) exit("-1");
        }

        $userID = $f->getUserID($accountID, $userName);

        $query = $db->prepare("UPDATE users SET stars = :stars, demons = :demons, icon = :icon, color1 = :color1, color2 = :color2 WHERE userID = :userID");
        $query->execute([':stars' => $stars, ':demons' => $demons, ':icon' => $icon, ':color1' => $color1, ':color2' => $color2, ':userID' => $userID]);

        exit("$userID");
    } else exit("-1");
?>