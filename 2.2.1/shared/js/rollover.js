function rollover_preload_images() 
{ 
  var d = document; 
  if(d.images)
  { 
  	if(!d.rollover_p) 
  		d.rollover_p = new Array();
    var i,j = d.rollover_p.length, a = rollover_preload_images.arguments;
    
    for(i=0; i<a.length; i++)
    	if (a[i].indexOf("#")!=0)
    	{ 
    		d.rollover_p[j] = new Image; 
    		d.rollover_p[j++].src = a[i];
    	}
  }
}

function rollover_swap_restore() 
{ 
  var i,x,a = document.rollover_sr; 
  for(i=0; a && i<a.length && (x=a[i]) && x.oSrc;i++) 
  	x.src = x.oSrc;
}

function rollover_find_obj(n, d) 
{ 
  var p,i,x;  if(!d) d=document; 
  if((p=n.indexOf("?"))>0&&parent.frames.length) 
  {
    d = parent.frames[n.substring(p+1)].document; 
    n = n.substring(0,p);
  }
  
  if(!(x=d[n])&&d.all) 
  	x = d.all[n]; 
  	
  for (i=0;!x&&i<d.forms.length;i++) 
  	x = d.forms[i][n];
  	
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) 
  	x = rollover_find_obj(n,d.layers[i].document);
  	
  if(!x && d.getElementById) 
  	x = d.getElementById(n); 
  
  return x;
}

function rollover_swap() 
{
  var i,j=0,x,a = rollover_swap.arguments; 
  
  document.rollover_sr=new Array; 
  
  for(i=0;i<(a.length-2);i+=3)
   if ((x=rollover_find_obj(a[i]))!=null)
   {
   	document.rollover_sr[j++] = x; 
   	if(!x.oSrc) 
   		x.oSrc = x.src; 
   	x.src=a[i+2];
   }
}