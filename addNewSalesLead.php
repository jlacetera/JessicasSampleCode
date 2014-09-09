<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sales Leads</title>
        
        <?php
         //include scripts and styles
        include 'standardFormScriptsCSS.php';
        include 'tableSorterScriptsCSS.php';
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
        
    <p class="mainTitle">Sales Entry Form - Sales Id: 1</p>
    <input type="text" hidden class="formInputSmall" name='salesId' id='salesId' value="">
        <!-- leftInfo  -->
    <div id='leftInfoAddSmall'> 
            <div id="leftColumnInfo"> 
            <label class="requiredField">Sales Person</label>
            </div>
            <div id="rightColumnInfo">
            <select class="formInputMedium">
                <Option Value=1>Scott  Abrams</option><Option Value=2>Hunter  Abrams</option><Option Value=3>Matthew  Amrhein</option><Option Value=4>Thomas  Bellavia</option><Option Value=5>Lloyd  Blueweiss</option><Option Value=6>Lori  Butler</option><Option Value=7>Vincent  Cice</option><Option Value=9>Joseph  DuPlessis</option><Option Value=8>Alfred  DuPlessis</option><Option Value=10>James  Fecteau</option><Option Value=11>Karen  Frank</option><Option Value=12>Paul  Grech</option><Option Value=13>Brian  Kain</option><Option Value=14>Michael  Kremlicka</option><Option Value=15>Jessica  Lacetera</option><Option Value=16>Jeffrey  Mars</option><Option Value=17>Jennifer  Millard</option><Option Value=18>Laurel  Murphy</option><Option Value=19>San  Nguyen</option><Option Value=20>Derek  O Connor</option><Option Value=21>Nathaniel  Ozarin</option><Option Value=22>Ilya  Rabkin</option><Option Value=23>Samir  Rawani</option><Option Value=24>Mark  Saglimbene</option><Option Value=25>Scott  Schulman</option><Option Value=26>Jonathan  Shore</option><Option Value=27>Christopher  Stevens</option><Option Value=28>Vincent  Sweeney</option><Option Value=29>Douglas  Wendt</option><Option Value='' selected></option> 
            </select>
            
            </div>
            <br><br>
            <div id="leftColumnInfo"> 
            <label>Sales Manager</label>
            </div>
            <div id="rightColumnInfo">
                <select class="formInputMedium">
                <Option Value=1>Scott  Abrams</option><Option Value=2>Hunter  Abrams</option><Option Value=3>Matthew  Amrhein</option><Option Value=4>Thomas  Bellavia</option><Option Value=5>Lloyd  Blueweiss</option><Option Value=6>Lori  Butler</option><Option Value=7>Vincent  Cice</option><Option Value=9>Joseph  DuPlessis</option><Option Value=8>Alfred  DuPlessis</option><Option Value=10>James  Fecteau</option><Option Value=11>Karen  Frank</option><Option Value=12>Paul  Grech</option><Option Value=13>Brian  Kain</option><Option Value=14>Michael  Kremlicka</option><Option Value=15>Jessica  Lacetera</option><Option Value=16>Jeffrey  Mars</option><Option Value=17>Jennifer  Millard</option><Option Value=18>Laurel  Murphy</option><Option Value=19>San  Nguyen</option><Option Value=20>Derek  O Connor</option><Option Value=21>Nathaniel  Ozarin</option><Option Value=22>Ilya  Rabkin</option><Option Value=23>Samir  Rawani</option><Option Value=24>Mark  Saglimbene</option><Option Value=25>Scott  Schulman</option><Option Value=26>Jonathan  Shore</option><Option Value=27>Christopher  Stevens</option><Option Value=28>Vincent  Sweeney</option><Option Value=29>Douglas  Wendt</option><Option Value='' selected></option> 
            </select>         
            </div>
            <br><br>
            <div id="leftColumnInfo"> 
            <label class="requiredField">Client</label>
            </div>
            <div id="rightColumnInfo">
            <select id="client" name="client" class="formInputMedium">
