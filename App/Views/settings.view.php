<div class="change-pass d-flex flex-column align-items-center justify-content-center">    
    <div class="card col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8"> 
        <div class="card-header d-flex flex-column align-items-center justify-content-center">
            <h1 class="titulo">Change <?php echo $label ?></h1>
        </div>
        <div class="container col-12 d-flex flex-column align-items-center justify-content-center">
            <div class="row col-12 d-flex flex-column align-items-center justify-content-center">
                <div class="col-sm-8 col-sm-offset-3">
                    <p class="text-center">Use the form below to change your <?php echo $label ?>. Your new <?php echo $label ?> cannot be the same as your actual <?php echo $label ?>.</p>
                    <form method="post" action="/change-<?php echo $name?>" id="passwordForm" class="form d-flex flex-column align-items-center justify-content-center">
                        <input type="<?php echo strtolower($label) !='password' ? 'text' :'password' ?>" class="input-lg form-control" name="<?php echo $name?>" placeholder="New <?php echo $label?>" autocomplete="off">
                        <div class="row">
                            <p class="text-danger"><?php echo isset($errores[$name]) ? $errores[$name] : '' ?></p>
                        </div>
                        <input type="<?php echo strtolower($label) !='password' ? 'text' :'password' ?>" class="input-lg form-control" name="<?php echo $name?>2" placeholder="Repeat <?php echo $label?>" autocomplete="off">
                        <div class="row">
                            <div class="col-sm-12">
                                <p class="text-danger"><?php echo isset($errores[$name.'2']) ? $errores[$name.'2'] : '' ?></p> 
                            </div>
                        </div>
                        <input type="submit" class="col-xs-12 btn btn-primary btn-load btn-lg" data-loading-text="Changing <?php echo $label?>..." value="Change <?php echo $label?>">
                    </form>
                </div><!--/col-sm-6-->
            </div><!--/row-->
        </div>
    </div>
</div>