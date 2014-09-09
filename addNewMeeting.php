<!DOCTYPE html>

<?php

/* define all include files required for this form */
REQUIRE_ONCE 'htmlRenderingLibrary.php';
REQUIRE_ONCE 'dbInterfaceLibrary.php';
REQUIRE_ONCE 'initializeFormData.php';

//phpinfo(); just for debug - shows you what versions are running.

//initialize error handling variables

$postError=0;
$postMessage='';
$dataFiled=0;

/* This is called when POST method is called.
 *  */
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    /* get row id being edited, if this is an edit */
    $editRowId=getFormValue('editId','TXT');
    
    /* fill array with column name=column value */
    $resultsArray['project_data_id']=getFormValue('project','TXT');
    $resultsArray['meeting_date']=getFormValue('dateOfmeeting','DATE');
    $resultsArray['meeting_type_id']=getFormValue('typemeeting','TXT');
    $resultsArray['meeting_minutes']=getFormValue('minutes','BLOB');
    $resultsArray['actions_required']=getFormValue('actions','BLOB');
    $resultsArray['other_attendees']=getFormValue('otherAttendees','TXT');
    $resultsArray['meeting_start_time']=getFormValue('startTime','TIME');
    $resultsArray['meeting_end_time']=getFormValue('endTime','TIME');
    $resultsArray['meeting_title']=getFormValue('meetingTitle','TXT');
    $resultsArray['scribe_id']=getFormValue('scribe','TXT');
    $resultsArray['location']=getFormValue('location','TXT');
    $resultsArray['agenda']=getFormValue('agenda','BLOB');
    $resultsArray['meeting_location_id']=getFormValue('meeting_location_id','TXT');
    $resultsArray['meeting_com_type_id']=getFormValue('meeting_com_type_id','TXT');
    
    //echo 'resultsArray meeting_minutes: '.$resultsArray['meeting_minutes'].' POST: '.$_POST['minutes'].'<br><br>';
    
    /* setup checkbox array/results for meeting participants */
    
    if (isset($_POST['meetingParticipants'])) {
        $resultsArrayMeetingParticipants=getFormValue('meetingParticipants','IDARRAY');
        foreach ($resultsArrayMeetingParticipants As $item=>$value) {
            $meetingParticipantsCheckedArray[$value]=$value;
        }
    }
    
    //call function in dbInterfaceLibrary to connect and file data
    
    $tableRowId=updateTableData('meeting_minutes_data',$resultsArray,$editRowId);
    //$tableRowId="";
    ////
    //////if error - then display message popup and don't re-initialize form.
    //else - re-initialize form data.  make sure POST flag cleared.
    if ($tableRowId=="") {
        /* put some error processing here - changes not filed */
        //echo 'data didnt file <br><br>';
        $postError=1;
        $postMessage="Error filing data.  Please contact IT Department.";
     }
    else {
        //child tables:  meeting_participants_data
        
        //first delete original table data for this project, because we will file with new fields that are checked.
        //for an adding new record, this step can be skipped.
        
        $whereClause='WHERE meeting_minutes_id = '.$tableRowId;
        deleteTableRowData('meeting_participants_data',$whereClause);
        
        /* for each checkbox list - file multiple rows. */
        
         if (isset($meetingParticipantsCheckedArray)) {
            $rowNum=0;
            foreach ($meetingParticipantsCheckedArray As $item=>$value) {
                //
                $fileArrayMeetingParticipants[$rowNum]['meeting_minutes_id']=$tableRowId;
                $fileArrayMeetingParticipants[$rowNum]['employee_data_id']=$item;
                $rowNum++;
            }
            insertMultipleTableRows('meeting_participants_data',$fileArrayMeetingParticipants);
        }
    }
    if ($postError==0) {
        $dataFiled=1;
    }
}  //end of POST section.

/* initialize arrays */

$ProjectArray="";
$meetingTypeArray="";
$employeeListArray="";
$meetingComTypeArray='';
$meetingLocationArray='';

/* this initializes the select and checkbox lists that are populated from the database */
initializeFormDataMM($ProjectArray, $meetingTypeArray, $employeeListArray,$meetingComTypeArray,$meetingLocationArray);

/* initialize html list that is used more than once for time selection */
/* this isn't used anymore.  JQueryUI time selector used. */
//$timeSelectionList=createTimeSelectionDataList('07:00AM','11:00PM',15,'timeList',0);

