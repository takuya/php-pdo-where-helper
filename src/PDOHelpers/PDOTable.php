<?php

/**
 * @author takuya
 * @contact https://github.com/takuya
 * @created 2020-12-12
 * @license GPL v3
 */

namespace PDOHelpers;

use phpDocumentor\Reflection\Types\This;

class PDOTable{
  public string $_table;
  public \PDO $pdo;
  protected \PDOStatement  $stmt;
  public array $where_conds;
  public array $where_bind_values;
  public int $default_fetch_style =\PDO::FETCH_COLUMN;
  public PDOWhere  $where;
  public array $last_query = [];
  protected array $orders = [];
  protected $limit;
  protected $offset;
  
  public function __construct ($dsn_or_pdo=null) {
    
    $this->where =  new PDOWhere();
    if ($dsn_or_pdo ){
      if (is_string($dsn_or_pdo)){
        $this->pdo = new \PDO($dsn_or_pdo) ;
      }
      else if (is_object($dsn_or_pdo) && get_class($dsn_or_pdo)=='PDO'){
        $this->pdo =  $dsn_or_pdo ;
      }
    }
  }
  public function openDsn($dsn) {
    $this->pdo = new \PDO($dsn);
  }
  
  public function OrderBy($col, $order='asc'){
    $this->orders[] = new OderBy($col, $order);
    return $this;
  }
  protected function build_order_by(){
    /** OrderBy $e  */
    $orders = array_map(function ($e){ return $e->build(); }, $this->orders );
    $orders = join(',' , $orders);
    return $orders ? 'order by '.$orders :'';
  }
  //
  public function getTables () {
    $pdo = $this->pdo;
    $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
    
    if ( $driver == 'sqlite'){
      $q = "SELECT name FROM sqlite_master
            WHERE type IN ('table','view')
           AND name NOT LIKE 'sqlite_%'
            ORDER BY 1;";
      $stmt = $pdo->query( $q );
      $ret =$stmt->fetchAll( \PDO::FETCH_COLUMN );
      return $ret;
    }
    else if ( $driver == 'mysql'){
      $stmt = $pdo->query( 'show tables' );
      return $stmt->fetchAll( \PDO::FETCH_COLUMN );
      
    }
    return [];
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
    
    $sql = $sql.$this->build_order_by();
    $sql = $sql. ( $this->limit ?  " limit {$this->limit} " : '' );
    $sql = $sql. ( $this->offset ? " offset {$this->offset} " : '' );
    
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
    // var_dump($stmt->queryString);
    $stmt->execute();
    $ret = $stmt->fetchAll( $fetch_style ?? $this->default_fetch_style );
    return $ret;
  }
  //
  public function fetchRows($cols=['*']){
    $this->prepare($cols);
    $ret =  $this->exec_query(\PDO::FETCH_ASSOC);
    return $ret;
  }
  public function fetchOne($cols=['*']){
    $this->prepare($cols);
    $ret =  $this->exec_query(\PDO::FETCH_ASSOC);
    if (sizeof($ret)>0){
      return array_shift($ret);
    }else{
      return [];
    }
  }
  public function Limit( int $size){
    $this->limit = $size;
    return $this;
  }
  public function Offset( int $size){
    $this->offset = $size;
    return $this;
  }
  public function Where($col, $cmp, $value){
    $this->where->addWhereCond($col, $cmp, $value);
    return $this;
  }
  public function WhereEq($col, $value){
    $this->where->addWhereCond($col, '=', $value);
    return $this;
  }
  public function WhereGt($col, $value){
    $this->where->addWhereCond($col, '>', $value);
    return $this;
  }
  public function WhereLt($col, $value){
    $this->where->addWhereCond($col, '<', $value);
    return $this;
  }
  public function where_like($col, $value){
    $this->where->addWhereCond($col, 'like', $value);
    return $this;
  }
  
  // ForDebug
  public function getLastPreparedQueryAsRawSQLforDebug(){
    ['sql'=>$str, 'binds'=>$arr] = $this->last_query;
    foreach ( $arr as $idx => $item ) {
      unset($arr[$idx]);
      $arr[":$idx"]="'$item'";
    }
    return strtr($str, $arr);
  }
  
}
