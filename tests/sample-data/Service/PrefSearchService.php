<?php

use PDOHelpers\PDOTable;

class PrefSearchService  extends PDOTable {
  public function __construct ($dsn=null) {
    parent::__construct($dsn);
    $this->_table = 'pref';
  }
  public function findPrefId(): array {
    $this->prepareForGetPrefId();
    return parent::exec_query();
  }
  protected function prepareForGetPrefId (): \PDOStatement {
    return parent::prepare(['distinct id']);
  }
  public function AddSearchByPrefId($id){
    $this->where->addWhereEqual('id', $id);
    return $this;
  }
  public function AddSearchByFreeWord ( string $words ): self {
    $this->where->addWhereLike('kana', $words);
    return $this;
  }
  
  //
  protected function prepareForGetPref($id): \PDOStatement {
    $this->AddSearchByPrefId($id);
    return parent::prepare(['*']);
  }
  
  public function findPref(int $pref_id): array {
    $this->prepareForGetPref($pref_id);
    $ret = parent::exec_query(\PDO::FETCH_ASSOC);
    if (sizeof($ret)==1){
      return $ret[0];
    }else{
      return [];
    }
  }
  
}