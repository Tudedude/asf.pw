<?php
  require_once("connect.php");
  $options = [
    "threshold" => 2.5
  ];
  $banlist = json_decode(file_get_contents("https://asf.pw/banlist.json"), true);
  if(isset($blacklist) && $blacklist == true){
    $ip = $_SERVER['REMOTE_ADDR'];
    foreach($banlist as $blockedip){
      if($ip == $blockedip){
        $stmt = $dbh->prepare("INSERT INTO blacklog (ip, time, request) VALUES (:ip, now(), :req)");
        $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(":req", $_SERVER['REQUEST_URI']);
        $stmt->execute();
        killScript("IP");
      }
      if(preg_match("/" . str_replace("*", "[0-9][0-9]?[0-9]?", str_replace(".", "\.", $blockedip)) . "/", $ip)){
        $stmt = $dbh->prepare("INSERT INTO blacklog (ip, time, request) VALUES (:ip, now(), :req)");
        $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(":req", $_SERVER['REQUEST_URI']);
        $stmt->execute();
        killScript("IPR");
      }
    }
  }
  if(isset($filter) && $filter == true){
    if(!isset($_SESSION['requests'])){
      $_SESSION['lastReq'] = microtime(true);
      $_SESSION['requests'] = [microtime(true)];
    }else{
      if(sizeof($_SESSION['requests']) >= 6){
        array_shift($_SESSION['requests']);
      }
      $_SESSION['requests'][] = microtime(true);
      if(sizeof($_SESSION['requests'] >= 5)){
        $freq = frequency($_SESSION['requests']);
        if($freq <= $options["threshold"]){
          $stmt = $dbh->prepare("INSERT INTO blacklog (ip, time, request) VALUES (:ip, now(), :req)");
          $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
          $stmt->bindParam(":req", $_SERVER['REQUEST_URI']);
          $stmt->execute();
          killScript("RL");
        }
      }
      $_SESSION['lastReq'] = microtime(true);
    }
  }
  function killScript($code = ""){
    header('HTTP/1.0 403 Forbidden');
    header('Content-Type: text/json');
    die('{"error":"true","errorMessage":"You are forbidden from acessing this page [403' . $code . ']","field":"null"}');
  }
  function frequency($arr = []){
    $lastTimestamp = 0;
    $frequencies = array();
    foreach($arr as $timestamp){
      if($lastTimestamp == 0){
        $lastTimestamp = $timestamp;
      }else{
        $frequencies[] = ($timestamp - $lastTimestamp);
        $lastTimestamp = $timestamp;
      }
    }
    $total = 0;
    foreach($frequencies as $freq){
      $total += $freq;
    }
    return ($total/(sizeof($arr)));
  }
?>
