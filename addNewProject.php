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

// This is called when POST method is called.
//if postError=1, then we have to load form data from POST array

$postError=0;
$postMessage='';
$projectNumberError=0;
$dataFiled=0;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //echo 'POST requested - should file data <br><br>';
    
    /* get row id being edited, if this is an edit */
    $editRowId=getFormValue('editId','TXT');
    
    /* fill array with column name=column value */
    $resultsArray['project_number']=getFormValue('projectNumber','TXT');
    $resultsArray['project_name']=getFormValue('projectName','TXT');
    $resultsArray['end_user_id']=getFormValue('endUser','TXT');
    $resultsArray['project_status_id']=getFormValue('projectStatus','ID');
    $resultsArray['client_data_id']=getFormValue('client','ID');
    $resultsArray['client_type_id']=getFormValue('customerType','ID');
    $resultsArray['project_type_id']=getFormValue('projectType','ID');
    $resultsArray['project_description']=getFormValue('projectDescription','BLOB');
    //$resultsArray['po_date']=getFormValue('poDate','DATE');
    $resultsArray['project_manager_id']=getFormValue('projectManager','ID');
    $resultsArray['contract_closed_date']=getFormValue('contractClosedDate','DATE');
    //$resultsArray['proposal_date']=getFormValue('proposalDate','DATE');
    $resultsArray['entered_by_employee_id']=getFormValue('entered_by_employee_id','ID');
    
    /* if editing record, project_number is disabled so post won't return anything, so get original value */
    if (($resultsArray['project_number'] == '') || ($resultsArray['project_number'] == null)) {
        $resultsArray['project_number']=getFormValue('origProjectNumber','TXT');
    }
    
    /* put checkbox selected results in resultsArray */
    /* setup reload arrays - just in case error and we need to reload the data */
    
    /* for each checkbox list - setup array of selected used for filing and redisplay.
        $projectClassCheckedArray
        $projectTasksCheckedArray
        $projectResourcesCheckedArray
    */
    
     if (isset($_POST['projectClass'])) {
            $resultsArrayProjectClass=getFormvalue('projectClass','IDARRAY');
            foreach ($resultsArrayProjectClass As $item=>$value) {
                $projectClassCheckedArray[$value]=$value;
            }
         }
    if (isset($_POST['projectTasks'])) {
            $resultsArrayProjectTasks=getFormvalue('projectTasks','IDARRAY');
            foreach ($resultsArrayProjectTasks As $item=>$value) {
                $projectTasksCheckedArray[$value]=$value;
            }
        }
        
    if (isset($_POST['projectResources'])) {
            $resultsArrayProjectResources=getFormvalue('projectResources','IDARRAY');
            foreach ($resultsArrayProjectResources As $item=>$value) {
                $projectResourcesCheckedArray[$value]=$value;
            }
        }
    /* for debug
    foreach ($resultsArray As $item => $value) {
        echo 'item: '.$item.' value: '.$value.'<br><br>';
    }
    */
    //******
    //must validate projectNumber field to make sure it is unique.
    
    $fieldArray=array('id','project_number');
    $whereClause="WHERE project_number='".$resultsArray['project_number']."' ";
    if ($editRowId != '') {
        $whereClause=$whereClause.' AND id <> '.$editRowId.' ';
    }
    //echo '$editRowId: '.$editRowId.'<br>';
    //echo 'whereClause: '.$whereClause.'<br>';
    
    //only 1 row should be returned for this query, or 0 rows
    $projectNumberArray=getTableData('project_data',$fieldArray,$whereClause,'');    
    if (is_array($projectNumberArray)) {
        $projectNumberError=1;
        $postError=1;
        $postMessage="Invalid Project Number.";
    }
    
    $tableRowId='';
    if ($postError != 1) {
        $tableRowId=updateTableData('project_data',$resultsArray,$editRowId);
    }
    if ($tableRowId=="") {
        /* put some error processing here - changes not filed */
        //echo 'data didnt file - setting postError=1 <br><br>';
        $postError=1;
        $postMessage=$postMessage.' Error Filing Data In Table.';
    }
    else {
        //child tables:  project_class_data, project_resources_data, project_task_data
        
        //first delete original table data for this project, because we will file with new fields that are checked.
        //for an adding new record, this step can be skipped.
        
        $whereClause='WHERE project_id = '.$tableRowId;
        deleteTableRowData('project_class_data',$whereClause);
        deleteTableRowData('project_resources_data',$whereClause);
        deleteTableRowData('project_task_data',$whereClause);
        
        /* file child data from these arrays - array[id]=id
        $projectClassCheckedArray
        $projectTasksCheckedArray
        $projectResourcesCheckedArray    
        */
        
        if (isset($projectClassCheckedArray)) {
            $rowNum=0;
            foreach ($projectClassCheckedArray As $item=>$value) {
                //echo 'projectClass, item: '.$item.'description: '.$value.'<br><br>';
                $fileArrayProjectClass[$rowNum]['project_id']=$tableRowId;
                $fileArrayProjectClass[$rowNum]['project_class_id']=$item;
                $rowNum++;
            }
            insertMultipleTableRows('project_class_data',$fileArrayProjectClass);
        }
        if (isset($projectTasksCheckedArray)) {
            $rowNum=0;
            foreach ($projectTasksCheckedArray As $item => $value) {
                //echo 'projectTask, item: '.$item.'description: '.$value.'<br><br>';
                $fileArrayProjectTasks[$rowNum]['project_id']=$tableRowId;
                $fileArrayProjectTasks[$rowNum]['project_task_description_id']=$item;
                $rowNum++;
            }
            insertMultipleTableRows('project_task_data',$fileArrayProjectTasks);
        }
        
        if (isset($projectResourcesCheckedArray)) {
            $rowNum=0;
            foreach ($projectResourcesCheckedArray As $item=>$value) {
                //echo 'projectResources, item: '.$item.'description: '.$value.'<br><br>';
                $fileArrayProjectResources[$rowNum]['project_id']=$tableRowId;
                $fileArrayProjectResources[$rowNum]['employee_data_id']=$item;
                $rowNum++;
            }
            insertMultipleTableRows('project_resources_data',$fileArrayProjectResources);
        }
   
    }
    if ($postError==0) {
        $dataFiled=1;
    }
}  //end of POST section.

