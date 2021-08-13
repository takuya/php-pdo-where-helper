<?php

namespace PDOHelpers;

use phpDocumentor\Reflection\Types\This;

class OderBy {
  
  public array $order_by;
  public function __construct($col, $order='asc'){
    $this->order($col,$order);
  }
  
  public function order($col, $order){
    if ( !in_array( strtolower($order), ['asc','desc'])){
      throw  new \InvalidArgumentException('order should be asc or desc');
    }
    $this->order_by = [$col, $order];
  }
  public function build(){
    return join(' ', $this->order_by);
  }
}