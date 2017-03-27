$('div.col-content').filter(function() {
  return $.trim($(this).html()) === ''
}).remove()