/* this queries database for table data to fill in form data on html below */
/* data read into local arrays to fill html */

/* if post error - then must load from POST array and display error  message  */
/* POST should be doing some validation and should report any errors filing data to database */
/* or for critical error - POST should display an errorHandling page */

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

/* initialize all fields on form from resultsArray if postError==1*/

if (($postError==1) || ($dataFiled == 1)) {      
    $projectNumber=$resultsArray['project_number'];
    $projectName=$resultsArray['project_name'];
    $client=$resultsArray['client_data_id'];
    $customerType=$resultsArray['client_type_id'];
    $endUser=$resultsArray['end_user_id'];
    $projectType=$resultsArray['project_type_id'];
    $projectDescription=$resultsArray['project_description'];
    $projectStatus=$resultsArray['project_status_id'];
    //$poDate=$resultsArray['po_date'];
    $projectManager=$resultsArray['project_manager_id'];
    $contractClosedDate=$resultsArray['contract_closed_date'];
    //$proposalDate=$resultsArray['proposal_date'];
    
    //entered_by_employee_id
    $entered_by_employee_id=$resultsArray['entered_by_employee_id'];
    
    $editId=$editRowId;
}
//if postError=0 = initializing and loading from database if edit.
else {   
    $projectNumber="";
    $projectName="";
    $client="";
    $customerType="";
    $endUser="";
    $projectType="";
    $projectDescription="";
    $projectStatus="";
    $poDate="";
    $projectManager="";
    $contractClosedDate="";
    //$proposalDate="";
    $entered_by_employee_id="";
    
    $editId="";
    
    /* this is the ID sent by calling form, which will be set only if we are editing an existing row */
    if (isset($_GET['ID'])) {
        $editId=$_GET['ID'];
    }
   
//These next 3 are for checklists, will be initiated to array of codes selected if we are doing an update.
//for in itializing - not sure about this, but works for now.

    $projectClass=array("0");
    $projectTasks=array("0");
    $projectResources=array("0");

/* if new form and no postError - then load from db with update id */
    if ($editId!='') {
        $whereClause='WHERE id = '.$editId;
        //setup to select all fields from table.
        $fieldArray=array('*');
    
        //only 1 row should be returned for this query
        $projectDataArray=getTableData('project_data',$fieldArray,$whereClause,'');
    
        /* for debug only
        foreach ($projectDataArray[0] As $item => $value) {
            echo 'item: '.$item.' value: '.$value.' <br><br>';
        }
        */
        
        if (isset($projectDataArray[0]['project_number'])) $projectNumber=$projectDataArray[0]['project_number'];    
        if (isset($projectDataArray[0]['project_name'])) $projectName=$projectDataArray[0]['project_name'];
        if (isset($projectDataArray[0]['client_data_id'])) $client=$projectDataArray[0]['client_data_id'];
        if (isset($projectDataArray[0]['client_type_id'])) $customerType=$projectDataArray[0]['client_type_id'];
        if (isset($projectDataArray[0]['end_user_id'])) $endUser=$projectDataArray[0]['end_user_id'];
        if (isset($projectDataArray[0]['project_type_id'])) $projectType=$projectDataArray[0]['project_type_id'];
        if (isset($projectDataArray[0]['project_description'])) $projectDescription=$projectDataArray[0]['project_description'];
        if (isset($projectDataArray[0]['project_status_id'])) $projectStatus=$projectDataArray[0]['project_status_id'];
        //if (isset($projectDataArray[0]['po_date'])) $poDate=$projectDataArray[0]['po_date'];
        if (isset($projectDataArray[0]['project_manager_id'])) $projectManager=$projectDataArray[0]['project_manager_id'];
        if (isset($projectDataArray[0]['contract_closed_date'])) $contractClosedDate=$projectDataArray[0]['contract_closed_date'];
        //if (isset($projectDataArray[0]['proposal_date'])) $proposalDate=$projectDataArray[0]['proposal_date'];
        if (isset($projectDataArray[0]['entered_by_employee_id'])) $entered_by_employee_id=$projectDataArray[0]['entered_by_employee_id'];

        /* need to setup checkbox list arrays with data from tables
         * so that html can set correct boxes checked.
         * projectClassCheckedArray
         * projectTasksCheckedArray
         * projectResourcesCheckedArray
         */
        
        $whereClause='WHERE project_id = '.$editId;
        $fieldArray=array('project_class_id');
        $projectClassCheckedArray=getTableData('project_class_data',$fieldArray,$whereClause,'');
    
        $fieldArray=array('project_task_description_id');
        $projectTasksCheckedArray=getTableData('project_task_data',$fieldArray,$whereClause,'');
    
        $fieldArray=array('employee_data_id');
        $projectResourcesCheckedArray=getTableData('project_resources_data',$fieldArray,$whereClause,'');
  
        /* these arrays are in the format array[rowNum]['idfield']=idValue.
        * it would be easier when initializing checkbox to reorder them so that they look like:
        * array[idValue]=[idValue] so that you can just check if the value is set in the array
        * to set item as checked.
        */
    
        $projectClassCheckedArray=reorderArrayIndexByCode($projectClassCheckedArray);
        $projectTasksCheckedArray=reorderArrayIndexByCode($projectTasksCheckedArray);
        $projectResourcesCheckedArray=reorderArrayIndexByCode($projectResourcesCheckedArray);
    
    }  //end of loading from selected id for edit, would only happen if postError=0.
} //end of else - postError=0
?>

