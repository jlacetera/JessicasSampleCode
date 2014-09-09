/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
  Function Name:  redirectForm

  This function will get the current url and will use that to build the new url to
  redirect to.

  Parameters:  type - determines where to redirect to.
                    AI:  action item entry - redirect to AI view form.
                    PROJ:  project entry - redirect to project view form.
                    MTG:  redirect to meeting minutes view form.
                    INDEX:  redirect to index.
                    CLOSE:  if CLOSE - then close self window.

  Return Value:  None



**/
function redirectForm(type) {
    /* type=AI, PROJ,MTG to start */
    /* if closing, reload parent window before closing */
    if (type == "CLOSE") {
        opener.location.reload();
        self.close();
    }

    var urlName=window.location.href;
    
    var tempArray=urlName.split('/');
    
    //console.log('urlName: '+urlName);
    
    /* setup next page based on type passed in.  default to index.php */
    var nextPage='index.php';
    
    switch (type) {
        case 'AI':
            nextPage='ViewActionDatabase.php';
            break;
        case 'PROJ':
            nextPage='ViewProjectDatabase.php';
            break;
            
        case 'MTG':
            nextPage='ViewMeetingDatabase.php';
            break;
        case 'INDEX':
            nextPage='index.php';
            break;
        default:
            nextPage='index.php';
            break;
    }
    
    /* rebuild newurl without page on it */
    var i;
    var newUrl='';
    /* for this to work must strip off http://localhost - because this will be put on when set */
    for (i=3; i<tempArray.length-1; i++ ) {
        newUrl=newUrl+'/'+tempArray[i];
    }
    
    //add next page to url.
    newUrl=newUrl+'/'+nextPage;
    
    //redirect to correct url
    window.location.href = newUrl;
}
     
/**
  Function Name:  displayMessage
  This function calls jAlert to display message.

  Parameters:  
             message - message to display in alert box.
             title - title of alert box.
             redirectType - where to redirect to.  If not set then redirect callback function
                            will not be included in the jAlert function call.

  Return Value:  None
**/     
     
function displayMessage(message,title,redirectType,confirm) {
    
    if (confirm == 1) {
        if (redirectType == "") {
            jConfirm(message,title);
        }
        else {
            jConfirm(message,title,function (callback){
                if (callback) {
                    redirectForm(redirectType);
                }});
        }
    }
    else {
        if (redirectType == "") {
            jAlert(message,title);
        }
        else {
            jAlert(message,title,function (){redirectForm(redirectType)});
        }
    }
}


/* function openWindow *
 * This function opens a new window based on type parameter passed in.
 * 
 * @param {type} url - url of window to open.
 * @param {type} type - type of  window opening, ei: INPUTFORM, VIEWFORM,CLOSESELF,REPORT
 * @returns {Window|String} - new window object pointer.
 */
function openNewWindow(url,type) {
    
   var windowHeight=950;
   var windowWidth=1160;
   var reportHeight=950;
   var reportWidth=800;
   var myWindow='';
   
    //console.log("openNewWindow url: "+url+" type: "+type);
    switch (type) {
        case "INPUTFORM":
            //changed for testing
            if (url) myWindow=window.open(url,'','location=0, toolbar=0, scrollbars=1, height='+windowHeight+', width='+windowWidth); 
            //if (url) myWindow=window.open(url,'',"toolbar=yes,location=yes,menubar=yes");
                break;
        case "VIEWFORM":
            if (url) myWindow=window.open(url,'','location=0, toolbar=0, scrollbars=1, height='+windowHeight+', width='+windowWidth); 
            break;
        
        case "CLOSESELF":
            // we don't want to reload parent form 
            // opener.location.reload();
            self.close();
            break;
        case "REPORT":
            if (url) myWindow=window.open(url,'','location=0, toolbar=0, scrollbars=1, height='+reportHeight+', width='+reportWidth); 
            break;
        default:
            if (url) myWindow=window.open(url,'','toolbar=0, location=0, srollbars=1, height='+windowHeight+', width='+windowWidth);
            break;
    
    }
    return myWindow;
}

