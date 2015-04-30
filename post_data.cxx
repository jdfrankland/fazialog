#include <curl/curl.h>
#include <string>
#include <iostream>
#include <fstream>
#include <map>

using namespace std;

CURL *curl = NULL;

void post_data(const multimap<string, string>& data)
{
   if (!curl) return;

   CURLcode res;

   struct curl_httppost *formpost = NULL;
   struct curl_httppost *lastptr = NULL;

   // Build contents of POST message with key=value pairs
   for (multimap<string, string>::const_iterator it = data.begin(); it != data.end(); ++it) {
      curl_formadd(&formpost,
                   &lastptr,
                   CURLFORM_COPYNAME, (*it).first.c_str(),
                   CURLFORM_COPYCONTENTS, (*it).second.c_str(),
                   CURLFORM_END);
   }

   if (curl) {
      curl_easy_setopt(curl, CURLOPT_HTTPPOST, formpost);
      /* Perform the request, res will get the return code */
      res = curl_easy_perform(curl);
      /* Check for errors */
      if (res != CURLE_OK)
         fprintf(stderr, "curl_easy_perform() failed: %s\n",
                 curl_easy_strerror(res));

      /* then cleanup the formpost chain */
      curl_formfree(formpost);
   }
}

void read_and_send_parameter_list(const string& filename)
{
   ifstream fin;
   fin.open(filename.c_str());

   string detname, modname, parname, detector, module, value, units, blk, qrt, tel, fee;
   int nlines = 0;
   while (fin.good()) {

      fin >> detname >> modname >> parname >> blk >> qrt >> tel >> detector >> fee >> module >> units >> value;

      multimap<string, string> data;
      data.insert(pair<string, string>("block", blk));
      data.insert(pair<string, string>("quartet", qrt));
      data.insert(pair<string, string>("telescope", tel));
      data.insert(pair<string, string>("detector", detector));
      data.insert(pair<string, string>("frontEnd", fee));
      data.insert(pair<string, string>("module", module));
      data.insert(pair<string, string>("detector_name", detname));
      data.insert(pair<string, string>("module_name", modname));
      data.insert(pair<string, string>("parameter", parname));
      data.insert(pair<string, string>("value", value));
      data.insert(pair<string, string>("units", units));
      post_data(data);

      if (fin.good()) {
         nlines++;
      }
   }
   cout << "read " << nlines << " lines" << endl;

   fin.close();
}