<html>
    <head>
        
        <title>Add New Project</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
    <?php
         //include scripts and styles
        include 'standardFormScriptsCSS.php';
    ?>
        
    </head>
    <body>
    <br>

<div class="main">
<?php
    //Adds Omnicon Dashboard Header
    include 'header.php';
?>
    
<!-- <div class="mainForm">  -->   
<div class="mainFormProjectInput">      
<form action="addNewProject.php" method="post" name="addNewProjectForm" onsubmit="return validateForm(this,'addNewProjectForm');" >   
    <p class="mainTitle">Project Data Entry Form</p>
    <input type="hidden" name="editId" id="editId" value=<?php echoValue($editId); ?>>
    <input type="hidden" name="origProjectNumber" id="origProjectNumber" value=<?php echoValue($projectNumber); ?>>

    <div id="leftInfoAdd">
        <div id="leftColumnInfo"><label class="requiredField">Project Number</label>
        </div>
        <div id="rightColumnInfo">
            <input type="text" class="formInputMedium" id="projectNumber" required name="projectNumber" value=<?php echoValue($projectNumber); ?>> 
        </div>
        <?php
        if ($projectNumberError==1) {
            echo '<br><p class="errorMessage"  >* Invalid Project Number. Project Number already used.</p>';
        }
        else {
            echo '<br><br>';
        }
        ?>    
        
        <div id="leftColumnInfo"><label class="requiredField">Project Name</label></div>
        <div id="rightColumnInfo"><input type="text" id="project_name" class="formInputLarge" required name="projectName" value=<?php echoValue($projectName);?>></div>
        <br><br>
        
        <div id="leftColumnInfo"><label>Entered By</label></div>
        <div id="rightColumnInfo">
        <select id="entered_by_employee_id" name="entered_by_employee_id" class="formInputMedium">