//if postError - then initialize from ResultsArray
if (($postError==1) || ($dataFiled == 1)) {      
 
    $project=$resultsArray['project_data_id'];
    $typemeeting=$resultsArray['meeting_type_id'];
    $dateOfmeeting=$resultsArray['meeting_date'];
    $minutes=$resultsArray['meeting_minutes'];
    $actions=$resultsArray['actions_required'];
    $startTime=$resultsArray['meeting_start_time'];
    $endTime=$resultsArray['meeting_end_time'];
    $otherAttendees=$resultsArray['other_attendees'];
    $meetingTitle=$resultsArray['meeting_title'];
    $scribe=$resultsArray['scribe_id'];
    $location=$resultsArray['location'];
    $agenda=$resultsArray['agenda'];
    $meeting_com_type_id=$resultsArray['meeting_com_type_id'];
    $meeting_location_id=$resultsArray['meeting_location_id'];
    
    
    $editId=$editRowId;
    
    //checkbox array already setup in POST
    //$meetingParticipantsCheckedArray
}
/* initialize all fields on form to blank or load from selected id - if no postError set */
else {
    $project="";
    $typemeeting="";
    $dateOfmeeting="";
    $minutes="";
    $actions="";
    $startTime="";
    $endTime="";
    $meetingTitle='';
    $otherAttendees="";
    $scribe='';
    $location='';
    $agenda='';
    $meeting_com_type_id='';
    $meeting_location_id='';
    $editId="";

    /* this is the ID sent by calling form, which will be set only if we are editing an existing row */
    if (isset($_GET['ID'])) {
        $editId=$_GET['ID'];
    }
  
    //These next 1 are for checklists, will be initiated to array of codes selected if we are doing an update.
    //for in itializing - not sure about this, but works for now.

    $meetingParticipants=array("0");

    if ($editId!='') {
        $whereClause='WHERE id = '.$editId;
    
        //setup to select all fields from table.
        $fieldArray=array('*');
    
        //only 1 row should be returned for this query
        $meetingDataArray=getTableData('meeting_minutes_data',$fieldArray,$whereClause,'');
    
        /* for debug only
        foreach ($meetingDataArray[0] As $item => $value) {
         echo 'item: '.$item.' value: '.$value.' <br><br>';
        }
        */
        
        if (isset($meetingDataArray[0]['project_data_id']))         $project=$meetingDataArray[0]['project_data_id'];    
        if (isset($meetingDataArray[0]['meeting_date']))            $dateOfmeeting=$meetingDataArray[0]['meeting_date'];
        if (isset($meetingDataArray[0]['meeting_type_id']))         $typemeeting=$meetingDataArray[0]['meeting_type_id'];
        if (isset($meetingDataArray[0]['meeting_minutes']))         $minutes=$meetingDataArray[0]['meeting_minutes'];
        if (isset($meetingDataArray[0]['actions_required']))        $actions=$meetingDataArray[0]['actions_required'];
        if (isset($meetingDataArray[0]['other_attendees']))         $otherAttendees=$meetingDataArray[0]['other_attendees'];
        if (isset($meetingDataArray[0]['meeting_start_time']))      $startTime=$meetingDataArray[0]['meeting_start_time'];
        if (isset($meetingDataArray[0]['meeting_end_time']))        $endTime=$meetingDataArray[0]['meeting_end_time'];
        if (isset($meetingDataArray[0]['meeting_title']))           $meetingTitle=$meetingDataArray[0]['meeting_title'];
        if (isset($meetingDataArray[0]['scribe_id']))               $scribe=$meetingDataArray[0]['scribe_id'];
        if (isset($meetingDataArray[0]['location']))                $location=$meetingDataArray[0]['location'];
        if (isset($meetingDataArray[0]['agenda']))                  $agenda=$meetingDataArray[0]['agenda'];
        if (isset($meetingDataArray[0]['meeting_location_id']))     $meeting_location_id=$meetingDataArray[0]['meeting_location_id'];
        if (isset($meetingDataArray[0]['meeting_com_type_id']))     $meeting_com_type_id=$meetingDataArray[0]['meeting_com_type_id'];
        
        /* need to setup checkbox list array with data from table
        * meetingParticipantsCheckedArray
        */ 

        $whereClause='WHERE meeting_minutes_id = '.$editId;
        $fieldArray=array('employee_data_id');
        $meetingParticipantsCheckedArray=getTableData('meeting_participants_data',$fieldArray,$whereClause,'');
  
        /* these arrays are in the format array[rowNum]['idfield']=idValue.
        * it would be easier when initializing checkbox to reorder them so that they look like:
        * array[idValue]=[idValue] so that you can just check if the value is set in the array
        * to set item as checked.
        */
    
        $meetingParticipantsCheckedArray=reorderArrayIndexByCode($meetingParticipantsCheckedArray);
    }
}  //end if no postError
?>
<html>
    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Meeting Minutes Form</title>

    <?php
         //include scripts and styles
        include 'standardFormScriptsCSS.php';
    ?>
        
    </head>
    <body>
    <br>
    <div class= "main">
        <?php
        //Adds Omnicon Dashboard Header
        include 'header.php';           
        ?>
     
    <div class="mainFormProjectInput">       
    <form action="addNewMeeting.php" method="post" name="addNewMeetingForm" onsubmit="return validateForm(this,'addNewMeetingForm');" >
    <p class="mainTitle">Meeting Minutes Entry Form</p>
        
        <input type="hidden" name="editId" id="editid" value=<?php echoValue($editId); ?>>
        <!-- leftInfo will have meeting info -->
        <div id='leftInfoAddMed'>
            
            <div id="leftColumnInfo"> 
            <label class="requiredField">Meeting Title</label>
            </div>
            <div id="rightColumnInfo">
            <input type="text" class="formInputLarge" name='meetingTitle' id='meetingTitle' required value="<?php echo $meetingTitle;?>">
            </div>
            <br><br>
            
            <div id="leftColumnInfo"><label>Project</label></div>
            <div id="rightColumnInfo">
            <select id="Project" name="project" class="formInputLarge">
            <?php createSelectHTML($ProjectArray,'id','project_number','project_name',$project ,' - ');?>
            </select>
            </div>
            <br><br>
            <div id="leftColumnInfo"><label class="requiredField">Meeting Type</label></div>
            <div id="rightColumnInfo">
            <select id="Type_of_Meeting" name="typemeeting" required class="formInputMedium">
            <?php createSelectHTML($meetingTypeArray,'id','value','',$typemeeting ,'');?>
            </select>  <!-- Closing Type of Meetings -->
            </div>
            <br><br>
            
            <!--Calendar Drop down-->
            <div id="leftColumnInfo"> 
                <label class="requiredField">Meeting Date</label>
            </div>
            <div id="rightColumnInfo">
            <input type="text" name="dateOfmeeting" id="dateOfMeeting" required class="dateInput" value="<?php echo $dateOfmeeting;?>">
            </div>
            <br><br>
            
            <div id="leftColumnInfo">
            Start Time
            </div>
            <div id="rightColumnInfo">
            <input type="text" id="startTime" name="startTime" class="dateInput timeInput" value=<?php echo $startTime;?>>
            </div>
            <br><br>
            <div id="leftColumnInfo">
            End Time
            </div>
            
            <div id="rightColumnInfo">
            <input type="text" id="endTime" name="endTime" class="dateInput timeInput" value=<?php echo $endTime;?> >
            </div>
            <br><br>
            
            <div id="leftColumnInfo"> 
            <label>Meeting Site</label>
            </div>
            <div id="rightColumnInfo">
            <select id="meeting_location_id" name="meeting_location_id" class="formInputMedium">
            <?php createSelectHTML($meetingLocationArray,'id','value','',$meeting_location_id,'');?>
            </select>  <!-- Closing Type of Meetings -->    
            </div>
            <br><br>
            
            <div id="leftColumnInfo"> 
            <label>Location</label>
            </div>
            
            <div id="rightColumnInfo">
            <input type="text" class="formInputLarge" name='location' id='location' value="<?php echo $location;?>">
            </div>
            <br><br>
            
            <div id="leftColumnInfo"><label>Meeting Scribe</label></div>
            <div id="rightColumnInfo">
            <select id="scribe" name="scribe" class="formInputMedium">
            <?php createSelectHTML($employeeListArray,'id','name_last','name_first',$scribe,', ');?>
            </select>  <!-- Closing Type of Meetings -->
            </div>
            <br><br>
         
            <?php
            if ($postError==1) {
                if ($postMessage=='') {
                    $postMessage='Error Filing Data.'; 
                }
                echo '<br><br>';
                echo '<p class="mainTitle">'.$postMessage.'  Data was not saved.</p><br>';
            }
            ?>     
           <!-- end leftInfo --> 
        </div>
        
        <div id="rightInfoAddMed">
        <label>Omnicon Participants</label>
            <br>
            <div class="checkBoxListContainerSmall">
                <?php createCheckBoxListHTML($employeeListArray,'meetingParticipants[]','id', 'name_last','name_first',$meetingParticipantsCheckedArray,', ','meetingParticipants'); ?>           
            </div>
            <br>
            <label>Other Attendees</label>
            <br>
            <textarea id ="otherattendees" name="otherAttendees" class='textAreaStyleXSmall'><?php echo $otherAttendees; ?></textarea>  
            <br><br>
            <div id="leftColumnInfo">
                <label>Attendance By</label>
            </div>
            <div id="rightColumnInfo">
            <select id="meeting_com_type_id" name="meeting_com_type_id" class="formInputMedium">
            <?php createSelectHTML($meetingComTypeArray,'id','value','',$meeting_com_type_id,'');?>
            </select>  <!-- Closing Type of Meetings -->
            </div>
        </div>
        <br><br>
        
        <label class="marginLeft textInputForm">Agenda</label><br>
        <div class="richTextAreaStyleLarge marginLeft">
        <textarea id = "agenda" name="agenda" class="editor"><?php echo $agenda; ?></textarea>   
        </div>
        <br><br>
        
        <label class="marginLeft textInputForm requiredField" >Meeting Minutes</label><br>
        <div class="richTextAreaStyleLarge marginLeft">
            <textarea class="editor" id="minutes" name="minutes"><?php echo $minutes; ?></textarea>
        </div>
        <br><br>
        
        <label class="marginLeft textInputForm">Actions Required</label><br>
        <div class="richTextAreaStyleLarge marginLeft">
        <textarea id = "Actions" name="actions" class="editor"><?php echo $actions; ?></textarea>   
        </div>
        <br><br>
        
        <div id="bottomInfoAdd">
            <input id="gosubmit" class="gosubmit" type="submit" name='fileAction' value="Save" />
            <input id="cancel" class="cancel" value="Cancel" name='cancel' type='button' />
            <br>
            <br>
            <br>
        </div><!-- End of Buttons -->
