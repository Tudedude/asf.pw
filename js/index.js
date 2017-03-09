/**
 * ASF.PW JavaScript utilites
 * asf.pw and all related files and scripts are developed
 * by Tudedude/Carson Faatz, copyright 2017-
 * and are released under the WTFPL2. Full license
 * text can be found under ./LICENSE.md
 */

// once the document is loaded, do the following
$(document).ready(function(){

  // hide the error box, hiding the JavaScript warning
  $(".errorBox").hide();

  // when the shorten button is clicked...
  $(".shorten").click(function(){

    // clear the errors in the error box
    clearErrors();

    // make sure the specified URL has only valid characters
    // this is validated on the server, so this is just a redundant
    // check
    if(!(/^[a-zA-Z0-9_+-.,!]+$/g.test($(".hash").val()))){

      // throw error if there are invalid characters
      $(".hash").toggleClass("error");
      addError("Invalid characters");

    }

    // make sure a link is specified, and if it isn't, throw an error
    if($("[name=link]").val().length == 0){
      $("[name=link]").toggleClass("error");
      addError("Must specify a link");
    }

    // if no errors have been encountered, prepare and send the request
    if(!hasErrors()){

      // prepare URL
      var AJAXUrl = 'https://asf.pw/create.php?hash=' + encodeURIComponent($(".hash").val()) + '&link=' + encodeURIComponent($("[name=link]").val());
      $.ajax({
        url: AJAXUrl
      }).done(function(data){

        // once the request is finished, begin processing
        var response = data;

        // if there were no errors, show the created link and notify them that it was successful
        if(response["error"] == null || response["error"] == "false"){
          $(".link").hide(100);
          setTimeout(function(){$(".success").show(100);}, 100);
          $(".success").html('<div class="succHeader">Link created!</div><div class="createdLink"><a href="https://asf.pw/' + response['hash'] + '">https://asf.pw/' + response['hash'] + '</a></div>');
        }else{

          // if there was an error, add it to the error box
          addError(response["errorMessage"]);

          // toggle the error class on the specified field
          $("." + response["field"]).toggleClass("error");

          showErrors();
        }
      });
      return;
    }
    showErrors();
  });
});

// the array where any errors encountered will be stored
var errors = [];


// reset the error array and remove any error classes on fields
function clearErrors(){
  errors = [];
  $(".error").toggleClass("error");
}

// add error to array (specified this way for readability)
function addError(error){
  errors[errors.length] = error;
}

// check if errors have occurred (specified this way for readability)
function hasErrors(){
  return (errors.length != 0);
}

// create list and add it to the error box, showing it and adjusting width
function showErrors(){
  var list = "<ul>";
  for(var i = 0; i < errors.length; i++){
    list += "<li>" + errors[i] + "</li>";
  }
  list += "</ul>";
  $(".errorBox").html(list);
  $(".errorBox").css('width', $(".link").width());
  $(".errorBox").show();
}
