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
class media_manager
{
  public function get_media_file_path($media_id)
  {
    return MEDIA_DIR . $media_id . '.media';
  }

  public function unlink_media($media_id)
  {
    unlink($this->get_media_file_path($media_id));
  }

  public function store($disk_file_path)
  {
    if(!file_exists($disk_file_path))
      throw new FileNotFoundException('file not found', $disk_file_path);

    srand(time());
    $media_id = md5(uniqid(rand()));

    fs :: mkdir(MEDIA_DIR);

    $media_file = $this->get_media_file_path($media_id);

    if (!copy($disk_file_path, $media_file))
    {
      throw new IOException('copy failed',
        array(
          'dst' => $media_file,
          'src' => $disk_file_path
          )
      );
    }
    return $media_id;
  }
}


?>