<option value="1">AAI</option><option value="2">AVIONIC INSTRUMENTS</option><option value="3">B/E AEROSPACE INC</option><option value="4">BREEZE-EASTERN CORPORATION</option><option value="5">BREN-TRONICS INC</option><option value="6">COMAC</option><option value="7">CORERO NETWORK SECURITY INC</option><option value="8">DDC</option><option value="9">EATON</option><option value="10">EDO DEFENSE SYSTEMS</option><option value="11">GOODRICH CORP</option><option value="12">KONGSBERG</option><option value="13">L3 COMMUNICATIONS-NARDA</option><option value="14">NORTHWEST AEROSPACE TECHNOLOGIES INC</option><option value="15">OPS ALA CARTE LLC</option><option value="16">PARKER CSD</option><option value="17">PARKER FSD</option><option value="18">PARKER GAS TURBINE FUEL SYSTEMS</option><option value="19">RCO ENGINEERING INC</option><option value="20">SIKORSKY</option><option value="21">SPARTON ELECTRONICS</option><option value="22">SYMBOL TECHNOLOGIES INC</option><option value="23">TELEPHONICS</option><option value="24">TERADYNE INC</option><option value="25">VICON</option><option value="" selected=""></option>                     
            </select>    
            </div>
                <!-- these will be filled in when form loads  based on interactions -->
            <br><br>
          
            <div id="leftColumnInfo"> 
            <label class="requiredField">Value</label>
            </div>
            <div id="rightColumnInfo">
              <select id="client" name="client" class="formInputMedium">
                <option value="1">Low (0 - 20,000)</option>
                <option value="2">Medium (20,000 - 50,000)</option>
                <option value="2">High (50,000 - 100,000)</option>
                <option value="2">Very High ( > 100,000)</option>
                <option value=""></option>
                <option value="" selected=""></option>
              </select>
           
            </div>
            <br><br>
              <div id="leftColumnInfo"> 
            <label class="requiredField">Priority</label>
            </div>
            <div id="rightColumnInfo">
            <select id="Priority" name="priority" class="formInputMedium">
        <option value="1">Low</option><option value="2">Medium</option><option value="3">High</option><option value="" selected=""></option>        
            </select>    
            </div>
            <br><br>
            <label> Initial Contact Date</label>
            <br>
            <label> Last Contact Date</label>
        <!-- end leftInfo --> 
        </div>
        
        <!-- rightInfo will have minutes, actions, decisions -->
        <div id="rightInfoAddSmall">
            <div id="leftColumnInfo"> 
            <label>Industry</label>
            </div>
            <div id="rightColumnInfo">
                <select>
                    <option></option>
                    <option>Aerospace</option>
<option>Agriculture</option>
<option>Alternative & Renewable Energy</option>
<option>Amusement Parks</option>
<option>Athletic & Sports</option>
<option>Automotive</option>
<option>Chemical</option>
<option>Communications</option>
<option>Computers</option>
<option>Consumer Electronics/Products</option>
<option>Construction</option>
<option>Control Systems</option>
<option>Defense</option>    
                  
                </select>    
                
          
            </div>
              <br><br>
            <div id="leftColumnInfo"> 
            <label>Source</label>
            </div>
            <div id="rightColumnInfo">
                <select>
                    <option></option>
                    <option>Trade Show</option>
