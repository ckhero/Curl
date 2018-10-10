<?php
/**
 * Created by PhpStorm.
 * User: ckhero
 * Date: 2018/10/10
 * Time: 4:46 PM
 */

file_put_contents('test.log', json_encode($_SERVER) . PHP_EOL, FILE_APPEND);