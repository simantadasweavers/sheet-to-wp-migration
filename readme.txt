*** change logs : 
change google_sheet_url to sheet_id in production apps. 
set parent category , child category for posts


                    /** 
                     * ARRAY MAP
                     * col-a -> POST TITLE
                     * col-b -> POST CONTENT
                     * col-c -> POST CATEGORY
                     * col-d -> POST TAGS
                     * col-e -> POST ID
                     */

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

DUE WORK ==> 21 oct 2024.

solved => tags of post during migration 

unsolved => thumbnail image, 

WORKFLOW => 
1. when migration start, it will insert all the posts into wordpress posts, means overlapping them. 
2. when no post id in the sheet filled, it will filled them first time. 

OK => 
1. when no post id on sheet, fresh migration - ok!
2. every time migration runs works well. 
3. if postid found then migration updation works well including post title, content, taxonomy(category), tags. 

PROBLEMS/RESTRICTION => 
1. if one post id missing that time updation failed, means it create a new post into db. 

IMPROVEMENT => 
1. NO DUPLICATE POST 
2. SET CRON TIME BY SELECT BOX - 2 , 4 , 6, 8, 10, 20, 30
3. SELECT POST TYPE -> custom post type, default -> post, page
4. LIST ALL CRONS AND DELETE OPTIONS 

===================================================================================
DUE WORK => 22 oct 2024 

1. SET CRON TIME BY SELECT BOX - 2 , 4 , 6, 8, 10, 20, 30 = done
2. SELECT POST TYPE -> custom post type, default -> post, page
====================================================================================
DUE WORK => 

1. LIST ALL CRONS AND DELETE OPTIONS 

** PROBLEMS => 
SELECT COLUMN INDIVISUALLY FOR SPECIFIC ROLE , means 
-> assuming my self first column for POST TITLE
-> assuming second column for POST CONTENT
-> third column for POST CATEGORY
note: we can't change the ordering by ourselves. 

** We should need to maintain the order => 
POST TITLE, CONTENT, CATEGORY, TAGS, POSTID
