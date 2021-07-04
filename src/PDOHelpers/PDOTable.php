<?php

/**
 * @author takuya
 * @contact https://github.com/takuya
 * @created 2020-12-12
 * @license GPL v3
 */

namespace PDOHelpers;


class PDOTable{
  public string $_table;
  public \PDO $pdo;
  protected \PDOStatement  $stmt;
  public array $where_conds;
  public array $where_bind_values;
  public int $default_fetch_style =\PDO::FETCH_COLUMN;
  public PDOWhere  $where;
  public array $last_query = [];
  
  
  public function __construct () {
    $this->pdo = \tunnel\getPDO();
    $this->where =  new PDOWhere();
  }
  
  public function create_view () {
    \tunnel\create_search_view();
  }
  //
  public function getTables () {
    $pdo = $this->pdo;
    $stmt = $pdo->query( 'show tables' );
    return $stmt->fetchAll( \PDO::FETCH_COLUMN );
  }
  //
  protected function refine_search_words(string $words ) :array{
    if ( empty( $words ) ) {
      return [];
    }
    $words = preg_split( '/\s+/', trim( $words ) );
    $words = array_map('preg_quote', $words);
    return $words;
  }
  //
  protected function addWhere( PDOWhere $where = null, $cond_opr='AND' ){
    $this->where->addWhere( $where ?? new PDOWhere() , $cond_opr);
  }
  
  //
  protected function prepare(array $cols){
    $sql = "select ".join(', ', $cols )." from {$this->_table} where ";
    
    $where_cond = !empty($this->where) ? $this->where->build_where() : "";
    [$where_str, $bind_values] = $where_cond;
    
    $sql = $sql.$where_str;
    $sql  = preg_replace('/\s+/', ' ', $sql);
    
    $this->last_query = ['sql'=>$sql, 'binds'=>$bind_values];
    
    $stmt = $this->pdo->prepare($sql);
    foreach ( $bind_values as $key => $val ) {
      $stmt->bindValue( ":$key", $val );
    }
    $this->stmt = $stmt;
    return $stmt;
    
  }
  protected function exec_query($fetch_style=null){
    $stmt = $this->stmt;
    //dd($stmt->queryString);
    $stmt->execute();
    $ret = $stmt->fetchAll( $fetch_style ?? $this->default_fetch_style );
    return $ret;
  }
  // ForDebug
  public function getLastPreparedQueryAsRawSQLforDebug(){
    list('sql'=>$str, 'binds'=>$arr) = $this->last_query;
    foreach ( $arr as $idx => $item ) {
      unset($arr[$idx]);
      $arr[":$idx"]="'$item'";
    }
    return strtr($str, $arr);
  }
  
}
