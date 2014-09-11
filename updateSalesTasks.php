<!DOCTYPE html>

<?php

/* define all include files required for this form */
REQUIRE_ONCE 'htmlRenderingLibrary.php';
REQUIRE_ONCE 'dbInterfaceLibrary.php';


/* initialize arrays used to build html*/

$AssignedToArray="";
$StatusArray="";

/* this initializes the select and checkbox lists that are populated from the database */
/* not sure we are doing it this way, but maybe.  this array list will change based on form */

initializeFormData($employeeListArray,$contractStatusArray);

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Task Entry Form</title>
        <?php
         //include scripts and styles
        include 'standardFormScriptsCSS.php';
        include 'tableSorterScriptsCSS.php';
        ?>    
        
    </head>
    <br>
    <body>
    <div class= "main">
        <?php
        //Adds Omnicon Dashboard Header
        include 'header.php';
        ?>
            
        <div id="viewTitle">
        <p class="mainTitle" id='mainTitle' >Task Entry Form For Sales Lead</p><br>
        </div>
       
    <div class="mainFormTableInput">           
    <br>
    <!-- added mainForm class, removed scroll -->
    <div class="scrollTableInput">
        <table id="table_sales_proj" class="tablesorter">  <!-- Sortable Table -->
            <thead> <!-- Headers -->
                <tr>
                    <th data-placeholder="" class="filter-match">Task Title</th> 
                    <th data-placeholder="" class="filter-match">RFP Date</th> 
                    <th data-placeholder="" class="filter-match">Proposal Lead</th> 
                    <th data-placeholder="" class="filter-match">Proposal Due Date</th> 
                    <th data-placeholder="" class="filter-match">Contract Status</th>
                    <!--<th data-placeholder="Try B*{space} or Task|br*|c" class="filter-match">Quote</th>-->
                    
                </tr> 
            </thead>
            <!-- need to build this tbody using javaScript, so that it can be refreshed after filing -->
        <tbody id="main_table_sales_proj"> <!-- Body of Data -->
        </tbody>
        </table>
    </div> <!-- End of Scroll -->
    </div> <!-- end of mainForrmtable -->
    <div class="mainFormProjectInput">
    <!-- form entry -->          
   
    <formInput id="myFormFields">   
    <p class="mainTitle">Sales Task Input</p>
    <input type="hidden" name="salesId" id='salesId' value=''>
    <input type="hidden" name="editId" id='editId' value=''>
    <div id="leftInfoAddSmall">
        <div id="leftColumnInfo"><label class="requiredField" id="task_title_label">Task Title</label>
        </div>
        <div id="rightColumnInfo">
            <input type="text" class="requiredFieldValue formInputLarge" id="task_title" required name="task_title" value=""> 
        </div>
        <br><br>
        <div id="leftColumnInfo"><label>RFP Date</label></div>
        <div id="rightColumnInfo"><input type="date" id="rfp_date" class="dateInput" required name="rfp_date" value=""></div>
        <br><br>
       
        <div id="leftColumnInfo"><label>Proposal Lead</label></div>
        <div id="rightColumnInfo">
        <select id='proposal_lead_id' name="proposal_lead" class="formInputMedium">
             <?php createSelectHTML($employeeListArray,'id','name_first','name_last',' ',' ');?>
        </select>
        </div>
        <br><br>
        <div id="leftColumnInfo"><label>Proposal Due</label></div>
        <div id="rightColumnInfo">
            <input type='date' id='proposal_due_date' name='proposal_due_date' class="dateInput" value=''>
        </div>
        <br><br>
        <div id="leftColumnInfo" class='requiredField'>
            <label id="contract_status_id_label">Contract Status</label></div>
        <div id="rightColumnInfo">
        <select id='contract_status_id' name='contract_status_id' value='' class='requiredFieldValue'>
            <?php createSelectHTML($contractStatusArray,'id','value','','','');?>
        </select>    
        </div>
        <br><br>
        <div id="leftColumnInfo"><label>Quote</label></div>
        <div id="rightColumnInfo">
        <input type='text' id='quote' name='quote' value=''>
        </div>
    </div>
  
    <div id="rightInfoAddSmall">    
        
        <div id="leftColumnInfo"><label>Anticipated Award Date</label></div>
        <div id="rightColumnInfo">
        <input type='date' id='anticipated_award_date' name='anticipated_award_date' value='' class="dateInput">
        </div>
        <br><br>
        <br>
        
        <div id="leftColumnInfo"><label>Award Date</label></div>
        <div id="rightColumnInfo"><input type='date' id='award_date' name='award_date' value='' class="dateInput">
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label>POP Start Date</label></div>
        <div id="rightColumnInfo">
        <input type='date' id='pop_start_date' name='pop_start_date' value=''class="dateInput">
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label>POP End Date</label></div>
        <div id="rightColumnInfo">
        <input type='date' id='pop_end_date' name='pop_end_date' value='' class="dateInput">
        </div>
        <br><br>
        
        <div id="leftColumnInfo"><label>Project Number</label></div>
        <div id="rightColumnInfo">
        <input type='text' id='project_number' name='project_number' value=''>
        </div>
        <br><br>
           
    </div> <!-- end of rightInfo div -->
    
    <label class="requiredField marginLeft textInputForm" id="task_description_label" >Task Description</label><br>
    <div class="richTextAreaStyleLarge marginLeft">
        <input class="editor requiredFieldValue" id="task_description" name="task_description" value=''>
    </div>  
    <br>
    <br>
    <div id="bottomInfoAdd">
        <input id="saveRow" class="gosubmit" type="button" name='fileAction' value="Save Row" />
        <input id="deleteRow" class="cancel" value="Delete Row" name='cancel' type='button' />
        <input type="button" id="return" class="cancel" value="Return" />
        <br><br>
    </div><!-- End of Buttons -->
    
