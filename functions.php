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
    $cron_time = sanitize_text_field($_POST['cron_time']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $post_category = sanitize_text_field($_POST['category']);
    $post_tag_name = sanitize_text_field($_POST['tag_name']);

    /**** checking google sheet connection ****/
    try {
        require __DIR__ . '/vendor/autoload.php';
        // Set Google Client using database values
        $client = new \Google_Client();
        $client->setApplicationName('My PHP App');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        $client->setAuthConfig([
            "type" => $account_type,
            "project_id" => $project_id,
            "private_key_id" => $private_key_id,
            "private_key" => $private_key,
            "client_email" => $client_email,
            "client_id" => $client_id,
            "auth_uri" => $auth_uri,
            "token_uri" => $token_uri,
            "auth_provider_x509_cert_url" => $auth_provider_x509_cert_url,
            "client_x509_cert_url" => $client_x509_cert_url,
            "universe_domain" => $universe_domain
        ]);


        $sheets = new \Google_Service_Sheets($client);

        // Fetch all posts from the sheet
        $data = [];
        $currentRow = 2;

        $spreadsheetId = $google_sheet_url;
        $range = 'A2:H';
        $rows = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);

    } catch (Exception $e) {
        wp_send_json_error();
        wp_die();
    }

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
        'cron_job_time' => $cron_time,
        'post_type' => $post_type,
        'post_category' => $post_category,
        'post_tag' => $post_tag_name,
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
    try {
        global $wpdb;
        $table = $wpdb->prefix . 'sheet_to_wp_post';

        // Execute the query directly without prepare
        $query = "SELECT * FROM $table ORDER BY id DESC LIMIT 1";
        $row = $wpdb->get_row($query);

        if ($wpdb->num_rows > 0) {
            if ($row->cron_job_time) {
                $time = ($row->cron_job_time) * 60;
            }
            $schedules['custom_cron_job_timing'] = array(
                'interval' => $time,
                'display' => __("Every $time Seconds"),
            );
            return $schedules;
        }
    } catch (Exception $e) {
        wp_send_json_error();
        wp_die();
    }

}
add_filter('cron_schedules', 'addCronIntervals');

// Handle AJAX request for migration
add_action('wp_ajax_posts_migration', 'handle_posts_migration');

