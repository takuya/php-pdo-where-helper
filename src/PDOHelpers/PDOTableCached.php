<?php

namespace PDOHelpers;


class PDOTableCached extends PDOTable {
  
  public bool  $cache_enabled = false;
  public string $cache_file = 'search_cache';
  public CacheKeyValueService $cacheMgr;
  public function __construct () {
    parent::__construct();
    
    global $search_cache_path;
    $this->cache_file = $search_cache_path;
    $this->cacheMgr = new CacheKeyValueService($this->cache_file);
  }
  
  public function is_cached($fetch_style=null){
    return $this->cacheMgr->keyExists($this->cache_key($fetch_style));
  }
  public function getCache($fetch_style=null):array{
    $a =  $this->cacheMgr->getValue($this->cache_key($fetch_style));
    $a = json_decode($a,JSON_OBJECT_AS_ARRAY);
    return $a;
  }
  
  public function setCache( array $data, $fetch_style=null  ){
    $data =json_encode($data);
    return $this->cacheMgr->putValue($this->cache_key($fetch_style), $data);
  }
  protected function cache_key($fetch_style=null){;
    $str = get_class($this).":";
    $str .= "fetch=".($fetch_style??$this->default_fetch_style).":";
    $str .= $this->stmt->queryString;
    $str .= join(',',$this->where_bind_values);
    //
    //$str = md5($str);
    //
    return $str;
  }
  protected function exec_query($fetch_style=null) :array{
    //
    //dd($this->stmt->queryString);
    if ( !$this->stmt){
      throw new  \RuntimeException("exec_query called before preare");
    }
    //
    if ($this->cache_enabled && $this->is_cached($fetch_style)){
      return $this->getCache($fetch_style);
    }else{
      $ret =  parent::exec_query($fetch_style);
      $this->cache_enabled && $this->setCache($ret, $fetch_style);
      return $ret;
    }
  }
}