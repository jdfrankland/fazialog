<?php
   // add_entry.php
   // expects to receive a HTTP POST containing an array 'par':
   //     par[0][key1] = value1
   //     par[0][key2] = value2
   //       ...
   //     par[1][key1] = valueN
   //       ....
   // will insert all par[n] 'keys' into a row of the table SCdetectors

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
      
      // Create or connect to SQLite database in file
      $file_db = new PDO('sqlite:fazia.db');
      // Set errormode to exceptions
      $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
      // the following optimisations allow to gain a little more speed
      $file_db->query("PRAGMA synchronous = OFF");
      $file_db->query("PRAGMA journal_mode = MEMORY");
  
      // The names of the table columns do not have to be known in advance
      // We extract them from the first parameter in the POSTed list
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
       
      // the values for each parameter will be copied into the array $arr_values
      $stmt = $file_db->prepare($insert);
      foreach($par[0] as $key => $value){
         $arr_values[$key]='';
         $stmt->bindParam(":$key", $arr_values[$key]);
      }
      unset($key,$value);
 
      // this is the timestamp to be used for all parameters sent by POST
      $time = date('Y-m-d H:i:s');
      $stmt->bindParam(':time', $time);
  
      // perform all INSERTs in a single TRANSACTION (=> speed!)
      $file_db->beginTransaction();
      
      foreach($par as $id => $item){// loop over list of parameters         
         
         foreach($item as $key => $value){// loop over parameter infos  
       
            $arr_values[$key]=$value;
         
         }
         unset($key,$value);
    
         $stmt->execute();// prepare insertion into database
         
      }
      unset($item,$id);
  
      $file_db->commit();// execute insertion into database
      
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
