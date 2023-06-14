
<div class="content-body col-12 d-flex justify-content-center align-items-center">
    <div class='col-3 d-flex justify-content-between align-items-center'>
    <form method="post" action="/meal-plan">    
        <label for='fecha' class='form-label mt-2'>
            Date
        </label>
        <input class='form-control align-self-center justify-content-center' type='date' name='fecha' id='fecha' value="<?php echo isset($input['fecha']) ? $input['fecha'] : '' ?>">
        <p class='text-danger'><?php echo isset($errores['fecha']) ? $errores['fecha'] : '' ?></p>
        <input type="submit" class="btn btn-danger" target="_blank" name="submit" value="Get Meal Plan">
    </form>
        <p><?php echo isset($error) ? $error : '' ?></p>
</div>
</div>