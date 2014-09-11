/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* 
 * 
 * @param {type} thisForm - pointer to form 'this'.
 * @param {type} formName - name of the input form.
 * @returns {Boolean}
 */
function validateForm(thisForm,formName) {
   
    var dispError='';
    var returnVal=true;
    var tempValue='';
    
    switch (formName) {
        case 'addNewProjectForm':
            /* put form specific field validation here */
            /* for new project form - fields status and projectType must be filled in, but standard
             * required field checking does not work.  Also - we can double check projectName and projectNumber.
             */
            //console.log('Status:b'+thisForm.projectStatus.value+"b");
            //console.log('type:b'+thisForm.projectType.value+"b");
            
            if (thisForm.projectType.value.trim() =='') {
                dispError=dispError+'Project Type entry is required. \n';
            }
            
            if (thisForm.projectStatus.value.trim()=='') {
                dispError=dispError+'Project Status entry is required. \n';
            }
            
            //validate projectNumber
            //regex used for this pattern.test(value) returns true or false.
            var tempNum=thisForm.projectNumber.value;
            //projectNumber must be all numbers.  Returns true if anything found that isn't between 0 - 9, ^ add 'not' to pattern match expression.
            if (/[^0-9-]/.test(tempNum)) {
                dispError=dispError+'Project Number Invalid.  Numbers only allowed.\n';
            }
            
            //validate project name
            var tempNum=thisForm.projectName.value;
            //projectName can be alpha, numberic, -, _, and spaces (/s).
            //returns true if any chars found that aren't alpha, numberic, space, _ or -, .,& or /
            if (/[^a-zA-Z0-9_-\s/\.&]/.test(tempNum)) {
                dispError=dispError+'Project Name Invalid.  Only a-z, A-Z, 0-9, - and _ allowed.\n';
            }
            //should validate that first character is upper case alpha for name.
            if (/[^A-Z]/.test(tempNum.charAt(0))) {
                dispError=dispError+' Project Name Invalid.  Must start with upper case letter.\n';
            }
            
           tempValue=thisForm.projectDescription.value;
           tempValue=tempValue.trim();
           if (tempValue == '') {
               dispError=dispError+'Project Description entry is required. \n';
           }
            
            break;
            
        case 'addNewActionItemForm':
            /* must validate that  status and assignedTo are set */
            if (thisForm.status.value.trim()=='') {
                dispError=dispError+'Status entry is required. \n';
            }
            
            if (thisForm.AssignedTo.value.trim() =='') {
                dispError=dispError+'Assigned To entry is required. \n';
            }
            
            if (thisForm.ai_progress_id.value.trim()=='') {
                dispError=dispError+'Task State entry is required. \n';
            }
            
            tempValue=thisForm.taskdescription.value;
            if (tempValue.trim() == '') {
               dispError=dispError+'Task Description entry is required. \n';
            }
            
            /* if deliverable - then project should be required */
          
            if (thisForm.deliverablebox.value == 1) {
               if (thisForm.project.value.trim()=='') {
                dispError=dispError+'Project entry is required. \n';
                } 
            }
            /**/
            break;
            
        case 'addNewMeetingForm':
            /* must validate that an omnicon participant was selected */
            /* name of field - meetingParticipants[]  - must have at least one entry */
            /* must see if thisForm.meetingParticipants[] has anything set.
             * 
             */
            /* must validate that  meeting type is entered */
             if (thisForm.Type_of_Meeting.value.trim() =='') {
                dispError=dispError+'Meeting Type entry is required. \n';
            }
            
            
           tempValue=thisForm.minutes.value;
           tempValue=tempValue.trim();
           if (tempValue == '') {
               dispError=dispError+'Meeting Minutes entry is required. \n';
           }
            
            break;
    }
   
    if (dispError !== '') {
        returnVal=false;
        jAlert(dispError);
    }
    
    return returnVal; 
}


