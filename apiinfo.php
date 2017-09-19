<!DOCTYPE html>
<html>
  <head>
    <?php
      include 'connect.php';
      include 'analytics.php';
      log_visit($dbh, 'apiinfo', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    ?>
    <title>Tudedude's Link Shortener | API</title>
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
    <link href="css/api.css" rel="stylesheet" type="text/css" />
    <script src="//cdn.tudedude.me/jquery/jquery-2.2.4.min.js"></script>
    <script async src="js/apiinfo.js"></script>
  </head>
  <body>
    <div class="pure-menu custom-restricted-width">
      <a href="#" id="head"><span class="pure-menu-heading">TLS API</span></a>
      <ul class="pure-menu-list">
        <li class="pure-menu-item" id="create"><a href="#" class="pure-menu-link">Create Links</a></li>
        <li class="pure-menu-item" id="info"><a href="#" class="pure-menu-link">Get Link Info</a></li>
        <li class="pure-menu-item" id="back"><a href="/" class="pure-menu-link">Back</a></li>
      </ul>
    </div>
    <div class="wrapper">
      <div class="page-header" id="top">Tudedude's Link Shortener API</div>
      <div class="page-content">
        <p>
           Tudedude's Link Shortener features full API support, allowing you to
           implement link shortening services into the service of your choice!
        </p>
        <p>
          The API can be accessed via HTTP GET requests, and responds with
          JSON information. There is currently a rate limit of 5 requests per
          second. If you would like to expand the rate limit for your scripts,
          there will soon be an account system, allowing you to register API
          keys with larger allowances. If you would like special allowance
          in the meantime, please e-mail me at <strong>me@tudedude.me</strong>
        </p>
        <p>
          To access the API, send a GET request to the endpoint at
          <code class="inline">https://asf.pw/api.php</code>. For more
          information, select an operation from the sidebar.
        </p>
        <div class="create-docs" id="create-docs">
          <div class="doc-header">Create Links</div>
          To create a link with the API, send a GET request to
          <code class="inline">https://asf.pw/api.php?action=create</code>.<br/>
          <div class="doc-subheader">Arguments</div>
          <table class="args-table">
            <thead>
              <tr>
                <th>Field</th>
                <th>Values</th>
                <th>Description</th>
                <th>Required</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>link</td>
                <td>String</td>
                <td>This argument is the link to redirect the shortened link to.</td>
                <td>Yes</td>
              </tr>
              <tr>
                <td>hash</td>
                <td>String</td>
                <td>The short URL to use for the shortened link. May not be longer
                    than 32 characters or use characters other than a-z, A-Z and
                    0-9, as well as <code class="inline">_+-.,!</code>.
                </td>
                <td>Yes</td>
              </tr>
              <tr>
                <td>domain</td>
                <td>Specific String</td>
                <td>This argument specifies which domain the link should be under.
                    It must be one of the following strings: <strong>asf.pw</strong>,
                    <strong>2meirl4.me</strong>,<strong>tdls.wtf</strong>,
                    <strong>sht.wtf</strong>,<strong>hotdamn.cam</strong>,
                    or <strong>jointhe.murdersuicide.club</strong>. If a domain is not
                    specified, it will default to asf.pw.
                </td>
                <td>No</td>
              </tr>
            </tbody>
          </table>
          <div class="doc-subheader">Sample Responses</div>
          <strong>Successful Creation</strong>
          <p>
            Upon successful link creation, the returned JSON object will have fields
            containing the hash that was entered into the database, the domain it was
            entered under, and an error field, set to false.
          </p>
          <code>
            <div class="code-header">https://asf.pw/api.php?action=create&hash=short&link=https://tudedude.me</div>
            HTTP/1.0 200 OK<br/>{"error":"false","hash":"short","domain":"asf.pw"}
          </code>
          <strong>Missing Required Field</strong>
          <p>
            If an API request is missing a required field, such as the hash or link,
            the returned JSON object will contain an "error" field set to true, as
            well as an "errorMessage" field containing the human-readable error
            message specifying what field is missing.
          </p>
          <code>
            <div class="code-header">https://asf.pw/api.php?action=create&hash=short</div>
            HTTP/1.0 200 OK<br/>{"error":"true","errorMessage":"Link unspecified","field":"link"}
          </code>
          <code>
            <div class="code-header">https://asf.pw/api.php?action=create&link=https://tudedude.me</div>
            HTTP/1.0 200 OK<br/>{"error":"true","errorMessage":"Link hash unspecified","field":"hash"}
          </code>
          <strong>Hash In Use</strong>
          <p>
            If a link is attempted to be created with a hash that is already in use,
            the returned JSON object will contain an "error" field set to true, as
            well as an "errorMessage" field containing a human-readable error message
            notifying the user that the hash is already in use.
          </p>
          <code>
            <div class="code-header">https://asf.pw/api.php?action=create&hash=Tudedude&link=https://tudedude.me</div>
            HTTP/1.0 200 OK<br/>{"error":"true","errorMessage":"That name is not available","field":"hash"}
          </code>
          <strong>API Rate Limit</strong>
          <p>
            Until the implementation of the upcoming API key & developer account system,
            API requests are rate-limited to 30 requests per minute per IP. This is measured by
            checking against all requests within the last 60 seconds, at the time of
            request. Exceeding this rate limit will simply result in your requests
            being filtered out until you have space for more requests. No permanent action
            will be taken unless abuse is detected. The JSON object will have an "error" field
            set to true, as well as an "errorMessage" field containing a human-readable error
            message notifying the user that your requests are currently being sent too fast.
            The server will also return a 429 request.
          </p>
          <code>
            <div class="code-header">https://asf.pw/api.php</div>
            HTTP/1.0 429 Too many requests<br/>{"error":"true","errorMessage":"You are forbidden from acessing this page [429 Too many requests]","field":"null"}
          </code>
        </div>
        <div class="info-docs" id="info-docs">
          <div class="doc-header">Get Link Info</div>
          To retrieve link info with the API, send a GET request to <code class="inline">https://asf.pw/api.php?action=info</code>.
          <div class="doc-subheader">Arguments</div>
          <table class="args-table">
            <thead>
              <tr>
                <th>Field</th>
                <th>Values</th>
                <th>Description</th>
                <th>Required</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>hash</td>
                <td>String</td>
                <td>The hash of the link to retrieve info for.</td>
                <td>Yes</td>
              </tr>
              <tr>
                <td>domain</td>
                <td>Specific String</td>
                <td>This argument specifies which domain the link should be under.
                    It must be one of the following strings: <strong>asf.pw</strong>,
                    <strong>2meirl4.me</strong>,<strong>tdls.wtf</strong>,
                    <strong>sht.wtf</strong>,<strong>hotdamn.cam</strong>,
                    or <strong>jointhe.murdersuicide.club</strong>. If a domain is not
                    specified, it will default to asf.pw.
                </td>
                <td>No</td>
              </tr>
            </tbody>
          </table>
          <div class="doc-subheader">Sample Responses</div>
          <strong>Successful Search</strong>
          <p>
            With a successful link search, the returned JSON object will have an "error"
            field, set to false, a "hash" field with the hash of the link returned, a
            "link" field with the link the hash points to, a "time" field with the
            timestamp that the link was created, and a "visits" field with the number
            of times the link has been visited.
          </p>
          <code>
            <div class="code-header">https://asf.pw/api.php?action=info&hash=Tudedude</div>
            HTTP/1.0 200 OK<br/>{"error":"false","hash":"Tudedude","link":"https://tudedude.me","time":"2017-02-26 07:13:36","visits":"3"}
          </code>
          <strong>Link Not Found</strong>
          <p>
            If an API request specifies a link hash that does not exist, or fails to
            specify the correct domain, the returned JSON object will have an "error"
            field, set to true, and an "errorMessage" field with the human-readable
            error message notifying the user that the link was not found.
          </p>
          <code>
            <div class="code-header">https://asf.pw/api.php?action=info&hash=Tudedudee</div>
            HTTP/1.0 200 OK<br/>{"error":"true","errorMessage":"Link not found"}
          </code>
          <strong>Missing Required Field</strong>
          <p>
            If an API request is missing a required field,
            the returned JSON object will contain an "error" field set to true, as
            well as an "errorMessage" field containing the human-readable error
            message specifying what field is missing.
          </p>
          <code>
            <div class="code-header">https://asf.pw/api.php?action=info</div>
            HTTP/1.0 200 OK<br/>{"error":"true","errorMessage":"Link hash unspecified"}
          </code>
        </div>
      </div>
    </div>
  </body>
</html>
