
<?php
  session_start();

  $filter = true;
  $blacklist = true;

  include 'filter.php';
  include 'connect.php';
  include 'analytics.php';
  header('Content-Type: text/json');
  log_visit($dbh, "create", $_SERVER['REQUEST_URI']);
  if(isset($_GET['hash']) && isset($_GET['link'])){
    if(preg_match('/^[a-zA-Z0-9_+-.,!]+$/', $_GET['hash'])){
      if(strlen($_GET['hash']) !== 0){
        $stmt = $dbh->prepare("SELECT ip FROM links WHERE hash = :hash");
        $stmt->bindParam(":hash", $_GET['hash']);
        $stmt->execute();
        if($stmt->rowCount() == 0){
          $stmt = $dbh->prepare("INSERT INTO `links`(`hash`, `time`, `ip`, `link`, `domain`) VALUES (:hash, now(), :ip, :link, :domain)");
          $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
          $stmt->bindParam(":link", $_GET['link']);
          $stmt->bindParam(":hash", $_GET['hash']);
          $stmt->bindParam(":domain", (isset($_GET['domain']) ? $_GET['domain'] : 'https://asf.pw/'));
          $stmt->execute();
          die('{"error":"false","hash":"' . htmlentities($_GET['hash']) . '"}');
        }else{
          die('{"error":"true","errorMessage":"That name is not available","field":"hash"}');
        }
      }else{
        die('{"error":"true","errorMessage":"URL is longer than 32 characters","field":"hash"}');
      }
    }else{
      die('{"error":"true","errorMessage":"Invalid characters in name","field":"hash"}');
    }
  }else{
    die('{"error":"true","errorMessage":"Invalid request parameters","field":"null"}');
  }
?>
