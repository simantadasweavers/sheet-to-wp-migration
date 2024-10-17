*** change logs : 
change google_sheet_url to sheet_id in production apps. 
set parent category , child category for posts

====================================================================================

DUE WORK ==> 17 oct 2024.
managing tags, thumbnail image, 

WORKFLOW => 
1. when migration start, it will insert all the posts into wordpress posts, means overlapping them. 
2. when no post id in the sheet filled, it will filled them first time. 

OK => 
1. when no post id on sheet, fresh migration - ok!
2. every time migration runs works well. 

PROBLEMS => 
1. if one post id missing that time updation failed, means it create a new post into db. 


===================================================================================
