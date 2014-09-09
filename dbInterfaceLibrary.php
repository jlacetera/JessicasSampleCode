<?php


function openDBConnection() {
    
    $db_hostname="localhost";
    $db_username="root";
    $db_password="";
    $db_name="omnicon_dbtest7";
    
    $connection=new mysqli($db_hostname,$db_username,$db_password,$db_name);
    
    if ($connection->connect_error) {
        echo 'Error connecting to database: '.$connection->connect_error;
    }
    
    
    /* echo '*** return from selecting db: '.mysql_error().' <br>'; */
    /* need to check if okay to return pointer to class */
    return $connection;
    
}
function closeDBConnection(&$connection) {
     $connection->close();
    
}

/* Function getTableData - 
 * Selects data from database based on input parameters.
 * Replaces NULL with "" in resultsArray.
 * Parameters:
 * tableName - name of table in database
 * fieldArray - array of field names to select
 * whereClause - where clause for select statement
 * orderBy - order by statement for select statement.
 * 
 * Return:  returns associative array with tabledata - array[rowNumber][columnName]=data.
 */
function getTableData($tableName,&$fieldArray,$whereClause,$orderBy) {
    
    /* set up select statement to select the data and fields*/
    
    $len=count($fieldArray);
    $sql="SELECT ";
    $fieldList="";
    
    $len=count($fieldArray);    
    $cnt=0;
    $returnArray="";
    
    foreach ($fieldArray as $fieldName) {
        $cnt++;
        $fieldList=$fieldList.$fieldName;
        if ($cnt!=$len) $fieldList=$fieldList.",";
    }
    
    $sql=$sql.' '.$fieldList.' FROM '.$tableName;
    
    //echo 'whereClause: '.$whereClause.'<br><br>';
    if (count($whereClause)>0) {
        $sql=$sql.' '.$whereClause;
    }
    
    if (count($orderBy)>0) {
        $sql=$sql.' '.$orderBy;
    }
    
    /* execute sql query and read results into return array */
    //echo 'sql: '.$sql.'<br><br>';
    //
    //open connection
    $connection=openDBConnection();
    
    $result=$connection->query($sql);
    
    if (!$result) {
        echo 'Error with sql: '.$sql.' <br><br>';
    }
    
    $totalRows=$result->num_rows;
    
    //rows is number of rows returned from the sql select statement.

    for ($j=0; $j<$totalRows; $j++) {
        $result->data_seek($j);
        //save array
        $returnArray[$j]=$result->fetch_array(MYSQLI_ASSOC);
    }
    
    $result->close();
    
    //close connection
    closeDBConnection($connection);
    
    removeNULLSFromResultsArray($returnArray);
    
    return $returnArray;
    }
    
    
    
    /* Function returnSQLQuery - 
     * this function will execute the sqlStatement passed in and return the data returned from the
     * query in returnArray.  NULL fields will be replaced with '' for display/processing on web pages.
     */
    
function returnSQLQuery($sqlStatement) {
    
    $returnArray='';
    
    if ($sqlStatement!='') {
 
        //open connection
        $connection=openDBConnection();   
        $result=$connection->query($sqlStatement);
        if (!$result) {
            echo 'Error with sql: '.$sql.' <br><br>';
        }
        $totalRows=$result->num_rows;

        for ($j=0; $j<$totalRows; $j++) {
            //echo '*** fetching row:'.$j.' totalRows: '.$totalRows.'<br>';
        
            //$row=mysql_fetch_row($result);
            $result->data_seek($j);
            //save array
            $returnArray[$j]=$result->fetch_array(MYSQLI_ASSOC);
        }
      
        /** DEBUG
        for ($j=0; $j<$totalRows; $j++) {
            foreach ($returnArray[$j] as $column => $value) {
            echo 'row: '.$j.'   column: '.$column.' value: '.$value.'<br><br>';    
        } 
        **/    

        $result->close();
    
        //close connection
        closeDBConnection($connection);
        
        removeNULLSFromResultsArray($returnArray);
        
    }    
    return $returnArray;
}
    
