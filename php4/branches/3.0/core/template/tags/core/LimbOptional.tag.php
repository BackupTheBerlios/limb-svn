<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RedirectCommand.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/

/**
* Register the tag
*/
$taginfo =& new TagInfo('limb:OPTIONAL', 'LimbOptionalTag');
$taginfo->setCompilerAttributes(array('for'));
TagDictionary::registerTag($taginfo, __FILE__);

/**
* Defines an action take, should a DataSource property have been set at runtime.
* The opposite of the CoreDefaultTag
* @see CoreDefaultTag
* @see http://wact.sourceforge.net/index.php/CoreOptionalTag
* @access protected
* @package WACT_TAG
*/
class LimbOptionalTag extends CompilerDirectiveTag {
  /**
  * @var DataBindingExpression
  * @access private
  */
    var $DBE;

  /**
  * @return int PARSER_REQUIRE_PARSING
  * @access protected
  */
  function preParse() {
    $binding = $this->getAttribute('for');
    if (empty($binding)) {
        $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
            array('attribute' => 'for'));
    }

        $this->DBE =& new DataBindingExpression($binding, $this);

    return PARSER_REQUIRE_PARSING;
  }

  function prepare() {
      $this->DBE->prepare();
    parent::prepare();
  }

  /**
  * @param CodeWriter
  * @return void
  * @access protected
  */
  function preGenerate(&$code) {
    parent::preGenerate($code);

    $tempvar = $code->getTempVariable();
    $this->DBE->generatePreStatement($code);
    $code->writePHP('$' . $tempvar . ' = ');
    $this->DBE->generateExpression($code);
        $code->writePHP(';');
    $this->DBE->generatePostStatement($code);

    $code->writePHP('if (!is_array($' . $tempvar .' )) $' . $tempvar . '= trim($' . $tempvar . ');');
    $code->writePHP('if (!empty($' . $tempvar . ')){');
  }

  /**
  * @param CodeWriter
  * @return void
  * @access protected
  */
  function postGenerate(&$code) {
    $code->writePHP('}');
    parent::postGenerate($code);
  }
}
?>