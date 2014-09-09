<?php

REQUIRE_ONCE 'dbInterfaceLibrary.php';
REQUIRE_ONCE 'initializeFormData.php';
REQUIRE_ONCE 'reportFunctions.php';

/* this script is called from showReport.  reportDiv on showReport is set by this function via ajax get call. */
/* The stlyle for the reports is in ReportStyle.css, included in showReport.php. */
/* Parameters:
 * reportType:  used to determine the type of report to print.  Supported values:  MeetingMinutes, 
 *      ActionItems, Projects.
 * rowsToPrint: the row ids that will be printed on the report.  The data will be printed in the order determined by
 *      rowsToPrint, because user is filtering/sorting in a table view.  We want the printed
 *      report to match the users filters/sort order.
 */

/* get report parameters */
$reportType=$_GET['reportType'];
$rowIds=$_GET['rowsToPrint'];

if (trim($rowIds) == '') {
    echo 'No Data For Report.';
}
else {
    switch ($reportType) {
        case 'MeetingMinutes':
            outputMeetingMinutesHTML($rowIds);
            break;
        case 'ActionItems':
            outputActionItemsHTML($rowIds);
            break;
        case 'Projects':
            outputProjectsHTML($rowIds);
            break;
        default:
            echo 'Report Type '.$reportType.' is not supported.';
            break;
    }
}

/* individual functions to output the report types */

function outputActionItemsHTML($rows) {
    
    $ProjectArray='';
    $AssignedToArray='';
    $PriorityArray='';
    $StatusArray='';
    $MeetingArray='';
    
    /* this initializes data arrays to display data on report */
    initializeFormDataAI($ProjectArray,$AssignedToArray,$PriorityArray,$StatusArray,$MeetingArray);
    
     /* reorder arrays by id - so that it is quick to lookup value for id */
    $ProjectArray=reorderArrayById($ProjectArray);  
    $AssignedToArray=reorderArrayById($AssignedToArray);
    $PriorityArray=reorderArrayById($PriorityArray);
    $StatusArray=reorderArrayById($StatusArray);
    $MeetingArray=reorderArrayById($MeetingArray); 
    
    /* get data for report and output */
    /* we want to keep the order that was on the table */
    $fieldArray=array('*');
    $whereClause='WHERE id in ('.$rows.')';
    $orderBy="ORDER BY FIELD (action_item_data.id,".$rows.")";
    $dataArray=getTableData('action_item_data',$fieldArray,$whereClause,$orderBy);
    
    displayHeader('Action Items');
    
    if (is_array($dataArray)) {
        foreach ($dataArray as $rowNum => $value) {
            
            $rowId=$dataArray[$rowNum]['id'];
               
            outputData('oneColumnDiv','','STARTDIV');
            outputData('AI Title',$dataArray[$rowNum]['task_title'],'TITLE');
           
            $value=returnValueForId($dataArray[$rowNum]['project_data_id'],$ProjectArray,'project_number','project_name',' - ');
            outputData('Project',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['meeting_data_id'],$MeetingArray,'meeting_date','meeting_title',' - ');
            outputData('Meeting',$value,'DATA');
           
            outputData('oneColumnDiv','','ENDDIV');
            outputData('leftColumnDiv','','STARTDIV');
                 
            $value=returnValueForId($dataArray[$rowNum]['assigned_to_employee_id'],$AssignedToArray,'name_first','name_last',' ');
            outputData('Assigned To',$value,'DATA'); 
            outputData('Date Assigned',$dataArray[$rowNum]['date_opened'],'DATA'); 
            outputData('Date Due',$dataArray[$rowNum]['date_due'],'DATA'); 
            
            $deliv='No';
            if ($dataArray[$rowNum]['deliverable_flag'] == 1) {
                $deliv='Yes';
            }
            
            outputData('Project Deliverable',$deliv,'DATA');
            outputData('leftColumnDiv','','ENDDIV');
            
            outputData('rightColumnDiv','','STARTDIV');
            
            $value=returnValueForId($dataArray[$rowNum]['ai_priority_id'],$PriorityArray,'value','','');
            outputData('Priority',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['ai_status_id'],$StatusArray,'value','','');
            outputData('Status',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['closed_by_employee_id'],$AssignedToArray,'name_first','name_last',' ');
            outputData('Closed By',$value,'DATA');          
            
            $value=returnValueForId($dataArray[$rowNum]['entered_by_employee_id'],$AssignedToArray,'name_first','name_last',' ');
            outputData('Entered By',$value,'DATA'); 
            outputData('rightColumnDiv','','ENDDIV');
            
            outputData('oneColumnDiv','','STARTDIV');
             /* output delivery date history */
            if ($deliv == 'Yes') {
                $returnVal=getDeliveryDateHistoryForAI($rowId,1);
                /* put deliverable date history in div */
                outputData('delivDateDiv','','STARTDIV');
                outputData('Delivery Date History',$returnVal,'DATA');
                outputData('delivDateDiv','','ENDDIV');
            }         
            //output task and notes field.
            outputData('Task Description',$dataArray[$rowNum]['task_description'],'BLOB');
            outputData('Notes',$dataArray[$rowNum]['ai_notes'],'BLOB');           
            outputData('oneColumnDiv','','ENDDIV');
        }
    }
}

