<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
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
    
    //admin code is correct
    /* fill array with column name=column value */
        $resultsArray['project_data_id']=getFormValue('project','ID');
        $resultsArray['meeting_data_id']=getFormValue('meeting','ID');
        $resultsArray['date_opened']=getFormValue('dateopen','DATE');
        $resultsArray['date_due']=getFormValue('datedue','DATE');
        $resultsArray['assigned_to_employee_id']=getFormValue('assignedto','ID');
        $resultsArray['ai_priority_id']=getFormValue('priority','ID');
        $resultsArray['task_title']=getFormValue('tasktitle','TXT');
        $resultsArray['task_description']=getFormValue('taskdescription','BLOB');
        $resultsArray['ai_notes']=getFormValue('notes','BLOB');
        $resultsArray['ai_status_id']=getFormValue('status','TXT');
        $resultsArray['deliverable_flag']=getFormValue('deliverablebox','TXT');
        
        $resultsArray['entered_by_employee_id']=getFormValue('entered_by_employee_id','ID');
        $resultsArray['closed_by_employee_id']=getFormValue('closed_by_employee_id','ID');
        
        $resultsDelivDateArray['reason_for_date_change']=getFormValue('reasonForDateChange','TXT');
        $resultsDelivDateArray['deliverable_date_due']=getFormValue('Deliverable_Date_Due','DATE');
        $resultsDelivDateArray['deliverable_date_actual']=getFormValue('Actual_Date_Due','DATE');
         
        //echo 'filing ai_priority_id: '.$resultsArray['ai_priority_id'].' from POST: '.$_POST['priority'].'<br><br>';
        
        /* for debug only
        foreach ($resultsDelivDateArray As $item => $value) {
            echo 'item: '.$item.' value: '.$value.' <br><br>';
        }  */
         
        $tableRowId=updateTableData('action_item_data',$resultsArray,$editRowId);
        //$tableRowId=''; for testing.
        //////if error - then display message popup and don't re-initialize form.
        //else - re-initialize form data.  make sure POST flag cleared.
        if ($tableRowId=="") {
            /* put some error processing here - changes not filed */
            $postError=1;
            $postMessage="Error filing data.  Please contact IT Department.";
        }
        else {  //file child table - if new record and deliverable date set, or reason for date change is set 
            //echo 'editRowId: '.$editRowId.' reasonforDateChange: '.$resultsDelivDateArray['reason_for_date_change'].'<br><br>';
            if ($resultsArray['deliverable_flag'] == 1) {
                //If new AI, or reason for change is set - then must file new delivery dates
                if (($editRowId =='') || (trim($resultsDelivDateArray['reason_for_date_change'])!= '')) {
                    $resultsDelivDateArray['action_item_id']=$tableRowId;
                    $tableDelivDateRowId=updateTableData('ai_delivery_date_data',$resultsDelivDateArray,'');
                }
            }
        }
        
        /* no child tables for action items, so filing is complete. */
    if ($postError==0) {
        $dataFiled=1;
    }
}  //end of POST section.

/* this queries database for table data to fill in form data on html below */
/* data read into local arrays to fill html */

/* initialize arrays */

$ProjectArray="";
$AssignedToArray="";
$PriorityArray="";
$StatusArray="";
$MeetingArray='';

/* this initializes the select and checkbox lists that are populated from the database */
initializeFormDataAI($ProjectArray,$AssignedToArray,$PriorityArray,$StatusArray,$MeetingArray);

