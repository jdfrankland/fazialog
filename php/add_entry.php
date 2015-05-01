
<?php
 //ini_set('memory_limit', '1024M');
 $par = $_POST['par'];
 $values = count($par);
 print "PHP: Received $values POSTs...\n";

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
    //$insert = "INSERT INTO SCdetectors (block, quartet, telescope, detector, detector_name, frontEnd, module, module_name, parameter, value, units, time) 
    //            VALUES (:block, :quartet, :telescope, :detector, :detector_name, :frontEnd, :module, :module_name, :parameter, :value, :units, :time)";
 $ins1 = "INSERT INTO SCdetectors (";
 $ins2 = "VALUES (";
 foreach($par[0] as $key => $value){
   $ins1 .= "$key, ";
   $ins2 .= ":$key, ";
 }
 unset($key,$value);
 $ins1 .= "time) ";
 $ins2 .= ":time)";
 $insert = $ins1.$ins2;
       
 
    // Bind parameters to POST variables
  $time = date('Y-m-d H:i:s');
  foreach($par as $id => $item){
    $stmt = $file_db->prepare($insert);
    foreach($item as $key => $value){
       $arr_values[$key]=$value;
       $stmt->bindParam(":$key", $arr_values[$key]);
    }
    unset($key,$value);
    $stmt->bindParam(':time', $time);
    $stmt->execute();
    //print "entered $id\n";
  }
  unset($item,$id);
  
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
