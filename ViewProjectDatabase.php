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

/* initialize arrays */

$ProjectArray="";
$StatusArray="";
$ProjectTypeArray="";
$ProgramManagerArray="";

/* this initializes the select and checkbox lists that are populated from the database */

initializeFormData($ProjectArray,$StatusArray,$ProjectTypeArray,$ProgramManagerArray);

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>View Project Database</title>
        
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
    <p class="mainTitle">Projects</p><br>
        
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
                    <th data-placeholder="14**">Project</th> 
                    <th data-placeholder="Try Parker or AAI or Motorola">Project Name</th> 
                    <th data-placeholder="Try Sent">Status</th> 
                    <th data-placeholder="Try Firm *">Project Type</th> 
                    <th data-placeholder="Try B* or A*" class="filter-match">Project Manager</th>
                    <th data-placeholder="Try 2014-07" class="filter-match">Closed Date</th>
                </tr> 
            </thead>
            <tbody id='view_table'> <!-- Body of Data -->
            <?php
            $htmlOutput='';
            if (is_array($ProjectArray)) {
                $len=count($ProjectArray);
                $htmlOutput='';
                 
                for ($i=0; $i<$len; ++$i) {
                    if ($ProgramManagerArray[$i]['name_last']=='') {
                        $ProgramManagerDisplayArray[$i]='';
                    }
                    else {
                        $ProgramManagerDisplayArray[$i]=$ProgramManagerArray[$i]['name_last']. ",". $ProgramManagerArray[$i]['name_first'];   
                    }
                    
                    $htmlOutput.='<tr id=row'.$ProjectArray[$i]['id'].'>';       
                    $htmlOutput.='<td>'.$ProjectArray[$i]['project_number'].'</td>';
                    $htmlOutput.='<td>'.$ProjectArray[$i]['project_name'].'</td>';
                    $htmlOutput.='<td>'.$StatusArray[$i]['value'].'</td>';
                    $htmlOutput.='<td>'.$ProjectTypeArray[$i]['value'].'</td>';                   
                    $htmlOutput.='<td>'.$ProgramManagerDisplayArray[$i].'</td>';
                    $htmlOutput.='<td>'.$ProjectArray[$i]['contract_closed_date'].'</td>';                    
                    $htmlOutput.='</tr>';
                }
                echo $htmlOutput;
            }
        ?>
        </tbody>
        </table>
    <input type="text" id='selectedId' hidden value=''>
    <input type="text" id='rowsToPrint' hidden value=''>
    
    <br>
    <br>
        </div> <!-- End of Scroll -->    
        <br>  
        <div class="Buttons">
            <input type="button" id="editView" class='gosubmit' value="Edit/View Project" />&nbsp; &nbsp;
            <input type="button" id="addNew" class='gosubmit' value="Add New Project" />&nbsp; &nbsp;
            <input type="button" class="gosubmit" id="printAll" value="Print Table" />&nbsp; &nbsp;
            <input type="button" class="gosubmit" id="printSelected" value="Print Selected" />&nbsp; &nbsp;
            <input type="button" id="return" class='cancel' value="Return" /> 
        </div>
        
        
    </div><!-- End of MainForm-->
</div> <!-- End of Main -->  
<!-- hidden field to store adminFlag -->
<input type="text" id="adminFlag" value="" visible=false>
</body>
</html>

<script type="text/javascript">
$(document).ready(function() {
    
    /* get parameter from url  and hide/show add new based on admin flag */
    
    /* hide adminFlag on form */
    $('#adminFlag').hide();
    
    var adminFlag=getParameter('ADMIN');
    if (adminFlag=='1') {
        $('#addNew').show();
        $('#adminFlag').val('1');
    }
    else {
        $('#addNew').hide();
        $('#adminFlag').val('');
    }
        
    $("#editView").click(function () {
         GetSelected('1');
    });
    
     $("#addNew").click(function () {
         openNewWindow('addNewProject.php','INPUTFORM');
         
    });
    
     $("#return").click(function () {
         openNewWindow('index.php','CLOSESELF');
    });
    
    /* highlight click when row selected from any table */
    $("#view_table").on('click','tr', function () {
        var selectedId=this.id;
        selectRow('view_table','selectedId',selectedId,1);
    });
    
    //on double click - open up edit window
    $("#view_table").on('dblclick','tr', function () {
        var selectedId=this.id;
        //remove 'row' to get real id
        selectRow('view_table','selectedId',selectedId,1);
        GetSelected('');
    }); 
    
    /* Print All  - get all rows that aren't hidden on form and send to function to print. */
    /* Print Selected - prints the selected row.  */
    $('#printSelected').click(function() {
        printSelected('Projects');
    });
    
    $('#printAll').click(function() {
        printAll('Projects');
    });
    
});


function GetSelected(displayAlert){   
    var selected=$('#selectedId').val();
    var url='';
    if (selected != '') {
        selected=getEditRowId(selected,'');
        url="addNewProject.php?ID="+selected+'&ADMIN='+$('#adminFlag').val();
        openNewWindow(url,'INPUTFORM');
    }
    else {
        if (displayAlert) jAlert("Please select Project!"); 
    }
}

</script>

<?php

/* every form should have an initializeFormData function that initializes the local arrays that are needed
 * to populate the select and checkbox list data needed for the form.
 */

function initializeFormData(&$ProjectArray,&$StatusArray,&$ProjectTypeArray,&$ProgramManagerArray ) {
    
/* fill arrays with data from tables that are needed for selection and check box lists */
    
    $fieldArray=array("id", "project_number", "project_name", "project_status_id", "project_type_id", "project_manager_id", "contract_closed_date");
    
    $ProjectArray=getTableData('project_data',$fieldArray,'','');
    
    $StatusArray=returnSQLQuery('SELECT value FROM project_data LEFT JOIN project_status_table ON project_data.project_status_id=project_status_table.id ORDER by project_data.id ASC');
    
    $ProgramManagerArray=returnSQLQuery('SELECT name_last, name_first FROM project_data LEFT JOIN employee_data ON project_data.project_manager_id=employee_data.id ORDER by project_data.id ASC ');
    
    $ProjectTypeArray=returnSQLQuery('SELECT value FROM project_data LEFT JOIN project_type_table ON project_data.project_type_id=project_type_table.id ORDER by project_data.id ASC ');
}
 
?>