/* if error on post or data filed already - load from post and display message */
if (($postError==1) || ($dataFiled == 1)) {   
    $project=$resultsArray['project_data_id'];
    $meeting=$resultsArray['meeting_data_id'];
    $dateopen=$resultsArray['date_opened'];
    $assignedto=$resultsArray['assigned_to_employee_id'];
    $priority=$resultsArray['ai_priority_id'];
    $datedue=$resultsArray['date_due'];
    $tasktitle=$resultsArray['task_title'];
    $taskdescription=$resultsArray['task_description'];
    $notes=$resultsArray['ai_notes'];
    $deliverablebox=$resultsArray['deliverable_flag'];
    $deliverabledatedue=$resultsDelivDateArray['deliverable_date_due'];
    $deliverabledateactual=$resultsDelivDateArray['deliverable_date_actual'];
    $reasonForDateChange=$resultsDelivDateArray['reason_for_date_change'];
    $status=$resultsArray['ai_status_id'];
    $entered_by_employee_id=$resultsArray['entered_by_employee_id'];
    $closed_by_employee_id=$resultsArray['closed_by_employee_id'];
     
    $editId=$editRowId;
}
/* initialize all fields on form to blank */
else {
    $project="";
    $meeting="";
    $dateopen="";
    $assignedto="";
    $priority="";
    $datedue="";
    $tasktitle="";
    $taskdescription="";
    $notes="";
    $deliverablebox="";
    $deliverabledatedue="";
    $deliverabledateactual="";
    $status="";
    $editId="";
    $entered_by_employee_id="";
    $closed_by_employee_id="";
    
    $Actual_Date_Due='';
    $Deliverable_Date_Due='';
    $reasonForDateChange='';
    
    //hidden field
    $dateHistoryExists='';
    
    // default dateopen to todays date.  If we are editing a record this will be overwritten by what is in the table.
    $dateopen=date("Y-m-d"); 

    /* this is the ID sent by calling form, which will be set only if we are editing an existing row */
    if (isset($_GET['ID'])) {
        $editId=$_GET['ID'];
    }
    //for initializing
    if ($editId!='') {
        $whereClause='WHERE id = '.$editId;
    
        //setup to select all fields from table.
        $fieldArray=array('*');
    
        //only 1 row should be returned for this query
        $ActionArray=getTableData('action_item_data',$fieldArray,$whereClause,'');
    
        //get delivery date info for this id to display on form
        $whereClause='WHERE action_item_id='.$editId;
        /* will take first row - which will be most recent */
        $orderBy='Order By id DESC';
        $delivDateArray=getTableData('ai_delivery_date_data',$fieldArray,$whereClause,$orderBy);
        
        if (is_array($delivDateArray) && count($delivDateArray) > 1) {
            $dateHistoryExists=1;
        }
        
        /* for debug only
        foreach ($ActionArray[0] As $item => $value) {
            echo 'item: '.$item.' value: '.$value.' <br><br>';
        }  */
    
        if (isset($ActionArray[0]['project_data_id'])) {         $project=$ActionArray[0]['project_data_id'];}
        if (isset($ActionArray[0]['meeting_data_id'])) {         $meeting=$ActionArray[0]['meeting_data_id'];}
        if (isset($ActionArray[0]['date_opened']))     {         $dateopen=$ActionArray[0]['date_opened'];}
        if (isset($ActionArray[0]['date_due']))        {         $datedue=$ActionArray[0]['date_due'];}
        if (isset($ActionArray[0]['assigned_to_employee_id'])) { $assignedto=$ActionArray[0]['assigned_to_employee_id'];}
        if (isset($ActionArray[0]['ai_priority_id']))  {         $priority=$ActionArray[0]['ai_priority_id'];}
        if (isset($ActionArray[0]['task_title']))      {         $tasktitle=$ActionArray[0]['task_title'];}
        if (isset($ActionArray[0]['task_description'])) {        $taskdescription=$ActionArray[0]['task_description'];}
        if (isset($ActionArray[0]['ai_notes']))         {        $notes=$ActionArray[0]['ai_notes'];}
        if (isset($ActionArray[0]['ai_status_id']))     {        $status=$ActionArray[0]['ai_status_id'];}
        if (isset($ActionArray[0]['deliverable_flag']))  {       $deliverablebox=$ActionArray[0]['deliverable_flag'];}
        if (isset($delivDateArray[0]['deliverable_date_due'])) {    $Deliverable_Date_Due=$delivDateArray[0]['deliverable_date_due'];}
        if (isset($delivDateArray[0]['deliverable_date_actual'])) { $Actual_Date_Due=$delivDateArray[0]['deliverable_date_actual'];}
        if (isset($ActionArray[0]['entered_by_employee_id'])) {  $entered_by_employee_id=$ActionArray[0]['entered_by_employee_id'];}
        if (isset($ActionArray[0]['closed_by_employee_id'])) {  $closed_by_employee_id=$ActionArray[0]['closed_by_employee_id'];}        
    }
}
?>

<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>New Action Item Form</title>
  
    <?php
         //include scripts and styles
        include 'standardFormScriptsCSS.php';
    ?>
   
    </head>
<body>
<br>
<div class= "main AIFormHeight">
<?php
    //Adds Omnicon Dashboard Header
    include 'header.php';  