<option>Omnicon Website Inquiry</option>
<option>Publication</option>
<option>Referral</option>
                </select>
                
            
            </div>
            <br><br>
            <div id="leftColumnInfo"> 
            <label class="requiredField">Source Info</label>
            </div>
            <div id="rightColumnInfo">
            <input type="text" class="formInputMedium" name='sourceInfo' id='sourceInfo' required value="">
            </div>
            <br><br>
          
            <!--Calendar Drop down-->
            <div id="leftColumnInfo"> 
                <label class="requiredField">NDA Date</label>
            </div>
            <div id="rightColumnInfo">
            <input type="date" name="NDADate" required class="NDADate" value="">
            </div>
        </div>        
        <br><br>
        
    <!-- beginning of first table info -->
            
    <div class="mainFormTableInput">           
    <!-- added mainForm class, removed scroll -->
    <p>Project/Task Information</p>
    <!-- working on <span class="ui-icon ui-icon-trash"></span> -->
    <div class="scrollTableInput scrollTableSubTable">
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
    <p> Point of Contact</p>
    <input type="text" id='sales_project_selected_id' hidden value=''>
    <div class="scrollTableInput scrollTableSubTable">
        <table id="table_sales_poc" class="tablesorter">  <!-- Sortable Table -->
            <thead> <!-- Headers -->
                <tr>
                    <th data-placeholder="" class="filter-match">Name</th> 
                    <th data-placeholder="" class="filter-match">Title</th> 
                    <th data-placeholder="" class="filter-match">Business Phone</th> 
                    <th data-placeholder="" class="filter-match">Cell Phone</th> 
                    <th data-placeholder="" class="filter-match">Email Address</th>          
                </tr> 
            </thead>
            <!-- need to build this tbody using javaScript, so that it can be refreshed after filing -->
        <tbody id="main_table_sales_poc"> <!-- Body of Data -->
        </tbody>
        </table>
    </div> <!-- End of Scroll -->
     
    </div> <!-- end of mainForrmtable -->        
        <!-- end of first table info -->
        <div id="bottomInfoAdd">           
            <input id="gosubmit" class="gosubmit" type="submit" name='fileAction' value="Save" />
            <input id="cancel" class="cancel" value="Cancel" name='cancel' type='button' />
            <br>
            <br>
            <br>
        </div><!-- End of Buttons -->
        
    </div> <!-- End of mainFormProjectInput -->
    </div> <!-- End of Main -->
    </body>
       
</html>


 <script type="text/javascript">
        
$(document).ready(function() {
    //make sure browser window is sized correctly.  IE opens in full window.
    window.resizeTo(1160,950);
    
    /* sort tables on load */
    $("#table_sales_proj").tablesorter(); 
    $("#table_sales_poc").tablesorter(); 
    
    /* load initial tables based on sales id entered */
    /* hardcoded for testing */
    var salesId=1;
    //console.log('**** sales id: '+salesId);
    buildTable('TaskEntryTable',salesId);
    
    
    /* highlight click when row selected from any table */
    $("#main_table_sales_proj").on('click','tr', function () {
        console.log('in click function for main_table row selected');
        console.log('this.id: '+this.id);
        var selectedId=this.id;
        selectRow('main_table_sales_proj','sales_project_selected_id',selectedId,1);
    });
    
    /* on double click - bring up updateSalesTasks form with row selected */
    $("#main_table_sales_proj").on('dblclick','tr', function () {
        console.log("***** in doubleClick function");
        /* single click function should have already been called to highlight row and to put id in the correct spot.
         * call input form with sales id and sales task id sent in as parameters. */
        var selectedId=this.id;
        //hardcoding for now
        var salesId=1;
        
        url="updateSalesTasks.php?SALESID="+salesId+'&ROWID='+selectedId;
        openNewWindow(url,'INPUTFORM');
     });
});

function selectRow(tableId,storeSelectedRowId,selectedRowId) {
        /* set all classes back to original styles */
        $('#'+tableId+' tr:nth-child(odd)').addClass('odd');
        $('#'+tableId+' tr:nth-child(even)').addClass('even');
        
        /* selected id - add selected class, and remove odd/even class */
        
        $('#'+selectedRowId).removeClass('odd');
        $('#'+selectedRowId).removeClass('even');
        $('#'+selectedRowId).addClass('taskTableSelected');
        
        /* put selected row in the storeSelectedRowId fied on form so that it is accessible for further processing */
        if (storeSelectedRowId != '' ) {
            $('#'+storeSelectedRowId).val(selectedRowId);
        }       
    }
    
</script>

