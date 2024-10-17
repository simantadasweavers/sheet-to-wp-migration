<?php
session_start();


add_action('wp_ajax_save_settings', 'save_settings');
function save_settings()
{
    date_default_timezone_set("Asia/Calcutta");

    global $wpdb;

    // Prepare data from the POST request
    $google_sheet_url = sanitize_text_field($_POST['sheet_url']);
    $account_type = sanitize_text_field($_POST['account_type']);
    $project_id = sanitize_text_field($_POST['project_id']);
    $private_key_id = $_POST['private_key_id'];
    $private_key = $_POST['private_key']; // Use wp_slash for multi-line text
    $client_email = sanitize_email($_POST['client_email']);
    $client_id = sanitize_text_field($_POST['client_id']);
    $auth_uri = esc_url_raw($_POST['auth_uri']);
    $token_uri = esc_url_raw($_POST['token_uri']);
    $auth_provider_x509_cert_url = esc_url_raw($_POST['auth_provider_x509_cert_url']);
    $client_x509_cert_url = esc_url_raw($_POST['client_x509_cert_url']);
    $universe_domain = sanitize_text_field($_POST['universe_domain']);

    // Prepare the data for insertion
    $data = array(
        'google_sheet_url' => $google_sheet_url,
        'account_type' => $account_type,
        'project_id' => $project_id,
        'private_key_id' => $private_key_id,
        'private_key' => $private_key,
        'client_email' => $client_email,
        'client_id' => $client_id,
        'auth_uri' => $auth_uri,
        'token_uri' => $token_uri,
        'auth_provider_x509_cert_url' => $auth_provider_x509_cert_url,
        'client_x509_cert_url' => $client_x509_cert_url,
        'universe_domain' => $universe_domain,
        'created_at' => date('d-m-Y H:i:s')
    );

    // Insert data into the database
    $table_name = $wpdb->prefix . 'sheet_to_wp_post';
    $result = $wpdb->insert($table_name, $data);

    // Check if the insertion was successful
    if ($result !== false) {
        wp_send_json_success('Data saved successfully.');
    } else {
        wp_send_json_error('Failed to save data.');
    }

    wp_die(); // Terminate and return a proper response
}


/************************************************/
add_action('wp_ajax_posts_migration', 'posts_migration');
// function posts_migration()
// {
//     global $wpdb;
//     $table = $wpdb->prefix . 'sheet_to_wp_post';
//     $query = $wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT 1");
//     $row = $wpdb->get_row($query);


//     if ($row) {
//         try {
//             require __DIR__ . '/vendor/autoload.php';

//             // Set Google Client using database values
//             $client = new \Google_Client();
//             $client->setApplicationName('My PHP App');
//             $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
//             $client->setAccessType('offline');

//             $client->setAuthConfig([
//                 "type" => $row->account_type,
//                 "project_id" => $row->project_id,
//                 "private_key_id" => $row->private_key_id,
//                 "private_key" => $row->private_key,
//                 "client_email" => $row->client_email,
//                 "client_id" => $row->client_id,
//                 "auth_uri" => $row->auth_uri,
//                 "token_uri" => $row->token_uri,
//                 "auth_provider_x509_cert_url" => $row->auth_provider_x509_cert_url,
//                 "client_x509_cert_url" => $row->client_x509_cert_url,
//                 "universe_domain" => $row->universe_domain
//             ]);

//             $sheets = new \Google_Service_Sheets($client);


//             // Step 1: Retrieve WordPress posts
//             $spreadsheetId = "$row->google_sheet_url";  // Replace with your Google Sheet ID
//             $range = 'A2';  // Start inserting data from this range

//             $args = array(
//                 'post_type' => 'post',
//                 'post_status' => 'publish',
//                 'posts_per_page' => -1,  // Fetch all posts
//             );
//             $posts = new WP_Query($args);

//             if ($posts->have_posts()) {
//                 $post_data = [];

