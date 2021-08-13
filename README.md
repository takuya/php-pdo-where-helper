# php-pdo-where-helper

極力、外部依存を減らしてぱぱっとSQLをビルドして検索できるようにした。

SQL実行ではprepare書く必要があるが、Prepareを書くとソースコードが煩雑になるのでなるべく隠蔽するようにした。

SQLの実行結果を再利用することでキャッシュしてパフォーマンスを上げるようにしてある。



## examples 

```php
<?php
    $dsn = "sqlite:pref.sqlite";
    $table = new \PrefSearchService($dsn);
    $ret = $table->Where('kana','like','%きょう%')
      ->Limit(1)
      ->OrderBy('id', 'desc')
      ->fetchRows();

```
