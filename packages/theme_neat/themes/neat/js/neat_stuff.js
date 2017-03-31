$('div.col-content').filter(function() {
  return $.trim($(this).html()) === ''
}).remove()

//Add custom colours tp columns
$('div').each(function(){
    if($(this).hasClass('white')) {
        $(this).parent('div').addClass("col-white");
    } 
});
$('div').each(function(){
    if($(this).hasClass('light-grey')) {
        $(this).parent('div').addClass("col-light-grey");
    } 
});
$('div').each(function(){
    if($(this).hasClass('mid-grey')) {
        $(this).parent('div').addClass("col-mid-grey");
    } 
});
$('div').each(function(){
    if($(this).hasClass('dark-grey')) {
        $(this).parent('div').addClass("col-dark-grey");
    } 
});
$('div').each(function(){
    if($(this).hasClass('light-green')) {
        $(this).parent('div').addClass("col-light-green");
    } 
});
$('div').each(function(){
    if($(this).hasClass('mid-green')) {
        $(this).parent('div').addClass("col-mid-green");
    } 
});