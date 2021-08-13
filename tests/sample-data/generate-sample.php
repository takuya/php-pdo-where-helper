<?php

function createSampleDB(){
  
  $file = __DIR__.DIRECTORY_SEPARATOR.'sample-01.sqlite';
  if ( file_exists($file)){
    unlink($file);
  }
  $dsn = "sqlite:$file";
  $pdo = new PDO($dsn);
  $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  
  $sql = [
    "drop table if exists pref;",
    "
    create table pref (
        id integer primary key,
        name text,
        kanji text,
        kana text
    );",
    "INSERT into pref  values ('1','hokkaido','北海道','ほっかいどう');",
    "INSERT into pref  values ('2','aomori','青森県','あおもりけん');",
    "INSERT into pref  values ('3','iwate','岩手県','いわてけん');",
    "INSERT into pref  values ('4','miyagi','宮城県','みやぎけん');",
    "INSERT into pref  values ('5','akita','秋田県','あきたけん');",
    "INSERT into pref  values ('6','yamagata','山形県','やまがたけん');",
    "INSERT into pref  values ('7','fukusima','福島県','ふくしまけん');",
    "INSERT into pref  values ('8','ibaraki','茨城県','いばらきけん');",
    "INSERT into pref  values ('9','tochigi','栃木県','とちぎけん');",
    "INSERT into pref  values ('10','gumma','群馬県','ぐんまけん');",
    "INSERT into pref  values ('11','saitama','埼玉県','さいたまけん');",
    "INSERT into pref  values ('12','chiba','千葉県','ちばけん');",
    "INSERT into pref  values ('13','tokyo','東京都','とうきょうと');",
    "INSERT into pref  values ('14','kanagawa','神奈川県','かながわけん');",
    "INSERT into pref  values ('15','niegata','新潟県','にいがたけん');",
    "INSERT into pref  values ('16','toyama','富山県','とやまけん');",
    "INSERT into pref  values ('17','ishikawa','石川県','いしかわけん');",
    "INSERT into pref  values ('18','fukui','福井県','ふくいけん');",
    "INSERT into pref  values ('19','yamanashi','山梨県','やまなしけん');",
    "INSERT into pref  values ('20','nagano','長野県','ながのけん');",
    "INSERT into pref  values ('21','gifu','岐阜県','ぎふけん');",
    "INSERT into pref  values ('22','sizuoka','静岡県','しずおかけん');",
    "INSERT into pref  values ('23','aichi','愛知県','あいちけん');",
    "INSERT into pref  values ('24','mie','三重県','みえけん');",
    "INSERT into pref  values ('25','shiga','滋賀県','しがけん');",
    "INSERT into pref  values ('26','kyoto','京都府','きょうとふ');",
    "INSERT into pref  values ('27','osaka','大阪府','おおさかふ');",
    "INSERT into pref  values ('28','hyogo','兵庫県','ひょうごけん');",
    "INSERT into pref  values ('29','nara','奈良県','ならけん');",
    "INSERT into pref  values ('30','wakayama','和歌山県','わかやまけん');",
    "INSERT into pref  values ('31','tottori','鳥取県','とっとりけん');",
    "INSERT into pref  values ('32','shimane','島根県','しまねけん');",
    "INSERT into pref  values ('33','okayama','岡山県','おかやまけん');",
    "INSERT into pref  values ('34','hirosima','広島県','ひろしまけん');",
    "INSERT into pref  values ('35','yamaguchi','山口県','やまぐちけん');",
    "INSERT into pref  values ('36','tokushima','徳島県','とくしまけん');",
    "INSERT into pref  values ('37','kagawa','香川県','かがわけん');",
    "INSERT into pref  values ('38','ehime','愛媛県','えひめけん');",
    "INSERT into pref  values ('39','kochi','高知県','こうちけん');",
    "INSERT into pref  values ('40','fukuoka','福岡県','ふくおかけん');",
    "INSERT into pref  values ('41','saga','佐賀県','さがけん');",
    "INSERT into pref  values ('42','nagasaki','長崎県','ながさきけん');",
    "INSERT into pref  values ('43','kumamoto','熊本県','くまもとけん');",
    "INSERT into pref  values ('44','ooita','大分県','おおいたけん');",
    "INSERT into pref  values ('45','miyazaki','宮崎県','みやざきけん');",
    "INSERT into pref  values ('46','kagoshima','鹿児島県','かごしまけん');",
    "INSERT into pref  values ('47','okinawa','沖縄県','おきなわけん');",
    "select * from pref;",
  ];
  
  foreach ($sql as $s) {
    $st = $pdo->prepare($s);
    $ret = $st->execute() ;
    $ret = $st->fetchAll(PDO::FETCH_ASSOC);
    var_dump($ret);
  }
  
}

try {
  createSampleDB();
  
}catch (\Exception $e){
  throw $e;
}


