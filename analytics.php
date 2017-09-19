<?php
  function log_visit($dbh, $page, $metadata = ""){
    $stmt = $dbh->prepare("INSERT INTO `analytics`(`page`, `time`, `url`, `ip`) VALUES (:page, now(), :metadata, :ip)");
    $stmt->bindParam(":page", $page);
    $stmt->bindParam(":metadata", $metadata);
    $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
    $stmt->execute();
  }
?>