/* this function takes results array in format [row][columnName]=value, and replaces all NULLs with '' for
 * display/use in php/html form.
 */

function removeNULLSFromResultsArray(&$resultsArray) {
    if (is_array($resultsArray)) {
        foreach ($resultsArray as $row => $val) {   
            foreach ($resultsArray[$row] as $column => $value) {
                if ($value == null) {
                    $resultsArray[$row][$column]="";
                }
            } 
        }    
    }
}

    
/* Function updateTableData - 
 * This function inserts or updates an existing row based on parameters passed in.
 * Returns rowId of updated or new row inserted into table.
 * Returns '' on error.
 * For inserting a new row, 'id' should not be in resultsArray.  Sending 'id' as blank on insert
 * can cause problems for some versions of mySQL.
 * 
 * Parameters:
 * tableName - name of database table.
 * resultsArray - assoc array with database field name=value.
 * rowId - set to rowId for update, or '' to insert new row.
 */
    

function updateTableData($tableName,&$resultsArray,$rowId) {
        
        //setup sql string
        // if id set - then we are executing an update
    if ($rowId!='') {
        $where=" WHERE id='".$rowId."'";
        $sqlFields="UPDATE ".$tableName.' SET ';
    }
        //else we are executing an insert command
    else {
        $sqlFields='INSERT INTO '.$tableName.' SET ';
        $where='';
    }
    
    $sqlValues='';
        
    $len=count($resultsArray);
    $cnt=0;
    //loop thru array and set fields/values
    foreach ($resultsArray as $column => $value) {
        //echo '$column: '.$column.' $value '.$value.'<br>';
        $cnt++;
        $last=',';
        if ($len==$cnt) $last='';
        if (($value == null) || ($value == "")) {
            $insertVal="NULL";
        }
        else {
            $insertVal="'".$value."'";
        }
        $sqlValues=$sqlValues.' '.$column.'='.$insertVal.$last;
    }
        
    $sql=$sqlFields.' '.$sqlValues.$where;
    
    //echo 'sql: '.$sql.' <br><br>';
        
    $connection=openDBConnection();
    
    $result=$connection->query($sql);
        
    //do some error checking
    //echo '$result from query: '.$result.'<br><br>';
    if (!$result) {
         //need to put in proper error handling here for all database errors.
        echo 'Error returned from inserting/updating new row: '.$connection->error;
    }
    
    //if $rowId is blank - then insert, return new rowId.
    if ($rowId=='') {
        $rowId=$connection->insert_id;
    }
    //echo '$updatedRowId: '.$rowId.'<br><br>';
        
    //disconnect from database
    closeDBConnection($connection);
        
    return $rowId;
}    
    

/* Function isertMultipleTableRows - 
   inserts multiple tables into database.  Used to insert multiple child table rows by calling
 * updateTableData.
 * 
   Parameters:
 * tableName - name of database table. 
 * resultsArray[numRows][fieldName]=value - array with multiple rows of data to insert.
 */
function insertMultipleTableRows($tableName,&$resultsArray) {
    
    $rowCount=0;
    foreach ($resultsArray As $tableData) {
        $rowId=updateTableData($tableName,$tableData,'');
        if (!$rowId) {
            echo 'Error inserting table data: '.$tableName.' rowCount: '.$rowCount.'<br><br>';
        }
        $rowCount++;
    }
    
}

/* deletes row from table based on where clase passed in */
function deleteTableRowData($tableName,$whereClause) {

    $sql='DELETE FROM '.$tableName;
 
    If (count($whereClause)>0) {
        $sql=$sql.' '.$whereClause;
    }
    
    /* connect and execute sql */
    $connection=openDBConnection();
    $result=$connection->query($sql);
        
    //do some error checking
    //echo '$result from query: '.$result.'<br><br>';
    if (!$result) {
            //need to put in proper error handling here for all database errors.
        echo 'Error returned from inserting/updating new row: '.$connection->error;
    }
    
    //disconnect from database
    closeDBConnection($connection);
    
 }

