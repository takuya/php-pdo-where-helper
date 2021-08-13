<?php

namespace Tests\Units;

use Tests\TestCase;
use PDOHelpers\PDOTable;

class TableBindTest extends TestCase{
  
  public function test_bind(){
    $file = __DIR__.'/../sample-data/sample-01.sqlite';
    $file = realpath($file);
    $dsn = "sqlite:$file";
    $pt = new PDOTable($dsn);
    $tables = $pt->getTables();
  
    $this->assertIsArray($tables);
  }
  public function test_query_table(){
    require_once  __DIR__.'/../sample-data/Service/PrefSearchService.php';
    $file = __DIR__.'/../sample-data/sample-01.sqlite';
    $file = realpath($file);
    $dsn = "sqlite:$file";
  
    $table = new \PrefSearchService($dsn);
    $ret = $table->findPref(1);
    $this->assertArrayHasKey('id', $ret);
    $this->assertEquals(1, $ret['id']);
  
    $table = new \PrefSearchService($dsn);
    $ret = $table->AddSearchByFreeWord('けん')
    ->AddSearchByFreeWord('やま')
      ->AddSearchByFreeWord('か')
      ->findPrefId();
    $this->assertEquals(['30','33'], $ret);
    $sql = $table->getLastPreparedQueryAsRawSQLforDebug();
    $this->assertEquals("select distinct id from pref where kana LIKE '%か%' AND kana LIKE '%やま%' AND kana LIKE '%けん%'"
    ,trim($sql));
  
    $table = new \PrefSearchService($dsn);
  
  }
  public function test_query_pdo_table(){
    require_once  __DIR__.'/../sample-data/Service/PrefSearchService.php';
    $file = __DIR__.'/../sample-data/sample-01.sqlite';
    $file = realpath($file);
    
    $table = new \PrefSearchService($dsn);
    $ret = $table->Where('kana','like','%きょう%')
      ->Limit(1)
      ->OrderBy('id', 'desc')
      ->fetchRows();
    $this->assertEquals(26,$ret[0]['id']);

    
  }

}