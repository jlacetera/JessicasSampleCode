<?php

/* this library contains functions that are used to render html elements on a web
 * page.
 */


function echoValue(&$strValue) {
    echo "'".$strValue."'";
}


/* Function:  createTimeSelectionDataList
 * Renders/outputs html for input type="time", selection list for selecting time.
 * Returns html string with dataList in it.  Will echo string if called with echoFlag=1.
 * Parameters:
 * start - start time - in format '08:00AM'
 * end - end time - in format '11:00PM'
 * increment - increment time (5, 10, 15 minutes, etc).
 * dataListId - id of the dataList
 * echoFlag = if 1 - echo html
 * 
 * This is what datalist looks like:
 *  <datalist id="timeList">
            <option value="08:00:00"></option>
            <option value="08:30:00"></option>
            <option value="13:00:00"></option>
            <option value="14:00:00"></option>
            </datalist>
 */

function createTimeSelectionDataList ($start,$end,$increment,$dataListId, $echoFlag) {
    
    if ($start=='') $start='00:00AM';
    if ($end == '') $end='11:59PM';
    $start = strtotime($start); 
    $end = strtotime($end);
    
    /* increment is in  minutes */
    if ($increment == '') $increment=15;
    /* convert increment to seconds */
    $increment=$increment*60;
    
    //echo 'start: '.$start.' end: '.$end.' incre: '.$increment.'<br><br>';
    
    $htmlOut='<datalist id="'.$dataListId.'">';
    
    for ($i = $start; $i <= $end; $i += $increment) {
        //convert seconds in date to military time/date
        $dispDate=date('H:i:s', $i);
        //echo 'dispDate: '.$dispDate.' <br><br>';
        $htmlOut=$htmlOut.'<option value="'.$dispDate.'"></option>';
    }
    
    $htmlOut=$htmlOut.'</datalist>';
    if ($echoFlag==1) {
        echo $htmlOut;
    }
    return $htmlOut;
}

/* Function getFormValue - used when POST called to file data on form, gets data based on 
 * type, using standard filtering
 */
/* type=TXT, DATE, TIME, ID, IDARRAY, BLOB
 * Return: - value that can be validated and filed in database.

 */

function getFormValue($fieldName,$type) {
    $returnVal='';
    
    switch ($type) {
        case 'TXT':
            $returnVal = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_STRING);
            if ($returnVal == null) $returnVal='';
            break;
        case 'DATE':
            $returnVal = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_STRING);
            $returnVal=fixDateFormat($returnVal);
            break;
        case 'TIME':
            $returnVal = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_STRING);
            $returnVal=fixTimeFormat($returnVal);
            break;
        case 'ID':
            $returnVal = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_NUMBER_INT);
            if ($returnVal == null) $returnVal='';
            //if ($returnVal == -1) $returnVal='';
            break;
        case 'IDARRAY':
            $returnVal = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);
            break;
        case 'BLOB':
            $returnVal = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            break;
            
        default:
            $returnVal = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_SPECIAL_CHARS);
            if ($returnVal == null) $returnVal='';
            break;
        
    }

    return $returnVal;
}

/* check date format, and if in form MM/DD/YYYY - convert to YYYY-MM-DD for filing in db */
function fixDateFormat($inputDate) {
    
    $returnVal=$inputDate;
    //if date contains / - then convert to YYYY-MM-DD
    if (strpos($inputDate,'/')) {
        $dateArray=explode('/',$inputDate);
        $returnVal=$dateArray[2].'-'.$dateArray[0].'-'.$dateArray[1];
    }
    
    return $returnVal;
}

/* fixes time format - converts from display time - HH:MM:SS AM/PM to military time- to save in database */

function fixTimeFormat($inputTime) {
    $returnVal='';
    if ($inputTime !='') {
        //add seconds to time field.
        $timeArray=explode(' ',$inputTime);
        //add seconds.
        $timeArray[0]=$timeArray[0].':00';
        $inputTime=$timeArray[0].' '.$timeArray[1];
        //convert to HH:MM:SS
        $returnVal=date('G:i:s',strtotime($inputTime)); 
        
    }
    return $returnVal;
}


