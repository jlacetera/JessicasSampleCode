<?php

/* 
    This takes parameters reportType and id that has & delimitted list of row ids to print.
 */

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Report</title>
        
        <!-- include style sheet for reports -->
        <link rel="stylesheet" href="Styles/ReportStyle.css">
        
        <!-- JavaScript Core jQuery -->
        <script src="javascript/jquery-1.11.1.js" type="text/javascript"></script>
        <script src="javascript/jquery-migrate-1.2.1.js" type="text/javascript"></script>

        <script src="JQueryUI/jquery-ui.js"></script>
        <script src="JQueryUI/jquery-ui.min.js"></script>
        <link rel='stylesheet' href='JQueryUI/jquery-ui.css'>
        <link rel='stylesheet' href='JQueryUI/jquery-ui.min.css'>

        <link rel='stylesheet' href='JQueryUI/jquery-ui.structure.css'>
        <link rel='stylesheet' href='JQueryUI/jquery-ui.structure.min.css'>

        <link rel='stylesheet' href='JQueryUI/jquery-ui.theme.css'>
        <link rel='stylesheet' href='JQueryUI/jquery-ui.theme.min.css'>
      
        <!-- jQuery text editor files -->
        <script type="text/javascript" src="http://jqueryte.com/js/jquery-te-1.4.0.min.js" charset="utf-8"></script>
        <link type="text/css" rel="stylesheet" href="http://jqueryte.com/css/jquery-te.css" charset="utf-8" >
        
        <!-- Omnicon js files -->
        <script language='JavaScript' src='javascript/validateForm.js' type='text/javascript'></script>
        <script src='javascript/customScripts.js' type='text/javascript'></script>    
        
    </head>
    <br>
    <body>
        <input type='text' id='rowsToPrint' hidden value=''>
        <input type='text' id='reportType' hidden value=''>
        <div id='reportDiv'>            
        </div>
    </body>
</html>

<script type="text/javascript">

$(document).ready(function() {
  
  /* read in parameters sent in and put in 'rowsToPrint' and 'reportType' */
   var reportType=getParameter('NAME');
   var elemId=getParameter('ElemId');
   console.log('reportType: '+reportType+' elemId: '+elemId);
   /* get rows to print from parent form */
   var rowsToPrint=window.opener.document.getElementById(elemId).value;
   console.log('row ids from parent window: '+rowsToPrint);
   
  /* ajax call to get all of the data back */
  //getReportData(reportType,rowsToPrint);
 
    $.get("PrintTableData.php",
    {reportType: reportType, rowsToPrint: rowsToPrint},
    function(ajaxresult,status,xhr){
        $('#reportDiv').html(ajaxresult);
        /* fix editor styling for report output */
        /* hide and disable toolbar */
        $(".editor").jqte({sub:false, sup:false});
        $(".jqte_toolbar").hide();
        /* fix height */
        $(".jqte_editor").css('min-height', 'auto');
        $(".jqte_editor").css('max-height', 'auto');
        $(".jqte_editor").css('height', 'auto');
        
        $(".jqte").css('min-height', 'auto');
        $(".jqte").css('max-height', 'auto');
        $(".jqte").css('height', 'auto');
        
        /* fix margin, default is 30px */
        $(".jqte").css('margin','10px');
        $(".jqte").css('font-size','12px');
    });
 
});

</script>

