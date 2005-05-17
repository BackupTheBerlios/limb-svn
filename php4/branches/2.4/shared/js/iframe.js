onload_iframe = function()
{
  try{
  top.LAYOUT_CONTROL.onLoadPage()
  }catch(ex){}
}
add_event(window, 'load', onload_iframe)

/****************************************/
/*            resize layout             */
/****************************************/
function resize_content()
{
  try{
  var obj = document.getElementById('lo-place-center')
  obj.style.width = obj.parentNode.offsetWidth
  obj.style.height = document.body.offsetHeight
  obj.style.top = 0
  }catch(ex){}

  try{
  var left_obj = document.getElementById('lo-place-left').parentNode
  obj.style.left = left_obj.offsetWidth
  }catch(ex){}
  try{
  var right_obj = document.getElementById('lo-place-right').parentNode
  obj.style.right = right_obj.offsetWidth
  }catch(ex){}
}
document.resize_content = resize_content
add_event(window, 'load', 		resize_content)
add_event(window, 'resize', 		resize_content)
add_event(document, 'resize', 	resize_content)

var arr_actions = new Array();

function initFileUploads()
{
  var arr = document.getElementsByTagName('button')
  for(var i=0; i<arr.length; i++)
  {
    if(arr[i].className != 'file') continue

    var input = document.getElementById( arr[i].id + '_input')
    var fake_file = input.fake_file
    if(!fake_file)  fake_file = document.createElement('input')
    fake_file.type = 'file'
    fake_file.size = 1
    fake_file.className = 'file'
    fake_file.style.position = 'absolute'

    arr[i].parentNode.appendChild(fake_file)
    fake_file.style.left = get_real_offset(arr[i], 'left', true) - (fake_file.offsetWidth - arr[i].offsetWidth)
    fake_file.style.top = get_real_offset(arr[i], 'top', true)
    input.fake_file = fake_file
    fake_file.input = input
    fake_file.onchange = function()
    {
      this.input.value = this.value
      if(this.input.onchange) this.input.onchange()
    }

    var clear = document.getElementById( arr[i].id + '_clear')
    if(!clear) continue
    clear.input = input
    clear.fake_file = fake_file
    clear.onclick = function()
    {
      this.input.value = ''
    }
  }

}
/*<!--END:[ fileopen ]-->*/

add_event(window, 'load', initFileUploads)


function get_filename(path)
{
  var arr = path.split('\\')
  var fn = arr[arr.length-1]

  return fn
}
