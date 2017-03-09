<?php

  /**
   * ASF.PW creation script
   * asf.pw and all related files and scripts are developed
   * by Tudedude/Carson Faatz, copyright 2017-
   * and are released under the WTFPL2. Full license
   * text can be found under ./LICENSE.md
   */
  
  // include the filtering script
  include 'filter.php';

  // connect to the database and initialize the $dbh variable
  include 'connect.php';

  // notify the client that this will be a JSON file
  header('Content-Type: text/json');

  // make sure the correct variables are set
  if(isset($_GET['hash']) && isset($_GET['link'])){

    // make sure the specified custom URL is valid (only has a-z, A-Z, 0-9, and allowed special characters:
    //                                                                          _, +, -, ., ,, and !
    if(preg_match('/^[a-zA-Z0-9_+-.,!]+$/', $_GET['hash'])){

      // prepare a SQL statement and bind the custom URL variable to it.
      // this query is meant to check if the URL already exists.
      $stmt = $dbh->prepare("SELECT ip FROM links WHERE hash = :hash");
      $stmt->bindParam(":hash", $_GET['hash']);

      // execute the query
      $stmt->execute();
      
      // if the URL is not in the database...
      if($stmt->rowCount() == 0){

        // prepare a SQL insert statement and bind the user's IP, the link, and the custom URL
        $stmt = $dbh->prepare("INSERT INTO `links`(`hash`, `time`, `ip`, `link`) VALUES (:hash, now(), :ip, :link)");
        $stmt->bindParam(":ip", $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(":link", $_GET['link']);
        $stmt->bindParam(":hash", $_GET['hash']);

        // insert the URL into the database
        $stmt->execute();

        // echo the successfully created URL
        die('{"error":"false","hash":"' . htmlentities($_GET['hash']) . '"}');

      }else{

        // the URL is not available, throw an error message
        die('{"error":"true","errorMessage":"That name is not available","field":"hash"}');

      }

    }else{

      // the URL has invalid characters, throw an error message
      die('{"error":"true","errorMessage":"Invalid characters in name","field":"hash"}');

    }

  }else{

    // invalid parameters are set, throw an error message
    die('{"error":"true","errorMessage":"Invalid request parameters","field":"null"}');

  }
?>
