<?php

require_once("koneksi/conn.php");

if (isset($_POST['register'])) {

    // filter data yang diinputkan
    $nip_dosen = filter_input(INPUT_POST, 'nip_dosen', FILTER_SANITIZE_STRING);
    $nama_dosen = filter_input(INPUT_POST, 'nama_dosen', FILTER_SANITIZE_STRING);
    // enkripsi password
    $password_dosen = password_hash($_POST["password_dosen"], PASSWORD_DEFAULT);
    $telepon_dosen = filter_input(INPUT_POST, 'telepon_dosen', FILTER_VALIDATE_EMAIL);


    // menyiapkan query
    $sql = "INSERT INTO dosen (nip_dosen, nama_dosen, telepon_dosen, password_dosen) 
            VALUES (:nip_dosen, :nama_dosen, :telepon_dosen, :password_dosen)";
    $stmt = $db->prepare($sql);

    // bind parameter ke query
    $params = array(
        ":nip_dosen" => $nip_dosen,
        ":nama_dosen" => $nama_dosen,
        ":password_dosen" => $password_dosen,
        ":telepon_dosen" => $telepon_dosen
    );

    // eksekusi query untuk menyimpan ke database
    $saved = $stmt->execute($params);

    // jika query simpan berhasil, maka user sudah terdaftar
    // maka alihkan ke halaman login
    if ($saved) header("Location: dosen/dosen_login.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Sistem Informasi Kenaikan Jabatan Dosen</title>

    <!-- Fontfaces CSS-->
    <link href="register/css/font-face.css" rel="stylesheet" media="all">
    <link href="register/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="register/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="register/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="register/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="register/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="register/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="register/vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="register/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="register/vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="register/vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="register/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/gijgo.css">
    <link rel="stylesheet" href="../assets/css/slicknav.css">
    <link rel="stylesheet" href="../assets/css/animate.min.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.css">
    <link rel="stylesheet" href="../assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/slick.css">
    <link rel="stylesheet" href="../assets/css/nice-select.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">

    <!-- Main CSS-->
    <link href="register/css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">
    <!-- Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/FT.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader Start -->
    <div class="page-wrapper">
        <div class="page-content--bge5">
            <div class="container">
                <div class="login-wrap">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="#">
                                <img src="register/images/FT-500.png" alt="FT-UHAMKA">
                            </a>
                        </div>
                        <div class="login-form">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label>NIDN</label>
                                    <input class="au-input au-input--full" type="text" name="nidn" placeholder="NIDN">
                                </div>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input class="au-input au-input--full" type="text" name="email" placeholder="Nama Lengkap">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input class="au-input au-input--full" type="text" name="telepon" placeholder="Telepon">
                                </div>
                                <div class="login-checkbox">
                                    <label>
                                        <input type="checkbox" name="aggree">Agree the terms and policy
                                    </label>
                                </div>
                                <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">register</button>
                            </form>
                            <div class="register-link">
                                <p>
                                    Anda sudah mempunyai akun?
                                    <a href="dosen/dosen_login.php">Login</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="register/vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="register/vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="register/vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="register/vendor/slick/slick.min.js">
    </script>
    <script src="register/vendor/wow/wow.min.js"></script>
    <script src="register/vendor/animsition/animsition.min.js"></script>
    <script src="register/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="register/vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="register/vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="register/vendor/circle-progress/circle-progress.min.js"></script>
    <script src="register/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="register/vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="register/vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="register/js/main.js"></script>

    <!-- All JS Custom Plugins Link Here here -->
    <script src="../assets/js/vendor/modernizr-3.5.0.min.js"></script>

    <!-- Jquery, Popper, Bootstrap -->
    <script src="../assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="../assets/js/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/slick.min.js"></script>
    <!-- Date Picker -->
    <script src="../assets/js/gijgo.min.js"></script>
    <!-- One Page, Animated-HeadLin -->
    <script src="../assets/js/wow.min.js"></script>
    <script src="../assets/js/animated.headline.js"></script>
    <script src="../assets/js/jquery.magnific-popup.js"></script>

    <!-- Scrollup, nice-select, sticky -->
    <script src="../assets/js/jquery.scrollUp.min.js"></script>
    <script src="../assets/js/jquery.nice-select.min.js"></script>
    <script src="../assets/js/jquery.sticky.js"></script>

    <!-- contact js -->
    <script src="../assets/js/contact.js"></script>
    <script src="../assets/js/jquery.form.js"></script>
    <script src="../assets/js/jquery.validate.min.js"></script>
    <script src="../assets/js/mail-script.js"></script>
    <script src="../assets/js/jquery.ajaxchimp.min.js"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/main.js"></script>

</body>

</html>
<!-- end document-->