function createSelectHTML(&$dataArray,$codeIndex,$value1Index,$value2Index,$setValue,$delim) {
    $len=count($dataArray);
    $selectedFound=0;
    
    if (is_array($dataArray)) {
        for ($i=0; $i<$len; ++$i) {
            $selected="";
            if ($dataArray[$i][$codeIndex]==$setValue) {
                $selected=" selected";
                $selectedFound=1;
            }
            //echo 'row i: '.$i.' codeIndex: '.$codeIndex.' codeIndexValue: '.$dataArray[$i][$codeIndex];
            $tempHtml='<Option Value='.$dataArray[$i][$codeIndex].$selected.'>'.$dataArray[$i][$value1Index];
            if ($value2Index !="") {
                $tempHtml=$tempHtml.$delim.$dataArray[$i][$value2Index];
            }
            echo $tempHtml.'</option>';
        }
    }
    
    if ($setValue==0 || $selectedFound==0) {
        $tempHtml="<Option Value='' selected></option>";
        echo $tempHtml;
    }
    /*  insert blank row as not selected */
    else {
        $tempHtml="<Option Value=''></option>";
        echo $tempHtml;
    }
}


function createCheckBoxListHTML(&$dataArray,$nameField,$codeIndex, $value1Index,$value2Index,&$selectedArray,$delim,$id) {
    
    $len=count($dataArray);
    $newId='';
    for ($i=0; $i<$len; ++$i) {
        $checked="";
        $codeValue=$dataArray[$i][$codeIndex];
        if (isset($selectedArray[$codeValue])) {
            $checked=" checked ";
        }
        if ($id!= '') {
            $newId='id = "'.$id.$codeValue.'"';
        }
            
        $tempHtml='<input type="checkbox" name='.$nameField.' '.$checked.' Value='.$codeValue.' '.$newId.' />'.$dataArray[$i][$value1Index];
        if ($value2Index!="") {
            $tempHtml=$tempHtml.$delim.$dataArray[$i][$value2Index];
        }
        echo $tempHtml.'<br/>';
    }
}

/* createRadioButonHTML is used to create radio button list from hardcoded values, like Yes/No.
 * 
 * Example:
 * <input type="radio" name="deliverablebox" class="radioBtn" value="1">Yes<br>
   <input type="radio" name="deliverablebox" class="radioBtn" value="0" >No
 * to call - for each row to be created - 
 * dataArray[radioRowNumber][0]=value
 * dataArray[radioRowNumber][1]=label
 * to initialize dataArray for above example:
 * 
 * $dataArray = array (array(1,"Yes"),
 *                     array(0,"No")
 *                     );
 * 
 * The php call would be:
 * createRadioButtonHTML($dataArray,'deliverablebox',$deliverablebox,'radioBtn');
 * 
*/


function createRadioButtonHTML(&$dataArray,$nameField,$selectedValue,$class,$inLine) {   
    
    $len=count($dataArray);
    
    $tempClass='';
    if ($class != '') {
        $tempClass=' class="'.$class.'" ';
    }
    if ($inLine == 1) {
        $br='&nbsp;&nbsp;';
    }
    else {
        $br='<br>';
    }
    
    for ($i=0; $i<$len; ++$i) {
        $selected='';
        if ($dataArray[$i][0]== $selectedValue) {
            $selected='checked';
        }
        $tempHTML='<input type="radio" name="'.$nameField.'" '.$tempClass.' '.$selected.' value='.$dataArray[$i][0].'>'.$dataArray[$i][1].$br;
        
        //output html
        echo $tempHTML;
    }
}


//takes arrays are in the format array[rowNum]['idfield']=idValue.
//  and returns returnArray[idValue]=idValue.
//This returnArray is used to set checkbox list fields on input form.
    
function reorderArrayIndexByCode(&$inputArray) {
    
    //for each row in input array
    $len=count($inputArray);
    //initialize just in case there is no data.
    $returnArray['init']='init';
    
    // make sure at least 1 row is set in inputArray.
    if (isset($inputArray[0])) {
        //for each row, get id and put in index
        for ($i=0; $i<$len; ++$i) {
            foreach ($inputArray[$i] As $index => $value) {
                $returnArray[$value]=$value;
            }
        }
    }
    return $returnArray;   
}
