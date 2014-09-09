<!DOCTYPE html>


<!--
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
-->
 
<html>
    <head>
        
        <title>Omnicon Dashboard</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
<div class="mainheadercontent">
    <!-- Title Column-->
    <!--<div class="Title"> -->
    <div class="Title">    
        <p class="dashboardTitle">Welcome to the Omnicon Dashboard</p>
        <br>
    </div>
    <div class="TitleRight">
    <p class="paragraphTitle"> Administrator Password</p>
    <input type="password" maxlength="4" id="adminPassword" value="">
    <input type="text" id="adminFlag" value="" visible=false>
    </div>
</div>
<div class="mainBody">
<!-- First Column-->
<div class="FirstCol">
    <p class="paragraphTitle">Projects</p>
    <p class="paragraphSubLines">
    <a id="ViewProjectData" class="link">View/Search/Edit Projects<br>
    <a id="addNewProject" class="link">Add New Project</a></p>
    
    <p class="paragraphTitle">Meeting Minutes</p>
    <p class="paragraphSubLines">
    <a class="link" id="ViewMeetingDatabase" >View/Search/Edit Meeting Minutes<br>
    <a class="link" id="addNewMeeting">Add New Meeting</a></p>
    
    <p class="paragraphTitle">Action Items</p>
    <p class="paragraphSubLines">
    <a class="link" id="ViewActionDatabase">View/Search/Edit Action Items<br>
    <a class="link" id="addNewActionItem">Add New Action Item</a></p>
    
    <p class="paragraphTitle">Sales Leads</p>
    <p class="paragraphSubLines"><a href="">View/Search/Edit Sales Leads<br> 
    <a class="link" id="addNewSalesLead">Add New Sales Lead</a></p>
    
</div>
<!-- Second Column-->
<div class="SecondCol">
    <p class="paragraphTitle">Omnicon Company Documents:</p>
    <p class="paragraphSubLines">Enter Timecard Info <br>
    <a href="PhoneList.php" target="_blank">Phone List</a>
    <br>
    Company Manual<br>
    Current Projects Folder
    <br><br><br></p>
    <div class="subCol">
        <p class="paragraphTitle">Omnicon Motto:</p>
        <br>
        <p class="paragraphSubLines">Trust Others</p>
        <p class="paragraphSubLines">Honor All Commitments</p>
        <p class="paragraphSubLines">Excel In All We Do</p>
        <p class="paragraphSubLines">Integrity That Is Uncompromising</p>
        <p class="paragraphSubLines">Respect Others</p>
    </div> <!-- End of subCol -->
</div><!-- End of Second Column -->
<br>

</div>  <!-- End of mainBody -->

</div> <!-- End of main -->
</body>
</html>
<!-- scripts that direct to next url based on id selected -->
<script>
$(document).ready(function() {
    
    //make sure browser window is sized correctly.  IE opens in full window.
    window.resizeTo(1160,950);
    
    $("#addNewProject").hide();
    $("#adminFlag").hide();
    $('#adminFlag').val('');
    
    //validate admin password.  If valid - enable and set flag to pass to child pages
    //to allow admin functions.
  
    $("#adminPassword").keyup(function() {
        var el = $(this);
        var pwVal=el.val();
        if (pwVal.length==4) {      
           //call function to validate password
            $.get("ValidatePassword.php",
                {password: pwVal},
                function(ajaxresult,status){  
                    if (ajaxresult==1) {
                        $("#addNewProject").show();
                        $('#adminFlag').val(1);
                        el.val('');
                        jAlert('Administrator Access Granted.','Admin Password Entry');
                    }
                    else {
                        $("#addNewProject").hide();
                        $('#adminFlag').val('');
                        el.val('');
                        //set focus back on admin password for re-entry after displaying jAlert message.
                        jAlert('Invalid Password','Admin Password Entry', function() {el.focus();});
                    }
                }    
            );
        }
    });  
    
    $("#addNewProject").click(function () {
        openNewWindow('addNewProject.php','INPUTFORM');
    });
    
    $("#ViewProjectData").click(function () {
        var url='ViewProjectDatabase.php';
        //add adminFlag as parameter.  If set to 1 then enable add button.
        //if set to 0 - then disable add buttonon.
        var adminFlag=$('#adminFlag').val();
        url=url+'?ADMIN='+adminFlag;
        openNewWindow(url,'VIEWFORM');
    });
    
    $("#addNewActionItem").click(function () {
        openNewWindow('addNewActionItem.php','INPUTFORM');
    });
    
    $("#ViewActionDatabase").click(function () {
        openNewWindow('ViewActionDatabase.php','VIEWFORM');
    });

    $("#addNewMeeting").click(function () {
        openNewWindow('addNewMeeting.php','INPUTFORM');
    });
    
    $("#ViewMeetingDatabase").click(function () {
        openNewWindow('ViewMeetingDatabase.php','VIEWFORM');
    });
    
    /* addNewSalesLead*/
     $("#addNewSalesLead").click(function () {
        openNewWindow('addNewSalesLead.php','INPUTFORM');
    });
    
});
</script>