//                 // Step 3: Loop through posts and prepare data for Google Sheets
//                 while ($posts->have_posts()) {
//                     $posts->the_post();
//                     $post_data[] = [
//                         get_the_title(),      // Post Title
//                         get_the_content(),    // Post Content
//                         get_the_date(),       // Post Date
//                         get_the_author(),     // Post Author
//                         get_permalink(),      // Post URL
//                     ];
//                 }

//                 // Reset post data
//                 wp_reset_postdata();

//                 // Step 4: Insert data into Google Sheets
//                 $body = new \Google_Service_Sheets_ValueRange([
//                     'values' => $post_data,  // Use the prepared post data
//                 ]);

//                 $params = ['valueInputOption' => 'RAW'];

//                 $result = $sheets->spreadsheets_values->append(
//                     $spreadsheetId,
//                     $range,
//                     $body,
//                     $params
//                 );

//                 echo "Posts have been successfully added to Google Sheet.";
//             } else {
//                 echo "No posts found.";
//             }

//             // step 2: these will fetch all posts from the sheet.
//             $data = [];
//             $currentRow = 2;

//             $spreadsheetId = "$row->google_sheet_url";
//             $range = 'A2:H';
//             $rows = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);
//             if (isset($rows['values'])) {
//                 foreach ($rows['values'] as $row) {

//                     if (empty($row[0])) {
//                         break;
//                     }

//                     $data[] = [
//                         'col-a' => $row[0],
//                         'col-b' => $row[1],
//                         'col-c' => $row[2],
//                         'col-d' => $row[3],
//                         'col-e' => $row[4],
//                         'col-f' => $row[5],
//                         'col-g' => $row[6],
//                         'col-h' => $row[7],
//                     ];

//                     // Update Google Sheet column I with current timestamp
//                     $updateRange = 'I' . $currentRow;
//                     $updateBody = new \Google_Service_Sheets_ValueRange([
//                         'range' => $updateRange,
//                         'majorDimension' => 'ROWS',
//                         'values' => [['values' => date('c')]],
//                     ]);
//                     $sheets->spreadsheets_values->update(
//                         $spreadsheetId,
//                         $updateRange,
//                         $updateBody,
//                         ['valueInputOption' => 'USER_ENTERED']
//                     );
//                     $currentRow++;
//                 }
//             }

//             // print_r($data);

//             foreach ($data as $data) {

//                 $parts = explode(",", $data['col-c']);
//                 $arr = array();
//                 foreach ($parts as $cat) {
//                     if (category_exists($cat)) {
//                         array_push($arr, get_cat_ID($cat));
//                     } else {
//                         $category = wp_insert_term(
//                             $cat,  // The category name
//                             'category',      // Taxonomy: 'category' for WordPress categories
//                         );

//                         // Check for errors and get the category ID
//                         if (is_wp_error($category)) {
//                         } else {
//                             $catid = $category['term_id'];
//                             array_push($arr, $catid);
//                         }
//                     }
//                 }

//                 if ($arr == NULL) {
//                     $new_post = array(
//                         'post_title' => $data['col-a'],
//                         'post_content' => $data['col-b'],
//                         'post_status' => 'publish',
//                     );

//                     echo $post_id = wp_insert_post($new_post);
//                     echo "  ";
//                 } else {
//                     $new_post = array(
//                         'post_title' => $data['col-a'],
//                         'post_content' => $data['col-b'],
//                         'post_status' => 'publish',
//                         'post_category' => $arr,  // Category ID(s) go here
//                     );

//                     echo $post_id = wp_insert_post($new_post);
//                     echo "  ";
//                 }
//             }

//         } catch (Exception $e) {
//             echo $e->getMessage();
//         }
//     }

//     wp_die();
// }


