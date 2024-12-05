<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if (isset($_POST['submit'])) {
    // Check if all required fields are filled
    if (empty($_POST['d_name']) || empty($_POST['about']) || empty($_POST['price'])) {
        $error =  '<div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>All fields must be filled out!</strong>
                    </div>';
    } else {
        // Handle image upload if a new image is provided
        if (!empty($_FILES['file']['name'])) {
            $fname = $_FILES['file']['name'];
            $temp = $_FILES['file']['tmp_name'];
            $fsize = $_FILES['file']['size'];
            $extension = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
            $fnew = uniqid() . '.' . $extension;
            $store = "Res_img/dishes/" . basename($fnew);

            if ($extension == 'jpg' || $extension == 'png' || $extension == 'gif') {
                if ($fsize >= 1000000) {
                    $error =  '<div class="alert alert-danger alert-dismissible fade show">
                                 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                 <strong>Max Image Size is 1024kb! Try a different Image.</strong>
                                </div>';
                } else {
                    // Prepare SQL query using prepared statements
                    $sql = $db->prepare("UPDATE dishes SET title=?, slogan=?, price=?, img=? WHERE d_id=?");
                    $sql->bind_param("sssss", $_POST['d_name'], $_POST['about'], $_POST['price'], $fnew, $_GET['menu_upd']);

                    if ($sql->execute()) {
                        // Move uploaded file to the destination folder
                        if (move_uploaded_file($temp, $store)) {
                            $success =  '<div class="alert alert-success alert-dismissible fade show">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Record Updated!</strong>
                                          </div>';
                            // Redirect to all_menu.php after success
                            header("Location: all_menu.php");
                            exit(); // Ensure no further code is executed after redirection
                        } else {
                            $error = '<div class="alert alert-danger alert-dismissible fade show">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <strong>Error uploading image.</strong>
                                      </div>';
                        }
                    } else {
                        $error = '<div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <strong>Error updating record:</strong> ' . mysqli_error($db) . '
                                  </div>';
                    }
                }
            } else {
                $error =  '<div class="alert alert-danger alert-dismissible fade show">
                             <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                             <strong>Invalid image format! Only JPG, PNG, and GIF are allowed.</strong>
                            </div>';
            }
        } else {
            // If no image is selected, update without changing the image
            $sql = $db->prepare("UPDATE dishes SET title=?, slogan=?, price=? WHERE d_id=?");
            $sql->bind_param("ssss", $_POST['d_name'], $_POST['about'], $_POST['price'], $_GET['menu_upd']);

            if ($sql->execute()) {
                $success =  '<div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Record Updated!</strong>
                              </div>';
                // Redirect to all_menu.php after success
                header("Location: all_menu.php");
                exit(); // Ensure no further code is executed after redirection
            } else {
                $error = '<div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>Error updating record:</strong> ' . mysqli_error($db) . '
                          </div>';
            }
        }
    }
}
?>


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Update Menu</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header">

    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <div id="main-wrapper">

        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                        <span><img src="images/icn.png" alt="homepage" class="dark-logo" /></span>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0">
                    </ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="logout.php">
                                <button class="btn btn-danger"><i class="fa fa-sign-out-alt"></i> Log Out</button>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li> <a href="all_users.php"> <span><i class="fa fa-user f-s-20 "></i></span><span>Users</span></a></li>
                        <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Menu</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_menu.php">All Menus</a></li>
                                <li><a href="add_menu.php">Add Menu</a></li>
                            </ul>
                        </li>
                        <li> <a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="page-wrapper">

            <div class="container-fluid">

                <?php echo $error; echo $success; ?>

                <div class="col-lg-12">
                    <div class="card card-outline-primary">
                        <div class="card-header">
                            <h4 class="m-b-0 text-white">Update Menu to Restaurant</h4>
                        </div>
                        <div class="card-body">
                            <form action='' method='post' enctype="multipart/form-data">
                                <div class="form-body">
                                    <?php
                                    $qml = "SELECT * FROM dishes WHERE d_id='$_GET[menu_upd]'";
                                    $rest = mysqli_query($db, $qml); 
                                    $roww = mysqli_fetch_array($rest);
                                    ?>
                                    <hr>
                                    <div class="row p-t-20">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Dish Name</label>
                                                <input type="text" name="d_name" value="<?php echo $roww['title'];?>" class="form-control" placeholder="Dish Name">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group has-danger">
                                                <label class="control-label">About</label>
                                                <input type="text" name="about" value="<?php echo $roww['slogan'];?>" class="form-control form-control-danger" placeholder="About the Dish">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row p-t-20">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Price</label>
                                                <input type="text" name="price" value="<?php echo $roww['price'];?>" class="form-control" placeholder="$ Price">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group has-danger">
                                                <label class="control-label">Image</label>
                                                <!-- Display current image -->
                                                <?php if (!empty($roww['img'])): ?>
                                                    <img src="Res_img/dishes/<?php echo $roww['img']; ?>" alt="Dish Image" width="150" height="150">
                                                <?php else: ?>
                                                    <p>No image available</p>
                                                <?php endif; ?>
                                                <input type="file" name="file" class="form-control form-control-danger">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <input type="submit" name="submit" class="btn btn-primary" value="Save">
                                    <a href="all_menu.php" class="btn btn-inverse">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php include "include/footer.php" ?>

        </div>

    </div>

    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>

</body>

</html>