?>    
<div class="mainFormProjectInput AIFormHeight"> 
    
    <form action="addNewActionItem.php" method="post" name="AI" onsubmit="return validateForm(this,'addNewActionItemForm');">
    <p class="mainTitle">Action Item Entry Form</p>
    <input type="hidden" name="editId" id="editid" value=<?php echoValue($editId); ?>>
    
    <div id="leftInfoAddSmall">     
        <div id="leftColumnInfo"> 
            <label class="requiredField">Task Title</label>
        </div>
        <div id="rightColumnInfo">
            <input type="text" class="formInputLarge" id="TaskTitle" name="tasktitle" required value="<?php echo $tasktitle;?>">   
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label id='projectLabel'>Project</label></div>
        <div id="rightColumnInfo">
            <select id="Project" name="project" class="formInputLarge">
                <?php createSelectHTML($ProjectArray,'id','project_number','project_name',$project,' - ');?>
            </select>
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label>Meeting</label></div>
        <div id="rightColumnInfo">
            <select id="meeting" name="meeting" class="formInputLarge">
                <?php createSelectHTML($MeetingArray,'id','meeting_date','meeting_title',$meeting,'-');?>
            </select>
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label class="requiredField">Assigned To</label></div>
        <div id="rightColumnInfo">
        <select id = "AssignedTo" name="assignedto" required class= "formInputMedium">
            <?php createSelectHTML($AssignedToArray,'id','name_last','name_first',$assignedto,', ');?>
        </select> <!-- Closing Assigned Too -->
        </div>
        <br><br>
    
        <div id="leftColumnInfo"><label class="requiredField">Date Assigned</label></div>
        <div id="rightColumnInfo">
        <input type="text" id="DateOpen" name="dateopen" class="dateInput" required value="<?php echo $dateopen;?>">   
        </div>
        <br><br>

          <!--Calendar Drop down-->
        <div id="leftColumnInfo"><label class="requiredField">Date Due</label></div>
        <div id="rightColumnInfo">  
        <input type="text" name="datedue" id="DateDue" required class="dateInput" value="<?php echo $datedue;?>">
        </div>
        <br><br>    
     
        <div id="leftColumnInfo"><label>Entered By</label></div>
        <div id="rightColumnInfo">
        <select id="entered_by_employee_id" name="entered_by_employee_id" class="formInputMedium">
<?php
        createSelectHTML($AssignedToArray,'id','name_last','name_first',$entered_by_employee_id,', ');
?>              
        </select>
        </div>
        
        <!--</div>-->
    <!-- end of leftInfoAdd div -->    
    </div>
    
    <div id='rightInfoAddSmall'>
    
        <!--Priority Title-->
        <div id="leftColumnInfo"><label>Priority</label></div>
        <div id="rightColumnInfo"><select id = "Priority" name="priority" class="formInputSmall">
        <?php createSelectHTML($PriorityArray,'id','value','',$priority,'');?>
        </select>  <!-- Closing Type of Priority -->
        </div>
        <br><br>
        
         <div id="leftColumnInfo"><label class="requiredField">Status</label></div>
        <div id="rightColumnInfo">
        <select id="status" name="status" required class="formInputSmall">
            <?php createSelectHTML($StatusArray,'id','value','',$status,'');?>
        </select>
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label id='closed_by_employee_id_label'>Closed By</label></div>
        <div id="rightColumnInfo">
        <select id="closed_by_employee_id" name="closed_by_employee_id" class="formInputMedium">
<?php
        createSelectHTML($AssignedToArray,'id','name_first','name_last',$closed_by_employee_id,'');