function handle_posts_migration()
{
    try {
        global $wpdb;
        $table = $wpdb->prefix . 'sheet_to_wp_post';

        // Execute the query directly without prepare
        $query = "SELECT * FROM $table ORDER BY id DESC LIMIT 1";
        $row = $wpdb->get_row($query);
        if ($row) {
            if (!$row->cron_job_time) {
                wp_send_json_error();
                wp_die();
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    if (isset($_POST['schedule_migration'])) {
        // Check if the cron job is already scheduled
        if (!wp_next_scheduled('custom_posts_migration')) {
            wp_schedule_event(time(), 'custom_cron_job_timing', 'custom_posts_migration'); // Correct action name
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

    try {
        global $wpdb;
        $table = $wpdb->prefix . 'sheet_to_wp_post';

        // Execute the query directly without prepare
        $query = "SELECT * FROM $table ORDER BY id DESC LIMIT 1";
        $db_row = $wpdb->get_row($query);
        $post_type = $db_row->post_type;
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    if ($db_row) {
        try {

            require __DIR__ . '/vendor/autoload.php';

            // Set Google Client using database values
            $client = new \Google_Client();
            $client->setApplicationName('My PHP App');
            $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
            $client->setAccessType('offline');

            $client->setAuthConfig([
                "type" => $db_row->account_type,
                "project_id" => $db_row->project_id,
                "private_key_id" => $db_row->private_key_id,
                "private_key" => $db_row->private_key,
                "client_email" => $db_row->client_email,
                "client_id" => $db_row->client_id,
                "auth_uri" => $db_row->auth_uri,
                "token_uri" => $db_row->token_uri,
                "auth_provider_x509_cert_url" => $db_row->auth_provider_x509_cert_url,
                "client_x509_cert_url" => $db_row->client_x509_cert_url,
                "universe_domain" => $db_row->universe_domain
            ]);

            $sheets = new \Google_Service_Sheets($client);

            // Fetch all posts from the sheet
            $data = [];
            $currentRow = 2;

            $spreadsheetId = "$db_row->google_sheet_url";
            $range = 'A2:H';
            $rows = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);

            if (isset($rows['values'])) {
                foreach ($rows['values'] as $row) {

                    // Ensure the array key exists before accessing it
                    /** 
                     * ARRAY MAP
                     * col-a -> POST TITLE
                     * col-b -> POST CONTENT
                     * col-c -> POST CATEGORY
                     * col-d -> POST TAGS
                     * col-e -> POST ID
                     */
                    $data[] = [
                        'col-a' => isset($row[0]) ? $row[0] : '',
                        'col-b' => isset($row[1]) ? $row[1] : '',
                        'col-c' => isset($row[2]) ? $row[2] : '',
                        'col-d' => isset($row[3]) ? $row[3] : '',
                        'col-e' => isset($row[4]) ? $row[4] : '',
                        // 'col-f' => isset($row[5]) ? $row[5] : '',
                        // 'col-g' => isset($row[6]) ? $row[6] : '',
                        // 'col-h' => isset($row[7]) ? $row[7] : '',
                    ];

                    $currentRow++;
                }
            }

            // Process each row and insert/update posts
            $currentRow = 2; // Reset currentRow counter for sheet update

            foreach ($data as $data) {

                // if post title present -> col-a 
                if (!empty($data['col-a'])) {

                    // col-e -> post_id 
                    if ($data['col-e']) {

                        if ($db_row->post_category) {
                            // Managing categories (custom taxonomy or default)
                            $parts = explode(",", $data['col-c']);
                            $arr = array();

                            foreach ($parts as $cat) {
                                // Use get_term_by() for any taxonomy, including custom ones
                                $existing_term = get_term_by('name', $cat, trim($db_row->post_category));

                                if ($existing_term) {
                                    array_push($arr, $existing_term->term_id);
                                } else {
                                    // Insert the term into the custom taxonomy (or default category if used)
                                    $category = wp_insert_term(
                                        $cat,  // The term name
                                        trim($db_row->post_category) // The taxonomy name
                                    );

                                    // Check for errors and get the term ID
                                    if (!is_wp_error($category)) {
                                        $catid = $category['term_id'];
                                        array_push($arr, $catid);
                                    }
                                }
                            }

                        }
                        
                        // updating the post title, content by ID
                        try {
                            // updaing post fields by post id
                            $post_array = array(
                                'ID' => $data['col-e'],
                                'post_title' => $data['col-a'],
                                'post_content' => $data['col-b'],
                            );
                            wp_update_post($post_array);

                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }

                        try {
                            $post_category = trim($db_row->post_category);
                            wp_set_post_terms($data['col-e'], $arr, trim($post_category));
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }


                        if ($db_row->post_tag) {
                            // Managing tags (can be default 'post_tag' or a custom taxonomy)
                            $parts = explode(",", $data['col-d']);
                            $tags_arr = array();

                            foreach ($parts as $tag) {
                                // Trim the tag and get the taxonomy
                                $taxonomy = trim($db_row->post_tag);
                                $tag_name = trim($tag);

                                // Check if the term exists in the specified taxonomy
                                $existing_tag = get_term_by('name', $tag_name, $taxonomy);

                                if ($existing_tag) {
                                    // If the term exists, add its ID to the array
                                    array_push($tags_arr, $existing_tag->term_id);
                                } else {
                                    // If it doesn't exist, insert it into the taxonomy
                                    $new_tag = wp_insert_term(
                                        $tag_name,  // The tag name
                                        $taxonomy  // The taxonomy name (can be custom)
                                    );

                                    // Check for errors and get the tag ID
                                    if (!is_wp_error($new_tag)) {
                                        $tag_id = $new_tag['term_id'];
                                        array_push($tags_arr, $tag_id);
                                    }
                                }
                            }

                            try {
                                wp_set_post_terms($data['col-e'], $tags_arr, trim($db_row->post_tag));
                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                        }

                    } else {
                        /** IF POST ID NOT FOUND, THEN CREATE INSERT POSTID INTO SHEET AND CREATE NEW POST. **/

                        // POST CATEGORY
                        if ($db_row->post_category) {
                            // Managing categories (custom taxonomy or default)
                            $parts = explode(",", $data['col-c']);
                            $arr = array();

                            foreach ($parts as $cat) {
                                // Use get_term_by() for any taxonomy, including custom ones
                                $existing_term = get_term_by('name', $cat, trim($db_row->post_category));

                                if ($existing_term) {
                                    array_push($arr, $existing_term->term_id);
                                } else {
                                    // Insert the term into the custom taxonomy (or default category if used)
                                    $category = wp_insert_term(
                                        $cat,  // The term name
                                        trim($db_row->post_category) // The taxonomy name
                                    );

                                    // Check for errors and get the term ID
                                    if (!is_wp_error($category)) {
                                        $catid = $category['term_id'];
                                        array_push($arr, $catid);
                                    }
                                }
                            }
                        }


                        try {
                            if ($arr == NULL) {
                                $new_post = array(
                                    'post_title' => $data['col-a'],
                                    'post_content' => $data['col-b'],
                                    'post_status' => 'publish',
                                    'post_type' => $post_type,
                                );

                                $post_id = wp_insert_post($new_post);
                            } else {
                                // $post_category = trim($db_row->post_category);
                                $new_post = array(
                                    'post_title' => $data['col-a'],
                                    'post_content' => $data['col-b'],
                                    'post_status' => 'publish',
                                    'post_type' => $post_type,
                                );

                                $post_id = wp_insert_post($new_post);

                                try {
                                    wp_set_post_terms($post_id, $arr, trim($db_row->post_category));
                                } catch (Exception $e) {
                                    echo $e->getMessage();
                                }
                            }
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }


                        // POST TAGS
                        if ($db_row->post_tag) {
                            // Managing tags (can be default 'post_tag' or a custom taxonomy)
                            $parts = explode(",", $data['col-d']);
                            $arr = array();

                            foreach ($parts as $tag) {
                                // Trim the tag and get the taxonomy
                                $taxonomy = trim($db_row->post_tag);
                                $tag_name = trim($tag);

                                // Check if the term exists in the specified taxonomy
                                $existing_tag = get_term_by('name', $tag_name, $taxonomy);

                                if ($existing_tag) {
                                    // If the term exists, add its ID to the array
                                    array_push($arr, $existing_tag->term_id);
                                } else {
                                    // If it doesn't exist, insert it into the taxonomy
                                    $new_tag = wp_insert_term(
                                        $tag_name,  // The tag name
                                        $taxonomy  // The taxonomy name (can be custom)
                                    );

                                    // Check for errors and get the tag ID
                                    if (!is_wp_error($new_tag)) {
                                        $tag_id = $new_tag['term_id'];
                                        array_push($arr, $tag_id);
                                    }
                                }
                            }
                        }



                        if ($arr != NULL) {
                            wp_set_post_terms($post_id, $arr, trim($db_row->post_tag));
                        }
                        // end managing of tags for post type

                        try {
                            // Update Google Sheet column E with the post ID only if it is empty
                            $updateRange = 'E' . $currentRow;
                            $updateBody = new \Google_Service_Sheets_ValueRange([
                                'range' => $updateRange,
                                'majorDimension' => 'ROWS',
                                'values' => [[$post_id]], // This is where you insert the post ID
                            ]);

                            // Update the sheet only if col-e (post ID) is empty
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
                        // Update Google Sheet column E with the post ID only if it is empty
                        $updateRange = 'E' . $currentRow;
                        $updateBody = new \Google_Service_Sheets_ValueRange([
                            'range' => $updateRange,
                            'majorDimension' => 'ROWS',
                            'values' => [[$post_id]], // This is where you insert the post ID
                        ]);

                        // Update the sheet only if col-e (post ID) is empty
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

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    wp_die();
}



add_action('wp_ajax_fetch_taxonomoes', 'fetch_taxonomoes');
function fetch_taxonomoes()
{
    $post_type = sanitize_text_field($_POST['post_type']);

    $return_arr = array();
    $arr = get_object_taxonomies($post_type);
    foreach ($arr as $arr) {
        $taxonomy = get_taxonomy($arr);
        array_push($return_arr, array('label' => $taxonomy->label, 'name' => $taxonomy->name));
    }

    wp_send_json_success($return_arr);

    wp_die();
}


?>