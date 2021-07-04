<?php

namespace PDOHelpers\Cache;


class CacheKeyValueService {
  protected $cache_mgr;
  protected $cached_data;
  
  public function __construct ( string $file_path){
    $this->cache_mgr = new CacheService($file_path);
    $this->__loadCache();
  }
  protected function __loadCache(){
    $data = $this->cache_mgr->getContent() ?? '[]';
    $data = json_decode($data, JSON_OBJECT_AS_ARRAY);
    return $this->cached_data=$data;
  }
  protected function __saveCache($data){
    $data = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    return $this->cache_mgr->putContent($data);
  }
  public function getValue(string $key){
    $data = $this->__loadCache();
    return !empty($data[$key]) ? $data[$key] : null;
  }
  public function putValue( string $key, string $value ) {
    $this->cached_data[$key] = $value;
    return $this->__saveCache($this->cached_data);
  }
  public function keyExists( $key ){
    return array_key_exists($key, $this->cached_data);
  }
  public function unsetValue($key){
    unset($this->cached_data[$key]);
    return $this->__saveCache($this->cached_data);
    
  }
  public function purge(){
    return $this->cache_mgr->purge();
  }
}