/* this function makes ajax call to validate project number entered and give error if this project number is already used by another project */
/* this should be updated to pass project number and do logic on server side.  Might be faster processing */
function validateProjectNumber (projectNumberEntered,projId) {
    //console.log("*** making ajax call, project: "+projectNumberEntered+' id: '+projId);
                $.ajax({
                    url: "GetProjectNumbers.php",
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    data: "projNum: "+projectNumberEntered,
                    success: function(result, success) {
                        //console.log('success from ajax call - success: '+success);
                        //console.log('result: '+result);
                        
                        $.each(result, function(idx,obj) {
                                //console.log('results: '+obj.id+' '+ obj.projectNumber);
                                //if matching projectNumber, and id's don't match - then return error.
                                //if id's match - then assuming we are looking at the record that we are editing, so match is okay.
                                if (obj.projectNumber == projectNumberEntered) {
                                    if (projId != obj.id) {
                                        jAlert("Invalid Entry: "+projectNumberEntered+".  Duplicate Project Number.");
                                        $('#projectNumber').val("");
                                    }
                                }
                        });                         
                    }
            });
}

/* this function is used to validate that an end time is > start time. 
 * Displays jAlert message box, and returns 0 on error, 1 for success
 * 
 * start - time informat HH:MM
 * end - time in format HH:MM
 * dispMessage - 1 to display generic jAlert message.
 * @returns {Number} - 1 for success, 0 for error.
 */
function validateTime(start,end,displayMessage) {
    var validTime=1;
    
    //only validate if both start and end are set
    if (start && end) {
        start=timeToSeconds(start);
        end=timeToSeconds(end);
        console.log("start sec: "+start+" end sec: "+end);
        if (end < start) {
            console.log("in end<start case");
            if (displayMessage) jAlert('Invalid Time Entry.  Start Time cannot be after End Time.');
            validTime=0;
        } 
    }
    return validTime;
}

/* this function converts hh:min AM/PM to seconds, and returns seconds.  If input time not set, '' is returned */
function timeToSeconds(time) {

    var seconds='';
    
    if (!time) {
        seconds='';
    }
    else {
        var timeElem=time.split(' ');
        var times = timeElem[0].split(":");
        // if hours = 12, set hours = 0
        if (times[0]==12) {
            times[0]=0;
        }
        //if PM - then add 12 to hours.
        if (timeElem[1]=='PM') {
            times[0]=times[0]+12;
        }
        seconds=(times[0]*3600)+(times[1]*60);
    }
    return seconds;
}

/* this function validates a start and end date from a form.
 * 
 * @param {type} start - start date in format dd/mm/yyyy
 * @param {type} end - end date in format dd/mm/yyyy
 * @param {type} displayMessage - 1 to display jAlert message
 * @returns {Number} - 1 for success, 0 for error.
 */

function validateStartEndDate(start,end,displayMessage) {
    var validDate=1;
    
    if (start && end) {     
        start=getDateValue(start);
        end=getDateValue(end);
        
        //console.log("validate dates, start: "+start+" end: "+end);
        
        if (end < start) {
            validDate=0;
            if (displayMessage) {
                jAlert("Invalid Date Entry. End Date must be prior to Start Date");
            }
        }
    }
    
    return validDate;
}


/* this function takes for input the date format from an input form and returns the date object */
/* if input date contains '-' assumes YYYY-MM-DD format.
 * if input date contains / assumes MM/DD/YYYY format.
 */

function getDateValue(inputFormDate) {
    
    var returnDate='';
    var dateInfo='';
    var day='';
    var month='';
    var year='';
    var newDate='';
    
    if (inputFormDate.indexOf('-')!= -1) {
        dateInfo=inputFormDate.split("-");
        day  = dateInfo[2]; 
        month = dateInfo[1]; 
        year  = dateInfo[0];
    }
    else if (inputFormDate.indexOf('/')!= -1) {
        dateInfo=inputFormDate.split("/");
        day  = dateInfo[1]; 
        month = dateInfo[0]; 
        year  = dateInfo[2];
    } 
    
    if (year != '') {
        newDate=new Date(year, month,day);  
        returnDate=newDate.getTime();
    }
   
    return returnDate;
}

