<?php

/**
 * @author takuya
 * @contact https://github.com/takuya
 * @created 2020-12-12
 * @license GPL v3
 */


namespace PDOHelpers;


class PDOWhere {
  public array $where_conds;
  public array $where_bind_values;
  public string $add_cond = ' AND ';
  public array $sub_where = [];
  
  public function __construct ( $comp_opr = 'AND' ) {
    preg_match( '/or/i', $comp_opr ) && $this->add_cond = ' OR ';
    preg_match( '/and/i', $comp_opr ) && $this->add_cond = ' AND ';
  }
  
  public function addWhere( $where,$cond='AND'){
    preg_match( '/or/i', $cond )  && $cond = ' OR ';
    preg_match( '/and/i', $cond ) && $cond = ' AND ';
    
    $uid = uniqid();
    $this->sub_where[$uid] = [
      'cond' => $cond,
      'query' => $where,
    ];
  }
  
  protected function refine_search_words ( $words ): array {
    if ( empty( $words ) ) {
      return [];
    }
    if ( !is_array($words)){
      $words = preg_split( '/\s+/', trim( $words ) );
    }
    return $words;
  }
  public function addWhereLike($column, $words){
    $words = $this->refine_search_words($words);
    foreach ( $words as $word ) {
      $word = preg_match('/%/', $word)? $word : "%{$word}%";
      $this->addWhereCond($column,'LIKE', $word);
    }
  }
  public function addWhereRegex( $column , $words ){
    $words = $this->refine_search_words($words);
    // prevent user regex
    //$words = array_map( 'preg_quote', $words );
    $this->addWhereCond($column, 'REGEXP', $words);
  }
  public function addWhereEqual( $column , $words) {
    $this->addWhereCond($column, '=', $words);
  }
  
  
  public function addWhereCond ( $column, $comparison_opr, $words ) {
    $words = $this->refine_search_words( $words );
    foreach ( $words as $word ) {
      $uid = dechex(crc32( $word ));
      //$uid = md5( $word );
      $this->where_conds[] = " ${column} {$comparison_opr} :{$uid} ";
      $this->where_bind_values[$uid] = $word;
    }
  }
  
  
  public function build_where () {
    
    $this->where_conds = $this->where_conds ?? ['1=1'];
    $this->where_bind_values = $this->where_bind_values ?? [];
    sort( $this->where_conds );
    $where_str = join( $this->add_cond, $this->where_conds );
    foreach ( $this->sub_where as $idx=> $item ) {
      /** @var PDOWhere $where */
      $where  = $item['query'];
      $cond_opr  = $item['cond'];
      [$str, $values] = $where->build_where();
      if ( empty($str) ){
        continue;
      }
      $where_str = $where_str. "  {$cond_opr}  ( {$str} ) ";
      $where_str = preg_replace('/\s+/', ' ', $where_str);
      $this->where_bind_values = array_merge([],$this->where_bind_values, $values);
    }
    //dd($this->where_conds);
    return [$where_str, $this->where_bind_values,];
  }
}
