
<?php
 
  try {
    /**************************************
    * Create databases and                *
    * open connections                    *
    **************************************/
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:/home/john/software/sources/FAZIALOG/wordpress/fazia.db');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
  
    /**************************************
    * Create tables                       *
    **************************************/
 
    // Create table messages
    $file_db->exec("DROP TABLE IF EXISTS SCdetectors");
    $file_db->exec("CREATE TABLE IF NOT EXISTS SCdetectors (
                    id INTEGER PRIMARY KEY, 
                    block INTEGER, 
                    quartet INTEGER, 
                    telescope INTEGER, 
                    detector TEXT, 
                    frontEnd INTEGER, 
                    module TEXT, 
                    parameter TEXT, 
                    alias TEXT, 
                    channel TEXT, 
                    value TEXT, 
                    units TEXT, 
                    time TEXT)");
    $file_db->exec("DROP TABLE IF EXISTS SCelectronics");
    $file_db->exec("CREATE TABLE IF NOT EXISTS SCelectronics (
                    id INTEGER PRIMARY KEY, 
                    block INTEGER, 
                    module TEXT, 
                    card TEXT, 
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
