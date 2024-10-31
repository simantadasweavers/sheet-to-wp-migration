<?php

error_reporting(0);

// Get all custom post types
$args = array(
  'public' => true,
  '_builtin' => false,  // Exclude built-in post types (like page)
);

$custom_post_types = get_post_types($args, 'names');

// Add the 'post' type to the array
$post_types = array_merge(array('post'), $custom_post_types);

?>
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
    margin-right: 5px;
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
          <label for="formFile" class="form-label">JSON Auth File</label>
          <input class="form-control" type="file" name="formFile" id="formFile" accept="application/json" required>
        </div>
        <div class="mb-3">
          <label for="cron-time" class="form-label">CRON Job Time</label>
          <select class="form-select" id="cron-time" aria-label="Default select example">
            <option value="2" selected>2 Mintes</option>
            <option value="5" selected>5 Mintes</option>
            <option value="7">7 Mintes</option>
            <option value="10">10 Mintes</option>
            <option value="15">15 Mintes</option>
            <option value="30">30 Mintes(Recomended)</option>
            <option value="60">1 Hour.</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="post-type" class="form-label">Post Type</label>
          <select class="form-select" id="post-type" aria-label="Default select example">
            <?php
            foreach ($post_types as $post_type) {
              ?>
              <option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="cateory-name" class="form-label">Category</label>
          <select class="form-select" id="cateory-name">
            <option>Select Category</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="tag-name" class="form-label">Tags</label>
          <select class="form-select" id="tag-name" aria-label="Default select example">
            <option>Select Tags</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="tag-name" class="form-label">Custom Field 1</label>
          <select class="form-select" id="custom-field-1" aria-label="Default select example">
            <option>Select Tags</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="tag-name" class="form-label">Custom Field 2</label>
          <select class="form-select" id="custom-field-2" aria-label="Default select example">
            <option>Select Tags</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="tag-name" class="form-label">Custom Field 3</label>
          <select class="form-select" id="custom-field-3" aria-label="Default select example">
            <option>Select Tags</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="tag-name" class="form-label">Custom Field 4</label>
          <select class="form-select" id="custom-field-4" aria-label="Default select example">
            <option>Select Tags</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="tag-name" class="form-label">Custom Field 5</label>
          <select class="form-select" id="custom-field-5" aria-label="Default select example">
            <option>Select Tags</option>
          </select>
        </div>


        <button type="submit" id="submit-btn" class="btn btn-primary">Submit</button>
      </form>

      <br>
      <p class="status d-inline">Status:</p>
      <?php if ($_SESSION['auth_token_status'] == "connected") { ?>
        <span class="badge text-bg-success d-inline">Connected</span>
      <?php } else { ?>
        <span class="badge text-bg-warning d-inline">Not Connected</span>
      <?php } ?>

    </div>
    <div class="col-2"></div>
  </div>


  <br>
  <br>

  <div class="row">
    <div class="col-1"></div>
    <div class="col-10" style="border: 2px solid green;">
      <h3>Step 1: Paste Your Full Google Sheet URL, like below.</h3>
      <font>https://docs.google.com/spreadsheets/d/1I9ypsvUFFcg9MN2xwbHCOLon7m7W8jgfNArEJAo1zz8/edit?gid=0#gid=0
      </font>
      <br>
      <br>

      <h3>Step 2: How to Download Google JSON Credentials</h3>
      <?php
      //echo '<h2>How to Download Google JSON Credentials</h2>';
      echo '<ol>';
      echo '<li>Go to the <a href="https://console.developers.google.com/" target="_blank">Google Cloud Console</a>.</li>';
      echo '<li>Create a new project or select an existing project.</li>';
      echo '<li>In the left sidebar, navigate to <strong>APIs & Services > Credentials</strong>.</li>';
      echo '<li>Click on <strong>+ CREATE CREDENTIALS</strong> at the top and select <strong>Service account</strong>.</li>';
      echo '<li>Fill in the details for your service account and click <strong>CREATE</strong>.</li>';
      echo '<li>On the next screen, select the role (e.g., <strong>Editor</strong> or <strong>Owner</strong>) and click <strong>CONTINUE</strong>.</li>';
      echo '<li>After creating the service account, click on the service account you just created.</li>';
      echo '<li>Under the <strong>Keys</strong> section, click on <strong>ADD KEY</strong> and choose <strong>Create new key</strong>.</li>';
      echo '<li>Select the <strong>JSON</strong> option and click <strong>CREATE</strong>.</li>';
      echo '<li>A JSON file will be downloaded to your computer. This file contains your credentials.</li>';
      echo '<li>Upload the downloaded JSON file above to use it with this plugin.</li>';
      echo '</ol>';
      ?>
      <br>
      <br>

      <h3>Step 3: Make sure your Google Sheet Maintain These column ordering ...</h3>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">POST TITLE</th>
            <th scope="col">POST CONTENT</th>
            <th scope="col">CATEGORIES</th>
            <th scope="col">TAGS</th>
            <th scope="col">POST ID(<font style='color: red;'>Should be Null</font>)</th>
            <th scope="col">META BOX 1</th>
            <th scope="col">META BOX 2</th>
            <th scope="col">META BOX 3</th>
            <th scope="col">META BOX 4</th>
            <th scope="col">META BOX 5</th>
          </tr>
        </thead>
      </table>

      <img src="<?php echo plugin_dir_url(__FILE__); ?>/google-sheet-sample.png" class="img-fluid" alt="...">

      <br>
      <br>

      <h3>Step 4: Make sure your Google Sheet Post ID Should be Null before migration!</h3>

      <br>

      <h3>Step 5: Enable Google Sheet API in Projet. <br/> Go to: Api & Services > Enable Api Services > Search Google Sheet Api. Now enable it. </h3>

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


    function checkFields() {
      // Clear all previous error messages
      jQuery('.error-message').remove();

      let isValid = true;

      // Check Google Sheet URL
      const sheetUrl = jQuery('#sheet_url').val();
      if (sheetUrl === '') {
        isValid = false;
        jQuery('#sheet_url').after('<span class="error-message" style="color:red;">Google Sheet URL is required.</span>');
      }

      // Check JSON Auth File
      const jsonFile = jQuery('#formFile').val();
      if (jsonFile === '') {
        isValid = false;
        jQuery('#formFile').after('<span class="error-message" style="color:red;">JSON Auth File is required.</span>');
      }

      // Check CRON Job Time
      const cronTime = jQuery('#cron-time').val();
      if (cronTime === '') {
        isValid = false;
        jQuery('#cron-time').after('<span class="error-message" style="color:red;">Please select a CRON Job Time.</span>');
      }

      // Check Post Type
      const postType = jQuery('#post-type').val();
      if (postType === null || postType === '') {
        isValid = false;
        jQuery('#post-type').after('<span class="error-message" style="color:red;">Please select a Post Type.</span>');
      }

      // Check Category
      const categoryName = jQuery('#cateory-name').val();
      if (categoryName === 'Select Category') {
        isValid = false;
        jQuery('#cateory-name').after('<span class="error-message" style="color:red;">Please select a Category.</span>');
      }

      // Check Tags
      const tagName = jQuery('#tag-name').val();
      if (tagName === 'Select Tags') {
        isValid = false;
        jQuery('#tag-name').after('<span class="error-message" style="color:red;">Please select Tags.</span>');
      }

      // If form is valid, submit the form
      if (isValid) {
        jQuery('#myForm').submit(); // Submit the form if all fields are valid
      }
    }


    jQuery(document).ready(function () {

      if (jQuery('#post-type').val()) {
        let name = jQuery('#post-type').val();
        jQuery('#cateory-name').empty();
        jQuery('#tag-name').empty();
        jQuery('#custom-field-1').empty();
        jQuery('#custom-field-2').empty();
        jQuery('#custom-field-3').empty();
        jQuery('#custom-field-4').empty();
        jQuery('#custom-field-5').empty();

        let formData = new FormData();
        let formData2 = new FormData();
        formData.append('action', 'fetch_taxonomoes'); // Add action for AJAX
        formData.append('post_type', name); // Add action for AJAX
        formData2.append('action', 'fetch_acf_fields'); // Add action for AJAX
        formData2.append('post_type', name); // Add action for AJAX

        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            if (response.data) {
              let data = response.data;

              let optionHTML = `
            <option value=""> 
            None 
            </option>`;
              jQuery('#cateory-name').append(optionHTML);
              jQuery('#tag-name').append(optionHTML);

              if (data) {
                data.forEach(function (data) {
                  let optionHTML = `
            <option value="${data.name}"> 
                ${data.label} 
            </option>`;
                  jQuery('#cateory-name').append(optionHTML);
                  jQuery('#tag-name').append(optionHTML);
                });
              }
            }
          },
          error: function (response) {
            console.error(response);
          }
        });

        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: formData2,
          processData: false,
          contentType: false,
          success: function (response) {
            if (response.data) {
              let data = response.data;

              let optionHTML = `
                <option value=""> 
                None 
                </option>`;
              jQuery('#custom-field-1').append(optionHTML);
              jQuery('#custom-field-2').append(optionHTML);
              jQuery('#custom-field-3').append(optionHTML);
              jQuery('#custom-field-4').append(optionHTML);
              jQuery('#custom-field-5').append(optionHTML);

              data.forEach(function (data) {
                let optionHTML = `
                  <option value="${data.name}"> 
                      ${data.label} 
                  </option>`;
                jQuery('#custom-field-1').append(optionHTML);
                jQuery('#custom-field-2').append(optionHTML);
                jQuery('#custom-field-3').append(optionHTML);
                jQuery('#custom-field-4').append(optionHTML);
                jQuery('#custom-field-5').append(optionHTML);
              });
            }
          },
          error: function (response) {
            console.error(response);
          }
        });
      }

      // fetch defaults category and tags against default post type select.
      jQuery('#post-type').change(function () {
        let name = jQuery(this).val();
        jQuery('#cateory-name').empty();
        jQuery('#tag-name').empty();
        jQuery('#custom-field-1').empty();
        jQuery('#custom-field-2').empty();
        jQuery('#custom-field-3').empty();
        jQuery('#custom-field-4').empty();
        jQuery('#custom-field-5').empty();

        let formData = new FormData();
        let formData2 = new FormData();
        formData.append('action', 'fetch_taxonomoes'); // Add action for AJAX
        formData.append('post_type', name); // Add action for AJAX
        formData2.append('action', 'fetch_acf_fields'); // Add action for AJAX
        formData2.append('post_type', name); // Add action for AJAX

        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            let data = response.data;

            let optionHTML = `
            <option value=""> 
            None 
            </option>`;
            jQuery('#cateory-name').append(optionHTML);
            jQuery('#tag-name').append(optionHTML);

            data.forEach(function (data) {
              let optionHTML = `
            <option value="${data.name}"> 
                ${data.label} 
            </option>`;
              jQuery('#cateory-name').append(optionHTML);
              jQuery('#tag-name').append(optionHTML);
            });
          },
          error: function (response) {
            console.error(response);
          }
        });


        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: formData2,
          processData: false,
          contentType: false,
          success: function (response) {
            let data = response.data;

            let optionHTML = `
            <option value=""> 
            None 
            </option>`;
            jQuery('#custom-field-1').append(optionHTML);
            jQuery('#custom-field-2').append(optionHTML);
            jQuery('#custom-field-3').append(optionHTML);
            jQuery('#custom-field-4').append(optionHTML);
            jQuery('#custom-field-5').append(optionHTML);

            data.forEach(function (data) {
              let optionHTML = `
            <option value="${data.name}"> 
                ${data.label} 
            </option>`;
              jQuery('#custom-field-1').append(optionHTML);
              jQuery('#custom-field-2').append(optionHTML);
              jQuery('#custom-field-3').append(optionHTML);
              jQuery('#custom-field-4').append(optionHTML);
              jQuery('#custom-field-5').append(optionHTML);
            });

          },
          error: function (response) {
            console.error(response);
          }
        });

      });




      jQuery('#submit-btn').on('click', function (e) {
        e.preventDefault(); // Prevent default form submission

        // checkFields();

        // Get the JSON file
        const jsonFile = document.getElementById('formFile').files[0];

        if (jsonFile) {
          const reader = new FileReader();

          // When the file is read, process the JSON data
          reader.onload = function (e) {
            try {
              const jsonData = JSON.parse(e.target.result);
              let url = document.getElementById('sheet_url').value;
              const cronTime = document.getElementById('cron-time').value;
              const postType = document.getElementById('post-type').value;
              const category = document.getElementById('cateory-name').value;
              const tag_name = document.getElementById('tag-name').value;
              const field1 = document.getElementById('custom-field-1').value;
              const field2 = document.getElementById('custom-field-2').value;
              const field3 = document.getElementById('custom-field-3').value;
              const field4 = document.getElementById('custom-field-4').value;
              const field5 = document.getElementById('custom-field-5').value;

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
              formData.append('cron_time', cronTime); // Append CRON job time
              formData.append('post_type', postType); // Append post type
              formData.append('category', category);
              formData.append('tag_name', tag_name);

              formData.append('field1', field1);
              formData.append('field2', field2);
              formData.append('field3', field3);
              formData.append('field4', field4);
              formData.append('field5', field5);
              formData.append('action', 'save_settings'); // Add action for AJAX

              // Now, make the AJAX request
              jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {

                  if (response.success) {
                    alert("Settings saved successfully!");
                    <?php
                    $_SESSION['auth_token_status'] = "connected";
                    ?>
                    jQuery('#myForm')[0].reset();

                    location.reload();

                  } else {
                    alert("Error in Google Spreadsheet ID or Auth.json file");
                  }
                },
                error: function (response) {
                  console.error(response.data);
                }
              });

            } catch (err) {
              console.error(err);
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