<?php 
namespace Auwa;
/*
 * MIDDLE WAY : index.php
 *
 * First file called
 *
 * Call the Core and the launch Middle Way
*/
$timestart=microtime(true);
header("Pragma: no-cache"); 
include ('core/core.php');

Auwa::launch();
?>
