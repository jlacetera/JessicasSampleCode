<?php

/* 
 This executes a select statement on database and returns data in JSON format.  Called by AJAX call.
 * Parameters:
 *  TABLE:  database table  name.
 *  WHERE:  where clause to be used with select statement.
 * 
 * Return Value:
 *  All columns in table are selected and returns in JSON format.
 */

REQUIRE_ONCE 'dbInterfaceLibrary.php';
header("Cache-Control: no-cache"); 

$table='';
$whereClause='';

 if (isset($_GET['TABLE'])) {
        $table=$_GET['TABLE'];
    } 

if (isset($_GET['WHERE'])) {
        $whereClause=$_GET['WHERE'];
    }     
$returnVal='';

$fieldArray=array('*');

/* this might be used in the future */
$orderBy='';
/* call getRowData with tablename and whereclause, all fields */

$tableArray='';

//$returnVal='table: '.$table.' whereClause: '.$whereClause;

$tableArray=getTableData($table,$fieldArray,$whereClause,$orderBy);

if (is_array($tableArray)){
    /* convert first index to associative, because now it returns index in first field.
     */
    foreach ($tableArray As $index => $val) {
        $resultsArray['row'.$index]=$tableArray[$index];
    }
    $returnVal=json_encode($resultsArray);
}
else {
    $returnVal='Error from table: '.$tableArray;
}

echo $returnVal;
?>
