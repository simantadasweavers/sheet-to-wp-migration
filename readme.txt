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
DUE WORK => 23 OCT

1. LIST ALL CRONS AND DELETE OPTIONS 

** PROBLEMS => 
SELECT COLUMN INDIVISUALLY FOR SPECIFIC ROLE , means 
-> assuming my self first column for POST TITLE
-> assuming second column for POST CONTENT
-> third column for POST CATEGORY
note: we can't change the ordering by ourselves.

CRON JOB OVERRIDE BY POST TYPES. CONFLICT!

** We should need to maintain the order => 
POST TITLE, CONTENT, CATEGORY, TAGS, POSTID
=================================================================================
DUE WORK => 24 OCT 

NEED TO FIX => 
1. post category and tags get not linked with post and custm post types. 
2. settings  page default category and tags selection for post type. 
3. CRON JOB OVERRIDE BY POST TYPES. CONFLICT!
4. migration work for default post_type = post but not for custom post types. 

DONE => 
1. post category and tags get not linked with post and custm post types. 
2. settings  page default category and tags selection for post type. 

================================================================================

TEST CASES ==> 
1. POST ADD , CATEGORY, TAGS
2. POST UPDATE , WITHOUT CATEGORY AND TAGS
3. POST UPDATE WITH CATEGORY AND TAGS
4. POST UPDATE WITHOUT TAGS AND CATEGORY
5. DIFFERENT POST TYPES SAME CHECK. 

DONE ALL. 

PROBLEMS STILL => 
1. ONLY CAN SET FOR ONE POST TYPE AT A TIME. 
2. IF CRON JOB SET THEN, BEFORE CREATING A NEW ONE IT REQUIRS PREVIOUS ONE DELETION.
[REQUIRED PLUGIN: CRON JOBS ARIEL: https://wordpress.org/plugins/leira-cron-jobs/#can%20i%20manage%20wordpress%20schedules%3F]
========================================================================================

