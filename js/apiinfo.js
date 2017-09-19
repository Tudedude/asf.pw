$(document).ready(function(){
  $("#create").click(function(event){
    event.preventDefault();
    $('html, body').animate({
      scrollTop: $("#create-docs").offset().top
    }, 1000);
  });
  $("#info").click(function(event){
    event.preventDefault();
    $('html, body').animate({
      scrollTop: $("#info-docs").offset().top
    }, 1000);
  });
  $("#head").click(function(event){
    event.preventDefault();
    $('html, body').animate({
      scrollTop: $("#top").offset().top
    }, 2000);
  });
});