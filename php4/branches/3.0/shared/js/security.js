function scramble(str, offset)
{
  if (!offset)
    offset = 1;

  var r = '';
  for(var i=0;i<str.split('').length;i++)
    r += String.fromCharCode(str.split('')[i].charCodeAt(0) + offset);
  return r
}