/* function getServerTableData *
 * 
 * This function makes an AJAX get call to return database table data

 * @param {type} tableName - name of mySQL database table
 * @param {type} formName - name of form to display data on, used to redirect
 * @param {type} whereClause - used for sql select statement
 * @param {type} orderBy - used for sql select statement.  Not being used at this time.
 * @return - no return value.
 */
/* use ajax to call function to return table data based on table name and where clause and order by */
/* for now - all columns are passed back, and orderBy is  not being passed in.  This will be added. */

function getServerTableData(tableName,formName,whereClause, orderBy) { 
    //console.log("*** making ajax call, table: "+tableName+' whereClause: '+whereClause+' orderBy: '+orderBy+" formName: "+formName);
    
    $.ajax({
        url: "GetTableData.php",
        cache: false,        
        dataType: "json",  
        type: "GET",        
        data: {TABLE: tableName, WHERE: whereClause, ORDERBY: orderBy},        
        success: function(result, success) {        
            //console.log('success from ajax call - success: '+success);            
            //console.log('result: '+result);
            //
            processTableData(formName,result);       
        }        
    });
}

/* this function loads database fields onto web form by calling loadDataOnForm or buildTableData.
 * 
 * @param {type} formName - descriptive name of form
 * @param {type} resultsJSON - database rows/fields in JSON
 */
function processTableData(formName,resultsJSON) {
  
    switch (formName) {
        case 'TaskEntryTableForm':
            loadDataOnForm(resultsJSON,'task_description');
            break;
            
        case 'main_table_sales_proj':
            buildTableData(formName,resultsJSON,1);
            break;
        default:
            break;
    }
}


/*  
 * Function: builtTableData
 * This functions builds an html table to displaying and selected data rows.
 * 
 * @param {type} tableBodyId - element id on form of table body
 * @param {type} tableRowsJSON - row information for table.
 * @returns - no return value.
 * 
 */

function buildTableData(tableBodyId,tableRowsJSON,addBlankRow) {
    
    var rowClass='';
    var rowIdPrePend='row';
    var rowArray='';
    var rowTypeArray='';
    var htmlString='';
    var value;
    var rowClassType='';
    
    switch (tableBodyId) {
        case 'main_table_sales_proj':
            rowClass ='';
            rowArray = ['task_title','rfp_date','proposal_lead_id','proposal_due_date','contract_status_id'];
            rowTypeArray=['text','date','select','date','select'];
            rowClassType='OddEven';
            break;         
        default:
            break;
    }
    
    /* for each tableRowsJSON - build table rows */
    
    $.each(tableRowsJSON, function(idx,obj) {  
        //console.log('BUILDTABLE:  idx: '+idx+' obj: '+obj);
        //console.log('  obj.id: '+obj.id);
        
        htmlString=htmlString+'<tr id="'+rowIdPrePend+obj.id+'" '+rowClass+'>';
        /* now build columns in this row */
        for (index = 0; index < rowArray.length; index++) {
            value='';
            if (obj[rowArray[index]] != undefined) {
                value=obj[rowArray[index]];
            }
            /* if lookup=select, value!= '' - get lookup from form select */
            if (value!='' && rowTypeArray[index]=='select') {
                value=getSelectLabel('#'+rowArray[index],value);
            }
            htmlString=htmlString+'<td>'+value+'</td>';
        }
        htmlString=htmlString+'</tr>';
    });
     
    if (addBlankRow) {
            htmlString=htmlString+'<tr id="'+rowIdPrePend+'" '+rowClass+'>';
            value='ADD NEW'
            for (index = 0; index < rowArray.length; index++) {
                htmlString=htmlString+'<td>'+value+' </td>';
                value='';
            }
            htmlString=htmlString+'</tr>';
        }
    
    //remove prevous rows from table
     $('#'+tableBodyId+' tr').remove(); 
    
    //add rows.
    $('#'+tableBodyId).append(htmlString);
    
    /* set class to odd/even based on row number.  Row number starts with 1 */
    if (rowClassType == 'OddEven') {
        $('tr:nth-child(odd)').addClass('odd');
        $('tr:nth-child(even)').addClass('even');
    }
    /*  need to add this so that table sorter knows about dynamically built table/row */
    $('#'+tableBodyId).trigger("update");
    $('#'+tableBodyId).trigger("appendCache");
}


