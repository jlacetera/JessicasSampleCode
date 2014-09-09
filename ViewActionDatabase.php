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

//phpinfo(); just for debug - shows you what versions are running.


/* this queries database for table data to fill in form data on html below */
/* data read into local arrays to fill html */

/* initialize arrays */

$ActionItemArray="";
$ProjectArray="";
$AssignedToArray="";
$PriorityArray="";
$StatusArray="";

/* this initializes the select and checkbox lists that are populated from the database */
initializeFormData($ActionItemArray,$ProjectArray,$AssignedToArray,$PriorityArray,$StatusArray);

/* initialize all fields on form to blank */

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>View Action Items</title>
        
              
        <?php
         //include scripts and styles
        include 'standardFormScriptsCSS.php';
        include 'tableSorterScriptsCSS.php';
        ?>    
        
    </head>
    <br>
    <body>
    <div class= "main mainTable">
        <?php
        //Adds Omnicon Dashboard Header
        include 'header.php';
        ?>
            
    <div id="viewTitle">
        <p class="mainTitle">Action Items</p><br>
    </div>
    <div id='resetFilter'>
        <button type="button" class="reset">Reset Filters Below</button>
        <br>
        <a href="TableSortingOptions.php" target="_blank">View Table Sorting Options</a>
    </div>    
    <div class="mainForm">           
    <br>
    <div class="scroll">
        <table id="table" class="tablesorter">  <!-- Sortable Table -->
            <thead> <!-- Headers -->
                <tr>
                    <!--<th data-placeholder="" class="filter-false"></th> -->
                    <th data-placeholder="20">AI #</th> 
                    <th data-placeholder="Try 14121">Project #</th> 
                    <th data-placeholder="Try /20[^0]\d/ or >1/1/2010">Date Opened</th> 
                    <th data-placeholder="Try /20[^0]\d/ or >1/1/2010">Date Due</th> 
                    <th data-placeholder="Try B*{space} or alex|br*|c" class="filter-match">Assigned To</th>
                    <th data-placeholder="Try High" class="filter-match">Priority</th>
                    <th data-placeholder="Try B*{space} or Task|br*|c" class="filter-match">Task Title</th>
                    <th data-placeholder="Try Open" class="filter-match">Status</th>
                    <th data-placeholder="Try 1">Deliverable</th>
                </tr> 
            </thead>
            <tbody id='view_table'> <!-- Body of Data -->
            <?php
            /* this should really be rendered/setup in javascript */
            $htmlOutput='';
            if (is_array($ActionItemArray)) {
            
            $len=count($ActionItemArray);
            $htmlOutput='';
            
            for ($i=0; $i<$len; ++$i) {
                /* set up rows/columns in table */
                $htmlOutput.='<tr id=row'.$ActionItemArray[$i]['id'].'>';
                $htmlOutput.='<td>'.$ActionItemArray[$i]['id'].'</td>';
                $htmlOutput.='<td>'.$ProjectArray[$i]['project_number'].'</td>';
                $htmlOutput.='<td>'.$ActionItemArray[$i]['date_opened'].'</td>';
                $htmlOutput.='<td>'.$ActionItemArray[$i]['date_due'].'</td>';
                $htmlOutput.='<td>'.$AssignedToArray[$i]['name_last'].", ".$AssignedToArray[$i]['name_first'].'</td>';
                $htmlOutput.='<td>'.$PriorityArray[$i]['value'].'</td>';
                $htmlOutput.='<td>'.$ActionItemArray[$i]['task_title'].'</td>';
                $htmlOutput.='<td>'.$StatusArray[$i]['value'].'</td>';
                if ($ActionItemArray[$i]['deliverable_flag']==0) {
                    $deliv='N';
                }
                else {
                    $deliv='Y';
                }
                $htmlOutput.='<td>'.$deliv.'</td>';
                $htmlOutput.='</tr>';
           }
         }
         echo $htmlOutput;
         ?>
        </tbody>
        </table>
    <!-- hidden field to put selected rowid in -->
    <input type="text" id='selectedId' hidden value=''>
    <input type="text" id='rowsToPrint' hidden value=''>
    
    <br>
    <br>
        </div> <!-- End of Scroll -->    
        <br>     
        <div class="Buttons">
            <input type="button" id="editView" class="gosubmit" value="Edit" />&nbsp; &nbsp;
            <input type="submit" id="addNew" class="gosubmit" value="Add New" />&nbsp; &nbsp;
            <input type="button" class="gosubmit" id="printAll" value="Print Table" />&nbsp; &nbsp;
            <input type="button" class="gosubmit" id="printSelected" value="Print Selected" />&nbsp; &nbsp;
            <input type="button" id="return" class="cancel" value="Return" />     
        </div>
        
    </div><!-- End of MainForm-->
