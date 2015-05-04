<?php
   $par = $_POST['par'];
   //$number_of_posts = count($par);
   //print "PHP: Received $number_of_posts POSTs...\n";

   // Set default timezone
   date_default_timezone_set('UTC');
 
   try {
      /**************************************
      * Create databases and                *
      * open connections                    *
      **************************************/
 
      //$time_start = microtime(true);
      
      // Create (connect to) SQLite database in file
      $file_db = new PDO('sqlite:fazia.db');
      // Set errormode to exceptions
      $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
      $file_db->query("PRAGMA synchronous = OFF");
      $file_db->query("PRAGMA journal_mode = MEMORY");
  
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
       
      $stmt = $file_db->prepare($insert);
      foreach($par[0] as $key => $value){
         $arr_values[$key]='';
         $stmt->bindParam(":$key", $arr_values[$key]);
      }
      unset($key,$value);
 
      $time = date('Y-m-d H:i:s');
      $stmt->bindParam(':time', $time);
  
      $file_db->beginTransaction();
      
      foreach($par as $id => $item){
         
         foreach($item as $key => $value){
       
            $arr_values[$key]=$value;
         
         }
         unset($key,$value);
    
         $stmt->execute();
         
      }
      unset($item,$id);
  
      $file_db->commit();
      
      /**************************************
      * Close db connections                *
      **************************************/
 
      // Close file db connection
      $file_db = null;
      
      //$time_end = microtime(true);
      //$execution_time = ($time_end - $time_start);
      //$posts_per_second = ($number_of_posts/$execution_time);
      //print "Execution Time: $execution_time seconds ($posts_per_second posts per second)\n";   
   }
   catch(PDOException $e) {
      // Print PDOException message
      echo $e->getMessage();
   }
?>