function posts_migration()
{
    global $wpdb;
    $table = $wpdb->prefix . 'sheet_to_wp_post';
    $query = $wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT 1");
    $row = $wpdb->get_row($query);

    if ($row) {
        try {
            require __DIR__ . '/vendor/autoload.php';

            // Set Google Client using database values
            $client = new \Google_Client();
            $client->setApplicationName('My PHP App');
            $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
            $client->setAccessType('offline');

            $client->setAuthConfig([
                "type" => $row->account_type,
                "project_id" => $row->project_id,
                "private_key_id" => $row->private_key_id,
                "private_key" => $row->private_key,
                "client_email" => $row->client_email,
                "client_id" => $row->client_id,
                "auth_uri" => $row->auth_uri,
                "token_uri" => $row->token_uri,
                "auth_provider_x509_cert_url" => $row->auth_provider_x509_cert_url,
                "client_x509_cert_url" => $row->client_x509_cert_url,
                "universe_domain" => $row->universe_domain
            ]);

            $sheets = new \Google_Service_Sheets($client);

            // Fetch all posts from the sheet
            $data = [];
            $currentRow = 2;

            $spreadsheetId = "$row->google_sheet_url";
            $range = 'A2:H';
            $rows = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);
            if (isset($rows['values'])) {
                foreach ($rows['values'] as $row) {

                    // if (empty($row[0])) {
                    //     break;
                    // }

                    $data[] = [
                        'col-a' => $row[0],
                        'col-b' => $row[1],
                        'col-c' => $row[2],
                        'col-d' => $row[3],
                        'col-e' => $row[4],
                        'col-f' => $row[5],
                        'col-g' => $row[6],
                        'col-h' => $row[7],
                    ];

                    $currentRow++;
                }
            }

            // // Process each row and insert/update posts
            $currentRow = 2; // Reset currentRow counter for sheet update

            foreach ($data as $data) {

                // if post title present -> col-a 
                if (!empty($data['col-a'])) {

                    // // if post tags not present
                    // if($data['col-d']){
                    //     $parts = explode(",", $data['col-d']);
                    //     $arr = array();
                    //     foreach ($parts as $tag) {
                    //         if (tag_exists($tag)) {
                    //             $tag = get_term_by('name', $tag, 'post_tag');
                    //             if ($tag) {
                    //                 $tag_id = $tag->term_id;
                    //             }
                    //             array_push($arr, $tag_id);
                    //         } else {
                    //             $tag = wp_insert_term(
                    //                 $tag,  // The tag name
                    //                 'post_tag'  // Taxonomy: 'post_tag' for WordPress tags
                    //             );

                    //             // Check for errors and get the tag ID
                    //             if (!is_wp_error($tag)) {
                    //                 $tag_id = $tag['term_id'];
                    //                 array_push($arr, $tag_id);
                    //             }
                    //         }
                    //     }

                    //    try{
                    //     $post_array = array(
                    //         'ID' => $data['col-f'],
                    //         'tags_input' => $arr,
                    //     );
                    //     wp_update_post($post_array);

                    //     echo "tags update done";
                    // }catch(Exception $e){
                    //     echo $e->getMessage();
                    //    }
                    // }else{
                    //     continue;
                    // }

                    // col-f -> post_id 
                    if ($data['col-f']) {
                        
                        $parts = explode(",", $data['col-c']);
                        $arr = array();
                        foreach ($parts as $cat) {
                            if (category_exists($cat)) {
                                array_push($arr, get_cat_ID($cat));
                            } else {
                                $category = wp_insert_term(
                                    $cat,  // The category name
                                    'category'      // Taxonomy: 'category' for WordPress categories
                                );

                                // Check for errors and get the category ID
                                if (!is_wp_error($category)) {
                                    $catid = $category['term_id'];
                                    array_push($arr, $catid);
                                }
                            }
                        }

                        $parts = explode(",", $data['col-d']);
                        $tags_arr = array();
                        foreach ($parts as $tag) {
                            if (tag_exists($tag)) {
                                $tag = get_term_by('name', $tag, 'post_tag');
                                if ($tag) {
                                    $tag_id = $tag->term_id;
                                }
                                array_push($tags_arr, $tag_id);
                            } else {
                                $tag = wp_insert_term(
                                    $tag,  // The tag name
                                    'post_tag'  // Taxonomy: 'post_tag' for WordPress tags
                                );

                                // Check for errors and get the tag ID
                                if (!is_wp_error($tag)) {
                                    $tag_id = $tag['term_id'];
                                    array_push($tags_arr, $tag_id);
                                }
                            }
                        }

                        // updaing post fields by post id
                        $post_array = array(
                            'ID' => $data['col-f'],
                            'post_title' => $data['col-a'],
                            'post_content' => $data['col-b'],
                            'post_category' => $arr,
                            'tags_input' => $tags_arr,
                        );
                        wp_update_post($post_array);

                        echo "update done";



                    } else {
                        /** IF POST ID NOT FOUND, THEN CREATE INSERT POSTID INTO SHEET AND CREATE NEW POST. **/

                        // managing categories
                        $parts = explode(",", $data['col-c']);
                        $arr = array();
                        foreach ($parts as $cat) {
                            if (category_exists($cat)) {
                                array_push($arr, get_cat_ID($cat));
                            } else {
                                $category = wp_insert_term(
                                    $cat,  // The category name
                                    'category'      // Taxonomy: 'category' for WordPress categories
                                );

                                // Check for errors and get the category ID
                                if (!is_wp_error($category)) {
                                    $catid = $category['term_id'];
                                    array_push($arr, $catid);
                                }
                            }
                        }
                        if ($arr == NULL) {
                            $new_post = array(
                                'post_title' => $data['col-a'],
                                'post_content' => $data['col-b'],
                                'post_status' => 'publish',
                            );

                            $post_id = wp_insert_post($new_post);
                        } else {
                            $new_post = array(
                                'post_title' => $data['col-a'],
                                'post_content' => $data['col-b'],
                                'post_status' => 'publish',
                                'post_category' => $arr,  // Category ID(s)
                            );

                            $post_id = wp_insert_post($new_post);
                        }

                        // managing tags for the post
                        $parts = explode(",", $data['col-d']);
                        $arr = array();
                        foreach ($parts as $tag) {
                            if (tag_exists($tag)) {
                                $tag = get_term_by('name', $tag, 'post_tag');
                                if ($tag) {
                                    $tag_id = $tag->term_id;
                                }
                                array_push($arr, $tag_id);
                            } else {
                                $tag = wp_insert_term(
                                    $tag,  // The tag name
                                    'post_tag'  // Taxonomy: 'post_tag' for WordPress tags
                                );

                                // Check for errors and get the tag ID
                                if (!is_wp_error($tag)) {
                                    $tag_id = $tag['term_id'];
                                    array_push($arr, $tag_id);
                                }
                            }
                        }

                        if ($arr != NULL) {
                            $post_array = array(
                                'ID' => $post_id,
                                'tags_input' => $arr,  // 'tags_input' is for tags, not 'post_category'
                            );
                            wp_update_post($post_array);
                        }

                        try {
                            // Update Google Sheet column F with the post ID only if it is empty
                            $updateRange = 'F' . $currentRow;
                            $updateBody = new \Google_Service_Sheets_ValueRange([
                                'range' => $updateRange,
                                'majorDimension' => 'ROWS',
                                'values' => [[$post_id]], // This is where you insert the post ID
                            ]);

                            // Update the sheet only if col-f (post ID) is empty
                            $sheets->spreadsheets_values->update(
                                $spreadsheetId,
                                $updateRange,
                                $updateBody,
                                ['valueInputOption' => 'USER_ENTERED']
                            );

                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }


                        // $currentRow++;

                    }



                } else {
                    $post_id = 0;

                    try {
                        // Update Google Sheet column F with the post ID only if it is empty
                        $updateRange = 'F' . $currentRow;
                        $updateBody = new \Google_Service_Sheets_ValueRange([
                            'range' => $updateRange,
                            'majorDimension' => 'ROWS',
                            'values' => [[$post_id]], // This is where you insert the post ID
                        ]);

                        // Update the sheet only if col-f (post ID) is empty
                        $sheets->spreadsheets_values->update(
                            $spreadsheetId,
                            $updateRange,
                            $updateBody,
                            ['valueInputOption' => 'USER_ENTERED']
                        );

                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }

                    // $currentRow++;
                }
                $currentRow++;

            }


            echo "DONE";

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    wp_die();
}


include('trash-functions.php');

?>