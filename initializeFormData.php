<?php

/* 
 * This contains initializeFormDataAI/MM/PROJ - which is called from PrintReports and add forms, to 
 * initialize data arrays required to display/report on all fields.
 */

REQUIRE_ONCE 'dbInterfaceLibrary.php';

function initializeFormDataAI(&$ProjectArray,&$AssignedToArray,&$PriorityArray, &$StatusArray, &$MeetingArray) {
     
    /* fill arrays with data from tables that are needed for selection and check box lists */
    
    $fieldArray=array("id","project_number","project_name");
    $orderBy="Order By project_number ASC";
    $ProjectArray=getTableData('project_data',$fieldArray,'',$orderBy);
    
    $fieldArray=array("id","name_first","name_last");
    $orderBy="Order By name_last ASC";
    $AssignedToArray=getTableData('employee_data',$fieldArray,"",$orderBy);
    
    $fieldArray=array("id","value");
    $orderBy="";
    $PriorityArray=getTableData('ai_priority_table',$fieldArray,'',$orderBy);
    
    $fieldArray=array("id","value");
    $orderBy="";
    $StatusArray=getTableData('ai_status_table',$fieldArray,'',$orderBy);
    
    $fieldArray=array("id","meeting_date","meeting_title","project_data_id");
    $orderBy="Order By meeting_date DESC";
    $MeetingArray=getTableData('meeting_minutes_data',$fieldArray,'',$orderBy);
  
    //going to loop thru meeting array and add project_number field to is based on project_data_id.
    //echo 'is_array meeting:'.is_array($MeetingArray).' is_array project: '.is_array($ProjectArray).'<br><br>';
    if (is_array($MeetingArray) && is_array($ProjectArray)) {
    //going to loop thru ProjectArray and return tempArray[id]=[project_number];
        $len=count($ProjectArray);
        for ($i=0; $i<$len; ++$i) {
            $tempArray[$ProjectArray[$i]['id']]=$ProjectArray[$i]['project_number'];
        }
    
        $len=count($MeetingArray);
        //echo 'len: '.$len.'<br><br>';
        for ($i=0; $i<$len; ++$i) {
            //echo 'row: '.$i.'<br><br>';
            $projectNum='Project: None - ';
            if ($MeetingArray[$i]['project_data_id'] != '') {
                $projectNum='Project: '.$tempArray[$MeetingArray[$i]['project_data_id']].' - ';
            }
            //echo 'projectNum: '.$projectNum;
            $MeetingArray[$i]['meeting_title']=$projectNum.$MeetingArray[$i]['meeting_title'];
        }   
    }
}

function initializeFormDataPROJ(&$projectTypeArray,&$projectStatusArray,&$customerTypeArray,&$projectTaskDescArray,&$projectClassArray,&$employeeListArray,&$clientDataArray, &$endUserArray) {
     
    /* fill arrays with data from tables that are needed for selection and check box lists */
    
    $fieldArray=array("id","value");
    $orderBy="";
    $projectTypeArray=getTableData('project_type_table',$fieldArray,'',$orderBy);
    
    $projectStatusArray=getTableData('project_status_table',$fieldArray,'',$orderBy);
    
    $customerTypeArray=getTableData('client_type_table',$fieldArray,'',$orderBy);
    
    $orderBy="Order By value ASC";
    $projectTaskDescArray=getTableData('project_task_description_table',$fieldArray,'',$orderBy);
    
    $orderBy="Order By value ASC";
    $projectClassArray=getTableData('project_class_table',$fieldArray,'',$orderBy);
    
    $orderBy="Order By value ASC";
    $endUserArray=getTableData('project_end_user_table',$fieldArray,'',$orderBy);
    
    $fieldArray=array("id","name_first","name_last");
    $orderBy="Order By name_last ASC";
    $employeeListArray=getTableData('employee_data',$fieldArray,"",$orderBy);
    
    $fieldArray=array("id","client_name");
    $orderBy="Order By client_name ASC";
    $clientDataArray=getTableData('client_data',$fieldArray,'',$orderBy);
    
}
 
function initializeFormDataMM(&$ProjectArray, &$meetingTypeArray, &$employeeListArray, &$meetingComTypeArray, &$meetingLocationTypeArray) {
     
    /* fill arrays with data from tables that are needed for selection and check box lists */
    
    $fieldArray=array("id","project_number","project_name");
    $orderBy="Order By project_number ASC";
    $ProjectArray=getTableData('project_data',$fieldArray,'',$orderBy);
   
    $fieldArray=array("id","value");
    $orderBy="Order By value ASC";
    $meetingTypeArray=getTableData('meeting_type_table',$fieldArray,'',$orderBy);
    
    $fieldArray=array("id","name_first","name_last");
    $orderBy="Order By name_last ASC";
    $employeeListArray=getTableData('employee_data',$fieldArray,"",$orderBy);
    
    $fieldArray=array("id","value");
    $orderBy="Order By value ASC";
    $meetingComTypeArray=getTableData('meeting_com_type_table',$fieldArray,'',$orderBy);
    
    $fieldArray=array("id","value");
    $orderBy="Order By value ASC";
    $meetingLocationTypeArray=getTableData('meeting_location_table',$fieldArray,'',$orderBy);
    
}

function getDeliveryDateHistoryForAI($ai,$reportFlag) {

$fieldArray=array('*');
$whereClause='WHERE action_item_id='.$ai;
$table='ai_delivery_date_data';

/* this might be used in the future */
$orderBy='Order By data_entry_timestamp DESC';
/* call getRowData with tablename and whereclause, all fields */

$tableArray='';

$tableArray=getTableData($table,$fieldArray,$whereClause,$orderBy);
$returnVal='';

if (is_array($tableArray)){
    /* convert first index to associative, because now it returns index in first field.
     */
    $len=count($tableArray);
    $returnVal='';
    $class='dateHistoryMsgBox';
    $indent='';
    if ($reportFlag) {
        $indent='&nbsp; &nbsp;';
    }
    //we don't want to show the last record, because it is the initial one.
    for ($i=0; $i<$len-1; ++$i) {
        $returnVal=$returnVal.'<p class="'.$class.'"><b>Date Due:</b> '.$tableArray[$i]['deliverable_date_due'].'      <b>Delivered:</b> '.$tableArray[$i]['deliverable_date_actual'].'</p>';
        $returnVal=$returnVal.'<p class="'.$class.'"><b>'.$indent.'Reason:</b> ';
        $returnVal=$returnVal.$tableArray[$i]['reason_for_date_change'].'</p>';
        $returnVal=$returnVal.'<p>  </p>';
    }
    //add original date without reason
     $returnVal=$returnVal.'<p class="'.$class.'"><b>Date Due:</b> '.$tableArray[$len-1]['deliverable_date_due'].'      <b>Delivered:</b> '.$tableArray[$len-1]['deliverable_date_actual'].'</p>';
}
else {
    $returnVal='<p class="dateHistoryMsgBox">Delivery Date History:  Data Not Available.</p>';
}

return $returnVal;
}

?>