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
                        console.log(response);
                        if(response.success){
                            Swal.fire({
                            icon: "success",
                            title: "Awesome!",
                            text: response.data,
                        });
                        }else{
                            Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Migration Task Not Schedulled. Set the Auth Settings!",
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