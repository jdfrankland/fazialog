
<?php
 
  try {
    /**************************************
    * Create databases and                *
    * open connections                    *
    **************************************/
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:fazia.db');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
  
    /**************************************
    * Create tables                       *
    **************************************/
 
    // Create table messages
    $file_db->exec("DROP TABLE SCdetectors");
    $file_db->exec("CREATE TABLE IF NOT EXISTS SCdetectors (
                    id INTEGER PRIMARY KEY, 
                    block INTEGER, 
                    quartet INTEGER, 
                    telescope INTEGER, 
                    detector TEXT, 
                    detector_name TEXT, 
                    frontEnd INTEGER, 
                    module TEXT, 
                    module_name TEXT, 
                    parameter TEXT, 
                    value TEXT, 
                    units TEXT, 
                    time TEXT)");
 
  
    /**************************************
    * Close db connections                *
    **************************************/
 
    // Close file db connection
    $file_db = null;
  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
?>
