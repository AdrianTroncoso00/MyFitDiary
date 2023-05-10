<!DOCTYPE html>
<html lang="en">
<head>
	<title>MyFitDiary</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="assets/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/css/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="assets/css/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="assets/css/main.css">
	<script src="https://kit.fontawesome.com/4799b9b69b.js" crossorigin="anonymous"></script>
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="assets/images/Myfitdiary.png" alt="IMG">
				</div>

				<form class="login100-form" method="post" href="/signup">
					<span class="login100-form-title">
						Create Account
					</span>

					<div class="wrap-input100" >
						<input class="input100" type="text" name="email" placeholder="Email" value="<?php echo isset($input['email']) ? $input['email'] : ''; ?>" required>
                                                <p class="text-danger" style="text-align: center"><?php echo isset($errores['email']) ? $errores['email'] : ''; ?></p>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
						
					</div>
					<div class="wrap-input100">
						<input class="input100" type="text" name="username" placeholder="Username" value="<?php echo isset($input['username']) ? $input['username'] : ''; ?>" required>
                                                <p class="text-danger"style="text-align: center"><?php echo isset($errores['username']) ? $errores['username'] : ''; ?></p>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="bi bi-people" aria-hidden="true"></i>
						</span>
						
					</div>

					<div class="wrap-input100" >
                                            <input class="input100" type="password" name="pass" placeholder="Password" required>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					<div class="wrap-input100" >
						<input class="input100" type="password" name="pass2" placeholder="Repeat Password" required>
                                                <p class="text-danger"style="text-align: center"><?php echo isset($errores['pass']) ? $errores['pass'] : ''; ?></p>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Sign Up
						</button>
					</div>

					

					<div class="text-center p-t-136">
						<a class="txt2" href="login">
							Already have an account?
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="assets/js/jquery-3.2.1.min.js"></script>
        <!--===============================================================================================-->
        <script src="assets/js/popper.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <!--===============================================================================================-->
        <script src="assets/js/select2.min.js"></script>
        <!--===============================================================================================-->
        <script src="assets/js/tilt.jquery.min.js"></script>
        <script >
            $('.js-tilt').tilt({
                scale: 1.1
            })
        </script>
        <!--===============================================================================================-->
        <script src="assets/js/main.js"></script>

</body>
</html>