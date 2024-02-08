<!doctype html>
<html lang="en">

<head>
  <title>Title</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- bootstrap 5.x or 4.x is supported. You can also use the bootstrap css 3.3.x versions -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" crossorigin="anonymous">

  <!-- default icons used in the plugin are from Bootstrap 5.x icon library (which can be enabled by loading CSS below) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">

  <!-- alternatively you can use the font awesome icon library if using with `fas` theme (or Bootstrap 4.x) by uncommenting below. -->
  <!-- link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" crossorigin="anonymous" -->

  <!-- the fileinput plugin styling CSS file -->
  <link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

  <!-- if using RTL (Right-To-Left) orientation, load the RTL CSS file after fileinput.css by uncommenting below -->
  <!-- link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput-rtl.min.css" media="all" rel="stylesheet" type="text/css" /-->

  <!-- the jQuery Library -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>

  <!-- buffer.min.js and filetype.min.js are necessary in the order listed for advanced mime type parsing and more correct
     preview. This is a feature available since v5.5.0 and is needed if you want to ensure file mime type is parsed 
     correctly even if the local file's extension is named incorrectly. This will ensure more correct preview of the
     selected file (note: this will involve a small processing overhead in scanning of file contents locally). If you 
     do not load these scripts then the mime type parsing will largely be derived using the extension in the filename
     and some basic file content parsing signatures. -->
     <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/buffer.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/filetype.js" type="text/javascript"></script>

  <!-- piexif.min.js is needed for auto orienting image files OR when restoring exif data in resized images and when you
    wish to resize images before upload. This must be loaded before fileinput.min.js -->
  <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/piexif.min.js" type="text/javascript"></script>

  <!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview. 
    This must be loaded before fileinput.min.js -->
  <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/sortable.min.js" type="text/javascript"></script>

  <!-- bootstrap.bundle.min.js below is needed if you wish to zoom and preview file content in a detail modal
    dialog. bootstrap 5.x or 4.x is supported. You can also use the bootstrap js 3.3.x versions. -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <!-- the main fileinput plugin script JS file -->
  <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script>
  
  

  <!-- following theme script is needed to use the Font Awesome 5.x theme (`fas`). Uncomment if needed. -->
  <!-- script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/themes/fas/theme.min.js"></script -->

  <!-- optionally if you need translation for your language then include the locale file as mentioned below (replace LANG.js with your language locale) -->
  <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/locales/LANG.js"></script>
</head>

<body>


  <label for="input-res-1">File Gallery</label>

  <div class="file-loading">
    <input id="input-res-1" name="files[]" type="file" multiple>
    <input type="hidden" id="csrf_token" name="_token" value="<?php echo e(csrf_token()); ?>">
  </div>



<script>
$(document).ready(function() {
    $("#input-res-1").fileinput({
        uploadUrl: "<?php echo e(url('/fileUpload/store/')); ?>",
        uploadAsync: true,
        maxFileCount: 15,
        maxFileSize: 4096,
        allowedFileExtensions : ['jpg', 'png'],
        uploadExtraData: {'_token':$('#csrf_token').val()},  
        deleteExtraData: {'_token':$('#csrf_token').val()},  
        initialPreviewAsData: false,
        initialPreview: [],          // if you have previously uploaded preview files
        initialPreviewConfig: [], 
        showRemove: true,
      }).on('filebatchpreupload', function(event, data) {
        var n = data.files.length,
          files = n > 1 ? n + ' files' : 'one file';
        if (!window.confirm("Are you sure you want to upload " + files + "?")) {
          return {
            message: "Upload aborted!", // upload error message
            data: {} // any other data to send that can be referred in `filecustomerror`
          };
        }
      }).on('filesorted', function(e, params) {
        console.log('File sorted params', params);
      }).on("fileuploaded", function(event, data, previewId, index) {
        // Handle file uploaded event
        console.log('File uploaded', data);
        
        preview(data.response);
    });

      function preview(data) {

          // Get the uploaded file's URL from the data object
          var uploadedFiles = data; // Assuming the response contains the URL of the uploaded file
          
          var previewMarkup = uploadedFiles.map(function(url) {
              return `<div class="file-preview-item">
                          <img src="` + url.url + `" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">
                      </div>`
          });
          // Prepare initialPreviewConfig for each uploaded file
          var initialPreviewConfig = uploadedFiles.map(function(url) {
              return {
                  width: '120px', // Set width as needed
                  url: url.url, // URL for the file (optional)
                  key: url.key, // Unique key for the file (optional)
                  showRemove : true,
                  downloadUrl : url.url,
                  deleteUrl : "<?php echo e(url('fileUpload/destroy"+url.key+"')); ?>",
                  showZoom: false, // Disable zoom button
                  showRotate: false, // Disable rotate button
              };
          });


          // Update the file input's initialPreview and initialPreviewConfig options
          $("#input-res-1").fileinput('destroy').fileinput({
              initialPreview: previewMarkup,
              initialPreviewAsData: false,
              initialPreviewConfig: initialPreviewConfig,
              showRemove: true,
              initialPreviewShowDelete: false,
              showUpload: true,

          });
          $(".kv-file-remove").removeAttr('disabled');
      }



      $(document).on("click", ".kv-file-remove", function() {
        var key = $(this).data("key");
        var url = "<?php echo e(url('/')); ?>";

        // Send an AJAX request to delete the file
        $.ajax({
            url: url + '/fileUpload/destroy/' + key , // URL for deleting the file
            type: 'GET',
            success: function(result) {
                if(result != "{}"){
                    console.log(result);
                    preview(result);
                }else{
                    //   Remove the file preview from the file input
                    $('#input-res-1').fileinput('clear').fileinput('unlock').fileinput('refresh').fileinput('enable').fileinput('create');
                    $('#input-res-1').fileinput('upload').fileinput('clear');
              
                }
            }
        });
    });

});
</script>

    </body>
</html>
<?php /**PATH /var/www/html/globtierMultiple/resources/views/welcome.blade.php ENDPATH**/ ?>