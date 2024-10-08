
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Migration ~ Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
</head>
<style>
  .status {
    display: inline;
    margin-right: 5px; /* Optional: Add some space between the text and badge */
  }
</style>

<body>

  <br>
  <br>

  <div class="row">
    <div class="col-2"></div>
    <div class="col-8">
      <form id="myForm">
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label">Google Sheet URL</label>
          <input type="text" class="form-control" name="sheet_url" id="sheet_url" required>
        </div>
        <div class="mb-3">
          <label for="formFile" class="form-label">JSON auth file</label>
          <input class="form-control" type="file" name="formFile" id="formFile" accept="application/json" required>
        </div>
        <button type="submit" id="submit-btn" class="btn btn-primary">Submit</button>
      </form>

      <br>
      <p class="status d-inline">Status:</p>  
      <?php if($_SESSION['auth_token_status'] == "connected"){ ?> 
      <span class="badge text-bg-success d-inline">Connected</span>
      <?php }else{ ?> 
      <span class="badge text-bg-warning d-inline">Not Connected</span>
      <?php } ?>

    </div>
    <div class="col-2"></div>

  </div>

  

  <script>
  var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
  function extractSheetId(url) {
    const regex = /\/d\/([a-zA-Z0-9-_]+)/;
    const match = url.match(regex);
    return match ? match[1] : null;
}

  jQuery(document).ready(function () {
    jQuery('#submit-btn').on('click', function (e) {
      e.preventDefault(); // Prevent default form submission

      // Get the JSON file
      const jsonFile = document.getElementById('formFile').files[0];

      if (jsonFile) {
        const reader = new FileReader();

        // When the file is read, process the JSON data
        reader.onload = function (e) {
          try {
            const jsonData = JSON.parse(e.target.result);
            let url = document.getElementById('sheet_url').value;
            // Create FormData object and append data
            let formData = new FormData();
            formData.append('sheet_url', extractSheetId(url));
            formData.append('account_type', jsonData.type);
            formData.append('project_id', jsonData.project_id);
            formData.append('private_key_id', jsonData.private_key_id);
            formData.append('private_key', jsonData.private_key);
            formData.append('client_email', jsonData.client_email);
            formData.append('client_id', jsonData.client_id);
            formData.append('auth_uri', jsonData.auth_uri);
            formData.append('token_uri', jsonData.token_uri);
            formData.append('auth_provider_x509_cert_url', jsonData.auth_provider_x509_cert_url);
            formData.append('client_x509_cert_url', jsonData.client_x509_cert_url);
            formData.append('universe_domain', jsonData.universe_domain);
            formData.append('action', 'save_settings'); // Add action for AJAX

            // Now, make the AJAX request
            jQuery.ajax({
              type: "POST",
              url: ajaxurl,
              data: formData,
              processData: false,
              contentType: false,
              success: function (response) {
                console.warn(response);
                 if (response.success) {
                 alert("Settings saved successfully!");
                 <?php 
                 $_SESSION['auth_token_status'] = "connected";
                 ?> 
                 jQuery('#myForm')[0].reset();

                 location.reload();

               } else {
                  alert("Failed to save settings: " + response.data);
                }
              },
              error: function () {
                alert("Error during AJAX request!");
              }
            });

          } catch (err) {
            console.error("Invalid JSON file", err);
            alert("Invalid JSON file. Please check the format.");
          }
        };

        // Read the file
        reader.readAsText(jsonFile);
      } else {
        alert("No JSON auth file selected");
      }
    });
  });
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>