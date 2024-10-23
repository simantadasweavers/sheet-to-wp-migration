<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Google Sheet Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

</head>

<body>

    <br>
    <br>

    <?php
    try {
        require __DIR__ . '/vendor/autoload.php';

        // Set Google Client using database values
        $client = new \Google_Client();
        $client->setApplicationName('My PHP App');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        global $wpdb;
        $table = $wpdb->prefix . 'sheet_to_wp_post';

        // Execute the query directly without prepare
        $query = "SELECT * FROM $table ORDER BY id DESC LIMIT 1";
        $row = $wpdb->get_row($query);
        if (!$row) {
            $row = NULL;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    if ($row) {
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

        // Fetch column names from the first row of the sheet
        $spreadsheetId = "$row->google_sheet_url";
        $range = 'A1:H1';  // First row (column headers)
        $headerRow = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);

        // Extract and print the column names
        if (isset($headerRow['values'][0])) {
            $columnNames = $headerRow['values'][0]; // This will be an array of column names
        } else {
            $columnNames = NULL;
        }
    }

    ?>

    <div class="row">

        <h2 class="text-center">Set Google Sheet Settings</h2>

        <br>
        <br>

        <div class="col-2"></div>
        <div class="col-8">
            <form id="myForm">
                <input type="hidden" id="recordid" value="<?php if ($row) {
                    echo $row->id;
                } ?>" required>
                <div class="mb-3">
                    <label for="" class="form-label">Post ID(Should Be Blank Before Migration)</label>
                    <select class="form-select" id="postid" aria-label="Default select example">
                        <?php foreach ($columnNames as $column) { ?>
                            <option value="<?php echo $column; ?>"><?php echo $column; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Post Title</label>
                    <select class="form-select" id="posttitle" aria-label="Default select example">
                        <?php foreach ($columnNames as $column) { ?>
                            <option value="<?php echo $column; ?>"><?php echo $column; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Post Content</label>
                    <select class="form-select" id="postcontent" aria-label="Default select example">
                        <?php foreach ($columnNames as $column) { ?>
                            <option value="<?php echo $column; ?>"><?php echo $column; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Post Category</label>
                    <select class="form-select" id="postcategory" aria-label="Default select example">
                        <?php foreach ($columnNames as $column) { ?>
                            <option value="<?php echo $column; ?>"><?php echo $column; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Post Tags</label>
                    <select class="form-select" id="posttags" aria-label="Default select example">
                        <?php foreach ($columnNames as $column) { ?>
                            <option value="<?php echo $column; ?>"><?php echo $column; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <button id="submitBtn" class="btn btn-primary">Submit</button>
                </div>
            </form>

        </div>
        <div class="col-2"></div>
    </div>


    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('#submitBtn').on('click', function (e) {
                e.preventDefault();

                // Gather form data
                var recordid = jQuery('#recordid').val();
                var postid = jQuery('#postid').val();
                var posttitle = jQuery('#posttitle').val();
                var postcontent = jQuery('#postcontent').val();
                var postcategory = jQuery('#postcategory').val();
                var posttags = jQuery('#posttags').val();

                // console.warn(postid, posttitle, postcontent, postcategory, posttags);


                // AJAX request
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>", // WordPress AJAX URL
                    type: 'POST',
                    data: {
                        action: 'submit_google_sheet_form',
                        recordid: recordid,
                        postid: postid,
                        posttitle: posttitle,
                        postcontent: postcontent,
                        postcategory: postcategory,
                        posttags: posttags
                    },
                    success: function (response) {
                        console.log(response);

                        alert('Form submitted successfully! ');
                    },
                    error: function (xhr, status, error) {
                        alert('Something went wrong: ');
                    }
                });
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>