?>              
        </select>
        </div> 
        <br><br>   
        
      <!--<div class="Deliverable"> -->
        <!-- Deliverable Box-->
        <div id="leftColumnInfo">
            <label class="requiredField">Deliverable</label></div>
            <div id="rightColumnInfo">
            <?php 
            $dataArray =    array (array(1,"Yes"),
                            array(0,"No"));
            createRadioButtonHTML($dataArray,'deliverablebox',$deliverablebox,'radioBtn',1);
            ?>
            </div>
        <br><br>
        <!--Calendar Drop down-->
        <div id="leftColumnInfo"><label id="clientDueDateLabel">Client Due Date</label></div>
        <!-- hidden field to keep original value of client due date -->
        <input id='origDueDate' type='date' value="<?php echo $Deliverable_Date_Due;?>" hidden >
        <input id='origDeliveredDate' type='date' value="<?php echo $Actual_Date_Due;?>" hidden >
        <div id="rightColumnInfo">
            <input type="text" name="Deliverable_Date_Due"  id="Deliverable_Date_Due" class="dateInput" required value="<?php echo $Deliverable_Date_Due;?>" disabled>
        </div>
        <br><br>
        <div id="leftColumnInfo">
            <label id="clientDeliveredDateLabel">Delivered Date</label></div>
        <div id="rightColumnInfo">
            <input type="text" name="Actual_Date_Due" id="Actual_Date_Due" class="dateInput" value="<?php echo $Actual_Date_Due;?>" disabled >
        </div> 
        <br><br>
        <div id='leftColumnInfoLarge'>
        <label id='reasonForDateChangeLabel'>Reason For Change</label> 
        </div>
        <div id='dialog' title='Date Change History'><p>test</p></div>
        <div id='rightColumnInfoRight'>    
            <label id='dateHistory' class='linkToPopup'>Date Change History</label>
        </div>
        <textarea id='reasonForDateChange' name='reasonForDateChange' class='textAreaStyleXXSmall'></textarea>
        <input type='text' id='dateHistoryExists' name='dateHistoryExists'  hidden value='<?php echo $dateHistoryExists;?>'
     <!-- end of rightInfoAdd div -->       
    </div>

    <label class="marginLeft textInputForm requiredField">Task Description</label><br>
    <div class="richTextAreaStyleLarge marginLeft">
    <textarea id = "TaskDescription" name="taskdescription" class="editor"><?php echo $taskdescription;?></textarea>
    </div>
    <br><br>
    <!--Notes Text Area-->
    <label class="marginLeft textInputForm">Notes</label><br>
    <div class="richTextAreaStyleLarge marginLeft">
    <textarea id = "Notes" name ="notes" class="editor"><?php echo $notes;?></textarea>
    </div>
    <?php
        if ($postError==1) {
            if ($postMessage=='') {
                $postMessage='Error Filing Data.'; 
            }
            echo '<br><br>';
            echo '<p class="mainTitle">'.$postMessage.'  Data was not saved.</p><br>';
        }
    ?>
    
    <br>
    <br>
    <div id="bottomInfoAdd">
        <input id="gosubmit" class="gosubmit" type="submit" name='fileAction' value="Save" />
        <input id="cancel" class="cancel" value="Cancel" name='cancel' type='button' />
        <br><br><br>
    </div><!-- End of Buttons -->
    
    </form>
<!-- End of Main Form -->
</div>
</div> <!-- End of Main -->

<?php
    if ($dataFiled == 1) {
        echo '<script type="text/javascript">displayMessage("Record Saved","Action Item","CLOSE","");</script>';
    }
    if ($postError == 1) {
        echo '<script type="text/javascript">displayMessage("Error Saving Record.","Error","","");</script>';
    }
?>

</body>
</html>

<script type="text/javascript">
$(document).ready(function() {

    //on form load, setup required/enabled fields
    
    $(".editor").jqte();
    $(".jqte_editor").css('height', '100px');
    
    $("#DateOpen").datepicker({
    });
    
    $("#DateDue").datepicker({
    });
    
    $("#Deliverable_Date_Due").datepicker({
    });
    
    $("#Actual_Date_Due").datepicker({
    });
    
    $("#dialog").dialog({
	autoOpen: false,
	width: 400
    });
    
    //setup required and enabled fields
    setupDeliverableFields();
    setupStatusFields();
    setupReasonDateChangeField(0);
    
   /* check format of date fields to make sure that they are the correct format */
   /* class="dateInput" */
   fixDateFormat('dateInput');
   
    /* event code below */
   
    $("#cancel").click(function () {
            displayMessage("Are you sure you want to cancel without saving your changes?","Cancel Form","CLOSE",1);     
    });
       
   
    $("#dateHistory").click(function (e) {
        console.log("**** in click function for dateHistory");
        if (e.handled !== true) {
            var aiId=$("#editid").val();
            var url='GetDelivDateHistory.php';
            event.handled=true;
            if (aiId != '' ) {
                console.log('calling get function');
                $.get(url,
                    {AI: aiId},
                    function(ajaxresult,status){
                        console.log("returned from get, status: "+status);
                        console.log("returned from get, result: "+ajaxresult);
                        $('#dialog').html(ajaxresult);
                    }
                );
        
                $("#dialog").dialog("open");
             }
             
            else {
                jAlert('Action Item Delivery Date History Not Available.');
            }
        }
    });
     
       
    $(".radioBtn").click(function () {
        setupDeliverableFields();
    });
            
    $('#status').change(function() {
        setupStatusFields();
    });
    
    $('#DateOpen').change(function() {
        console.log("in date opened change, DateOpen: "+$('#DateOpen').val()+" dateDue: "+$('#DateDue').val());
        if (!validateStartEndDate($('#DateOpen').val(),$('#DateDue').val(),0)) {
            jAlert("Invalid Entry for Date Assigned: '"+$('#DateOpen').val()+"'.  Date Assigned cannot be after Due Date.");
            $('#DateOpen').val("");
        }
    });
    
    $('#DateDue').change(function() {
        if (!validateStartEndDate($('#DateOpen').val(),$('#DateDue').val(),0)) {
            jAlert("Invalid Entry for Due Date: '"+$('#DateDue').val()+"'.  Due Date cannot be prior to Date Assigned.");
            $('#DateDue').val("");
        }
    });
    
    /* if Deliverable_Date_Due or Actual_Date_Due changes - then make reason required and enabled.*/
    
    $('#Deliverable_Date_Due').change(function() {   
        checkDeliverableDateChange('origDueDate','Deliverable_Date_Due');
    });
    
    $('#Actual_Date_Due').change(function() {
        checkDeliverableDateChange('origDeliveredDate','Actual_Date_Due');
    });
   
});


