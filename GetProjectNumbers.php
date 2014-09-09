<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

REQUIRE_ONCE 'dbInterfaceLibrary.php';
header("Cache-Control: no-cache"); 

/* connect to database and get list of ids and project numbers to return to javascript function */

$fieldArray=array('id','project_number');
$whereClause='';
/* this didn't work - need to find out how to pass/access data sent in from ajax call */

if (isset($_REQUEST['projNum'])) {
    $projectNumber=$_REQUEST['projNum'];
    $whereClause=' where project_number <> "'.$projectNumber.'"';
 }
 
//only 1 row should be returned for this query, or 0 rows
$projectNumberArray=getTableData('project_data',$fieldArray,$whereClause,'');

$returnVal='';

//return JSON format for javascript to use data.
/** {"projectNumbers":[
    {"id":"1", "projectNumber":"14101"}, 
    {"id":"2", "projectNumber":"14102"},
    {"id":"3", "projectNumber":"14103"}
 ]}
**/


if (isset($projectNumberArray)) {
    $returnVal='[';
    foreach($projectNumberArray As $item=>$value) {
        $returnVal=$returnVal.'{"id": "'.$projectNumberArray[$item]['id'].'", "projectNumber":"'.$projectNumberArray[$item]['project_number'].'"},';
        
        //$returnVal=$returnVal.','.'id: '.$projectNumberArray[$item]['id'].' number: '.$projectNumberArray[$item]['project_number'].',';
    }
  //strip off last , - not needed.
    
  $returnVal=substr($returnVal,0,strlen($returnVal)-1);  
  
  $returnVal=$returnVal.']';
}

//ideally - want to return list of project numbers from database in JSON format.
echo $returnVal;

?>