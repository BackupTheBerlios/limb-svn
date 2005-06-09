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
@define('UOW_CACHE_GROUP', 'identity_map');

class UnitOfWork
{
  var $existing;
  var $new;
  var $deleted;
  var $toolkit;

  function UnitOfWork()
  {
    $this->toolkit =& Limb :: toolkit();
    $this->reset();
  }

  function reset()
  {
    $this->existing = array();
    $this->new = array();
    $this->deleted = array();

    $this->_purgeAllFromCache();
  }

  function registerExisting(&$obj)
  {
    if($id = $this->_getObjectId($obj))
    {
      $this->_putToCache($id, $obj);
      $this->existing[$id] = $this->_getHash($obj);
      if(isset($this->new[$id]))
        unset($this->new[$id]);
    }
    else
    {
      return throw_error(new LimbException("Can't register object in UnitOfWork as existing because there is no id field!",
                                    array('class' => get_class($obj))));
    }
  }

  function registerNew(&$obj)
  {
    $this->_setObjectId($obj, $this->toolkit->nextUID());
    $id = $this->_getObjectId($obj);
    $this->_putToCache($id, $obj);
    $this->new[$id] =& $obj;
  }

  function isRegistered(&$obj)
  {
    return ($this->isExisting($obj) || $this->isNew($obj));
  }

  function isExisting(&$obj)
  {
    if($id = $this->_getObjectId($obj))
    {
      return isset($this->existing[$id]);
    }
  }

  function isNew(&$obj)
  {
    if($id = $this->_getObjectId($obj))
    {
      return isset($this->new[$id]);
    }
  }

  function isDeleted(&$obj)
  {
    if($id = $this->_getObjectId($obj))
    {
      return isset($this->deleted[$id]);
    }
  }

  function isDirty(&$obj)
  {
    if(!$this->isExisting($obj))
      return false;

    return $this->_isExistingObjectDirty($obj);
  }

  function evict(&$obj)
  {
    if($id = $this->_getObjectId($obj))
    {
      $this->_purgeFromCache($id);

      if(isset($this->existing[$id]))
        unset($this->existing[$id]);

      if(isset($this->new[$id]))
        unset($this->new[$id]);

      if(isset($this->deleted[$id]))
        unset($this->deleted[$id]);
    }
  }

  function & _putToCache($id, &$obj)
  {
    $cache =& $this->_getCache();
    $cache->put($id, $obj, UOW_CACHE_GROUP);
  }

  function & _getFromCache($id)
  {
    $cache =& $this->_getCache();
    return $cache->get($id, UOW_CACHE_GROUP);
  }

  function _purgeFromCache($id)
  {
    $cache =& $this->_getCache();
    $cache->flushValue($id, UOW_CACHE_GROUP);
  }

  function _purgeAllFromCache()
  {
    $cache =& $this->_getCache();
    $cache->flushGroup(UOW_CACHE_GROUP);
  }

  function _getObjectId(&$obj)
  {
    $mapper =& $this->_getDataMapper($obj->__class_name);
    return $obj->get($mapper->getIdentityKeyName());
  }

  function _setObjectId(&$obj, $id)
  {
    $mapper =& $this->_getDataMapper($obj->__class_name);
    return $obj->set($mapper->getIdentityKeyName(), $id);
  }

  function & load($class, $id)
  {
    if(($obj =& $this->_getFromCache($id)) !== CACHE_NULL_RESULT)
      return $obj;

    $dao =& $this->_getDAO($class);

    if(!is_object($dao))
    {
      return throw_error(new LimbException("Cant create DAO",
                                           array('class' => $class)));
    }

    $obj =& $this->_getObject($class);

    if(!$record =& $dao->fetchById((int)$id))
      return null;

    $mapper =& $this->_getDataMapper($class);
    $mapper->load($record, $obj);

    $this->registerExisting($obj);

    return $obj;
  }

  function delete(&$obj)
  {
    if($id = $this->_getObjectId($obj))
    {
      if(isset($this->new[$id]))
      {
        unset($this->new[$id]);
        return;
      }
      $this->deleted[$id] =& $obj;

    }
    else
    {
      return throw_error(new LimbException("Can't delete object in UnitOfWork as existing because there is no id field!",
                                    array('class' => get_class($obj))));
    }
  }

  function commit()
  {
    $this->_commitExisting();
    $this->_commitNew();
    $this->_commitDeleted();
  }

  function _commitExisting()
  {
    foreach(array_keys($this->existing) as $id)
    {
      $obj =& $this->_getFromCache($id);
      if($this->_isExistingObjectDirty($obj))
      {
        $mapper =& $this->_getDataMapper($obj->__class_name);
        $mapper->save($obj);
        $this->registerExisting($obj);
      }
    }
  }

  function _commitNew()
  {
    foreach(array_keys($this->new) as $key)
    {
      $obj =& $this->new[$key];
      $mapper =& $this->_getDataMapper($obj->__class_name);
      $mapper->save($obj);
      $this->registerExisting($obj);
    }

    $this->new = array();
  }

  function _commitDeleted()
  {
    foreach(array_keys($this->deleted) as $id)
    {
      $obj =& $this->deleted[$id];
      $mapper =& $this->_getDataMapper($obj->__class_name);
      $mapper->delete($obj);
      $this->evict($obj);
    }
  }

  function _isExistingObjectDirty(&$obj)
  {
    return $this->_getHash($obj) != $this->existing[$this->_getObjectId($obj)];
  }

  function _getHash($obj)
  {
    return md5(serialize($obj));
  }

  function & _getDAO($class)
  {
    return $this->toolkit->createDAO($class . 'DAO');
  }

  function & _getDataMapper($class)
  {
    return $this->toolkit->createDataMapper($class . 'Mapper');
  }

  function & _getObject($class)
  {
    return $this->toolkit->createObject($class);
  }

  function & _getCache()
  {
    return $this->toolkit->getCache();
  }
}

?>