<?php
        createSelectHTML($employeeListArray,'id','name_last','name_first',$entered_by_employee_id,', ');
?>              
        </select>
        </div>
        <br><br>
        <!-- this list will be filled by the client_data table -->
        <div id="leftColumnInfo">Client</div>
        <div id="rightColumnInfo">
        <!-- this needs a table of clients for project selection -->
        <select id="client" name="client" class="formInputMedium">
<?php       
         createSelectHTML($clientDataArray,'id','client_name','',$client,'');
?>                     
        </select>
        </div>
        <br>
        <br>
        <div id="leftColumnInfo">Client Type</div>
        <div id="rightColumnInfo">
        <select id="customerType" name="customerType" class="formInputMedium">
<?php
        createSelectHTML($customerTypeArray,'id','value','',$customerType,'');
?>
        </select>
        </div>
        <br>
        <br>
        <div id="leftColumnInfo">End User</div>
        <div id="rightColumnInfo">
        <select id="endUser" name="endUser" class="formInputMedium">
<?php
        createSelectHTML($endUserArray,'id','value','',$endUser,'');
?>
        </select>
        </div>
        <!--    <input type="text" id="endUser" name="endUser" value=<?php echoValue($endUser); ?>  ></div>
        -->
        <br>           
        <br>
        <label>Class/Project Category</label>
        <div class="checkBoxListContainerSmall">
        <!-- need to fill this with  projectClassArray -->
<?php
        createCheckBoxListHTML($projectClassArray,'projectClass[]','id', 'value','',$projectClassCheckedArray,'','projectClass');
?>
        </div>
        <br>
        <div id="leftColumnInfo">
        <label class="requiredField"> Project Type </label>
        </div>
        <div id="rightColumnInfo">
        <select id="projectType" required name="projectType" class="formInputMedium">
<?php
        createSelectHTML($projectTypeArray,'id','value','',$projectType,'');
?>
        </select>
        </div>   
        <br><br>
       
        <div id="leftColumnInfo"><label class="requiredField">Project Status</label></div>
        <div id="rightColumnInfo">
        <select id="projectStatus" required name="projectStatus" class="formInputMedium">
        <?php createSelectHTML($projectStatusArray,'id','value','',$projectStatus,'');?>                  
        </select>
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label>Closed Date</label></div>
        <div id="rightColumnInfo">
        <input type="text" name="contractClosedDate" id="contractClosedDate" class='dateInput' value=<?php echo $contractClosedDate;?> >
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
    </div>
    <div id="rightInfoAdd">
        <div id="leftColumnInfo"><label>Project Manager</label></div>
        <div id="rightColumnInfo">
        <select id=projectManager name="projectManager" class="formInputMedium">
<?php
        createSelectHTML($employeeListArray,'id','name_last','name_first',$projectManager,', ');
?>              
        </select>
        </div>
        <br>
        <br><br>
        <label id="projectResourcesLabel">Resources Assigned</label>
        <div class="checkBoxListContainerLarge">
<?php
        createCheckBoxListHTML($employeeListArray,'projectResources[]','id', 'name_last','name_first',$projectResourcesCheckedArray,', ','projectResources'); 
?>           
        </div>
        <br><br>
        <div id="projectTaskCont">
        <label>Project Task Description</label>
        <div class="checkBoxListContainerLarge" id="projectTaskDiv">     
<?php     
        createCheckBoxListHTML($projectTaskDescArray,'projectTasks[]','id','value','',$projectTasksCheckedArray,'','projectTasks');
?>
         </div>
        </div>
         
    </div> <!-- end of rightInfo div -->
    <br><br>
    <label class="marginLeft textInputForm requiredField" >Project Description</label><br>
    <div class="richTextAreaStyleLarge marginLeft">    
        <textarea class="editor requiredText" id="projectDescription" name="projectDescription"><?php echo $projectDescription;?></textarea>
    </div>  
    <br>
    <div id="bottomInfoAdd">
        <br>
        <input id="gosubmit" class="gosubmit" type="submit" name='fileAction' value="Save" />
        <input id="cancel" class="cancel" value="Cancel" name='cancel' type='button' />
        <br><br><br>
    </div>  <!-- End of Buttons -->
    
