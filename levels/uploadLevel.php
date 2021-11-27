<?php
    chdir(dirname(__FILE__));
    ini_set("memory_limit", "64M");
    ini_set("post_max_size", "1M");
    ini_set("upload_max_filesize", "1M");

    include "../include/database.php";
    require_once "../include/functions.php";
    $f = new Functions();
    require_once "../config/settings.php";

    $f->checkBanIP();

    $ip = $f->getIP();

    // Data - version 1.2
    $accountID = isset($_POST["udid"]) ? $f->checkString($_POST["udid"]) : "";
    $userName = isset($_POST["userName"]) ? $f->checkDefaultString($_POST["userName"]) : "";
    // $levelID = isset($_POST["levelID"]) ? $f->checkNum($_POST["levelID"]) : 0;
    $levelName = isset($_POST["levelName"]) ? $f->checkDefaultString($_POST["levelName"]) : "";
    $levelDesc = isset($_POST["levelDesc"]) ? $f->checkMultiString($_POST["levelDesc"]) : "";
    $levelString = isset($_POST["levelString"]) ? $f->checkMultiString($_POST["levelString"]) : "";
    $levelVersion = isset($_POST["levelVersion"]) ? $f->checkNum($_POST["levelVersion"]) : 1;
    $levelLength = isset($_POST["levelLength"]) ? $f->checkNum($_POST["levelLength"]) : 0;
    $audioTrack = isset($_POST["audioTrack"]) ? $f->checkNum($_POST["audioTrack"]) : 0;
    $gameVersion = isset($_POST["gameVersion"]) ? $f->checkNum($_POST["gameVersion"]) : 3;

    // Check data
    if($accountID === "" OR is_numeric($accountID)) exit("-1");
    if($userName === "") exit("-1");
    // if($levelID === "") exit("-1");
    if($levelName === "") exit("-1");
    if($levelString === "") exit("-1");
    if($levelVersion === "") exit("-1");
    if($levelLength === "") exit("-1");
    if($audioTrack === "") exit("-1");
    if($gameVersion === "") exit("-1");

    if($_POST["secret"] === "Wmfd2893gb7"){
        if($checkGameVersion AND $gameVersion != $totalGameVersion) exit("-1");
        $levelDesc = str_replace(":", "-", $levelDesc);
        $levelDesc = base64_encode($levelDesc);

        $userID = $f->getUserID($accountID, $userName);

        // Level limit
        if($levelLimiting === true){
            $query = $db->prepare("SELECT count(*) FROM levels WHERE (IP = :ip OR userID = :userID) AND uploadDate > :time");
            $query->execute([':ip' => $ip, ':userID' => $userID, ':time' => time()-$levelLimitingTime]);
            if($query->fetchColumn() >= $levelLimitingCount) exit("-1");
        }

        $query = $db->prepare("SELECT levelID FROM levels WHERE levelName = :levelName AND userID = :userID LIMIT 1");
        $query->execute([':levelName' => $levelName, ':userID' => $userID]);
        if($query->rowCount() != 0){
            $levelID = $query->fetchColumn();
        } else $levelID = 0;

        if($levelID == 0){
            $query = $db->prepare("INSERT INTO levels (levelName, levelDesc, levelVersion, levelLength, audioTrack, gameVersion, uploadDate, userID) VALUES (:levelName, :levelDesc, :levelVersion, :levelLength, :audioTrack, :gameVersion, :uploadDate, :userID)");
            $query->execute([':levelName' => $levelName, ':levelDesc' => $levelDesc, ':levelVersion' => $levelVersion, ':levelLength' => $levelLength, ':audioTrack' => $audioTrack, ':gameVersion' => $gameVersion, ':uploadDate' => time(), ':userID' => $userID]);
            $levelID = $db->lastInsertId();
            file_put_contents(dirname(__FILE__)."/data/$levelID", $levelString);
        } else {
            $query = $db->prepare("UPDATE levels SET levelDesc = :levelDesc, levelVersion = :levelVersion, levelLength = :levelLength, audioTrack = :audioTrack, gameVersion = :gameVersion, updateDate = :updateDate, deleted = 0 WHERE levelName = :levelName AND userID = :userID");
            $query->execute([':levelDesc' => $levelDesc, ':levelVersion' => $levelVersion, ':levelLength' => $levelLength, ':audioTrack' => $audioTrack, ':gameVersion' => $gameVersion, ':updateDate' => time(), ':levelName' => $levelName, ':userID' => $userID]);
            file_put_contents(dirname(__FILE__)."/data/$levelID", $levelString);
        }

        exit("$levelID");
    } else exit("-1");
?>