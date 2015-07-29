<?php
$target_dir = "../exif-orientation-examples/";


$files=[];

foreach(glob($target_dir.'/*.*') as $file) {
    $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
    //picture or fake picture ?
    $check = getimagesize($file);
    if($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        $fileOK = 1;
    } else {
        //echo "File is not an image.";
        $fileOK = 0;
    }
    // Check file size
    if (filesize($file) > 500000) {
        //echo "Sorry, your file is too large.";
        $fileOK = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $fileOK = 0;
    }

    // Check if $fileOk is set to 0 by an error
    if ($fileOK == 0) {
        //do nothing
    // if everything is ok, try to upload file
    } else {
        $data = file_get_contents($file);
        $base64 = 'data:image/' . $imageFileType . ';base64,' . base64_encode($data);
        $files[] = ['path' => $file, 'data' => $base64, 'exif' => read_exif_data($base64)];
    }

}



?><html>
   <head>
        <title>Base 64 picture library</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <style>
div {
    margin-top: 20px;
    /*word-wrap:break-word;*/
}

h1 {
    color: maroon;
    margin-le: ;ft: 40px;
} 

</style>
   </head>
       <body>
        <h1>Base 64 picture library</h1>
<div class="container">
    <table class="table table-responsive">
        <thead>
            <tr>
                <th>#</th>
                <th>Picture</th>
                <th>Base64 picture</th>
                <th>EXIF Data</th>
            </tr>
        </thead>
        <tbody>

<?php $i=1; foreach ($files as $filebase64) { ?>
            <tr>
                <th scope="row"><?php echo $i++; ?></th>
                <td> 
                    <img class="img-responsive" src="<?php echo $filebase64['path']; ?>" alt="<?php echo $filebase64['path']; ?>"/>
                </td>
                <td>
                        <input type="text" id="input-<?php echo $i-1; ?>" aria-describeby="helpInput-<?php echo $i-1; ?>" class="form-control" onClick="this.setSelectionRange(0, this.value.length)" value="<?php
                        echo $filebase64['data']
                        ?>" readonly>
                    <span id="helpInput-<?php echo $i-1; ?>" class="help-block">
                        Click in the field above and press <kbd><kbd>ctrl</kbd> + <kbd>C</kbd></kbd> or <kbd><kbd>cmd</kbd> + <kbd>C</kbd></kbd> 
                    </span>
                </td>
                <td>
                    <pre>
                    <?php
                    print_r($filebase64['exif']);
                    ?>            
                    </pre>
                </td>
            </tr>
<?php  } ?>
    </tbody>
    </table>
</div>
</body>
</html>


