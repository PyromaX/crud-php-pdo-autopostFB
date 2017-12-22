<?php
session_start();
if (($loader = require_once __DIR__ . '/vendor/autoload.php') == null)  {
    die('Vendor directory not found, Please run composer install.');
}
$facebook = new Facebook\Facebook([
    'app_id' => "app_id",
    'app_secret' => "secret_app",
    'default_graph_version' => 'v2.11',
]);

$pageID='page_id'; //ID de la page facebook
$facebook->setDefaultAccessToken('access_token_page');

error_reporting( ~E_NOTICE );
//including the database connection file
include_once("config.php");

if(isset($_POST['Submit'])) {
    $author = $_POST['author'];
    $title = $_POST['title'];
    $message = $_POST['message'];
    $link = $_POST['link'];

    $picFile = $_FILES['picture']['name'];
    $tmp_dir = $_FILES['picture']['tmp_name'];
    $picSize = $_FILES['picture']['size'];

    // checking empty fields
    if (empty($author)) {
        $errMSG = "Please Enter Name";
    }
    else if(empty($title)){
        $errMSG = "Please Enter a Title";
    }
    else if(empty($message)){
        $errMSG = "Please Enter a Message";
    }
    else if(!empty($picFile)){
        $upload_dir = 'images/'; //upload directory

        $picExt = strtolower(pathinfo($picFile,PATHINFO_EXTENSION)); //get image extensions

        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); //valid extensions

        $picture = rand(1000,1000000).".".$picExt; //rename uploading image

        if (in_array($picExt, $valid_extensions)){
            //check file size '5MB'
            if ($picSize < 5000000) {
                move_uploaded_file($tmp_dir,$upload_dir.$picture);
            }
            else {
                $errMSG = "Sorry, your file is too large.";
            }
        }
        else {
            $errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    //if no error occured
    if (!isset($errMSG)) {
        //insert data to database
        $sql = "INSERT INTO users(picture, author, title, message, link) VALUES(:upicture ,:uauthor, :utitle, :umessage, :ulink)";
        $query = $dbConn->prepare($sql);

        $query->bindparam(':upicture', $picture);
        $query->bindparam(':uauthor', $author);
        $query->bindparam(':utitle', $title);
        $query->bindparam(':umessage', $message);
        $query->bindParam(':ulink', $link);

        // Alternative to above bindparam and execute
        // $query->execute(array(':picture' => $picture,':author' => $author, ':title' => $title, ':message' => $message, ':link' => $link));

        if ($query->execute()){
            $successMSG = "new post succesfully inserted ...";

            if (!$picFile){
                $data = [
                    'message' => $title.'
                    
                    
                    '.$message.'
                    
                    By : '.$author,
                    'link' => $link,
                ];
                $deb = $facebook->post('/'.$pageID.'/feed/', $data);
            }
            else {
                $dataMedia = [
                    'caption' => $title.'
                    
                    
                    '.$message.'
                    
                    '.$link.'
                    
                    By : '.$author,
                    'url' => 'yourPath/images/'.$picture,
                ];
                $deb = $facebook->post('/'.$pageID.'/photos/', $dataMedia);
            }

            header("refresh:5;/index.php");
        }
        else {
            $errMSG = "error while inserting";
        }
    }
//		echo "<br/><a href='javascript:self.history.back();'>Go Back</a>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Addpage</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
</head>
<body>
    <div class="container">

        <div class="page-header">
            <h1 class="h2">add new user. <a class="btn btn-default" href="index.php"> <span class="glyphicon glyphicon-eye-open"></span> &nbsp; view all </a></h1>
        </div>

        <?php
        if(isset($errMSG)){
            ?>
            <div class="alert alert-danger">
                <span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG; ?></strong>
            </div>
            <?php
        }
        else if(isset($successMSG)){
            ?>
            <div class="alert alert-success">
                <strong><span class="glyphicon glyphicon-info-sign"></span> <?php echo $successMSG; ?></strong>
            </div>
            <?php
        }
        ?>

        <form method="post" enctype="multipart/form-data" class="form-horizontal">

            <table class="table table-bordered table-responsive">
                <tr>
                    <td><label class="control-label">Author</label></td>
                    <td><input class="form-control" type="text" name="author" placeholder="Enter your Name" value="<?php echo $author; ?>" ></td>
                </tr>
                <tr>
                    <td><label class="control-label">Title</label></td>
                    <td><input class="form-control" type="text" name="title" placeholder="Enter Title" value="<?php echo $title; ?>" ></td>
                </tr>
                <tr>
                    <td><label class="control-label">Message</label></td>
                    <td><input class="form-control" type="text" name="message" placeholder="Enter Message" value="<?php echo $message; ?>" ></td>
                </tr>
                <tr>
                    <td><label class="control-label">Link</label></td>
                    <td><input class="form-control" type="text" name="link" placeholder="Enter Link (optional)" value="<?php echo $link; ?>" ></td>
                </tr>
                <tr>
                    <td><label class="control-label">Picture</label></td>
                    <td><input class="input-group" type="file" accept="image/*" name="picture"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="submit" name="Submit" class="btn btn-default">
                            <span class="glyphicon glyphicon-save"></span> &nbsp; save
                        </button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