</div> <!-- End of Main -->
</body>
</html>

<script type="text/javascript">

$(document).ready(function() {
    

    $("#editView").click(function () {
         GetSelected('1');
    });
    
     $("#addNew").click(function () {
         openNewWindow('addNewActionItem.php','INPUTFORM');
         
    });
    
     $("#return").click(function () {
         openNewWindow('index.php','CLOSESELF');
    });
    
    /* highlight click when row selected from any table */
    $("#view_table").on('click','tr', function () {
        console.log('in click function for main_table row selected');
        console.log('this.id: '+this.id);
        var selectedId=this.id;
        //remove 'row' to get real id
        selectRow('view_table','selectedId',selectedId,1);
    });
    
    $("#view_table").on('dblclick','tr', function () {
        var selectedId=this.id;
        //remove 'row' to get real id
        selectRow('view_table','selectedId',selectedId,1);
        GetSelected('');
    });
    
    /* Print All  - get all rows that aren't hidden on form and sent to function to print. */
    /* Print Selected - prints that row.  */
    $('#printSelected').click(function() {
        printSelected('ActionItems');
    });
    
    $('#printAll').click(function() {
        printAll('ActionItems');
    });
    
    
});

function GetSelected(displayAlert){
    var selected=$('#selectedId').val();
    var url='';
    if (selected != '') {
        selected=getEditRowId(selected,'');
        url="addNewActionItem.php?ID="+selected;
        openNewWindow(url,'INPUTFORM');
    }
    else {
        if (displayAlert) jAlert("Please select Action Item!"); 
    }
}
</script>

<?php

/* every form should have an initializeFormData function that initializes the local arrays that are needed
 * to populate the select and checkbox list data needed for the form.
 */
function initializeFormData(&$ActionItemArray,&$ProjectArray,&$AssignedToArray,&$PriorityArray,&$StatusArray) {
     
    /* fill arrays with data from tables that are needed for selection and check box lists */
      
    $fieldArray=array("id","project_data_id", "date_opened", "date_due", "assigned_to_employee_id", "ai_priority_id", "task_title", 
        "ai_status_id", "deliverable_flag");
    $ActionItemArray=getTableData('action_item_data',$fieldArray,'','');
   
    $ProjectArray=returnSQLQuery('SELECT project_number FROM action_item_data LEFT JOIN project_data ON action_item_data.project_data_id=project_data.id ORDER by action_item_data.id ASC');
    
    $AssignedToArray=returnSQLQuery('SELECT name_last, name_first FROM action_item_data LEFT JOIN employee_data ON action_item_data.assigned_to_employee_id=employee_data.id ORDER by action_item_data.id ASC ');
    
    $PriorityArray=returnSQLQuery('SELECT value FROM action_item_data LEFT JOIN ai_priority_table ON action_item_data.ai_priority_id=ai_priority_table.id ORDER by action_item_data.id ASC');
    
    $StatusArray=returnSQLQuery('SELECT value FROM action_item_data LEFT JOIN ai_status_table ON action_item_data.ai_status_id=ai_status_table.id ORDER by action_item_data.id ASC');
    
}
 
?>