/* Function selectRow -
 * function to select row on the table.  Highlights selected row, and will put the
 * rowId in the id field if storeSelectedRowId is set.
 * 
 * @param {type} tableId
 * @param {type} storeSelectedRowId
 * @param {type} selectedRowId
 * @returns {undefined}
 */
function selectRow(tableId,storeSelectedRowId,selectedRowId,clickEvent) {
   
    /* set all classes back to original styles */
        
        $('#'+tableId+' tr:nth-child(odd)').addClass('odd');
        $('#'+tableId+' tr:nth-child(even)').addClass('even');
        
        /* selected id - add selected class, and remove odd/even class */
        //console.log('in selectRow, selectedRowId: '+selectedRowId);
        
        if (clickEvent) {
            $('#'+selectedRowId).removeClass('odd');
            $('#'+selectedRowId).removeClass('even');
            $('#'+selectedRowId).addClass('taskTableSelected');
        }
        else {
            $('tr').each(function(){
                //console.log('row id: '+$(this).id);
                if( $(this).id==selectedRowId) {
                    console.log('match found');
                }
            });   
        }
       
        /* put selected row in the storeSelectedRowId fied on form so that it is accessible for further processing */
        if (storeSelectedRowId != '' ) {
            $('#'+storeSelectedRowId).val(selectedRowId);
        }       
    }

/* Function getSelectLabel - 
 * this is a debug function that accepts jquery selector as parameter, and then displays all
 * objects associated with the selector.
 * @param {type} resultsJSON
 * @param {type} editorFields
 * @returns - no return value
 */
function getSelectLabel(selector,value) {
    
    /* display select options */
    var returnVal='';
    selector=selector+' option';
    if (value!= '' ) {
        selector=selector+'[value='+value+']';
    }
    //console.log(" ******* IN DEBUG:  SELECTOR: "+selector);
    returnVal=$(selector).text();
    
    return returnVal;
    
    /* for debug
     $(selector).each(function() {
        var label=$(this).text();
        var val=$(this).val();
        console.log("select: "+val+" - "+label);
     });
    */
   }
    
    /* Function debugElementBySelector - this function is for debug only.
     */
 function debugElementBySelector(selector){   
    /*
    console.log(" ******* IN DEBUG:  SELECTOR: "+selector);
     $(selector).each(function() {
         for (var prop in this) {
             console.log('DEBUG:  prop: '+prop+' obj[prop]: '+this[prop]);
         }
     });
    */
}

/* Function loadDataOnForm -
 * this takes database table rows returned from in JSON format and sets id elements on form.  
 * id elements on form must match the database column names.
 * Parameters:  
 * resultsJSON - contains table data.
 * editorFields - string containing the ids of the editor fields, since val is set differently for jqte fields. 
 * */

function loadDataOnForm(resultsJSON,editorFields) {
    
    console.log('loadData, resultsJSON: '+resultsJSON);
    $.each(resultsJSON, function(idx,obj) {
        
        //console.log('idx: '+idx+' obj: '+obj);
        
        for (var prop in obj) {
         if (obj.hasOwnProperty(prop)) {
            //console.log(prop + " = " + obj[prop]);
            /* set id on form to this value */  
            var fieldId='#'+prop;
            /* if prop is in editorFields - then use jqteVal function */
            if (editorFields.search(prop) != -1) {
                $(fieldId).jqteVal(obj[prop]);
            }
            else {
                $(fieldId).val(obj[prop]);
            }
         }
      }
    });
}

/* Function getFormValuesById - 
 * 
 * this function returns dataArray[id]=value using jquery
 * 
 * @param {type} returnArray - array that is set - returnArray[id]=value
 * @param {type} jSelectorArray - array of jquery selectors
 * @param {type} typeExclude - string with types that should not be included.  Example:  'button,hidden'
 * @returns {undefined} - no return value.
 */

