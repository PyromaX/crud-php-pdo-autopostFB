<?php
//including the database connection file
include_once("config.php");

$Gid = $_GET['delete_id'];

if(isset($Gid))
{
    // select image from db to delete
    $result = $dbConn->prepare('SELECT picture FROM users WHERE id =:uid');
    $result->execute(array(':uid'=>$Gid));
    $imgRow=$result->fetch(PDO::FETCH_ASSOC);
    unlink("images/".$imgRow['picture']);

    // it will delete an actual record from db
    $stmt_delete = $dbConn->prepare('DELETE FROM users WHERE id =:uid');
    $stmt_delete->bindParam(':uid',$Gid);
    $stmt_delete->execute();

    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Homepage</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="page-header col-xs-offset-2">
        <h1 class="h2">all Post. / <a class="btn btn-default" href="add.php"> <span class="glyphicon glyphicon-plus"></span> &nbsp; add new </a></h1>
    </div>
    <br />
    <div class="row">
    <?php

        $result = $dbConn->query("SELECT * FROM users ORDER BY id DESC");

        if($result->rowCount() > 0)
        {
            while($row=$result->fetch(PDO::FETCH_ASSOC))
            {
                extract($row);
                ?>
                <span class="col-xs-10 col-xs-offset-1" style="border: solid black 1px"></span>
                <div class="col-xs-8 col-xs-offset-2">
                    <p class="page-header"><?php echo "Title : ".$title; ?></p>
                    <p class="page-header"><?php echo "Auteur : ".$author; ?></p>
                    <p class="page-header"><?php echo "Message : ".$message; ?></p>
                    <?php if(!empty($link)){ ?>
                        <p class="page-header">Link :
                            <a href="<?php echo $link; ?>"><?php echo $link; ?></a>
                        </p>
                    <?php } if(!empty($picture)){ ?>
                        <div style="width:300px ;height:300px" >
                            <img src="images/<?php echo $row['picture']; ?>" class="img-rounded" id="myImg<?php echo $row['id']?>" height="100%" alt=""/>
                            <!-- The Modal -->
                            <div id="myModal" class="modal01">
                                <!-- The Close Button -->
                                <span class="close">&times;</span>
                                <!-- Modal Content (The Image) -->
                                <img class="modal-content01" id="img01">
                                <!-- Modal Caption (Image Text) -->
                                <div id="caption"></div>
                            </div>
                        </div>
                    <?php } ?>
                    <p class="page-header">
                        <span>
                            <a class="btn btn-info" href="edit.php?edit_id=<?php echo $row['id']; ?>" title="click for edit" onclick="return confirm('sure to edit ?')"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                            <a class="btn btn-danger" href="?delete_id=<?php echo $row['id']; ?>" title="click for delete" onclick="return confirm('sure to delete ?')"><span class="glyphicon glyphicon-remove-circle"></span> Delete</a>
                        </span>
                    </p>
                </div>
                <script type="application/javascript"> // Get the modal
                    var modal = document.getElementById('myModal');
                    var CurrentId = <?php echo $row['id'];?>;
                    var img = document.getElementById('myImg'+CurrentId);
                    var modalImg = document.getElementById("img01");
                    var captionText = document.getElementById("caption");
                    img.onclick = function(){
                        modal.style.display = "block";
                        modalImg.src = this.src;
                        captionText.innerHTML = this.alt;
                    }
                    var span = document.getElementsByClassName("close")[0];
                    span.onclick = function() {
                        modal.style.display = "none";
                    }
                </script>
                <?php
            }
        }
        else
        {
            ?>
            <div class="col-xs-12">
                <div class="alert alert-warning">
                    <span class="glyphicon glyphicon-info-sign"></span> &nbsp; No Data Found ...
                </div>
            </div>
            <?php
        }
    ?>
    </div>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
