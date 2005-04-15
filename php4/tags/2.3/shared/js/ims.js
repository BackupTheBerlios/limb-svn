function get_msn_status_icon(email, icon_set)
{
  if (!icon_set)
    icon_set = 3;

  st = "<img alt='" +
       email +
       "' title='" +
       email +
       "' onerror='this.onerror=null;this.src=\"http://www.the-server.net/osi" +
       icon_set +
       "/image/msnunknown.gif\";' src='http://www.the-server.net/osi" +
       icon_set +
       "/msn/" +
       email +
       "' align='absMiddle' border='0' />"

  return st;
}

function get_icq_status_icon(icq_number, icon_set)
{
  if (!icon_set)
    icon_set = 5;

  st = "<img alt='" +
       icq_number +
       "' title='" +
       icq_number +
       "' src='http://online.mirabilis.com/scripts/online.dll?icq=" +
       icq_number +
       "&img=" +
       icon_set +
       "' align='absMiddle' border='0' />"

  return st;
}

