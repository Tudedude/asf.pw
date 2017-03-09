
  <!--
   * ASF.PW index page
   * asf.pw and all related files and scripts are 
   * developed by Tudedude/Carson Faatz, copyright 2017-
   * and are released under the WTFPL2. Full license
   * text can be found under ./LICENSE.md
   -->

<html>
  <head>
    <?php

      // initialize connection to the database
      include 'connect.php';

      // if a request URI is set... (e.g. /index.php or /Google)
      if(isset($_SERVER['REQUEST_URI'])){

        // initialize a SELECT SQL statement, to check if the specified shortlink is valid
        $stmt = $dbh->prepare("SELECT link FROM links WHERE hash = :hash");

        // parse specified URI without the leading slash
        $uri = substr($_SERVER['REQUEST_URI'], 1);

        // bind specified uri to query
        $stmt->bindParam(":hash", $uri);

        // execute query
        $stmt->execute();

        // if a singular link is returned...
        if($stmt->rowCount() == 1){

          // set row equal to the first (and only) result returned
          $row = $stmt->fetchAll[0];

          // add http:// to the link we are going to send them to if there is no URL scheme specified
          $link = parse_url($row['link'], PHP_URL_SCHEME) === null ? 'http://' . $row['link'] : $row['link'];

          // specify 301 redirect header to the target link
          header('Location: ' . $link);

          // stop script execution; we're done here
          die();

        }

      }

      /**
       * A function to generate a random URL with the allowed characters.
       * a length must be specified, and the default should normally be 5.
       */
      function genHash($length){

        // the array of allowed characters.
        $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '_', '+', '-', '.', ',', '!');

        // initialize a string to return
        $out = "";

        // grab a random character until the length is satisfied
        for($i = 0; $i < $length; $i++){

          // grab a random character and append it to the output string
          $out .= $alphabet[rand(0, sizeof($alphabet)-1)];

        }

        // return the generated string
        return $out;
      }

      // specify a pre-generated custom URL with length 5
      $defHash = genHash(5);

      // prepare a SELECT statement to count the number of links shortened
      $stmt = $dbh->prepare("SELECT count(*) FROM links");

      // query the server
      $stmt->execute();

      // set a variable with the number of links shortened
      $num = $stmt->fetchAll()[0]["count(*)"];
    ?>

    <title>Tudedude's Link Shortener</title>
    <link rel="stylesheet" type="text/css" href="css/index.css"/>
    <script src="//cdn.tudedude.me/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/index.js"></script>
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
          https://asf.pw/<input type="text" name="hash" class="hash" placeholder="<?= $defHash ?>" value="<?= $defHash ?>"><br>
          <input type="text" name="link" placeholder="Link To Shorten" class="link"/><br/>
          <button name="submit" class="shorten">Go!</button>
        </div>
        <div class="success"></div>
      </div>
      <footer>
        <a href="//tudedude.me">Tudedude</a> &copy; <?= date("Y"); ?><span class="float-right" style="display:none"><span class="warning">Warning:</span> You do not have JavaScript enabled, which is used in some styling. Things may not look quite right.</span>
      </footer>
    </div>
  </body>
</html>
