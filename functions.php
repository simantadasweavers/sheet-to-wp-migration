<?php
session_start();

include('trash-functions.php');
include('posts-cron-functions.php');


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
// Add custom cron schedule interval
function addCronIntervals($schedules)
{
    $schedules['sixty_seconds'] = array(
        'interval' => 70,
        'display' => __('Every 70 Seconds'),
    );
    return $schedules;
}
add_filter('cron_schedules', 'addCronIntervals');

// Handle AJAX request for migration
add_action('wp_ajax_posts_migration', 'handle_posts_migration');

function handle_posts_migration()
{
    if (isset($_POST['schedule_migration'])) {
        // Check if the cron job is already scheduled
        if (!wp_next_scheduled('custom_posts_migration')) {
            wp_schedule_event(time(), 'sixty_seconds', 'custom_posts_migration'); // Correct action name
            wp_send_json_success("Cron job scheduled.");
        } else {
            wp_send_json_error("Cron job already scheduled.");
        }
    } else {
        wp_send_json_error("Migration cron job not scheduled.");
    }

    wp_die(); // Properly end AJAX request
}

// Hook for custom posts migration
add_action('custom_posts_migration', 'posts_migration');

function posts_migration()
{

    global $wpdb;
    $table = $wpdb->prefix . 'sheet_to_wp_post';

    // Execute the query directly without prepare
    $query = "SELECT * FROM $table ORDER BY id DESC LIMIT 1";
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

                    // Ensure the array key exists before accessing it
                    $data[] = [
                        'col-a' => isset($row[0]) ? $row[0] : '',
                        'col-b' => isset($row[1]) ? $row[1] : '',
                        'col-c' => isset($row[2]) ? $row[2] : '',
                        'col-d' => isset($row[3]) ? $row[3] : '',
                        'col-e' => isset($row[4]) ? $row[4] : '',
                        'col-f' => isset($row[5]) ? $row[5] : '',
                        'col-g' => isset($row[6]) ? $row[6] : '',
                        'col-h' => isset($row[7]) ? $row[7] : '',
                    ];


                    $currentRow++;
                }
            }

            // // Process each row and insert/update posts
            $currentRow = 2; // Reset currentRow counter for sheet update

            foreach ($data as $data) {

                // if post title present -> col-a 
                if (!empty($data['col-a'])) {

                    // col-f -> post_id 
                    if ($data['col-f']) {

                        $parts = explode(",", $data['col-c']);
                        $arr = array();
                        foreach ($parts as $cat) {
                            if (term_exists($cat, 'category')) {
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
                            if (term_exists($tag, 'post_tag')) {
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

                        try {
                            // updaing post fields by post id
                            $post_array = array(
                                'ID' => $data['col-f'],
                                'post_title' => $data['col-a'],
                                'post_content' => $data['col-b'],
                                'post_category' => $arr,
                                'tags_input' => $tags_arr,
                            );
                            wp_update_post($post_array);

                            echo "UPDATE MIGRATION DONE";
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }

                    } else {
                        /** IF POST ID NOT FOUND, THEN CREATE INSERT POSTID INTO SHEET AND CREATE NEW POST. **/

                        // managing categories
                        $parts = explode(",", $data['col-c']);
                        $arr = array();
                        foreach ($parts as $cat) {
                            if (term_exists($cat, 'category')) {
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


                        try {
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
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }

                        // managing tags for the post
                        $parts = explode(",", $data['col-d']);
                        $arr = array();
                        foreach ($parts as $tag) {
                            if (term_exists($tag, 'post_tag')) {
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
                }
                $currentRow++;

            }
            echo "NEW POST CREATION MIGRATION DONE";

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    wp_die();
}

?>