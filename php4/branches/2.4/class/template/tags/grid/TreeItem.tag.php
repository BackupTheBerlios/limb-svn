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

define('TREE_LINE_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100% style='display:inline'><tr><td background='/shared/images/t_l.gif' width=20><img src='/shared/images/1x1.gif'></td></tr></table>");
define('TREE_SPACER_IMG', "<img src='/shared/images/0.gif' width=20>");
define('TREE_END_IMG', 	"<table border=0 cellspacing=0 cellpadding=0 height=100% style='display:inline'><tr><td valign=top><img src='/shared/images/t_e.gif'></td></tr></table>");
define('TREE_END_P_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td><a href='%s'><img src='/shared/images/t_e_p.gif'></a></td></tr><tr><td height=100%%></td></tr></table>");
define('TREE_END_M_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td><a href='%s'><img src='/shared/images/t_e_m.gif' border=0></a></td></tr><tr><td height=100%%></td></tr></table>");
define('TREE_CROSS_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td background='/shared/images/t_l.gif' valign=top><img src='/shared/images/t_c.gif' border=0></td></tr></table>");
define('TREE_CROSS_P_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td background='/shared/images/t_l.gif' valign=top><a href='%s'><img src='/shared/images/t_c_p.gif' border=0></a></td></tr></table>");
define('TREE_CROSS_M_IMG', "<table border=0 cellspacing=0 cellpadding=0 height=100%% style='display:inline'><tr><td background='/shared/images/t_l.gif' valign=top><a href='%s'><img src='/shared/images/t_c_m.gif' border=0></a></td></tr></table>");

class GridTreeItemTagInfo
{
  var $tag = 'grid:TREE_ITEM';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'grid_tree_item_tag';
}

registerTag(new GridTreeItemTagInfo());

class GridTreeItemTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!is_a($this->parent, 'GridIteratorTag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITERATOR',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function generateContents($code)
  {
    $ref = $this->getComponentRefCode();

    $code->writeHtml("<table border='0' cellpadding='0' cellspacing='0' height='100%'><tr><td>");

    $i = '$' . $code->getTempVariable();
    $node_htm = '$' . $code->getTempVariable();
    $level = '$' . $code->getTempVariable();
    $levels_status = '$' . $code->getTempVariable();

    $code->writePhp(
      "{$node_htm} = '';
      {$level} = {$ref}->get('level');
      {$levels_status} = {$ref}->get('levels_status');
      "
    );

    $code->writePhp("
      for({$i}=1; {$i} < {$level}; {$i}++)
      {
        if(isset({$levels_status}[{$i}]) && {$levels_status}[{$i}])
          {$node_htm} .= \"" . TREE_SPACER_IMG . "\";
        else
          {$node_htm} .= \"" . TREE_LINE_IMG . "\";
      }
    ");

    $open_params = '$' . $code->getTempVariable();
    $close_params = '$' . $code->getTempVariable();
    $open_link = '$' . $code->getTempVariable();
    $close_link = '$' . $code->getTempVariable();
    $anchor = '$' . $code->getTempVariable();
    $next_img = '$' . $code->getTempVariable();
    $tmp = '$' . $code->getTempVariable();

    $code->writePhp("
      {$open_params}['id'] = {$ref}->get('node_id');
      {$open_params}['action'] = 'toggle';{$open_params}['expand'] = 1;
      {$close_params}['id'] = {$ref}->get('node_id');
      {$close_params}['action'] = 'toggle';{$close_params}['collapse'] = 1;
      {$anchor} = '#' . {$ref}->get('node_id');
      "
    );

    $code->writePhp("
      if({$ref}->get('is_last_child'))
      {
        {$open_link} = sprintf(\"" . TREE_END_P_IMG . "\", addUrlQueryItems(\$_SERVER['PHP_SELF'], {$open_params}) . {$anchor});
        {$close_link} = sprintf(\"" . TREE_END_M_IMG . "\", addUrlQueryItems(\$_SERVER['PHP_SELF'], {$close_params}) . {$anchor});
        {$next_img} = \"" . TREE_END_IMG . "\";
      }
      else
      {
        {$open_link} = sprintf(\"" . TREE_CROSS_P_IMG . "\", addUrlQueryItems(\$_SERVER['PHP_SELF'], {$open_params}) . {$anchor});
        {$close_link} = sprintf(\"" . TREE_CROSS_M_IMG . "\", addUrlQueryItems(\$_SERVER['PHP_SELF'], {$close_params}) . {$anchor});
        {$next_img} = \"" . TREE_CROSS_IMG . "\";
      }
    ");

    $code->writePhp("
      if({$ref}->get('children') > 0)
      {
        if({$ref}->get('is_expanded'))
          {$node_htm} .= {$close_link};
        else
          {$node_htm} .= {$open_link};
      }
      else
          {$node_htm} .= {$next_img};
    ");

    $code->writePhp("echo '<a name=' . {$ref}->get('node_id') . '>';");

    $code->writePhp("echo {$node_htm};");

    $code->writeHtml("</td><td nowrap class='text'>");

    $img_alt = '$' . $code->getTempVariable();
    $img_htm = '$' . $code->getTempVariable();

    $code->writePhp("
      if(!{$ref}->get('img_alt'))
        {$img_alt} = {$ref}->get('identifier');
    ");

    $code->writePhp("echo
      \"<table border=0 cellspacing=0 cellpadding=0 height=100% style='display:inline'>
        <tr>
          <td><img src='/shared/images/1x1.gif' height=3 width=1></td>
        </tr>
        <tr>
        <td>\";
    ");

    $code->writePhp("echo
      \"<img alt='{$img_alt}' border='0' align='middle' src='\" . {$ref}->get('icon') . \"'>\";
    ");

    $code->writePhp("echo
      \"</td></tr>\";
    ");
    $code->writePhp("
      echo \"<tr><td height=100% \";

      if({$ref}->get('children') > 0)
      {
        if({$ref}->get('is_expanded'))
        {
          echo \" background='/shared/images/t_l.gif'\";
        }
      }

      echo \"></td></tr>\";
    ");
    $code->writePhp("echo \"</table>\";");
    $code->writeHtml("</td><td valign=top style='padding:6px 3px 3px 2px'>");

    if(!array_key_exists('nolink', $this->attributes))
      $code->writePhp("echo '<a href=' . {$ref}->get('path') . '>';");

    $code->writePhp("echo {$ref}->get('identifier');");

    if(!array_key_exists('nolink', $this->attributes))
      $code->writePhp("echo '</a>';");

    $code->writeHtml("</td></tr></table>");

    parent :: generateContents($code);
  }
}

?>