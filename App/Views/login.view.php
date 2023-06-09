<!DOCTYPE html>
<html lang="en">
    <head>
        <title>MyFitDiary</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">	
        <link rel="icon" type="image/png" href="assets/images/Myfitdiary.png"/>
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/animate.css">	
        <link rel="stylesheet" type="text/css" href="assets/css/hamburgers.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/util.css">
        <link rel="stylesheet" type="text/css" href="assets/css/main.css">
        <script src="https://kit.fontawesome.com/4799b9b69b.js" crossorigin="anonymous"></script>
    </head>
    <body>

        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <div class="login100-pic js-tilt" data-tilt>
                        <img src="assets/images/Myfitdiary.png" alt="IMG">
                    </div>

                    <form class="login100-form validate-form" action="/login" method="post">
                        <span class="login100-form-title">
                            Member Login
                        </span>

                        <div class="wrap-input100">
                            <input class="input100" type="text" name="email" placeholder="Email">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                            </span>

                        </div>

                        <div class="wrap-input100">
                            <input class="input100" type="password" name="pass" placeholder="Password">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </span>
                        </div>
                        <?php if(isset($error)){?>
                        <p class="text-danger"><?php echo $error;?></p>
                        <?php }?>
                        
                        <div class="container-login100-form-btn">
                            <button class="login100-form-btn" action="post" href="/login">
                                Login
                            </button>
                        </div>


                        <div class="text-center p-t-136">
                            <a class="txt2" href="signup">
                                Create your Account
                                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script src="assets/js/popper.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/select2.min.js"></script>
        <script src="assets/js/tilt.jquery.min.js"></script>
        <script >
            $('.js-tilt').tilt({
                scale: 1.1
            })
        </script>
        <script src="assets/js/main.js"></script>

    </body>
</html>

