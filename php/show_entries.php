
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
  
  
    // Select all data from db messages table 
    $result = $file_db->query('SELECT * FROM SCdetectors');
 
    //now output the data to a simple html table...
    print "<table border=1>";
    print "<tr><td>Time</td><td>Detector</td><td>parameter</td><td>BLK</td><td>QRT</td><td>TEL</td><td>DET</td><td>FEE</td><td>Module</td><td>Modname</td><td>VALUE</td><td>Units</td></tr>";
    foreach($result as $row)
    {
      print "<tr><td>".$row['time']."</td>";
      print "<td>".$row['detector_name']."</td>";
      print "<td>".$row['parameter']."</td>";
      print "<td>".$row['block']."</td>";
      print "<td>".$row['quartet']."</td>";
      print "<td>".$row['telescope']."</td>";
      print "<td>".$row['detector']."</td>";
      print "<td>".$row['frontEnd']."</td>";
      print "<td>".$row['module']."</td>";
      print "<td>".$row['module_name']."</td>";
      print "<td>".$row['value']."</td>";
      print "<td>".$row['units']."</td></tr>";
    }
    print "</table>";

 
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
