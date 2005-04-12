var agt = navigator.userAgent.toLowerCase();
var is_ie = (agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1);
var is_gecko = navigator.product == "Gecko";
var is_opera  = (agt.indexOf("opera") != -1);
var is_mac    = (agt.indexOf("mac") != -1);
var is_mac_ie = (is_ie && is_mac);
var is_win_ie = (is_ie && !is_mac);