function getFormValuesById(returnArray,jSelectorArray,typeExclude) {
    
    var value;
    var id;
    var type;
    var j;
    var jSelector;
        
    for (var i = 0; i < jSelectorArray.length; i++) {
        jSelector=jSelectorArray[i];
        //console.log('**** GETIDS: jSelector: '+jSelector);
        $(jSelector).each(function() {
           value = $(this).val();
            id=this.id;
            if (id != '') {
                //console.log('    jSelector id - value: '+id+' - '+value+" type: "+type);
                type=$('#'+id).attr('type');
                //console.log('    jSelector id - value: '+id+' - '+value+" type: "+type);
                if ((type==undefined) || ((!typeExclude || (typeExclude.search(type)==-1)))) {
                    returnArray[id]=value;
                }
            }
        });
    }
}

/* Function fileDataOnServer - 
 * This function files data in mySQL database on server using ajax POST.
 * 
 * @param {type} tableName - name of database table
 * @param {type} dataString - data to be filed
 * @param {type} deleteFlag - set to 1 if deleting database table row.
 * @param {type} whereClause - used for sql
 * @param {type} formName - used for redirecting after call
 * @param {type} editRowId - unique row id used for edit or delete sql.
 * @returns - no return value.
 */

function fileDataOnServer(tableName,dataString,deleteFlag, whereClause, formName, editRowId) {
    
    $.ajax({
        url: "SaveTableData.php",
        cache: false,        
        dataType: "text",        
        type: "POST",        
        data: { dataToFile: dataString, tableName: tableName, deleteFlag: deleteFlag, whereClause: whereClause, editRowId: editRowId },        
        success: function(result, success) {        
            //console.log('success from ajax call - success: '+success);            
            //console.log('result: '+result);
            saveTableDataReturn(formName,result);
        }        
    });
}


/* based on formName - calls appropriate function to refresh page after filing */
function saveTableDataReturn(formName,result) {
    //console.log("in saveTableData, result: "+result);
    
    switch (formName) {
        case 'TaskEntryTable':
            //check result.  if not success - give error.  Delete row also calls this function.
            var typeArray=['input','select'];
            clearAllFields(typeArray,'button','task_description','.editor');
            salesId=getSalesId();
            //console.log('salesId: '+salesId);
            buildTable(formName,salesId);
            break;
        
        default:
            break;
    }
}

/* Function clearAllFields - 
 * this function will clear all input fields on web page. 
 * type can be any pattern match that works with jquery selector.  
 * id must be set for field for it to be cleared 
 * 
 * Parameters:
 * typeArray - array of jquery selectors
 * exclude - string of attribute types to exclude (example - label, button.
 * editorFields - string with jqte editor fields
 * editorClass - class that defines jqte editor fields.
 * */

function clearAllFields (typeArray, exclude, editorFields, editorClass) {
    var id='';
    var type;
    
    for (var i = 0; i < typeArray.length; i++) {
        type=typeArray[i];
        
        $(type).each(function() {
            id=this.id;
            //console.log('clear fields:   id: '+id);
            if (id != '') {
                //allInputs.attr('type');
                //console.log('clear fields:   id: '+id+' TYPE: '+$('#'+id).attr('type'));
                if ((exclude!= '') &&  $('#'+id).attr('type') != exclude) {
                    $('#'+id).val('');
                }
            }
        });
    }    
    
    if (editorClass != '') {
        $(editorClass).each(function() {
            id=this.id;
            //console.log('clear fields:   id: '+id);
            if (id != '') {
                //allInputs.attr('type');
                //console.log('clear fields:   id: '+id+' TYPE: '+$('#'+id).attr('type'));
                /* check editor fields */
                if (editorFields.search(id) != -1) {
                    $('#'+id).jqteVal('');
                }         
            }
        });
    }    
}



/* function builtTable - this function calls getServerTableData to execute ajax call to get
 * the data required to build html table.  Used on sales data form to get data to build sales task
 * table.  Sales_task_data is child tabel of sales_leads_data, and is linked by sales_leads_id.
 * Eventually this function will be called to support getting data to build additional child tables.
 * 
 * @param {type} formName - name used to identify table
 * @param {type} selectId - id of selected row in table, used in whereclause
 * @returns - no return value
 */

