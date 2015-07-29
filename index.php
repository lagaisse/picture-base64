<?php
$target_dir = "../exif-orientation-examples";


$files=[];

foreach(glob($target_dir.'/*.*') as $file) {
    //picture or fake picture ?
    $check = getimagesize($file);
    if($check !== FALSE) {
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
    // $check[2] == exif_imagetype($file)
    $imageFileType = image_type_to_extension($check[2], FALSE); 
    if ($imageFileType === FALSE)
    {
        $imageFileType = pathinfo($file,PATHINFO_EXTENSION);
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
        $exif_data = read_exif_data($base64);
        $exif_orientation = 1;
        if ($exif_data !== FALSE)
        {
            $exif_orientation = $exif_data['Orientation'];
        }
     
        $files[] = ['path' => $file, 'data' => $base64, 'exif' => read_exif_data($base64), 'orientation' => $exif_orientation];
    }

}

function picture_orientate(&$picture, $orientation=1)
{
    switch($orientation) {
        case 2:
            imageflip($picture, IMG_FLIP_HORIZONTAL);
            break;
        case 3:
            $picture = imagerotate($picture, 180, 0);
            break;
        case 4:
            imageflip($picture, IMG_FLIP_VERTICAL);
            break;
        case 5:
            $picture = imagerotate($picture, -90, 0);
            imageflip($picture, IMG_FLIP_HORIZONTAL);
            break;
        case 6:
            $picture = imagerotate($picture, -90, 0);
            break;
        case 7:
            $picture = imagerotate($picture, 90, 0);
            imageflip($picture, IMG_FLIP_HORIZONTAL);
            break;
        case 8:
            $picture = imagerotate($picture, 90, 0);
            break;
    }
    return $picture;
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
                <th class="col-md-1">#</th>
                <th class="col-md-3">Source picture</th>
                <th class="col-md-2">Base64 picture</th>
                <th class="col-md-3">EXIF Data</th>
                <th class="col-md-3">Oriented picture</th>
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
                <td> 
<?php
                    $picture = imagecreatefromstring(file_get_contents($filebase64['path']));
                    $picture = picture_orientate($picture, $filebase64['orientation']);
                    ob_start();
                    imagejpeg($picture);
                    $contents =  ob_get_contents();
                    ob_end_clean();
?>
                    <img class="img-responsive" src="data:image/jpeg;base64,<?php echo base64_encode($contents); ?>" alt="Computed <?php echo $filebase64['path']; ?>"/>
                </td>
            </tr>
<?php  } ?>
    </tbody>
    </table>
</div>
</body>
</html>


