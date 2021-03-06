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
class UserInGroupsTagInfo
{
  public $tag = 'user:IN_GROUPS';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'user_in_groups_tag';
}

registerTag(new UserInGroupsTagInfo());

class UserInGroupsTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/simple_authenticator_component';
  }

  public function preParse()
  {
    if (!isset($this->attributes['groups']) ||  !$this->attributes['groups'])
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'groups',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  public function generateContents($code)
  {
    $code->writePhp('if ' .
      $this->getComponentRefCode() . '->is_user_in_groups(' . $this->attributes['groups'] .'){');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>