function checkDeliverableDateChange(origId, newId) {
    
    var origDate=$('#'+origId).val();
    var newDate=$('#'+newId).val();
    var enableType=0;
    
    console.log("origDate: "+origDate+" newDate: "+newDate);
    if ((origDate != '') && (origDate != newDate)) {
        enableType=1;
    }
    
    setupReasonDateChangeField(enableType);
    
}

/* type = 1 for enable, type = 0 for disable */

function setupReasonDateChangeField(type) {
     if (type) {
        $('#reasonForDateChange').attr("disabled",false);
        $('#reasonForDateChangeLabel').addClass('requiredField');
    }
    else {
        $('#reasonForDateChange').attr("disabled",true);
        $('#reasonForDateChangeLabel').removeClass('requiredField');
    }
}


function setupStatusFields() {
    if ($('#status').val() == 2){
        console.log('val=2, assuming this is closed');   
        $("#closed_by_employee_id").attr("required",true);
        $("#closed_by_employee_id").attr("disabled",false);
        $("#closed_by_employee_id_label").attr("class","requiredField");
        
        if ($("input[name=deliverablebox]:checked").val() == "1") {
                $("#Actual_Date_Due").attr("required", true);
                $("#clientDeliveredDateLabel").attr("class", "requiredField");
        }
    }
    else {
        console.log('val not 2, in else');
        $("#closed_by_employee_id").attr("required",false);
        $("#closed_by_employee_id_label").attr("class","");
        $("#closed_by_employee_id").attr("disabled",true);
        $("#closed_by_employee_id").val('');
 
        $("#Actual_Date_Due").attr("required", false);
        $("#clientDeliveredDateLabel").attr("class", "");
        
        if ($("input[name=deliverablebox]:checked").val() == "1") {
            $("#Actual_Date_Due").attr("required", false);
            $("#clientDeliveredDateLabel").attr("class", "");
        }    
        
    }
}

function setupDeliverableFields() {
    
    if ($("input[name=deliverablebox]:checked").val() == "1") {
        $("#Actual_Date_Due").attr("disabled", false);
        $("#Deliverable_Date_Due").attr("disabled", false);
        $("#Deliverable_Date_Due").attr("required", true);
        $("#clientDueDateLabel").attr("class", "requiredField");
        $("#Project").attr("required", true);
        $("#projectLabel").addClass('requiredField');
        
        
        /* if status is closed */
        if ($('#status').val() == 2){
            $("#Actual_Date_Due").attr("required", true);
            $("#clientDeliveredDateLabel").attr("class", "requiredField");
        }
        else {
            $("#Actual_Date_Due").attr("required", false);
            $("#clientDeliveredDateLabel").attr("class", "");
        }
    }
    if ($("input[name=deliverablebox]:checked").val() == "0") {
        $("#Actual_Date_Due").attr("disabled", true);
        $("#Deliverable_Date_Due").attr("disabled", true);
        $("#Deliverable_Date_Due").attr("required", false);
        $("#clientDueDateLabel").attr("class", "");
        $("#Actual_Date_Due").attr("required", false);
        $("#clientDeliveredDateLabel").attr("class", "");
        $("#Project").attr("required", false);
        $("#projectLabel").removeClass('requiredField');
    }
}

</script>

