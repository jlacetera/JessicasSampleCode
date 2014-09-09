<?php

/* 
 * Functions in this file are used for printing report data in web page.
 * Class styles are defined in ReportStyle.css.
 */


function returnValueForId($rowId,$tableArray,$fieldName,$fieldName1,$delim) {
    $returnVal='';
    if (isset($rowId)) {
        if (!(isset($fieldName))) {
            $fieldName='value';
        }
        if (isset($tableArray[$rowId][$fieldName])) {
            $returnVal=$tableArray[$rowId][$fieldName];
            if (isset($fieldName1)) {
                if (!(isset($delim))) {
                    $delim=' ';
                }
                if (isset($tableArray[$rowId][$fieldName1])) {
                    $returnVal=$returnVal.$delim.$tableArray[$rowId][$fieldName1];
                }
            }
        }
    }
    return $returnVal;
}
/* function to display report header */
function displayHeader($reportTitle) {
    $reportDate=date("l F d, Y");
    
    echo '<div class="reportHeader">';
    //output header - TITLE, report date/time.
    echo '<p class="reportTitle">'.$reportTitle.'</p>';
    echo '<p class="reportTitle"> Report Date: '.$reportDate.' </p>';
    echo '</div>';
    
}

/* classes for types defined in css for div on page */

function outputData($label,$data,$type) {
    
    switch ($type) {
        case 'DATA':
            echo '<p><b>'.$label.':</b> '.$data.'</p>';
            break;
        case 'TITLE':
            echo '<br><h3>'.$label.': '.$data.'</h3>';
            break;
        //BLOB field must be displayed in textarea    
        case 'BLOB':
           if (trim($data) == '') {
                $data='No Entry';
                echo '<p><b>'.$label.':</b> '.$data.'</p><br>';
            }
            else {
                echo '<p><b>'.$label.'</b></p>';
                $output='<textarea class="editor">'.$data.'</textarea><br>';
                echo $output;
            }
           
            break;
        case 'STARTDIV':
            if (isset($label)) {
                echo '<div class='.$label.'>';
            }
            else {
                echo '<div>';
            }
            break;
        case 'ENDDIV':
            echo '</div>';
            break;
    }
}

/* input array is dataArray[rowNumber][columnName]=columnvalue.
 * return dataArray[rowId][columnName]=columnValue;
 */
function reorderArrayById($inputArray) {
    $returnArray='';
    
    foreach ($inputArray As $index => $value) {
        if (isset($inputArray[$index]['id'])) {
            $rowId=$inputArray[$index]['id'];
            foreach ($inputArray[$index] As $col => $val) {
                $returnArray[$rowId][$col]=$val;
            }
        }      
    }
    return $returnArray;
}
?>



