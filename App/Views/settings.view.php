<section class="col-12 d-flex justify-content-center align-items-center">
    <?php if (isset($_SESSION['exito'])) { ?>
        <div class="card bg-success">
            <div class="card-body">
                <p class="text-center"><?php echo $_SESSION['exito'] ?></p>
            </div>
        </div>
    <?php } ?>
    <?php if (isset($_SESSION['error'])) { ?>
        <div class="card bg-danger">
            <div class="card-body">
                <p class="text-center"><?php echo $_SESSION['error'] ?></p>
            </div>
        </div>
    <?php } ?>
    <div class="card col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8 d-flex align-items-center justify-content-center"> 
        <div class="card-header">
            <h1 class="titulo"><?php echo $titulo ?></h1>
        </div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
            <div class="col-sm-8 col-sm-offset-3">
                <p class="text-center"><?php echo $descript ?></p>
                <form method="post" action="<?php echo isset($deleteAccount) ? '/delete-account' : '/change-'.$name ?>" id="passwordForm" class="form d-flex flex-column align-items-center justify-content-center">
                    <input type="password" class="input-lg form-control" name="passVerify" placeholder="Insert actual password" autocomplete="off">
                    <div class="col-sm-12">
                        <p class="text-danger"><?php echo isset($errores['passVerify']) ? $errores['passVerify'] : '' ?></p> 
                    </div>
                    <?php if(!isset($deleteAccount)){ ?>
                    <input type="<?php echo strtolower($label) != 'password' ? 'text' : 'password' ?>" class="input-lg form-control" name="<?php echo $name ?>" placeholder="New <?php echo $label ?>" value="<?php echo $name != 'pass' && isset($input[$name]) ? $input[$name] : '' ?>" autocomplete="off">
                    <div class="col-sm-12">
                        <p class="text-danger"><?php echo isset($errores[$name]) ? $errores[$name] : '' ?></p>
                    </div>
                    <input type="<?php echo strtolower($label) != 'password' ? 'text' : 'password' ?>" class="input-lg form-control" name="<?php echo $name ?>2" placeholder="Repeat <?php echo $label ?>" value="<?php echo $name != 'pass' && isset($input[$name.'2']) ? $input[$name.'2'] : '' ?>" autocomplete="off">
                    <div class="col-sm-12">
                        <p class="text-danger"><?php echo isset($errores[$name.'2']) ? $errores[$name.'2'] : '' ?></p> 
                    </div>
                    <?php } ?>
                    <input type="submit" class="col-xs-12 btn btn-primary btn-load btn-lg" value="<?php echo isset($deleteAccount) ? 'Delete Account' : 'Change '.$name ?>">
                </form>
            </div><!--/col-sm-6-->
        </div>
    </div>
</section>