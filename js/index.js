$(document).ready(function(){
  $(".errorBox").hide();
  $(".shorten").click(function(){
    clearErrors();
    if(!(/^[a-zA-Z0-9_+-.,!]+$/g.test($(".hash").val()))){
      $(".hash").toggleClass("error");
      addError("Invalid characters");
    }
    if($("[name=link]").val().length == 0){
      $("[name=link]").toggleClass("error");
      addError("Must specify a link");
    }
    if($(".hash").val().length > 32){
      $(".hash").toggleClass("error");
      addError("URL is longer than 32 characters.");
    }
    if(!hasErrors()){
      var AJAXUrl = 'https://asf.pw/create.php?hash=' + encodeURIComponent($(".hash").val()) + '&link=' + encodeURIComponent($("[name=link]").val());
      console.log(AJAXUrl);
      $.ajax({
        url: AJAXUrl
      }).done(function(data){
        console.log(data);
        var response = data;
        if(response["error"] == null || response["error"] == "false"){
          $(".link").hide(100);
          setTimeout(function(){$(".success").show(100);}, 100);
          $(".success").html('<div class="succHeader">Link created!</div><div class="createdLink"><a href="https://asf.pw/' + response['hash'] + '">https://asf.pw/' + response['hash'] + '</a></div>');
        }else{
          addError(response["errorMessage"]);
          $("." + response["field"]).toggleClass("error");
          showErrors();
        }
      });
      return;
    }
    showErrors();
  });
});
var errors = [];

function clearErrors(){
  errors = [];
  $(".error").toggleClass("error");
}

function addError(error){
  errors[errors.length] = error;
}

function hasErrors(){
  return (errors.length != 0);
}

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
