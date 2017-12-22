
<?php

error_reporting( ~E_NOTICE );

include_once("config.php");

if (isset($_GET['edit_id'])&& !empty($_GET['edit_id'])){
    $id = $_GET['edit_id'];
    $result_edit = $dbConn->prepare('SELECT picture, author, title, message, link FROM users WHERE id =:uid');
    $result_edit->execute(array(':uid'=>$id));
    $edit_row = $result_edit->fetch(PDO::FETCH_ASSOC);
    extract($edit_row);
} else {
    header("location: index.php");
}

if(isset($_POST['Submit'])) {
    $author = $_POST['author'];
    $title = $_POST['title'];
    $message = $_POST['message'];
    $link = $_POST['link'];

    $picFile = $_FILES['picture']['name'];
    $tmp_dir = $_FILES['picture']['tmp_name'];
    $picSize = $_FILES['picture']['size'];

    // checking empty fields
    if($picFile){
        $upload_dir = 'images/'; //upload directory
        $picExt = strtolower(pathinfo($picFile,PATHINFO_EXTENSION)); //get image extensions
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); //valid extensions
        $picture = rand(1000,1000000).".".$picExt; //rename uploading image
        if (in_array($picExt, $valid_extensions)){
            //check file size '5MB'
            if ($picSize < 5000000) {
                unlink($upload_dir.$edit_row['picture']);
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
    else {
        $picture = $edit_row['picture'];
    }


    //if no error occured
    if (!isset($errMSG)) {
        //insert data to database
        $sql = "UPDATE users 
                    SET picture=:upicture,
                        author=:uauthor,
                        title=:utitle,
                        message=:umessage,
                        link=:ulink
                    WHERE id=:uid";
        $query = $dbConn->prepare($sql);

        $query->bindparam(':upicture', $picture);
        $query->bindparam(':uauthor', $author);
        $query->bindparam(':utitle', $title);
        $query->bindparam(':umessage', $message);
        $query->bindParam(':ulink', $link);
        $query->bindParam(':uid', $id);

        if ($query->execute()){
            ?>
            <script>
                alert('Successfully Updated ...');
                window.location.href='index.php';
            </script>
            <?php
        }
        else{
            $errMSG = "Sorry Data Could Not Updated !";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Editpage</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
<!--    <link rel="stylesheet" href="style.css">-->
    <script src="bootstrap/js/bootstrap.min.js"></script>
<!--    <script src="jquery-1.11.3-jquery.min.js"></script>-->
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="h2">update profile. <a class="btn btn-default" href="index.php"> all members </a></h1>
        </div>
        <div class="clearfix"></div>

        <form method="post" enctype="multipart/form-data" class="form-horizontal">


            <?php
            if(isset($errMSG)){
                ?>
                <div class="alert alert-danger">
                  <span class="glyphicon glyphicon-info-sign"></span> &nbsp; <?php echo $errMSG; ?>
                </div>
                <?php
            }
            ?>

            <table class="table table-bordered table-responsive">

            <tr>
                <td><label class="control-label">Author</label></td>
                <td><input class="form-control" type="text" name="author" value="<?php echo $author; ?>" required /></td>
            </tr>

            <tr>
                <td><label class="control-label">Title</label></td>
                <td><input class="form-control" type="text" name="title" value="<?php echo $title; ?>" required /></td>
            </tr>

            <tr>
                <td><label class="control-label">Message</label></td>
                <td><input class="form-control" type="text" name="message" value="<?php echo $message; ?>" required /></td>
            </tr>

            <tr>
                <td><label class="control-label">Link</label></td>
                <td><input class="form-control" type="text" name="link" value="<?php echo $link; ?>" /></td>
            </tr>

            <tr>
                <td><label class="control-label">Picture</label></td>
                <td>
                    <?php if(!empty($picture)){ ?>
                    <img src="images/<?php echo $picture; ?>" width="150px" height="150px" />
                    <?php } ?>
                    <input class="input-group" type="file" name="picture" accept="image/*" />
                </td>
            </tr>

            <tr>
                <td colspan="2"><button type="submit" name="Submit" class="btn btn-default">
                <span class="glyphicon glyphicon-save"></span> Update
                </button>

                <a class="btn btn-default" href="index.php"> <span class="glyphicon glyphicon-backward"></span> cancel </a>

                </td>
            </tr>

            </table>
        </form>
    </div>
</body>
</html>
