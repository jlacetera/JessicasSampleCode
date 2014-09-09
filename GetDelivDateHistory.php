<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

REQUIRE_ONCE 'dbInterfaceLibrary.php';
REQUIRE_ONCE 'initializeFormData.php';

$table='';
$whereClause='';

 if (isset($_GET['AI'])) {
        $AI=$_GET['AI'];
    }
    
$returnVal='';
//$returnVal='in GetDelivDateHistory<br>';

$returnVal=getDeliveryDateHistoryForAI($AI,0);

echo $returnVal;

?>
