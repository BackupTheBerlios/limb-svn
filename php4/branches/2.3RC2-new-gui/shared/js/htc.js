tmp_window_onload = window.onload
window.onload = function()
{
  if(window != top)
    if(top.onload_iframe)
      top.onload_iframe()
  if(tmp_window_onload) tmp_window_onload()

  apply_behavior()
}
function apply_behavior()
{
  CLASS_MAP = build_class_map(document)
  for(var v in CLASS_MAP)
  {
      var tagName = v.substr(v.lastIndexOf('_')+1)
      if(!tagName)continue
      var arrTags = document.getElementsByTagName(tagName)
      var className = v
      for(var i in arrTags)
      {
        if(arrTags[i].className == className)
        {
          var cl = CLASS_MAP[className]
          if(cl)
          {
            for(var j in cl.prototype)
            {
              arrTags[i][j] = cl.prototype[j]
            }
            arrTags[i].constructor = cl
            arrTags[i].constructor()
          }
        }
      }
  }
}
function build_class_map(doc)
{
  var arr = []
  for(var i = 0; i<doc.styleSheets.length; i++)
  {
    var rules = doc.styleSheets[i].rules
    if(is_gecko) rules = doc.styleSheets[i].cssRules

    for(var j = 0; j<rules.length; j++)
    {
      var rule = rules[j]
      if(rule.selectorText.indexOf('htc_')!=-1)
      {
        var name = rule.selectorText.substr(1)
        if(name.indexOf(' ')!=-1) name = name.substr(name.indexOf(' ')+2)
        if(window[name]) arr[name] = window[name]
      }
    }
  }

  return arr
}

