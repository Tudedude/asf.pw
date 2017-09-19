<?php
  include 'connect.php';
  include 'analytics.php';

  $filter = true;
  $blacklist = true;

  include 'filter.php';
  log_visit($dbh, 'api', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

  header("Content-Type: text/json");

  if(isset($_GET) && isset($_GET['action'])){
    if(strtolower($_GET['action']) == 'create'){
      if(!isset($_GET['hash'])){
        die('{"error":"true","errorMessage":"Link hash unspecified"}');
      }
      if(!isset($_GET['link'])){
        die('{"error":"true","errorMessage":"Link unspecified"}');
      }
      if(preg_match('/^[a-zA-Z0-9_+-.,!]+$/', $_GET['hash'])){
        if(strlen($_GET['hash']) <= 32){
          $domain = (isset($_GET['domain']) ? $_GET['domain'] : 'asf.pw');
          $stmt = $dbh->prepare("SELECT ip FROM links WHERE hash = :hash AND domain = :domain");
          $stmt->bindParam(":hash", $_GET['hash']);
          $stmt->bindParam(":domain", $domain);
          $stmt->execute();
          if($stmt->rowCount() == 0){
            $stmt = $dbh->prepare("INSERT INTO `links`(`hash`, `time`, `ip`, `link`, `domain`) VALUES (:hash, now(), :ip, :link, :domain)");
            $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
            $stmt->bindParam(":link", $_GET['link']);
            $stmt->bindParam(":hash", $_GET['hash']);
            $stmt->bindParam(":domain", $domain);
            $stmt->execute();
            die('{"error":"false","hash":"' . htmlentities($_GET['hash']) . '","domain":"' . htmlentities($domain) . '"}');
          }else{
            die('{"error":"true","errorMessage":"That name is not available","field":"hash"}');
          }
        }else{
          die('{"error":"true","errorMessage":"Hash is longer than 32 characters","field":"hash"}');
        }
      }else{
        die('{"error":"true","errorMessage":"Invalid characters in name","field":"hash"}');
      }
    }else if(strtolower($_GET['action']) == 'info'){
      if(!isset($_GET['hash'])){
        die('{"error":"true","errorMessage":"Link hash unspecified"}');
      }
      $stmt = $dbh->prepare("SELECT * FROM `links` WHERE `hash` = :hash AND `domain` = :domain");
      $stmt->bindParam(":hash", $_GET['hash']);
      $domain = (isset($_GET['domain']) ? $_GET['domain'] : 'asf.pw');
      $stmt->bindParam(":domain", $domain);
      $stmt->execute();
      if($stmt->rowCount() == 0){
        die('{"error":"true","errorMessage":"Link not found"}');
      }else{
        $link = $stmt->fetchAll()[0];
        $stmt = $dbh->prepare("SELECT COUNT(*) AS visits FROM `analytics` WHERE `page` = 'index' AND `url` = :path");
        $path = $domain . '/' . $_GET['hash'];
        $stmt->bindParam(":path", $path);
        $stmt->execute();
        if($stmt->rowCount() == 0){
          die('{"error":"true","errorMessage":"Link not found"}');
        }else{
          $visits = $stmt->fetchAll()[0]["visits"];
          die('{"error":"false","hash":"' . htmlentities($link['hash']) . '","link":"' . htmlentities($link['link']) . '","time":"' . htmlentities($link['time']) . '","visits":"' . htmlentities($visits) . '"}');
        }
      }
    }else{
      die('{"error":"true","errorMessage":"Unknown API action"}');
    }
  }else{
    die('{"error":"true","errorMessage":"No parameters set."}');
  }

?>