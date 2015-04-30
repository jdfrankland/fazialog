
<?php
 
  // Set default timezone
  date_default_timezone_set('UTC');
 
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
    * Play with databases and tables      *
    **************************************/
 
    // Prepare INSERT statement to SQLite3 file db
    $insert = "INSERT INTO SCdetectors (block, quartet, telescope, detector, detector_name, frontEnd, module, module_name, parameter, value, units, time) 
                VALUES (:block, :quartet, :telescope, :detector, :detector_name, :frontEnd, :module, :module_name, :parameter, :value, :units, :time)";
    $stmt = $file_db->prepare($insert);
 
    // Bind parameters to POST variables
    $time = date('Y-m-d H:i:s');
    $stmt->bindParam(':block', $_POST["block"]);
    $stmt->bindParam(':quartet', $_POST["quartet"]);
    $stmt->bindParam(':telescope', $_POST["telescope"]);
    $stmt->bindParam(':detector', $_POST["detector"]);
    $stmt->bindParam(':detector_name', $_POST["detector_name"]);
    $stmt->bindParam(':frontEnd', $_POST["frontEnd"]);
    $stmt->bindParam(':module', $_POST["module"]);
    $stmt->bindParam(':module_name', $_POST["module_name"]);
    $stmt->bindParam(':parameter', $_POST["parameter"]);
    $stmt->bindParam(':value', $_POST["value"]);
    $stmt->bindParam(':units', $_POST["units"]);
    $stmt->bindParam(':time', $time);
 
    print "[".$time."]: Adding entry ".$_POST["parameter"]." to database";
    $stmt->execute();
  
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
