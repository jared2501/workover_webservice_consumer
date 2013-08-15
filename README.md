#Workover Webservice Consumer

This is the webservice consumer that should be located at workover.cs.auckland.ac.nz

It contains all the code to manage courses, questions, and students. It consume a webservices from the systems table in the database. The webservices have to implement the following five endpoints with URLS specified in the systems table:

* get_question
* get_create
* post_create
* get_edit
* post_edit