</form><!-- End of  form -->
</div><!-- End of Main Form -->
</div> <!-- End of Main -->

<?php
    if ($dataFiled == 1) {
        echo '<script type="text/javascript">displayMessage("Record Saved.","Meeting Minutes","CLOSE","");</script>';
    }
    if ($postError == 1) {
        echo '<script type="text/javascript">displayMessage("Error Saving.","Error","","");</script>';
    }
?>    
    
</body>
</html>

<script type="text/javascript">
$(document).ready(function() {

    //console.log("in document ready function");

    $(".editor").jqte();
    $(".jqte_editor").css('height', '100px');
    
    //setup date field - dateOfMeeting
    $("#dateOfMeeting").datepicker({});
    fixDateFormat('dateInput');

    //setup time fields
    $('#startTime').timepicker({
        showPeriod: true,
        showLeadingZero: true
    });
    $('#endTime').timepicker({
        showPeriod: true,
        showLeadingZero: true
    });
    //convert military time from db to AM/PM time for display */
    fixTimeFormat('timeInput');
    
    $("#cancel").click(function () {
            displayMessage("Are you sure you want to cancel without saving your changes?","Cancel Form","CLOSE",1);     
    });
       
             
    $("#startTime").change(function() {
        console.log("in startime change: starttime val: "+$('#startTime').val());
        console.log("in endtime change: endtime val: "+$('#endTime').val());
        if (!validateTime($('#startTime').val(),$('#endTime').val(),1)) {
            $('#startTime').val("");
        }
    });   
             
    $("#endTime").change(function() {
        console.log("in endtime change: endtime val: "+$('#endTime').val());
        if (!validateTime($('#startTime').val(),$('#endTime').val(),1)) {
            $('#endTime').val("");;
        }
    }); 
});

</script>

