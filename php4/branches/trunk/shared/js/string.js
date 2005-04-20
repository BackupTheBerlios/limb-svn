
String.prototype.trim = function(){
     var r=/^\s+|\s+$/;
     return this.replace(r,'');
}

function trim(str)
{
  var r=/^\s+|\s+$/;
  return str.replace(r,'');
}
