<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

/*returns
  $client_info['architecture']
  $client_info['os']
  $client_info['os_ver']
  $client_info['os_vendor']
  $client_info['browser']
  $client_info['browser_ver']
  $client_info['browser_subver']
  $client_info['browser_sup']
  $client_info['lang']
  $client_info['sub_lang']
  $client_info['http_lang']
  $client_info['ip']
  $client_info['render_engine']
  $client_info['render_engine_ver']
*/
function get_client_info()
{
  $arch = '';
  $browser = '';
  $browser_ver = '';
  $browser_subver = '';
  $browser_sup = '';
  $http_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
  $ip = '';
  $render_engine = '';
  $render_engine_ver = '';

  $agentstring = $_SERVER['HTTP_USER_AGENT'];

  $luser_agent0 = explode('(', $agentstring);
  $luser_agent1 = isset($luser_agent0[1]) ? explode(')', $luser_agent0[1]) : '';
  $luser_agent2 = isset($luser_agent1[0]) ? explode(';', $luser_agent1[0]) : '';

  $chkvers = isset($luser_agent0[0]) ? explode('/', $luser_agent0[0]) : '';
  $ns4_minvers = isset($chkvers[1]) ? explode('.', $chkvers[1]) : '';
  $moz_extra_ver = explode(' ', $agentstring);
  $moz_extra_ver1 = isset($moz_extra_ver[8]) ? explode('/', $moz_extra_ver[8]) : '';
  $moz_extra_ver2 = isset($moz_extra_ver[7]) ? explode('/', $moz_extra_ver[7]) : '';
  $moz_extra_ver3 = isset($moz_extra_ver[6]) ? explode('/', $moz_extra_ver[6]) : '';
  $moz_rv = isset($luser_agent2[4]) ?  explode(':', $luser_agent2[4]) : '';

  $os_array = array
  (
    array('Linux', 'Linux'),
    array('FreeBSD', 'FreeBSD'),
    array('OpenBSD', 'OpenBSD'),
    array('NetBSD', 'NetBSD'),
    array('Windows 2000', 'Windows','2000','Microsoft'),
    array('Windows NT 5.0','Windows','2000','Microsoft'),
    array('Windows NT 5.1','Windows','XP','Microsoft'),
    array('Windows NT 5.2','Windows','2003','Microsoft'),
    array('Windows NT','Windows','NT 4.0','Microsoft'),
    array('Windows XP','Windows','XP','Microsoft'),
    array('Windows 95','Windows','95','Microsoft'),
    array('Windows 98','Windows','98','Microsoft'),
    array('Windows ME','Windows','ME','Microsoft'),
    array('WinNT4.0','Windows','NT 4.0','Microsoft'),
    array('Win98','Windows','98','Microsoft'),
    array('Win95','Windows','95','Microsoft'),
    array('mac os x','MacOS','X','Apple'),
    array('MacOS X','MacOS','X','Apple'),
    array('Mac_PowerPC','MacOS','','Apple'),
    array('Macintosh','MacOS','','Apple'),
    array('macintosh','MacOS','','Apple'),
    array('OS/2','OS/2','','IBM'),
    array('SunOS 5.7','Solaris','5.7','Sun'),
    array('SunOS 5.8','Solaris','5.8','Sun'),
    array('SunOS 5.9','Solaris','5.9','Sun'),
    array('SunOS','Solaris','','Sun'),
    array('AIX','AIX','','IBM'),
    array('HP-UX','HP-UX','','HP'),
    array('IRIX','IRIX','','Sgi'),
    array('BeOS','BeOS','','Be'),
    array('Unix','UNIX','',''),
    array('unix_sv','UNIX','','SCO'),
    array('X11','UNIX','','')
  );

  $arch_array = array(
    0 => array('i386', 'i386'),
    1 => array('i486', 'i486'),
    2 => array('i586', 'i586'),
    3 => array('i686', 'i686'),
    4 => array('PPC', 'PowerPC'),
    5 => array('sun4u', 'UltraSPARC'),
    6 => array('sun4m', 'MicroSPARC'),
    7 => array('PPC64', 'PowerPC64'),
    8 => array('ARM', 'ARM'),
    9 => array('IA64', 'Itamium'),
    10 => array('x86-64', 'x86-64'),
    11 => array('PA-RISC', 'PA-RISC'),
    12 => array('Alpha', 'Alpha'),
    13 => array('ppc', 'PowerPC'),
    14 => array('r4000', 'MIPS')
  );

  function architecture_lookup($arg1, $arch_array)
  {
    for ($i=0; $i<=sizeof($arch_array); $i++)
    {
      if(isset($arch_array[$i]) && (strpos($arg1, $arch_array[$i][0]) || $arg1 == $arch_array[$i][0]))
        return $arch_array[$i][1];
    }
  }

  function os_lookup($arg1, $os_array)
  {
    for ($i=0; $i <= sizeof($os_array); $i++)
    {
      if ((strpos($arg1, $os_array[$i][0])) || ($arg1 == $os_array[$i][0]))
      {
        $os = $os_array[$i][1];

        if ($os_array[$i][2] != '')
          $os_ver = $os_array[$i][2];

        if ($os_array[$i][3] != '')
          $os_vendor = $os_array[$i][3];

        return array($os, $os_ver, $os_vendor);
      }
    }
  }

  //detect Mozilla, Netscape6+ or Phoenix
  if (($chkvers[0] == 'Mozilla') && ($chkvers[1] == '5.0 ') && ($luser_agent2[0] != 'compatible') && ($moz_extra_ver3[0] != 'Galeon'))
  {
    $fbird_extra_ver1 = isset($moz_extra_ver[10]) ? explode('/', $moz_extra_ver[10]) : array(); //Firebird detection
    if(isset($fbird_extra_ver1[0]) && $fbird_extra_ver1[0] == 'Firebird')
    {
      $browser = 'Firebird';
      $browser_ver = $fbird_extra_ver1[1];
      $render_engine = $moz_extra_ver1[0];
      $render_engine_ver = $moz_extra_ver1[1];
    }

    $phoenix_extra_ver1 = isset($moz_extra_ver[9]) ? explode('/', $moz_extra_ver[9]) : array(); //Phoenix detection
    if(isset($phoenix_extra_ver1[0]) && $phoenix_extra_ver1[0] == 'Phoenix')
    {
      $browser = 'Phoenix';
      $browser_ver = $phoenix_extra_ver1[1];
      $render_engine = $moz_extra_ver1[0];
      $render_engine_ver = $moz_extra_ver1[1];
    }

    $os = os_lookup($luser_agent2[2], $os_array);
    if (($os == null) && ($luser_agent2[0] == 'Macintosh'))
      $os[0] = 'MacOS';

    $arch = architecture_lookup($luser_agent2[2], $arch_array);

    switch($moz_extra_ver1[0])
    {
      case 'Netscape':
        $browser = 'Netscape';
        $browser_ver = $moz_extra_ver1[1];
      break;
      case 'Netscape6':
        $browser = 'Netscape';
        $browser_ver = $moz_extra_ver1[1];
      break;
      case 'Phoenix':
        $browser = 'Phoenix';
        $browser_ver = $moz_extra_ver1[1];
      break;
      default:case 'Netscape':
        $browser = 'Netscape';
        $browser_ver = $moz_extra_ver1[1];
      break;
      case 'Epiphany':
        $browser = 'Epiphany';
        $browser_ver = $moz_extra_ver1[1];
      break;
      default:
        switch ($moz_extra_ver2[0])
        {
          case 'Netscape':
            $browser = 'Netscape';
            $browser_ver = $moz_extra_ver2[1];
          break;
          case 'Netscape6':
            $browser = 'Netscape';
            $browser_ver = $moz_extra_ver2[1];
          break;
          case 'Phoenix':
            $browser = 'Phoenix';
            $browser_ver = $moz_extra_ver2[1];
          break;
          default:
            if($browser == '')
            {
              $browser = 'Mozilla';
              $browser_ver = $moz_rv[1];
            }
        }
    }
    if($moz_extra_ver1[0] == 'Debian')
    {
      $os[2] = 'Debian';
      $browser_subver1 = explode('-', $moz_extra_ver1[1]);
      $browser_subver = $browser_subver1[1];
      $browser_sup = 'Debian';
    }

    if ($moz_extra_ver3[0] == 'Gecko')
    {
      $render_engine='Gecko';
      $render_engine_ver=$moz_extra_ver3[1];
    }
    if ($moz_extra_ver2[0] == 'Gecko')
    {
      $render_engine='Gecko';
      $render_engine_ver=$moz_extra_ver2[1];
    }
    if ($moz_extra_ver1[0] == 'Gecko')
    {
      $render_engine='Gecko';
      $render_engine_ver=$moz_extra_ver1[1];
    }

    $lang1 = explode('-', $luser_agent2[3]);
    $lang[0] = $lang1[0];
    if ($lang1[1] != null)
      $lang[1] = $lang1[1];

    if (strpos($agentstring, 'Opera'))
    {
      $arch = architecture_lookup($luser_agent2[0], $arch_array);
      $os = os_lookup($luser_agent2[0], $os_array);

      $browser = 'Opera';
      $render_engine = 'Opera';
      $browser_ver = $moz_extra_ver[6];
      $render_engine_ver = $moz_extra_ver[6];
    }
  }//detect Microsoft Internet Explorer and Opera in MSIE mode
  elseif (($chkvers[0] == 'Mozilla') && ($chkvers[1] == '4.0 ') && ($luser_agent2[0] == 'compatible') && (strpos($luser_agent2[1],'MSIE')))
  {
    if(strpos($agentstring, 'Opera'))//detect Opera in MSIE mode
    {
      $arch = architecture_lookup($luser_agent2[2], $arch_array);
      $os = os_lookup($luser_agent2[2], $os_array);

      $browser = 'Opera';
      if ($os == null)
        $os = os_lookup($luser_agent2[3], $os_array);

      $render_engine = 'Opera';
      if ($moz_extra_ver[9] != 'Opera')
      {
        if ($moz_extra_ver[9] != '')
        {
          $render_engine_ver = $moz_extra_ver[9];
          $browser_ver = $moz_extra_ver[9];
        }
        else
        {
          $render_engine_ver = $moz_extra_ver[8];
          $browser_ver = $moz_extra_ver[8];
        }
      }
      else
      {
        $render_engine_ver = $moz_extra_ver[10];
        $browser_ver = $moz_extra_ver[10];
      }
    }
    else
    {
      $os = os_lookup($luser_agent2[2], $os_array);

      $browser = 'Internet Explorer';
      $render_engine = 'MSIE';
      $msie_ver = explode(' ', $luser_agent2[1]);
      $render_engine_ver = $msie_ver[2];
      $browser_ver = $msie_ver[2];
      if (is_array($moz_rv) && isset($moz_rv[0]) && $moz_rv[0] == ' TUCOWS')
        $browser_sup = 'Tucows';

      if(isset($moz_extra_ver[7]))
        switch($moz_extra_ver[7])
        {
          case '{Tiscali})':
            $browser_sup='Tiscali';
          break;
          case 'Avant':
            $browser='Avant Browser';
            $browser_ver = null;
          break;
          case 'MyIE2;':
            $browser = 'MyIE';
            $browser_ver = '2';
          break;
          case 'MyIE2)':
            $browser = 'MyIE';
            $browser_ver = '2';
          break;
          case 'BurntMail':
            $browser = 'BurntMail';
            $browser_ver = $moz_extra_ver[9];
          break;
          case 'FTDv3':
            $browser = 'FTD';
            $browser_ver = '3';
          break;
        }

      if ($os == null) //detect the os if MSN or Compuserve is used.
        $os = os_lookup($luser_agent2[3], $os_array);

      //assume that if MacOS is used that the PPC architecture is used
      //this would probaly break 68k detection.
      if ($os == 'MacOS')
        $arch = 'PPC';
    }
  }//detect konqueror
  elseif (($chkvers[0] == 'Mozilla') && ($chkvers[1] == '5.0 ') && ($luser_agent2[0] == 'compatible'))
  {
    $konq_vers = explode('/',$luser_agent2[1]);
    if ($konq_vers[0] == ' Konqueror')
    {
      $os = os_lookup($luser_agent2[2], $os_array);

      $browser = 'Konqueror';
      $browser_ver = $konq_vers[1];
      $render_engine = 'KHTML';

      //konqueror shows the kernel version, use it!
      //probaly also usable for FreeBSD?
      if (($os[0] == 'Linux') || ($os[0] == 'FreeBSD'))
      {
        $konq_os = explode(' ', $luser_agent2[2]);
        $os[1] = $konq_os[2];
        if (strpos($konq_os[2],'gentoo'))
          $os[2] = 'Gentoo';
      }
      $arch = architecture_lookup($luser_agent2[4], $arch_array);

      $lang1 = explode('-', $luser_agent2[5]);
      $lang2 = explode(',', $lang1[0]);
      $lang3 = explode('_', $lang2[0]);
      $lang[0] = $lang3[0];
      if ($lang3[1] != null)
        $lang[1] = $lang3[1];
      else
        $browser = 'Mozilla 5.0 compatible';
    }
  }//detect netscape4.x
  elseif (($chkvers[0] == 'Mozilla') && ($ns4_minvers[0] == '4') && ($ns4_minvers[1] != '0 '))
  {
    $arch = architecture_lookup($luser_agent2[2], $arch_array);
    $os = os_lookup($luser_agent2[2], $os_array);

    $browser = 'Netscape';
    $ns4_minvers1 = explode(' ', $ns4_minvers[1]);
    $browser_ver = "4.$ns4_minvers1[0]";

    if ($os[0] == null)
      $os = os_lookup($luser_agent2[0], $os_array);

    if ($http_lang == '')
    {
      $ns4_lang[0] = '[en]';
      $ns4_lang[1] = '[nl]';
      for ($i=0; $i <= sizeof($ns4_lang); $i++)
      {
        if (strpos($agentstring,$ns4_lang[$i]))
          $lang[0] = $ns4_lang[$i];
      }
    }
    if (strpos($agentstring,'Opera'))
    {
      $browser = 'Opera';
      $arch = architecture_lookup($luser_agent2[0], $arch_array);
    }
  }//detect Lynx
  elseif ($chkvers[0] == 'Lynx')
  {
    $browser = 'Lynx';
    $render_engine = 'Lynx';
    $lynx_ver = explode(' ',$chkvers[1]);
    $browser_ver = $lynx_ver[0];
    $render_engine_ver = $lynx_ver[0];
  }//detect Links and ELinks
  elseif (($chkvers[0] == 'Links ') || ($chkvers[0] == 'ELinks '))
  {
    $arch = architecture_lookup($luser_agent2[1], $arch_array);

    switch ($chkvers[0])
    {
      case 'Links ':
        $browser = 'Links';
        $render_engine = 'Links';
        break;
      case 'ELinks ':
        $browser = 'ELinks';
        $render_engine = 'ELinks';
        break;
    }
    $browser_ver = $luser_agent2[0];
    $render_engine_ver = $luser_agent2[0];
    $links_os = explode(' ', $luser_agent2[1]);
    $os[0] = $links_os[1];
    $os[1] = $links_os[2];
  }//detect java
  elseif ($chkvers[0] == 'Java')
  {
    $browser = 'Java';
    $browser_ver = $chkvers[1];
    $render_engine = 'Java';
    $render_engine_ver = $chkvers[1];
  }//detect w3m
  elseif ($chkvers[0] == 'w3m')
  {
    $browser = 'w3m';
    $render_engine = 'w3m';
    $browser_ver = $chkvers[1];
    $render_engine_ver = $chkvers[1];
  }//detect NCSA Mosaic
  elseif (($chkvers[0] == 'ncsa mosaic') || ($chkvers[0] == 'NCSA_Mosaic'))
  {
    $arch = architecture_lookup($luser_agent2[1], $arch_array);
    $browser = 'NCSA Mosaic';
    $render_engine = 'mosaic';
    $mosaic_ver = explode(' ',$chkvers[1]);
    $browser_ver = $mosaic_ver[0];
    $render_engine_ver = $mosaic_ver[0];
    $browser_sup = 'NCSA';
    $mosaic_os = explode(' ', $luser_agent2[1]);
    $os = os_lookup($mosaic_os[0], $os_array);
    $os[1] = $mosaic_os[1];
  }//detect opera in native mode
  elseif ($chkvers[0] == 'Opera')
  {
    $arch = architecture_lookup($luser_agent2[0], $arch_array);
    $os = os_lookup($luser_agent2[0], $os_array);
    $browser = 'Opera';
    $render_engine = 'Opera';
    $opera_browser_ver = explode(' ',$chkvers[1]);
    $browser_ver = $opera_browser_ver[0];
    $render_engine_ver = $opera_browser_ver[0];
    if ($os[0] == '')
    {
      $os = os_lookup($moz_extra_ver[1], $os_array);
      if ($os[0] == 'Linux')
        $os[1] = $moz_extra_ver[2];
    }
  }//detect fetch
  elseif ($moz_extra_ver[0] == 'fetch')
  {
    $browser = $moz_extra_ver[0];
    $render_engine = $moz_extra_ver[0];
    $else_browser_ver = explode(' ', $chkvers[1]);
    $browser_ver = $else_browser_ver[0];
    $render_engine_ver = $else_browser_ver[0];
  }//detect other
  else
  {
    $browser = $chkvers[0];
    $render_engine = $chkvers[0];
    $else_browser_ver = explode(' ', $chkvers[1]);
    $browser_ver = $else_browser_ver[0];
    $render_engine_ver = $else_browser_ver[0];
  }

  //detect language using HTTP_ACCEPT_LANGUAGE

  //windows seems to send a different HTTP_USER_AGENT, language detection does not work
  //this code should workaround this, as IE sends HTTP_ACCEPT_LANGUAGE
  //also useable for Lynx and w3m
  if ((!isset($lang[0]) || $lang[0] == '') && ($http_lang != ''))
  {
    $lang[0] = substr($http_lang, 0, 2);
    $tmp  = substr($http_lang, 3, 2); // this only gives 'en' e.g.
    if ($tmp == 'q=')
      $tmp = '';
    if (!isset($lang[1]) || $lang[1] == '')
      $lang[1] = $tmp;
  }

  //ip
  if( getenv('HTTP_X_FORWARDED_FOR') != '' )
  {
    $ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR );

    if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", getenv('HTTP_X_FORWARDED_FOR'), $ip_list) )
    {
      $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10..*/', '/^224..*/', '/^240..*/');
      $ip = preg_replace($private_ip, $ip, $ip_list[1]);
    }
  }
  else
    $ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR );

  $client_info['architecture'] = $arch;
  $client_info['os'] = (isset($os[0])) ? $os[0] : '';
  $client_info['os_ver'] = (isset($os[1])) ? $os[1] : '';
  $client_info['os_vendor'] = (isset($os[2])) ? $os[2] : '';
  $client_info['browser'] = $browser;
  $client_info['browser_ver'] = $browser_ver;
  $client_info['browser_subver'] = $browser_subver;
  $client_info['browser_sup'] = $browser_sup;
  $client_info['lang'] = (isset($lang[0])) ? $lang[0] : '';
  $client_info['sub_lang'] = (isset($lang[1])) ? $lang[1] : '';
  $client_info['http_lang'] = $http_lang;
  $client_info['ip'] = $ip;
  $client_info['render_engine'] = $render_engine;
  $client_info['render_engine_ver'] = $render_engine_ver;

  return $client_info;
}

?>