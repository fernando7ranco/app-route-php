<?php
$start_time = microtime(true); 

define('__APP_ROOT__', dirname(__DIR__));

require __APP_ROOT__ . '/vendor/autoload.php';

require __APP_ROOT__ . '/routes/web.php';

// End the clock time in seconds 
$end_time = microtime(true); 
  
// Calculate the script execution time 
$execution_time = ($end_time - $start_time); 

echo " It takes ".$execution_time." seconds to execute the script"; 
