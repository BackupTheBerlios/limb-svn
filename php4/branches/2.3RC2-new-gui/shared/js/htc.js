tmp_window_onload = window.onload
window.onload = function()
{
	if(window != top) if(top.onload_iframe) top.onload_iframe()
	
  if(tmp_window_onload) tmp_window_onload()

  try{window.CSSMAP = build_cssClasses_map(document)}catch(ex){/*alert(ex)*/}
  apply_behavior(document, document)
}
function apply_behavior(obj, parent)
{
  var arrTags = obj.getElementsByTagName('span')
  for(var i=0; i<arrTags.length; i++)
    if(arrTags[i].getAttribute('behavior'))
      behavior(arrTags[i].getAttribute('behavior'), arrTags[i], parent)
}
function behavior(class_name, obj, parent)
{
  var cl = window[class_name]
	if(!cl)return
  var target = obj.getAttribute('target')*1
  var o = obj
  if(target < 0) while(++target<=0) o = is_gecko ? o.parentNode : o.parentElement
  else if(target > 1) while(--target>=0) o = o.firstChild

  for(var i in cl.prototype) o[i] = cl.prototype[i]
  o.constructor = cl
  o.init_obj = obj
  o.constructor(parent)

  return o
}
//===========================
// [ common super method ]
//===========================
_extends = function(subclass, superclass)
{
	var target = subclass.prototype
	if(!target) target = subclass

	target._super = superclass
	for(var v in superclass.prototype)
  {
		if(!target[v])
		target[v] = superclass.prototype[v]
  }
	return subclass
}


function build_cssClasses_map(doc)
{
  var arr = []
  for(var i = 0; i<doc.styleSheets.length; i++)
  {
    var rules = doc.styleSheets[i].rules
    if(is_gecko) rules = doc.styleSheets[i].cssRules
		
    for(var j = 0; j<rules.length; j++)
      arr[rules[j].selectorText.substr(1)] = rules[j].style
  }
  return arr
}
