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

$MeetingArray="";
$MeetingTypeArray="";
$ProjectArray="";


/* this initializes the select and checkbox lists that are populated from the database */
initializeFormData($MeetingArray,$ProjectArray,$MeetingTypeArray);

/* initialize all fields on form to blank */

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>View Meeting Minutes</title>
        
         <?php
            //include scripts and styles
            include 'standardFormScriptsCSS.php';
            include 'tableSorterScriptsCSS.php';
        ?>
        
        
    </head>
    <br>
    <body>
    <div class="main mainTable">
    <?php
        //Adds Omnicon Dashboard Header
        include 'header.php';
    ?>
    <div id="viewTitle">
        <p class="mainTitle">Meeting Minutes</p><br>
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
                    <th data-placeholder="Try 141**">Project Number</th> 
                    <th data-placeholder="Try 2014-07-">Meeting Date</th> 
                    <th data-placeholder="Try Review">Meeting Type</th> 
                    <th data-placeholder="Try Kickoff">Meeting Title</th> 
                </tr> 
            </thead>
            <tbody id='view_table'> <!-- Body of Data -->
            <?php
            
            $htmlOutput='';
   
            if (is_array($MeetingArray)) {
                $len=count($MeetingArray);  
                for ($i=0; $i<$len; ++$i) {             
                    $htmlOutput.='<tr id=row'.$MeetingArray[$i]['id'].'>';       
                    $htmlOutput.='<td>'.$ProjectArray[$i]['project_number'].'</td>';
                    $htmlOutput.='<td>'.$MeetingArray[$i]['meeting_date'].'</td>';
                    $htmlOutput.='<td>'.$MeetingTypeArray[$i]['value'].'</td>';
                    $htmlOutput.='<td>'.$MeetingArray[$i]['meeting_title'].'</td>';
                    $htmlOutput.='</tr>';
                
                }
                echo $htmlOutput;
            }
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
            <input type="button" class="gosubmit" id="editView" value="Edit" />&nbsp; &nbsp;
            <input type="button" class="gosubmit" id="addNew" value="Add New" />&nbsp; &nbsp;
            <input type="button" class="gosubmit" id="printAll" value="Print Table" />&nbsp; &nbsp;
            <input type="button" class="gosubmit" id="printSelected" value="Print Selected" />&nbsp; &nbsp;
            <input type="button" class="cancel"  id="return" value="Return" /> 
        </div>
        
    </div><!-- End of MainForm-->
</div> <!-- End of Main -->
  
</body>
</html>

    
<script type="text/javascript">

$(document).ready(function() {
    
    /* add logic for editView, addNew and return onclick events */

    $("#editView").click(function () {
         GetSelected('1');
    });
    
     $("#addNew").click(function () {
         openNewWindow('addNewMeeting.php','INPUTFORM');
         
    });
    
     $("#return").click(function () {
         openNewWindow('index.php','CLOSESELF');
    });
    
       /* highlight click when row selected from any table */
    $("#view_table").on('click','tr', function () {
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
        printSelected('MeetingMinutes');
    });
    
    $('#printAll').click(function() {
        printAll('MeetingMinutes');
    });
});
   

function GetSelected(displayAlert){
    var selected=$('#selectedId').val();
    var url='';
    if (selected != '') {
        selected=getEditRowId(selected,'');
        url="addNewMeeting.php?ID="+selected;
        openNewWindow(url,'INPUTFORM');
    }
    else {
        if (displayAlert) jAlert("Please select Meeting!"); 
    }
}
</script>

<?php

/* every form should have an initializeFormData function that initializes the local arrays that are needed
 * to populate the select and checkbox list data needed for the form.
 */
function initializeFormData(&$MeetingArray,&$ProjectArray,&$MeetingTypeArray) {
     
    /* fill arrays with data from tables that are needed for selection and check box lists */
    
    $fieldArray=array('id', 'project_data_id', 'meeting_date', 'meeting_type_id', 'meeting_title');
    $MeetingArray=getTableData('meeting_minutes_data',$fieldArray,'','');
    
    //this didn't work $ProjectArray=returnSQLQuery('SELECT project_number FROM project_data LEFT JOIN meeting_minutes_data ON project_data.id=meeting_minutes_data.project_data_id ORDER by meeting_minutes_data.id ASC');
    
    $sql='SELECT project_data.project_number, meeting_minutes_data.project_data_id, meeting_minutes_data.id FROM meeting_minutes_data LEFT JOIN project_data ON project_data.id=meeting_minutes_data.project_data_id ORDER by meeting_minutes_data.id ASC';
    $ProjectArray=returnSQLQuery($sql);
    
    $MeetingTypeArray=returnSQLQuery('SELECT value FROM meeting_minutes_data LEFT JOIN meeting_type_table ON meeting_type_id=meeting_type_table.id ORDER by meeting_minutes_data.id ASC');
    
}
 
?>