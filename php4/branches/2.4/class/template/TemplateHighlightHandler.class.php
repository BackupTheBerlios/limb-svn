<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(XML_HTMLSAX3 . '/HTMLSax3.php');

class TemplateHighlightHandler
{
  protected $html = '';
  protected $current_tag = '';
  protected $template_path_history = array();
  protected $tag_dictionary = null;

  function __construct($tag_dictionary)
  {
    $this->tag_dictionary = $tag_dictionary;
  }

  public function setTemplatePathHistory($history)
  {
    $this->template_path_history = $history;
  }

  protected function writeAttributes($attributes)
  {
    if (is_array($attributes))
    {
      foreach ($attributes as $name => $value)
      {
        $name_html = $name;
        $value_html = $value;

        if($this->tag_dictionary->getTagInfo($this->current_tag))
        {
          $name_html = "<span style='color:red;'>{$name}</span>";
          $value_html = "<span style='color:brown;'>{$value}</span>";
        }

        if($this->current_tag == 'core:wrap' ||  $this->current_tag == 'core:include')
        {
          if($name == 'file')
          {
            $history = array();
            $history = $this->template_path_history;
            $history[] = $value;

            $history_string = 't[]=' . implode('&t[]=', $history);

            $href = "/root/template_source?{$history_string}";

            $value_html = "<a style='text-decoration:underline;font-weight:bold;' href={$href}>{$value}</a>";
          }
        }

        $this->html .= ' ' . $name_html . '="' . $value_html . '"';
      }
    }
  }

  public function openHandler($parser, $name, $attrs)
  {
    $this->current_tag = strtolower($name);

    if($this->tag_dictionary->getTagInfo($name))
      $this->html .= '&lt;<span style="color:orange;font-weight:bold;">' . $name . '</span>';
    else
      $this->html .= '&lt;<span style="color:blue">' . $name . '</span>';

    $this->writeAttributes($attrs);

    $this->html .= '&gt;';
  }

  public function closeHandler($parser, $name)
  {
    if($this->tag_dictionary->getTagInfo($name))
      $this->html .= '&lt;/<span style="color:orange;font-weight:bold;">' . $name . '</span>&gt;';
    else
      $this->html .= '&lt;/<span style="color:blue">' . $name . '</span>&gt;';
  }

  public function dataHandler($parser, $data)
  {
    $data = str_replace("\t", '  ', $data);
    $this->html .= $data;
  }

  public function escapeHandler($parser, $data)
  {
    $this->html .= '<span style="color:green;font-style:italic;">&lt;!' . $data . '&gt;</span>';
  }

  public function getHtml()
  {
    $this->html = preg_replace('~(\{(\$|\^|#)[^\}]+\})~', "<span style='background-color:lightgreen;font-weight:bold;'>\\1</span>", $this->html);

    $lines = preg_split( "#\r\n|\r|\n#", $this->html);

    $content = '';
    $max = sizeof($lines);
    $digits = strlen("{$max}");

    for($i=0; $i < $max; $i++)
    {
      $j = $i + 1;
      $content .= "<span style='font-family:courier;color:#c0c0c0;'>{$j}" . str_repeat('&nbsp;', $digits - strlen("{$j}")) . "</span> " .  $lines[$i] . "\n";
    }

    return $content;
  }
}
?>