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
require_once(LIMB_DIR . '/class/template/compiler/Codewriter.class.php');
require_once(LIMB_DIR . '/class/template/compiler/utils.inc.php');
require_once(LIMB_DIR . '/class/template/compiler/TagDictionary.class.php');

require_once(LIMB_DIR . '/class/template/compiler/CompilerComponent.class.php');
require_once(LIMB_DIR . '/class/template/compiler/CompilerDirectiveTag.class.php');
require_once(LIMB_DIR . '/class/template/compiler/SilentCompilerDirectiveTag.class.php');
require_once(LIMB_DIR . '/class/template/compiler/ServerComponentTag.class.php');
require_once(LIMB_DIR . '/class/template/compiler/ServerTagComponentTag.class.php');
require_once(LIMB_DIR . '/class/template/compiler/TextNode.class.php');
require_once(LIMB_DIR . '/class/template/compiler/RootCompilerComponent.class.php');

require_once(LIMB_DIR . '/class/template/compiler/SourceFileParser.class.php');
require_once(LIMB_DIR . '/class/template/compiler/Codewriter.class.php');
require_once(LIMB_DIR . '/class/template/compiler/VariableReference.class.php');

require_once(LIMB_DIR . '/class/template/fileschemes/compiler_support.inc.php');
require_once(LIMB_DIR . '/class/core/PackagesInfo.class.php');

/**
* Create the tag_dictionary global variable
*/
$GLOBALS['tag_dictionary'] = new TagDictionary();

function loadTagsFromDirectory($tags_repository_dir)
{
  if(!is_dir($tags_repository_dir))
    return;

  $repository_dir = opendir($tags_repository_dir);

  while(($tag_dir = readdir($repository_dir)) !== false)
  {
    if(!is_dir($tags_repository_dir . $tag_dir))
      continue;

    if(($dir = opendir($tags_repository_dir . $tag_dir)) == false)
      continue;

    while(($tag_file = readdir($dir)) !== false)
    {
      if  (substr($tag_file, -8,  8) == '.tag.php')
      {
        include_once($tags_repository_dir . $tag_dir . '/' . $tag_file);
      }
    }
    closedir($dir);
  }
  closedir($repository_dir);
}

function loadCoreTags()
{
  loadTagsFromDirectory(LIMB_DIR . '/class/template/tags/');
}

loadCoreTags();

function loadPackagesTags()
{
  $info =& PackagesInfo :: instance();
  $packages = $info->getPackages();

  foreach($packages as $package)
  {
    loadTagsFromDirectory($package['path'] . '/template/tags/');
  }
}

loadPackagesTags();

/**
* Compiles a template file. Uses the file scheme to location the source,
* instantiates the code_writer and root_compiler_component (as the root) component then
* instantiates the source_file_parser to parse the template.
* Creates the initialize and render functions in the compiled template.
*/
function compileTemplateFile($filename, $resolve_path = true)
{
  global $tag_dictionary;

  if($resolve_path)
  {
    if(!$sourcefile = resolveTemplateSourceFileName($filename))
      return new FileNotFoundException('template file not found', $filename);
  }
  else
    $sourcefile = $filename;

  $destfile = resolveTemplateCompiledFileName($sourcefile);

  if (empty($sourcefile))
  {
    return new FileNotFoundException('compiled template file not found', $filename);
  }

  $code = new Codewriter();
  $code->setFunctionPrefix(md5($destfile));

  $tree = new RootCompilerComponent();
  $tree->setSourceFile($sourcefile);

  $sfp = new SourceFileParser($sourcefile, $tag_dictionary);
  $sfp->parse($tree);

  $tree->prepare();

  $render_function = $code->beginFunction('($dataspace)');
  $tree->generate($code);
  $code->endFunction();

  $construct_function = $code->beginFunction('($dataspace)');
  $tree->generateConstructor($code);
  $code->endFunction();

  $code->writePhp('$GLOBALS[\'template_render\'][$this->codefile] = \'' . $render_function . '\';');
  $code->writePhp('$GLOBALS[\'template_construct\'][$this->codefile] = \'' . $construct_function . '\';');

  writeTemplateFile($destfile, $code->getCode());
}

?>