</formInput>

</div> <!-- end of mainFormTableInput -->        
</div> <!-- End of Main -->
</body>
</html>

<script type="text/javascript">

$(document).ready(function() {
    
    
    $("#table_sales_proj").tablesorter();
    
    $(".editor").jqte(); 
    
    setupInputParameters();
    
    salesId=$('#salesId').val();
    
    $('#mainTitle').text('Projects/Tasks Input For Sales Lead Id: '+salesId);
    
    //console.log('**** sales id: '+salesId);
    buildTable('TaskEntryTable',salesId);
    
    $("#return").click(function () {
        
        /* need to add updating the parent window table before closing. */
        openNewWindow('','CLOSESELF');
    });
 
    $("#main_table_sales_proj").on('click','tr', function () {
     
        var id = this.id;
        var rowId=getEditRowId(id,"#editId");
        
        highlightAndLoadRow(id,rowId,'main_table_sales_proj',1);
        
    });
    
    /* when submit selected */
    $("#saveRow").click(function () {
        
        //get all input fields to file.
       
        var dataArray = new Array();
        var inputTypesArray = ['input','select','.editor'];
        
        /* must initialize like this for JSON.stringify to work */
        dataArray={};
      
        getFormValuesById(dataArray,inputTypesArray,'button,hidden');
        
        /* for debug 
        for (var key in dataArray) {
                console.log('---- dataArray: '+key+' = '+dataArray[key]);
            }
        */
        
        dataArray['sales_leads_id']=getSalesId();

        /* validate data.  If error - JAlert and remain on form */
        /* check for required fields */
        var errMsg=checkRequiredFieldsError('.requiredFieldValue');
        
        //console.log("Returned from checkrequired, errMsg: "+errMsg);
        
        if (errMsg != '') {
            jAlert(errMsg,'Missing Required Fields');
        }
        else {
           /* if no error - then create results string from array */
            
            var dataString=JSON.stringify(dataArray);
       
            var editId=$("#editId").val();
            var whereClause='';
        
            if (editId != '') {
                whereClause=' WHERE id='+editId;    
            }
        
            /* call AJAX to file data  */
            //function fileDataOnServer(tableName,dataString,deleteFlag, whereClause, formName, editRowId) 
            fileDataOnServer('sales_task_data',dataString,'0',whereClause,'TaskEntryTable',editId); 
        }
    });
    
    
    $("#deleteRow").click(function () {
        /* make sure row is selected */
        var editId=$("#editId").val();
        
        if (editId == '') {
            jAlert('Please Select A Row','Delete');
        }
        else {
            jConfirm('Are you sure you want to delete this task?','Delete',function (callback){
                if (callback) {
                    var editId=$("#editId").val();
                    var whereClause='WHERE id='+editId;
                    fileDataOnServer('sales_task_data','',1,whereClause,'TaskEntryTable',editId);    
                }
            });    
        }      
    });
    
});

//this function highlights the selected row on the table and loads the
//fields on the form with the data from the database.
function highlightAndLoadRow(id, rowId, tableId,clickEvent) {        

    console.log('in highlist, tableId: '+tableId+' id: '+id);
    selectRow(tableId,'',id,clickEvent);
        
    if (rowId=='') {
    	//clear all fields on form - input fields, except button fields, task_description in editor field.
        var clearArray=['input','select'];
        clearAllFields(clearArray,'button','task_description','.editor');
            
    }
    else {
        var whereClause='WHERE id='+rowId;
        console.log("calling getServerTableData, whereClause: "+whereClause);
        getServerTableData('sales_task_data','TaskEntryTableForm',whereClause,'');
    }   
}


function setupInputParameters() {
    var salesId=getParameter('SALESID');
    var editId=getParameter('ROWID');
    var rowId='';
    
    //console.log('On Load:   salesId: '+salesId+' editId: '+editId);
    if (editId) {
        rowId=getEditRowId(editId,"#editId");
        //if rowId is not blank - 
        if (rowId) highlightAndLoadRow(editId, rowId,'main_table_sales_proj',''); 
    }
    $('#salesId').val(salesId);
    
}

function getSalesId() {
    return $('#salesId').val();
}


</script>

<?php

/* initializes data that is used to build form selection lists */
function initializeFormData(&$employeeListArray,&$contractStatusArray) {
     
    /* fill arrays with data from tables that are needed for selection and check box lists */
    $fieldArray=array("id","value");
    $contractStatusArray=getTableData('sales_task_status_table',$fieldArray,'','');
    
    $fieldArray=array("id","name_first","name_last");
    $orderBy="Order By name_last ASC";
    $employeeListArray=getTableData('employee_data',$fieldArray,"",$orderBy);
}
 
?>

