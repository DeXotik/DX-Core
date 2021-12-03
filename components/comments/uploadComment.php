<?php
    include dirname(__FILE__)."/../include/database.php";
    include dirname(__FILE__)."/../../config/settings.php";
    require_once dirname(__FILE__)."/../include/functions.php";
    $f = new Functions();
    require_once dirname(__FILE__)."/../include/commands.php";
    $c = new Commands();

    $f->checkBanIP();

    $ip = $f->getIP();

    // Data - version 1.0
    $accountID = isset($_POST["udid"]) ? $f->checkString($_POST["udid"]) : "";
    $userName = isset($_POST["userName"]) ? $f->checkDefaultString($_POST["userName"]) : "";
    $levelID = isset($_POST["levelID"]) ? $f->checkNum($_POST["levelID"]) : "";
    $comment = isset($_POST["comment"]) ? $f->checkMultiString($_POST["comment"]) : "";

    // Check data
    if($accountID === "" OR is_numeric($accountID)) exit("-1");
    if($userName === "") exit("-1");
    if($levelID === "") exit("-1");
    if($comment === "") exit("-1");

    if($_POST["secret"] === "Wmfd2893gb7"){
        $comment = str_replace(":", "-", $comment);
        $comment = str_replace("~", "-", $comment);
        $comment = str_replace("|", "-", $comment);
        $comment = base64_encode($comment);

        $userID = $f->getUserID($accountID, $userName);

        // Check command
        if($c->checkCommand($userID, $levelID, base64_decode($comment))) exit("-1");
        
        // Comment limit
        if($commentLimit === true){
            $query = $db->prepare("SELECT count(*) FROM comments WHERE (IP = :ip OR userID = :userID) AND uploadDate > :time");
            $query->execute([':ip' => $ip, ':userID' => $userID, ':time' => time()-$commentLimitTime]);
            if($query->fetchColumn() >= $commentLimitCount) exit("-1");
        }
        if($commentLimitAtLevel === true){
            $query = $db->prepare("SELECT count(*) FROM comments WHERE (IP = :ip OR userID = :userID) AND levelID = :levelID");
            $query->execute([':ip' => $ip, ':userID' => $userID, ':levelID' => $levelID]);
            if($query->fetchColumn() >= $commentLimitAtLevelCount) exit("-1");
        }

        $query = $db->prepare("INSERT INTO comments (levelID, comment, uploadDate, userID, IP) VALUES (:levelID, :comment, :time, :userID, :ip)");
        $query->execute([':levelID' => $levelID, ':comment' => $comment, ':time' => time(), ':userID' => $userID, ':ip' => $ip]);

        exit("1");
    } else exit("-1");
?>