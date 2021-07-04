<?php

namespace PDOHelpers\Cache;


class CacheService {
  protected string $cache_file;
  public function __construct ($file_path) {
    $this->cache_file = $file_path;
  }
  public function getContent(){
    return $this->exists() ? file_get_contents($this->cache_file) : null;
  }
  public function putContent($data){
    return file_put_contents($this->cache_file,$data);
  }
  public function exists(){
    return is_readable($this->cache_file);
  }
  public function purge(){
    if (!$this->exists()){return true;}
    return unlink($this->cache_file) ;
  }
}
