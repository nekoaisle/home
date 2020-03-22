<?php
/**
 * 
 * 
 * filename:  arduino.php
 * 
 * @package   
 * @version   1.0.0
 * @copyright Copyright (C) 2017 CREANSMAERD CO.,LTD.  All rights reserved.
 * @date      2017-05-23
 * @author    木屋 善夫
 */

$s = serialize( $_REQUEST );
$now = date( 'Ymd-His' );
$fn = "../log/arduino/{$now}.txt";
file_put_contents( $fn, $s );
echo 'Ok';
?>