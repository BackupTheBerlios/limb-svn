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

function process_output(& $output)
{
  _highlight_output($output);
  _gzip_output($output);

  return $output;
}

function _gzip_output(& $output)
{
  global $HTTP_SERVER_VARS;

  if(defined('OUTPUT_GZIP_ENABLED') && constant('OUTPUT_GZIP_ENABLED') == false)
    return;

  if(isset($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING']) && strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip'))
  {
    $size = strlen($output);
    if(extension_loaded('zlib') &&  $size >= 20000)
    {
      $crc = crc32($output);

      $output = gzcompress($output, 9);

      // We can't just output it here, since the CRC is messed up. Strip off the old CRC
      $output = substr($output, 0, strlen($output) - 4);

      _gzip_append_4_chars($output, $crc);
      _gzip_append_4_chars($output, $size);

      $output = "\x1f\x8b\x08\x00\x00\x00\x00\x00" . $output;

      header('Content-Encoding: gzip');
    }
  }
}

function _gzip_append_4_chars(& $content, $value)
{
  for ($i = 0; $i < 4; $i ++)
  {
    $content .= chr($value % 256);
    $value = floor($value / 256);
  }
}

function _highlight_output(& $output)
{
  if(isset($_GET['h']) && strlen(trim($_GET['h'])) > 1)
  {
    $pieces1 = explode('<!--content_object_begin-->', $output);

    for($i=1; $i<sizeof($pieces1); $i++)
    {
      $pieces2 = explode('<!--content_object_end-->', $pieces1[$i]);

      if(isset($pieces2[0]))
        $pieces2[0] = preg_replace_callback("#(([^>]+)>)([^<]*)#si", '_highlite_callback', $pieces2[0]);

      $pieces1[$i] = implode(' ', $pieces2);
    }

    $output = implode(' ', $pieces1);
  }
}

function _highlite_callback($matches)
{
  static $tag_context = array();
  static $words_regxp = '';

  if(preg_match('#<?(/?\S+).*#i', $matches[2], $preg_matches))
  {
    $tag = strtolower($preg_matches[1]);
    $is_closing = false;

    if($tag{0} == '/')
    {
      $tag = substr($tag, 1);
      $is_closing = true;
    }

    if(!$is_closing)
      $tag_context[$tag] = 1;
    elseif(isset($tag_context[$tag]))
      unset($tag_context[$tag]);

    if(	isset($tag_context['head']) ||
        isset($tag_context['textarea']) ||
        isset($tag_context['button']) ||
        isset($tag_context['script']) ||
        isset($tag_context['pre'])
      )
    return $matches[0];
  }

  if(!$words_regxp)
  {
    $words = explode(' ', trim($_GET['h']));

    foreach($words as $id => $word)
      $words[$id] = preg_quote(strtolower(trim($word)));

    $words_regxp = implode('|', $words);
  }
  return 	$matches[1] .
          preg_replace('/(' . $words_regxp . ')/i', '<span class="high">\\1</span>', $matches[3]);
}

?>