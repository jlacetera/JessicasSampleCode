JessicasSampleCode
==================

This is sample code from an application I am currently working on to manage projects, meeting minutes, and action items/deliverables.

It is standard WAMP - apache/MySQL/PHP running with XAMPP.

Although I have many years of experience in software development, this is my first project using these web-based tools.
Screenshots of the forms are in ScreenShotsPDF.pdf - I cropped off corporate logo/header information.

addNewProject.php
addNewMeeting.php
addNewActionItem.php

These are the first 3 forms that I developed.  I had some help with front-end design so some of the naming conventions is not consistent throughout - which should be fixed.  The forms do all of the processing in PHP - form initializing and filing with POST.
Validation is done with javascript in validateForm.js.
Looking back on this - validation could have been done more efficiently using jQuery.  But for now I haven't had time to go back and change this, and it works.

jQueryUI is used for date/time fields for cross-browser support.

ViewActionDatabase.php
ViewProjectDatabase.php
ViewMeetingDatabase.php

These forms contain a table with all rows in the table.  Rows can be selected to edit by double clicking or by the 'Edit' button.  New rows can be added, and basic reports run on the data.  
The table can be sorted and filtered.


updateSalesTasks.php
This form is part of a sales tracking functionality that I am working on.  The table rows are displayed on the top of the form, and they are selected, edited, and deleted from within the form.
Almost all of the processing is done in javascript/jQuery using ajax calls to retrieve and file the data.  
Looking back at the addNew*.php forms - I think they should have been done more like this form, with most of the processing 
done in javascript.


dbInterfaceLibrary.php - this contains all functions that are used to retrieve/save data to the database.

htmlRenderingLibrary.php - this contains functions that are used to build html elements on the forms.

reportFunctions.php - this contains functions used by reports.


Most javascript is in customScripts.js.  Javascript that is specific to each form is in that form's .php file.  Maybe I should have created a .js file for each .php?  On my list of things to do when I have more time.


MeetingMinutesSampleReport.pdf - sample of the report output.  There are 3 basic reports that are created by PrintTableData.php 
and displayed thru showReport.php.

The database tables are created with a php script, which is not included in this sample code.

Please feel free to make any comments/suggestion on my code.
I am working pretty independently because this is a small company, and the software engineers here work mostly on embedded 
systems, and don't have experience with this kind of programming.  A code review would be appreciated.

