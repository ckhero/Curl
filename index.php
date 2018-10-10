<?php
/**
 * Created by PhpStorm.
 * User: ckhero
 * Date: 2018/10/10
 * Time: 2:42 PM
 */

require dirname(__FILE__) . '/vendor/autoload.php';

use Curl\Curl;
var_dump($_SERVER);
for($i = 0; $i < 2; $i++) {
    $url = "http://www.mafengwo.cn/i/10465318.html";
    $curl = (new Curl());
    $res = $curl->get($url, ['ip' => '186.65.87.18', 'port' => 49839]);
    var_dump($curl->getErrorInfo(), $curl->getOptions());
}