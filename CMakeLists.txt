cmake_minimum_required(VERSION 2.8)

project(URLencoder)

find_package(CURL REQUIRED)
if(CURL_FOUND)
   include_directories(${CURL_INCLUDE_DIRS})
endif()

add_library(POSTdata SHARED post_data.cxx)

add_executable(send_data send_data.cxx)
target_link_libraries(send_data POSTdata ${CURL_LIBRARIES})
