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
require_once(LIMB_DIR . '/class/template/tags/form/ControlTag.class.php');

class DateTagInfo
{
  var $tag = 'date';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'date_tag';
}

registerTag(new DateTagInfo());

class DateTag extends ControlTag
{
  function DateTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/date_component';
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->init_date();');

    parent :: preGenerate($code);
  }

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_date();');
  }
}

?>