</form>
<!-- end of maininputdiv -->    
</div>
<!-- end of div class=main-->    
</div>
<?php
    if ($dataFiled == 1) {
        echo '<script type="text/javascript">displayMessage("Record Saved.","Project Database","CLOSE","");</script>';
    }
    if ($postError == 1) {
        echo '<script type="text/javascript">displayMessage("Error Saving.","Error","","");</script>';
    }
?>

</body>
</html>

<script type="text/javascript">
$(document).ready(function() {
    
    $("#projectDescription").jqte();
    $(".jqte_editor").css('height', '100px');
    
    
    $('#projectTaskCont').hide();
    
    var adminFlag=getParameter('ADMIN');
    var id=getParameter('ID');
    
    //console.log("adminFlag: "+adminFlag+" id: "+id);
        
    //disable project number if admin flag is set 
    if ((adminFlag == '1') || (id == null)) {
        $('#projectNumber').attr('disabled',false);
    }
    else {
        $('#projectNumber').attr('disabled',true);
    }
    
    // setup datePicker date fields.
     $("#contractClosedDate").datepicker({});
    fixDateFormat('dateInput');
    
    //on initial load - setup status date field
    setupStatusDateFields();

    $("#cancel").click(function () {
        displayMessage("Are you sure you want to cancel without saving your changes?","Cancel Form","CLOSE",1);     
    });
    
    $('#projectStatus').change(function() {
        setupStatusDateFields();
    });
    
    $('#projectNumber').blur(function () {
        console.log("in projectNumber blur function - tabbed off projectNumber: "+$('#projectNumber').val());
        if ($('#projectNumber').val()!='') {
            validateProjectNumber ($('#projectNumber').val(),$('#editId').val());
        }
    });
    
});

function setupStatusDateFields(){
    
    //projectStatus
    //proposalDate
    //poDate
    //contractClosedDate
    
    var status=$('#projectStatus').val();
    
    /* still need to add logic for disabling project manager and project resources */
    /* will wait for feedback on form */
    
    switch (status) {
        case '1':  /* Proposal Submitted */
            console.log('in case 1');
            //$("#proposalDate").attr("disabled",false);
            $("#poDate").attr("disabled",true);
            $("#contractClosedDate").attr("disabled",true);
            //$("#projectManager").attr("disabled",true);
            //$("#resourcesAssigned").attr("disabled",true);
            
            break;
        case '2': //Contract Not Awarded
            //$("#proposalDate").attr("disabled",false);
            $("#poDate").attr("disabled",true);
            $("#contractClosedDate").attr("disabled",true);
            //$("#projectManager").attr("disabled",true);
            break;
        case '3': //contract awarded
            //$("#proposalDate").attr("disabled",false);
            $("#poDate").attr("disabled",false);
            $("#contractClosedDate").attr("disabled",true);
            //$("#projectManager").attr("disabled",false);
            break;
        case '4':  //contract in progress
            //$("#proposalDate").attr("disabled",false);
            $("#poDate").attr("disabled",false);
            $("#contractClosedDate").attr("disabled",true);
            //$("#projectManager").attr("disabled",false);
            break;
        case '5':  //Contract complete
            //$("#proposalDate").attr("disabled",false);
            $("#poDate").attr("disabled",false);
            $("#contractClosedDate").attr("disabled",true);
            //$("#projectManager").attr("disabled",false);
            break;
        case '6':  //contract closed
            //$("#proposalDate").attr("disabled",false);
            $("#poDate").attr("disabled",false);
            $("#contractClosedDate").attr("disabled",false);
            //$("#projectManager").attr("disabled",false);
            break;
        default:
            console.log('in default case');
            //$("#proposalDate").attr("disabled",true);
            $("#poDate").attr("disabled",true);
            $("#contractClosedDate").attr("disabled",true);
            //$("#projectManager").attr("disabled",true);
            //this isn't working for check box
            //$("#projectTaskDiv").attr("disable",false);
            //console.log("trying to set projectResources");
            //$('[id^=projectResources]').attr("disable",true);
            break;
    }
}
</script>



