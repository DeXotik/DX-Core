<?php
    include dirname(__FILE__)."/../../include/database.php";
    include dirname(__FILE__)."/../../config/settings.php";

    echo "Auto Creator Points:<br>";

    // Remove creatore points
    $query = $db->prepare("SELECT userID FROM users WHERE creatorPoints > 0");
    $query->execute();
    $users = $query->fetchAll();
    foreach($users AS $user){
        $query = $db->prepare("SELECT count(*) FROM levels WHERE userID = :userID");
        $query->execute([':userID' => $user["userID"]]);
        if($query->fetchColumn() == 0){
            $query = $db->prepare("UPDATE users SET creatorPoints = 0 WHERE userID = :userID");
            $query->execute([':userID' => $user["userID"]]);

            echo 'Creator points removed from (userID: '.$user["userID"].')<br>';
        }
    }

    // Add creatore points
    $query = $db->prepare("SELECT userID, sum(rated) AS rated FROM levels GROUP BY userID");
    $query->execute();
    $users = $query->fetchAll();
    foreach($users AS $user){
        $creatorPoints = 0;
        $creatorPoints += $user["rated"] * $ratedCPs;

        $query = $db->prepare("UPDATE users SET creatorPoints = :cps WHERE userID = :userID");
        $query->execute([':cps' => $creatorPoints, ':userID' => $user["userID"]]);

        if($creatorPoints > 0){
            echo 'The (userID: '.$user["userID"].') was given '.$creatorPoints.' creator point(-s)<br>';
        } else {
            echo 'Creator points removed from (userID: '.$user["userID"].')<br>';
        }
    }
?>