function buildTable(formName,selectId) {
    
     switch (formName) {    
        case 'TaskEntryTable':
            var whereClause='';
            if (selectId != '') {
                whereClause='WHERE sales_leads_id = '+selectId;
            }
            getServerTableData('sales_task_data','main_table_sales_proj',whereClause,'')
            break;
     
        default:
            break;
    }
}

/* function checkRequiredFieldsError - 
 * This function selects all fields based on requiredFieldSelector (for our form, class), and checks that
 * data is entered.  
 
 * @param {type} requiredSelector
 * @returns {String} - returns blank, or string with list of fields that are missing.
 */

function checkRequiredFieldsError(requiredSelector) {
    var errorFoundMessage='';
    var value='';
    var select='';
    var id='';
    var name='';
    
    /* select all input fields that are visible and required */
    //$('.requiredFieldValue').each(function() {
    $(requiredSelector).each(function() {
        id=this.id;
        value = $(this).val();
        if (id != '') {
            select='#'+id;
            value = $(this).val();
            console.log('**** checkRequired id: '+select+' value: '+value);
            if (value == '') {
                name=$(select+'_label').text();
                errorFoundMessage=errorFoundMessage+name+'\n';
                
            }   
        }
    });
    if (errorFoundMessage != '') {
        errorFoundMessage='The following fields require entry: \n'+errorFoundMessage;
    }
    
    return errorFoundMessage;
}

/* Function fixTimeFormat - 
 * This function selects all fields with the time class (passed in) and converts
 * from military time (as saved in database) to HH:MM Am/PM for display on form, and to match
 * jqueryUI time picker.
 * 
 * @param {type} timeClass - jquery class to select.
 * @returns - no returnvalue
 */
/* on form entry fixes all time picker fields with class timeClass from Military time to HH:MM AM/PM for display on form */
function fixTimeFormat(timeClass) {
    var id;
    var timeVal;
    
    if (timeClass != '') {
        $('.'+timeClass).each (function() {
            id=this.id;
            if (id != '') {
                timeVal=$(this).val();
                if (timeVal != '') {
                    timeVal=convertMilTimeToDisplayTime(timeVal);
                    //reset form val
                    $(this).val(timeVal);
                }
            }
        });
    }
}

/* Function convertMilTimeToDisplayTime - 
 * This function converts military time to HH:MM AM/PM.
 * @param {type} value - military time value
 * @returns {String} - HH:MM AM/PM
 */

function convertMilTimeToDisplayTime(value) {
if (value !== null && value !== undefined){ //If value is passed in
    if(value.indexOf('AM') > -1 || value.indexOf('PM') > -1){ //If time is already in standard time then don't format.
      return value;
    }
    else {
      if(value.length == 8){ //If value is the expected length for military time then process to standard time.
        var hour = value.substring ( 0,2 ); //Extract hour
        var minutes = value.substring ( 3,5 ); //Extract minutes
        var identifier = 'AM'; //Initialize AM PM identifier
 
        if(hour == 12){ //If hour is 12 then should set AM PM identifier to PM
          identifier = 'PM';
        }
        if(hour == 0){ //If hour is 0 then set to 12 for standard time 12 AM
          hour=12;
        }
        if(hour > 12){ //If hour is greater than 12 then convert to standard 12 hour format and set the AM PM identifier to PM
          hour = hour - 12;
          identifier='PM';
        }
        return hour + ':' + minutes + ' ' + identifier; //Return the constructed standard time
      }
      else { //If value is not the expected length than just return the value as is
        return value;
      }
    }
  } 
}


/* Function fixDateFormat - 
 * This function selects all jquery fields that have date class and converts them from
 * YYYY-MM-DD to MM/DD/YYYY for display on form, and to match jqueryui date picker.
 * 
 * @param {type} dateClass - class of date fields to convert.
 * @returns - no return value
 */
