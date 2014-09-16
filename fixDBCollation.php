
<?php

/* this function fixes all tables in database, and all varchar fields to have utf8 collation */

/* not having a lot of database administration experience, mysql as installed by xampp
does not default the database, table, or column collation to utf-8.  So this script was
created to fix collation.  Also, XAMPP install of php does not default to utf-8, so
php.ini needed to be updated as well.
*/


$db = new database();

$dbName='omnicon_dbtest4';
/* first change collation on database */
$dbQuery="ALTER DATABASE ".$dbName.' CHARACTER SET utf8 COLLATE utf8_unicode_ci';
$query=$db->query($dbQuery);

//select all tables to fix.
$query=$db->query("SHOW TABLES FROM ".$dbName);

//echo 'query from show tables: '.$query.'<br><br>';
$queryArray=$db->fetch_assoc($query);

$db->close_database();

foreach ($queryArray As $index => $value) {
    if (is_array($queryArray[$index])) {
        foreach ($queryArray[$index] As $ind1=>$val1) {
            echo 'ind1: '.$ind1.' val1: '.$val1.'<br><br>';
            //for each table - must select all columns and change collation for varchar and blob fields.
            $tableName=$dbName.'.'.$val1;
            
            //fix collation for table.
            $db = new database();
            $tableQuery="ALTER TABLE ".$tableName." CONVERT TO CHARACTER SET utf8";
            $tableQuery = $db->query($tableQuery);
        
            //get all columns from the table.
            $colQuery=$db->query('SHOW COLUMNS FROM '.$tableName);
            $colQueryArray=$db->fetch_assoc($colQuery);
            $db->close_database();
            
            /* return array of column names that need collation set */
            $colsToFix=checkColumnNames($colQueryArray);
            
            //for each column that is varchar - fix
            //ALTER TABLE <table_name> MODIFY <column_name> VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
           
            if (is_array($colsToFix)) {
                $db=new database();
                foreach ($colsToFix As $colName=>$colType) {
                    $colQuery='ALTER TABLE '.$tableName.' MODIFY '.$colName.' '.$colType.' CHARACTER SET utf8 COLLATE utf8_unicode_ci';
                    $queryRes = $db->query($colQuery);
                }
                $db->close_database();
            }
        }
    }
}


class database {
    public $connection;

    function __construct() {
        $this->connection = mysqli_connect("localhost","root","","omnicon_dbtest4");
    }
    
    public function close_database() { 
        return mysqli_close($this->connection); 
    }
    
    public function query($query) {
        $returnStatus = mysqli_query($this->connection ,$query);
        if (!$returnStatus) {
            echo 'query: '.$query.' *** ERROR: '.mysqli_connect_error().'<br><br>';
        }
        
        return $returnStatus;  
    }
    
    public function fetch_assoc($query) {
        $resultsArray = mysqli_fetch_all($query);
        return $resultsArray;  
    }
}


/* returns array of colunn names for table that collation needs to be fixed on */

function checkColumnNames($col) {
    $colsToFix='';
    if (is_array($col)) {
        foreach ($col As $row1=>$junk) {
            $type=$col[$row1][1];
            $name=$col[$row1][0];
            //if ((stristr($type,'varchar')!= FALSE) || (stristr($type,'blob') != FALSE)) {
            if (stristr($type,'varchar')!= FALSE) {    
                //echo 'type: '.$type.' name: '.$name.' need to be fixed.  <br><br>';
                $colsToFix[$name]=$type;
            }
        }
    }
    return $colsToFix;
}

?>

