<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DomainObject.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
@define('UOW_CACHE_GROUP', 'identity_map');

class UnitOfWork
{
  var $existing = array();
  var $new = array();
  var $deleted = array();

  function & _getDAO($class)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDAO($class . 'DAO');
  }

  function & _getDataMapper($class)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDataMapper($class . 'Mapper');
  }

  function & _getObject($class)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createObject($class);
  }

  function & _getCache()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->getCache();
  }

  function register(&$obj)
  {
    if($id = $this->_getId($obj))
    {
      $this->_putToCache($id, $obj);
      $this->existing[$id] = $this->_getHash($obj);
    }
    else
    {
      $this->new[] =& $obj;
    }
  }

  //stolen from SimpleTest
  function _isReference(&$first, &$second)
  {
    if (version_compare(phpversion(), '5', '>=') && is_object($first))
      return ($first === $second);

    $temp = $first;
    $first = uniqid('test');
    $is_ref = ($first === $second);
    $first = $temp;
    return $is_ref;
  }

  function isRegistered(&$obj)
  {
    if($id = $this->_getId($obj))
    {
      return isset($this->existing[$id]);
    }
    else
    {
      foreach(array_keys($this->new) as $key)
      {
        if($this->_isReference($this->new[$key], $obj))
          return true;
      }
    }

    return false;
  }

  function evict(&$obj)
  {
    if($id = $this->_getId($obj))
    {
      $this->_purgeFromCache($id);

      if(isset($this->existing[$id]))
        unset($this->existing[$id]);

      //???
      foreach(array_keys($this->deleted) as $key)
      {
        if($this->_isReference($this->deleted[$key], $obj))
          unset($this->deleted[$key]);
      }
    }
    else
    {
      //???
      foreach(array_keys($this->new) as $key)
      {
        if($this->_isReference($this->new[$key], $obj))
          unset($this->new[$key]);
      }
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
    $cache->purge($id, UOW_CACHE_GROUP);
  }

  function _getId(&$obj)
  {
    $mapper =& $this->_getDataMapper($obj->__class_name);
    return $obj->get($mapper->getIdentityKeyName());
  }

  function _hasId(&$obj)
  {
    return $this->_getId($obj) !== null;
  }

  function & load($class, $id)
  {
    if($obj =& $this->_getFromCache($id))
      return $obj;

    $dao =& $this->_getDAO($class);

    $obj =& $this->_getObject($class);

    if(!$record =& $dao->fetchById((int)$id))
      return null;

    $mapper =& $this->_getDataMapper($class);
    $mapper->load($record, $obj);

    $this->register($obj);

    return $obj;
  }

  function delete(&$obj)
  {
    $this->deleted[] = &$obj;
  }

  function start()
  {
    $this->existing = array();
    $this->new = array();
    $this->deleted = array();

    $cache =& $this->_getCache();
    $cache->flush(UOW_CACHE_GROUP);
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
      if($this->_isDirty($id, $obj))
      {
        $mapper =& $this->_getDataMapper($obj->__class_name);
        $mapper->save($obj);
        $this->register($obj);
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
      $this->register($obj);
    }

    $this->new = array();
  }

  function _commitDeleted()
  {
    foreach(array_keys($this->deleted) as $key)
    {
      $obj =& $this->deleted[$key];

      if($id = $this->_getId($obj))
      {
        $mapper =& $this->_getDataMapper($obj->__class_name);
        $mapper->delete($obj);
        $this->evict($obj);
      }
    }
  }

  function _isDirty($id, $obj)
  {
    return $this->_getHash($obj) != $this->existing[$id];
  }

  function _getHash($obj)
  {
    return md5(serialize($obj));
  }
}

?>