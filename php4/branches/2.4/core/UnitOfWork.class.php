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
  var $registered = array();
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
      $this->registered[$id] = $this->_getHash($obj);
    }
    else
    {
      $this->new[] =& $obj;
    }
  }

  function evict(&$obj)
  {
    if($id = $this->_getId($obj))
    {
      $this->_purgeFromCache($id);

      if(isset($this->registered[$id]))
        unset($this->registered[$id]);

      //???
      foreach($this->deleted as $key => $deleted)
      {
        if($deleted == $obj)
          unset($this->deleted[$key]);
      }
    }
    else
    {
      //???
      foreach($this->new as $key => $new)
      {
        if($new == $obj)
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
    $this->registered = array();
    $this->new = array();
    $this->deleted = array();

    $cache =& $this->_getCache();
    $cache->flush(UOW_CACHE_GROUP);
  }

  function commit()
  {
    $this->_commitRegistered();
    $this->_commitNew();
    $this->_commitDeleted();
  }

  function _commitRegistered()
  {
    foreach(array_keys($this->registered) as $id)
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
    return $this->_getHash($obj) != $this->registered[$id];
  }

  function _getHash($obj)
  {
    return md5(serialize($obj));
  }
}

?>