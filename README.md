Example of C++ code using libcurl to POST 1392 FAZIA parameters & values to
an SQLite database through an apache webserver.

To build the C++:

    $ cmake
    $ make

Set up the webserver:
  
Make links to the files in php/ so that they are visible from your webserver e.g.
      
    http://localhost/add_entry.php

Run the example:

1. Enter the following in your browser URL bar:
   
    http://localhost/clear_table.php
   
  This will set up the database & table (the first time), or delete and remake the table (after that)
   
2. In source/build directory:
   
    $ ./send_data http://localhost/add_entry.php parlist.txt
   
3. Enter the following in your browser URL bar:
   
    http://localhost/show_entries.php
   
  This will display the contents of the table.