function outputProjectsHTML($rows) {

    /* initialize arrays */
    $projectTypeArray="";
    $projectStatusArray="";
    $customerTypeArray="";
    $projectTaskDescArray="";
    $projectClassArray="";
    $employeeListArray="";
    $clientDataArray="";
    $endUserArray="";

    /* this initializes the select and checkbox lists that are populated from the database */
    initializeFormDataPROJ($projectTypeArray,$projectStatusArray,$customerTypeArray,$projectTaskDescArray,$projectClassArray,$employeeListArray,$clientDataArray,$endUserArray);

    $projectTypeArray=reorderArrayById($projectTypeArray);
    $projectStatusArray=reorderArrayById($projectStatusArray);
    $customerTypeArray=reorderArrayById($customerTypeArray);
    $projectTaskDescArray=reorderArrayById($projectTaskDescArray);
    $projectClassArray=reorderArrayById($projectClassArray);
    $employeeListArray=reorderArrayById($employeeListArray);
    $clientDataArray=reorderArrayById($clientDataArray);
    $endUserArray=reorderArrayById($endUserArray);
    
    /* select data maintaining the order that was on the table */
    $fieldArray=array('*');
    $whereClause='WHERE id in ('.$rows.')';
    $orderBy="ORDER BY FIELD (project_data.id,".$rows.")";
    $dataArray=getTableData('project_data',$fieldArray,$whereClause,$orderBy);
    
    displayHeader('Projects');
    
    if (is_array($dataArray)) {
        foreach ($dataArray as $rowNum => $value) {         
            $rowId=$dataArray[$rowNum]['id'];
            
            outputData('oneColumnDiv','','STARTDIV');
            outputData('Project Number',$dataArray[$rowNum]['project_number'],'TITLE');
            outputData('oneColumnDiv','','ENDDIV');
        
            outputData('leftColumnDiv','','STARTDIV');
            outputData('Project Name',$dataArray[$rowNum]['project_name'],'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['client_data_id'],$clientDataArray,'value','','');
            outputData('Client',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['project_status_id'],$projectStatusArray,'value','','');
            outputData('Project Status',$value,'DATA');
            
            outputData('Closed Date',$dataArray[$rowNum]['contract_closed_date'],'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['project_manager_id'],$employeeListArray,'name_first','name_last',' ');
            outputData('Project Manager',$value,'DATA');
            outputData('leftColumnDiv','','ENDDIV');
            
            outputData('rightColumnDiv','','STARTDIV');
            $value=returnValueForId($dataArray[$rowNum]['project_type_id'],$projectTypeArray,'value','','');
            outputData('Project Type',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['entered_by_employee_id'],$employeeListArray,'name_first','name_last',' ');
            outputData('Project Entered By',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['client_type_id'],$customerTypeArray,'value','','');
            outputData('Client Type',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['end_user_id'],$endUserArray,'value','','');
            outputData('End User',$value,'DATA');
            outputData('rightColumnDiv','','ENDDIV');
            
            outputData('oneColumnDiv','','STARTDIV');
            
            //resources assigned
            $whereClause='WHERE employee_data.id in (select project_resources_data.employee_data_id FROM project_resources_data WHERE project_resources_data.project_id='.$rowId.')';
            $orderBy='ORDER BY name_last, name_first DESC';
            $fieldArray=array("employee_data.id","employee_data.name_first","employee_data.name_last");
            $resourcesArray=getTableData('employee_data',$fieldArray,$whereClause,$orderBy);
           
            if (is_array($resourcesArray)) {
                $value='';
                foreach ($resourcesArray As $resCnt => $resValue) {
                    $value=$value.$resourcesArray[$resCnt]['name_first'].' '.$resourcesArray[$resCnt]['name_last'].', ';
                }
                //remove last ,
                $value=rtrim($value, ", ");
                outputData('Resources Assigned',$value,'DATA');
            }
            else {
                outputData('Resources Assigned','No Entry','DATA');
            }
            
            // class/project category
            $whereClause='WHERE id in (select project_class_data.project_class_id FROM project_class_data WHERE project_class_data.project_id='.$rowId.')';
            $orderBy='ORDER BY value DESC';
            $fieldArray=array("id","value");
            $resourcesArray=getTableData('project_class_table',$fieldArray,$whereClause,$orderBy);
           
            if (is_array($resourcesArray)) {
                $value='';
                foreach ($resourcesArray As $resCnt => $resValue) {
                    $value=$value.$resourcesArray[$resCnt]['value'].', ';
                }
                //remove last ,
                $value=rtrim($value, ", ");
                outputData('Project Class/Category',$value,'DATA');
            }
            else {
                outputData('Project Class/Category','No Entry','DATA');
            }
            
           outputData('Project Description',$dataArray[$rowNum]['project_description'],'BLOB');
           outputData('oneColumnDiv','','ENDDIV');
                     
        }
    }  
}

function outputMeetingMinutesHTML($rows) {   
    
    /* initialize data arrays needed to display all row data */
    initializeFormDataMM($ProjectArray, $meetingTypeArray, $employeeListArray,$meetingComTypeArray,$meetingLocationTypeArray);
    
    /* reorder arrays by id - so that it is quick to lookup value for id */
    $ProjectArray=reorderArrayById($ProjectArray);  
    $meetingTypeArray=reorderArrayById($meetingTypeArray); 
    $employeeListArray=reorderArrayById($employeeListArray);
    $meetingComTypeArray=reorderArrayById($meetingComTypeArray); 
    $meetingLocationTypeArray=reorderArrayById($meetingLocationTypeArray);
    
    /* select data maintaining the order that was on the table */
    $fieldArray=array('*');
    $whereClause='WHERE id in ('.$rows.')';
    $orderBy="ORDER BY FIELD (meeting_minutes_data.id,".$rows.")";
    $dataArray=getTableData('meeting_minutes_data',$fieldArray,$whereClause,$orderBy);
    
    //start outputting data for report.
    displayHeader('Meeting Minutes');
    
    if (is_array($dataArray)) {
        foreach ($dataArray as $rowNum => $value) {         
            $meetingId=$dataArray[$rowNum]['id'];
               
            outputData('oneColumnDiv','','STARTDIV');
            outputData('Meeting Title',$dataArray[$rowNum]['meeting_title'],'TITLE');
            outputData('oneColumnDiv','','ENDDIV');
            
            outputData('leftColumnDiv','','STARTDIV');
            outputData('Meeting Date',$dataArray[$rowNum]['meeting_date'],'DATA');
            outputData('Meeting Start Time',$dataArray[$rowNum]['meeting_start_time'],'DATA');
            outputData('Meeting End Time',$dataArray[$rowNum]['meeting_end_time'],'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['meeting_type_id'],$meetingTypeArray,'value','','');
            outputData('Meeting Type',$value,'DATA');
            
            $value=returnValueForId($dataArray[$rowNum]['meeting_com_type_id'],$meetingComTypeArray,'value','','');
            outputData('Meeting By',$value,'DATA');
            outputData('leftColumnDiv','','ENDDIV');
            
            outputData('rightColumnDiv','','STARTDIV');
            $value=returnValueForId($dataArray[$rowNum]['project_data_id'],$ProjectArray,'project_number','project_name',' - ');
            outputData('Project',$value,'DATA');
            $value=returnValueForId($dataArray[$rowNum]['scribe_id'],$employeeListArray,'name_first','name_last',' ');
            outputData('Meeting Scribe',$value,'DATA');
            $value=returnValueForId($dataArray[$rowNum]['meeting_location_id'],$meetingLocationTypeArray,'value','','');
            outputData('Meeting Location',$value.' - '.$dataArray[$rowNum]['location'],'DATA');         
            outputData('rightColumnDiv','','ENDDIV');
            
            outputData('oneColumnDiv','','STARTDIV');

            //with meetingId - get participants in meeting.
            $whereClause='WHERE employee_data.id in (select meeting_participants_data.employee_data_id FROM meeting_participants_data WHERE meeting_participants_data.meeting_minutes_id='.$meetingId.')';
            $orderBy='ORDER BY name_last, name_first DESC';
            $fieldArray=array("employee_data.id","employee_data.name_first","employee_data.name_last");
            $attendeesArray=getTableData('employee_data',$fieldArray,$whereClause,$orderBy);
           
            if (is_array($attendeesArray)) {
                $value='';
                foreach ($attendeesArray As $attendeeCnt => $partValue) {
                    $value=$value.$attendeesArray[$attendeeCnt]['name_first'].' '.$attendeesArray[$attendeeCnt]['name_last'].', ';
                }
                //remove last ,
                $value=rtrim($value, ", ");
                outputData('Meeting Participants',$value,'DATA');
            }
            else {
                outputData('Meeting Participants','No Entry','DATA');
            }
            
            outputData('Meeting Minutes',$dataArray[$rowNum]['meeting_minutes'],'BLOB');
            outputData('Agenda',$dataArray[$rowNum]['agenda'],'BLOB');
            outputData('Actions Required',$dataArray[$rowNum]['actions_required'],'BLOB');            
            outputData('oneColumnDiv','','ENDDIV');
        }
    }   
}

?>

