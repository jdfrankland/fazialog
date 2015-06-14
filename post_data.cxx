#include <curl/curl.h>
#include <string>
#include <iostream>
#include <fstream>
#include <map>
#include <list>

using namespace std;

CURL *curl = NULL;

void read_and_send_parameter_list(const string& filename)
{
   ifstream fin;
   fin.open(filename.c_str());

   string detname, modname, parname, detector, module, value, units, blk, qrt, tel, fee;
   int nlines = 0;

   char par[100];

   CURLcode res;

   struct curl_httppost *formpost = NULL;
   struct curl_httppost *lastptr = NULL;

   int id = 0;
   int pairs = 0;
   
   while (fin.good()) {

      fin >> detname >> modname >> parname >> blk >> qrt >> tel >> detector >> fee >> module >> units >> value;
// par[0][block]
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
      
      for (multimap<string, string>::const_iterator it = data.begin(); it != data.end(); ++it) {
         sprintf(par, "par[%d][%s]", id, (*it).first.c_str());
         curl_formadd(&formpost,
                      &lastptr,
                      CURLFORM_COPYNAME, par,
                      CURLFORM_COPYCONTENTS, (*it).second.c_str(),
                      CURLFORM_END);
         ++pairs;
      }

         curl_formadd(&formpost,
                      &lastptr,
                      CURLFORM_COPYNAME, "category",
                      CURLFORM_COPYCONTENTS, "detectors",
                      CURLFORM_END);
         ++pairs;
         curl_formadd(&formpost,
                      &lastptr,
                      CURLFORM_COPYNAME, "user",
                      CURLFORM_COPYCONTENTS, "slowcontrol",
                      CURLFORM_END);
         ++pairs;
			
      ++id;
      if(pairs>(1000-(data.size()+2))){
         // default PHP limit for POST: 1000 key-value pairs (difficult to change for apache module)
         // if we get near the limit (i.e. if we cannot send the next full set of key-value pairs
         // without reaching the limit), we send everything we got so far now
         //cout << "Sending " << pairs << " key-value pairs to server..." << endl;
         curl_easy_setopt(curl, CURLOPT_HTTPPOST, formpost);
         res = curl_easy_perform(curl);
         if (res != CURLE_OK)
            fprintf(stderr, "curl_easy_perform() failed: %s\n",curl_easy_strerror(res));
         curl_formfree(formpost);
         formpost = lastptr = NULL;
         pairs = 0;
         id = 0;
      }

      if (fin.good()) {
         nlines++;
      }
   }
   //cout << "read " << nlines << " lines" << endl;
   // send any remaining data to server
   if(pairs){
        // cout << "END: Sending " << pairs << " key-value pairs to server..." << endl;
      curl_easy_setopt(curl, CURLOPT_HTTPPOST, formpost);
      res = curl_easy_perform(curl);
      if (res != CURLE_OK)
         fprintf(stderr, "curl_easy_perform() failed: %s\n",
              curl_easy_strerror(res));
      curl_formfree(formpost);
   }
   fin.close();
}
