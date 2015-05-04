#include <curl/curl.h>
#include <iostream>
#include <string>
#include <cstring>
#include <time.h>

using namespace std;

extern CURL *curl;

void read_and_send_parameter_list(const string& filename);

int main(int argv, char* argc[])
{
   // argc[1] = url of web server e.g. http://localhost/fazialog
   // argc[2] = parameter list file e.g. parlist.txt
   if(argv<3) return 1;
   clock_t cStartClock;
   
   /* initialise CURL stuff */
   curl_global_init(CURL_GLOBAL_ALL);
   curl = curl_easy_init();
   
   /* what URL that receives this POST */
   curl_easy_setopt(curl, CURLOPT_URL, argc[1]);

   cStartClock = clock();
   read_and_send_parameter_list(argc[2]);
   printf("1392 parameters stored in %4.2f seconds\n",(clock() - cStartClock) / (double)CLOCKS_PER_SEC);
   curl_easy_cleanup(curl);

   return 0;
}
