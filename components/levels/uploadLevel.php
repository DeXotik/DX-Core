<?php
    ini_set("memory_limit", "64M");
    ini_set("post_max_size", "1M");
    ini_set("upload_max_filesize", "1M");

    include dirname(__FILE__)."/../include/database.php";
    include dirname(__FILE__)."/../../config/settings.php";
    require_once dirname(__FILE__)."/../include/functions.php";
    $f = new Functions();

    $f->checkBanIP();

    $ip = $f->getIP();

    // Data - version 1.0
    $accountID = isset($_POST["udid"]) ? $f->checkString($_POST["udid"]) : "";
    $userName = isset($_POST["userName"]) ? $f->checkDefaultString($_POST["userName"]) : "";
    // $levelID = isset($_POST["levelID"]) ? $f->checkNum($_POST["levelID"]) : ";
    $levelName = isset($_POST["levelName"]) ? $f->checkDefaultString($_POST["levelName"]) : "";
    $levelDesc = isset($_POST["levelDesc"]) ? $f->checkMultiString($_POST["levelDesc"]) : "";
    $levelString = isset($_POST["levelString"]) ? $f->checkMultiString($_POST["levelString"]) : "";
    $levelVersion = isset($_POST["levelVersion"]) ? $f->checkNum($_POST["levelVersion"]) : "";
    $levelLength = isset($_POST["levelLength"]) ? $f->checkNum($_POST["levelLength"]) : "";
    $audioTrack = isset($_POST["audioTrack"]) ? $f->checkNum($_POST["audioTrack"]) : "";
    $gameVersion = isset($_POST["gameVersion"]) ? $f->checkNum($_POST["gameVersion"]) : "";
    // Data - version 1.6
    // $auto = isset($_POST["auto"]) ? $f->checkNum($_POST["auto"]) : 0;
    // $levelReplay = isset($_POST["levelReplay"]) ? $f->checkMultiString($_POST["levelReplay"]) : "";
    // Data - version 1.7
    $password = isset($_POST["password"]) ? $f->checkNum($_POST["password"]) : 0;

    // Check data
    if($accountID === "" OR is_numeric($accountID)) exit("-1");
    if($userName === "") exit("-1");
    // if($levelID === "") exit("-1");
    if($levelName === "") exit("-1");
    if($levelString === "" OR mb_strpos($levelString, "kS1,") !== 0 OR $levelString[mb_strlen($levelString) - 1] !== ";") exit("-1");
    if($levelVersion === "") exit("-1");
    if($levelLength === "") exit("-1");
    if($audioTrack === "") exit("-1");
    if($gameVersion === "") exit("-1");
    // if($auto === "") exit("-1");
    if($password === "" OR strlen($password) > 5) exit("-1");

    if($_POST["secret"] === "Wmfd2893gb7"){
        if($checkGameVersion AND $gameVersion != $totalGameVersion) exit("-1");
        $levelDesc = str_replace(":", "-", $levelDesc);
        $levelDesc = base64_encode($levelDesc);
        if($password > 1) $password = substr($password, 1);

        $userID = $f->getUserID($accountID, $userName);

        // Level limit
        if($levelLimit === true){
            $query = $db->prepare("SELECT count(*) FROM levels WHERE (IP = :ip OR userID = :userID) AND uploadDate > :time");
            $query->execute([':ip' => $ip, ':userID' => $userID, ':time' => time()-$levelLimitTime]);
            if($query->fetchColumn() >= $levelLimitCount) exit("-1");
        }

        $levelID = 0; $deleted = 0;
        $query = $db->prepare("SELECT levelID, deleted FROM levels WHERE levelName = :levelName AND userID = :userID LIMIT 1");
        $query->execute([':levelName' => $levelName, ':userID' => $userID]);
        if($query->rowCount() != 0){
            $levelInfo = $query->fetch();
            $levelID = $levelInfo["levelID"];
            $deleted = $levelInfo["deleted"];
        }

        if($levelID == 0){
            $query = $db->prepare("INSERT INTO levels (levelName, levelDesc, levelVersion, levelLength, password, audioTrack, gameVersion, uploadDate, userID) VALUES (:levelName, :levelDesc, :levelVersion, :levelLength, :password, :audioTrack, :gameVersion, :uploadDate, :userID)");
            $query->execute([':levelName' => $levelName, ':levelDesc' => $levelDesc, ':levelVersion' => $levelVersion, ':levelLength' => $levelLength, ':password' => $password, ':audioTrack' => $audioTrack, ':gameVersion' => $gameVersion, ':uploadDate' => time(), ':userID' => $userID]);
            $levelID = $db->lastInsertId();
            file_put_contents(dirname(__FILE__)."/../../data/levels/$levelID", $levelString);
        } else {
            $query = $db->prepare("UPDATE levels SET levelDesc = :levelDesc, levelVersion = :levelVersion, levelLength = :levelLength, password = :password, audioTrack = :audioTrack, gameVersion = :gameVersion, updateDate = :updateDate, deleted = 0 WHERE levelName = :levelName AND userID = :userID");
            $query->execute([':levelDesc' => $levelDesc, ':levelVersion' => $levelVersion, ':levelLength' => $levelLength, ':password' => $password, ':audioTrack' => $audioTrack, ':gameVersion' => $gameVersion, ':updateDate' => time(), ':levelName' => $levelName, ':userID' => $userID]);
            file_put_contents(dirname(__FILE__)."/../../data/levels/$levelID", $levelString);
            if($deleted == 1 AND file_exists(dirname(__FILE__)."/../../data/levels/deleted/$levelID")) unlink(dirname(__FILE__)."/../../data/levels/deleted/$levelID");
        }

        echo $levelID;
    } else exit("-1");
?>