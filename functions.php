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


// add_action('wp_ajax_posts_migration', 'posts_migration');
// function posts_migration()
// {

//     global $wpdb;
//     $table = $wpdb->prefix.'sheet_to_wp_post';
//     $query = $wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT 1");
//     $row = $wpdb->get_row($query);

//     try {
//         require __DIR__ . '/vendor/autoload.php';

//         $client = new \Google_Client();
//         $client->setApplicationName('My PHP App');
//         $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
//         $client->setAccessType('offline');

//         $client->setAuthConfig(__DIR__ . '/auth.json');
//         $sheets = new \Google_Service_Sheets($client);

//         $data = [];
//         $currentRow = 2;

//         $spreadsheetId = $row->google_sheet_url;
//         $range = 'A2:H';
//         $rows = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);
//         if (isset($rows['values'])) {
//             foreach ($rows['values'] as $row) {

//                 if (empty($row[0])) {
//                     break;
//                 }

//                 $data[] = [
//                     'col-a' => $row[0],
//                     'col-b' => $row[1],
//                     'col-c' => $row[2],
//                     'col-d' => $row[3],
//                     'col-e' => $row[4],
//                     'col-f' => $row[5],
//                     'col-g' => $row[6],
//                     'col-h' => $row[7],
//                 ];

//                 $updateRange = 'I' . $currentRow;
//                 $updateBody = new \Google_Service_Sheets_ValueRange([
//                     'range' => $updateRange,
//                     'majorDimension' => 'ROWS',
//                     'values' => ['values' => date('c')],
//                 ]);
//                 $sheets->spreadsheets_values->update(
//                     $spreadsheetId,
//                     $updateRange,
//                     $updateBody,
//                     ['valueInputOption' => 'USER_ENTERED']
//                 );
//                 $currentRow++;
//             }
//         }

//         print_r($data);
//         // echo $encoded = json_encode($data, true);

//     } catch (Exception $e) {
//         echo $e->getMessage();
//     }

//     wp_die();
// }


/*********************************************** */
add_action('wp_ajax_posts_migration', 'posts_migration');
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

            // $client->setAuthConfig(__DIR__ . '/sheet-to-php-437908-5faf0a4ea31b.json');


            $sheets = new \Google_Service_Sheets($client);

            $data = [];
            $currentRow = 2;

            $spreadsheetId = "$row->google_sheet_url";
            $range = 'A2:H';
            $rows = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);
            if (isset($rows['values'])) {
                foreach ($rows['values'] as $row) {

                    if (empty($row[0])) {
                        break;
                    }

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

                    // Update Google Sheet column I with current timestamp
                    $updateRange = 'I' . $currentRow;
                    $updateBody = new \Google_Service_Sheets_ValueRange([
                        'range' => $updateRange,
                        'majorDimension' => 'ROWS',
                        'values' => [['values' => date('c')]],
                    ]);
                    $sheets->spreadsheets_values->update(
                        $spreadsheetId,
                        $updateRange,
                        $updateBody,
                        ['valueInputOption' => 'USER_ENTERED']
                    );
                    $currentRow++;
                }
            }

            // print_r($data);

            foreach ($data as $data) {
                
                $parts = explode(",", $data['col-c']);
                foreach($parts as $cat){
                    if(category_exists($cat)){
                        // echo "exist: ".$cat;
                        $new_post = array(
                            'post_title' => $data['col-a'],
                            'post_content' => $data['col-b'],
                            'post_status' => 'publish',
                            'post_category' => array(get_cat_ID($cat)),  // Category ID(s) go here
                        );
        
                        echo $post_id = wp_insert_post($new_post);
                        echo "  ";
                    }else{
                        // echo "not exist: ".$cat;
                        $new_post = array(
                            'post_title' => $data['col-a'],
                            'post_content' => $data['col-b'],
                            'post_status' => 'publish',
                            'post_category' => array(get_cat_ID($cat)),  // Category ID(s) go here
                        );
        
                        echo $post_id = wp_insert_post($new_post);
                        echo "  ";
                    }
                    // echo "<br/>";

                    // echo get_cat_ID($cat)."  ";
                }
            }

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    wp_die();
}


?>