<?php

/* 
 This is called by Javascript AJAX call to file data database.
 * Parameters:
 * tableName:  database table name.
 * whereClause:  where clause used in insert/update sql statement.
 * deleteFlag:  set to 1 if deleting rows instead of inserting/updating.
 * dataToFile:  data to file sent in JSON format.
 * editRowId:  row id or row to update if updating an existing row.
 */

REQUIRE_ONCE 'dbInterfaceLibrary.php';
header("Cache-Control: no-cache"); 


/* this files data in database, add, update, delete */
/* tableName, Data, whereClause, deleteFlag = 0/1 - sent in. */


/* debug
foreach ($_POST As $item => $value) {
    $returnVal=$returnVal."  Post item: ".$item." value: ".$value;
}
*/

$dataToFile='';
$whereClause='';
$deleteFlag='';
$tableName='';
$editRowId='';

/* get input parameters.  
 * dataToFile - is JSON string.
 */

if (isset($_POST['tableName'])) $tableName=$_POST['tableName'];
if (isset($_POST['whereClause'])) $whereClause=$_POST['whereClause'];
if (isset($_POST['deleteFlag'])) $deleteFlag=$_POST['deleteFlag'];
if (isset($_POST['dataToFile'])) $dataToFile=$_POST['dataToFile'];
if (isset($_POST['editRowId'])) $editRowId=$_POST['editRowId'];

/* sales id and creat project flag should be included in datToFile */
$returnVal='';

//call file data or delete data based on what is sent in
if ($deleteFlag) {
     deleteTableRowData($tableName,$whereClause);
    $returnVal='1|Row Deleted.'.'RowId: '.$editRowId;
}
else {
    
    /* this is what $dataToFile looks like:
      {"task_title":"Task Title 3","rfp_date":"2014-01-01","proposal_due_date":"2014-06-01","contract_status_id":"5","quote":"quote3","ant_award_date":"2014-08-07","award_date":"","pop_start_date":"2014-08-19","pop_end_date":"","project_number":""} 
     * 
     */
    //this should decode a json string to an associative array
    $fileArray = json_decode($dataToFile, true);
   
    //don't really need to initialize this, but will anyway.
    $tableRowId='';
    
    $tableRowId=updateTableData($tableName,$fileArray,$editRowId);
    
    //Additional processing may be added here in the future.  
    //For example - if this is task table, row in project table may be inserted.
    
    $returnVal='1|Row Saved.'.'RowId: '.$tableRowId;
}

echo $returnVal;
?>
