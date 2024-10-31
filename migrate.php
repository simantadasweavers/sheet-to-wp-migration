<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post migration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<body>
    <br>

    <div class="row">
        <div>
            <button class="btn btn-dark" id="migrate-btn">RUN MIGRATION</button>
        </div>
    </div>

    <br>
    <br>


    <?php
    global $wpdb;
    try{
        $table_name = $wpdb->prefix . 'sheet_to_wp_post';

    // Fetch the last record from the table
    $last_record = $wpdb->get_row("SELECT * FROM $table_name ORDER BY id DESC LIMIT 1");
    }catch(Exception $e){
        echo $e->getMessage();
    }
    ?>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">

            <p class="text-center fs-5">Review Google Sheet Settings Before Migration</p>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">SHEET ID</th>
                        <th scope="col">POST TYPE</th>
                        <th scope="col">CATEGORY</th>
                        <th scope="col">TAG</th>
                        <th scope="col">CRON JOB TIME</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php if(! empty($last_record->google_sheet_url)){echo $last_record->google_sheet_url;} ?></td>
                        <td><?php if(!empty($last_record->post_type)){echo $last_record->post_type;} ?></td>
                        <td><?php if(!empty($last_record->post_category)){echo $last_record->post_category;} ?></td>
                        <td><?php if(!empty($last_record->post_tag)){echo $last_record->post_tag;} ?></td>
                        <td><?php if(!empty($last_record->cron_job_time)){echo $last_record->cron_job_time ? $last_record->cron_job_time." Minutes" : "";} ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-1"></div>
    </div>

    <script>
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

        jQuery('#document').ready(function () {
            jQuery('#migrate-btn').on('click', function (e) {
                e.preventDefault();

                let formData = new FormData();
                formData.append('action', 'posts_migration'); // Add action for AJAX
                formData.append('schedule_migration', true);

                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Awesome!",
                                text: response.data,
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Wait!",
                                text: response.data,
                            });
                        }

                    },
                    error: function (response) {
                        console.error(response);
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