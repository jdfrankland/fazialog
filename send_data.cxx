#include <curl/curl.h>
#include <iostream>
#include <string>
#include <cstring>

using namespace std;

extern CURL *curl;

void read_and_send_parameter_list(const string& filename);

int main(int argv, char* argc[])
{
   // argc[1] = url of web server e.g. http://localhost/fazialog
   // argc[2] = parameter list file e.g. parlist.txt
   
   /* initialise CURL stuff */
   curl_global_init(CURL_GLOBAL_ALL);
   curl = curl_easy_init();
   
   /* what URL that receives this POST */
   curl_easy_setopt(curl, CURLOPT_URL, argc[1]);

   read_and_send_parameter_list(argc[2]);

   curl_easy_cleanup(curl);

   return 0;
}
