<?php
  include 'connect.php';
  include 'analytics.php';
  session_start();

  $filter = false;
  $blacklist = true;

  include 'filter.php';
  if(isset($_SERVER['REQUEST_URI']) && strlen($_SERVER['REQUEST_URI']) > 1){
    $stmt = $dbh->prepare("SELECT link FROM links WHERE hash = :hash");
    $uri = substr($_SERVER['REQUEST_URI'], 1);
    $stmt->bindParam(":hash", $uri);
    $stmt->execute();
    if($stmt->rowCount() == 1){
      foreach($stmt->fetchAll() as $row){
        $link = parse_url($row['link'], PHP_URL_SCHEME) === null ? 'http://' . $row['link'] : $row['link'];
        header('Location: ' . $link);
        log_visit($dbh, "index", $_SERVER['REQUEST_URI']);
        die();
      }
    }
    header("HTTP/1.1 404 Not found");
    log_visit($dbh, "404", $_SERVER['REQUEST_URI']);
    $_SESSION['redirected'] = true;
    include("./404.php");
    die();
  }
  if(!isset($_SESSION['redirected']) || $_SESSION['redirected'] != true){
    log_visit($dbh, "index", $_SERVER['REQUEST_URI']);
  }
  $_SESSION['redirected'] = false;
  function genHash($length){
    $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '_', '+', '-', '.', ',', '!');
    $out = "";
    for($i = 0; $i < $length; $i++){
      $out .= $alphabet[rand(0, sizeof($alphabet)-1)];
    }
    return $out;
  }
  $defHash = genHash(5);
  $stmt = $dbh->prepare("SELECT count(*) FROM links");
  $stmt->execute();
  $num = $stmt->fetchAll()[0]["count(*)"];
?>

<html>
  <head>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta property="og:title" content="Tudedude's Link Shortener" />
    <meta name="description" content="Easily shorten links with custom URLs and no ads" />
    <meta property="og:description" content="Easily shorten links with custom URLs and no ads" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://asf.pw" />
    <meta property="og:image" content="https://asf.pw/preview.png" />
    <link rel="shortcut icon" href="https://asf.pw/favicon.ico" type="image/x-icon" />

    <!-- asf.pw -->
    <meta name="google-site-verification" content="bG3gUrrflN1JZAAZTxeVBWCOixQSJk9K7ggAxu1WjT4" />

    <!-- https://asf.pw -->
    <meta name="google-site-verification" content="a-AzkLonrUu3eBvWNd67eGRd8dD_KGojl-ALKyzkPKc" />

    <!-- http://asf.pw -->
    <meta name="google-site-verification" content="a-AzkLonrUu3eBvWNd67eGRd8dD_KGojl-ALKyzkPKc" />

    <!-- https://www.asf.pw -->
    <meta name="google-site-verification" content="a-AzkLonrUu3eBvWNd67eGRd8dD_KGojl-ALKyzkPKc" />

    <!-- http://www.asf.pw -->
    <meta name="google-site-verification" content="a-AzkLonrUu3eBvWNd67eGRd8dD_KGojl-ALKyzkPKc" />
    <title>Tudedude's Link Shortener</title>
    <script src="//cdn.tudedude.me/jquery/jquery-2.2.4.min.js"></script>
    <script async src="js/index.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/index.min.css"/>
  </head>
  <body>
    <div class="rapper"><!-- will the real slim shady please stand up -->
      <header>
        <div class="headertext">Tudedude's Link Shortener</div>
        <div class="subtext"><?= $num ?> links shortened so far</div>
      </header>
      <div class="content">
        <div class="conHeader">Make A Short Link</div>
        <div class="errorBox"><ul><li>JavaScript must be enabled.</li></ul></div><br/>
        <div class="link">
          <select style="padding: 9px;font-family: 'Lato', sans-serif;border-radius: 10px;border: 2px solid #ddd;outline: none;">
            <option value="asf.pw">https://asf.pw/</option>
            <option value="2meirl4.me">https://2meirl4.me/</option>
            <option value="hotdamn.cam">https://hotdamn.cam/</option>
            <option value="sht.wtf">https://sht.wtf/</option>
            <option value="tdls.wtf">https://tdls.wtf/</option>
          </select>
          <input type="text" name="hash" class="hash" placeholder="<?= $defHash ?>" value="<?= $defHash ?>"><br>
          <input type="text" name="link" placeholder="Link To Shorten" class="link"/><br/>
          <button name="submit" class="shorten">Go!</button>
        </div>
        <div class="success"></div>
      </div>
      <footer>
        <a href="//tudedude.me">Tudedude</a> &copy; <?= date("Y"); ?> | <a href="//github.com/Tudedude/asf.pw">GitHub</a> <span class="float-right" style="display:none"><span class="warning">Warning:</span> You do not have JavaScript enabled, which is used in some styling. Things may not look quite right.</span>
      </footer>
    </div>
    <noscript id="deferred-styles">
      <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Lato"/>
    </noscript>
    <script>
      var loadDeferredStyles = function() {
        var addStylesNode = document.getElementById("deferred-styles");
        var replacement = document.createElement("div");
        replacement.innerHTML = addStylesNode.textContent;
        document.body.appendChild(replacement)
        addStylesNode.parentElement.removeChild(addStylesNode);
      };
      var raf = requestAnimationFrame || mozRequestAnimationFrame ||
          webkitRequestAnimationFrame || msRequestAnimationFrame;
      if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
      else window.addEventListener('load', loadDeferredStyles);
    </script>
  </body>
</html>
