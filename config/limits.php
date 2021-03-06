<?php
    $createUserLimit["use"]   = true; // Checking the limit for create users
    $createUserLimit["count"] = 3; // The maximum number of create users
    $createUserLimit["time"]  = 3600; // Limit check time for create users (in seconds)

    $uploadLevelLimit["use"]   = true; // Checking the limit for uploading levels
    $uploadLevelLimit["count"] = 3; // The maximum number of uploaded levels
    $uploadLevelLimit["time"]  = 3600; // Limit check time for uploaded levels (in seconds)

    $uploadCommentLimit["use"]   = true; // Checking the limit for uploading comments
    $uploadCommentLimit["count"] = 10; // The maximum number of uploaded comments
    $uploadCommentLimit["time"]  = 300; // Limit check time for uploaded comments (in seconds)
?>