function fixDateFormat(dateClass) {
    var id;
    var dateVal;
    
    if (dateClass != '') {
        $('.'+dateClass).each (function() {
            id=this.id;
            if (id != '') {
                dateVal=$(this).val();
                if (dateVal != '') {
                    dateVal=convertDateToDisplayDate(dateVal);
                    //reset form val
                    $(this).val(dateVal);
                }
            }
        });
    }
}


/* Function convertDateToDisplayDate - 
 * Converts input parameter 'thisDate' to format MM/DD/YYYY from YYYY-MM-DD
 * @param {type} thisDate - input date
 * @returns - string in date format MM/DD/YYYY
 */

function convertDateToDisplayDate(thisDate) {
    var returnVal='';
    var year,month,day,dateInfo;
    
    returnVal=thisDate;
    //if date does not contain '-' then return.
    if (thisDate.indexOf('-')!= -1) {
        dateInfo=thisDate.split("-");
        day  = dateInfo[2]; 
        month = dateInfo[1]; 
        year  = dateInfo[0];
        returnVal=month+'/'+day+'/'+year;
    }
    return returnVal;
}

/* Function getParameter -
 * parses parameters from url string
 * @param {type} name - parameter name
 * @returns {Array|getParameter.results|Number} - value of parameter name
 */

function getParameter(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results==null){
            return null;
        }
        else{
            return results[1] || 0;
        }
}

/* Function getEditRowId -
 * takes rowNN and returns the actual id, and sets id on form to the element id.  This is called when
 * a row is selected on a table.
 * Although this is a fairly simple function, I didn't want to repeat the same code in multiple places.
 * @param {type} id - table row id in format 'row'NN, where NN is the actual id of the row in the database.
 * @param {type} idToSet - element id to save the row id in.
 * @returns {@arr;rowId|getEditRowId.rowId} - returns table row id.
 */

function getEditRowId(id,idToSet) {
       /* load data on page based on selected row */
        var rowId=id.split('w');
        rowId=rowId[1];
        if (idToSet != '') {
            $(idToSet).val(rowId);
        }
        
    return(rowId);   
}


/* the following functions support printing reports */

/* Function printSelected - 
 * This function is called when a selected row on a table in View*Database forms is selected for printing.
 * $selectedId should have the database row id of the table row selected to print.
 * 
 * @param {type} reportTitle - report identifier sent to showReport.
 * @returns - no return value
 */
function printSelected(reportTitle) {
    
    var selected=$('#selectedId').val();
    
    if (selected != '') {
        selected=getEditRowId(selected,'');
        showReport(reportTitle,selected,'rowsToPrint');
    }
    else {
        jAlert("Please Select Row To Print!"); 
    }
}

/* Function printAll - 
 * This function is called to print all of the rows that are visible on the table on the
 * View*Database forms.  All visible rows are put in comma delimited string and sent to showReport function.
 * 
 * @param {type} reportTitle - report identifier
 * @returns - no return value.
 */
function printAll(reportTitle) {
    
    var idList='';
    var id='';
    var finalIdList='';
         
    $('#table tr:visible').each(function() {
        //console.log('row id: '+this.id);
              
        id=this.id;
        if (id != '') {
            id=getEditRowId(id,"");
            idList=idList+','+id;
        }                             
        finalIdList=idList.substring(1);  
    });    
    showReport(reportTitle,finalIdList,'rowsToPrint');    
}
   
/* Function showReport - 
 * this function is called to call showReport.php, passing in as parameters the reportType and idsToPrint.
 * 
 * @param {type} reportType - report identifier (ActionItems, MeetingMinutes, Projects)
 * @param {type} idsToPrint - comma delimited list of row ids to print (from database).
 * @param {type} idToStoreRows - idsToPrint is stored in element on form, so that long list is not passed
 *                   in url as parameter.
 * @returns {undefined}
 */
function showReport(reportType,idsToPrint,idToStoreRows) {
    var url;
    
    if (idsToPrint != '') {
        /* store ids in hidden text field on form so that print url can access this */
        $('#'+idToStoreRows).val(idsToPrint);
        url="showReport.php?NAME="+reportType+'&ElemId='+idToStoreRows;
        openNewWindow(url,'REPORT');
    }
    else {
        jAlert('Please select rows to print.');
    }
}
