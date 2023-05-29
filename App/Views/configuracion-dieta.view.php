<div class="card col-12">
    <h1>CONFIGURACIÓN DIETA</h1>
    <form action="/configuracion-dieta" method="get">
        <div class="field">
                    <label for="actividad">Nivel de Actividad Fisica</label>
                    <select id="actividad" name="actividad" value="<?php echo isset($input['actividad']) ? $input['actividad'] : ''; ?>" required>
                        <option value="" disabled selected>-</option>
                        <?php foreach ($actFis as $act) { ?>
                            <option value="<?php echo $act['id_actividad'] ?>"><?php echo $act['descripcion_actividad'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
        <div class="field">
            <label for="num_comidas">Numero de comidas(diarias)</label>
            <select id="num_comidas" name="num_comidas" value="<?php echo isset($input['num_comidas']) ? $input['num_comidas'] : ''; ?>" required>
                <option value="" disabled selected>-</option>
                <?php foreach ($num_comidas as $comida) { ?>
                    <option value="<?php echo $comida ?>"><?php echo $comida ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="field">
            <label for="dietas">Dieta a seguir</label>
            <select class="form-select" aria-label=".form-select" id="dieta" name="dietas" value="<?php echo isset($input['dieta']) ? $input['dieta'] : ''; ?>" required>
                <option value="" disabled selected>-</option>
                <?php foreach ($dietas as $dieta) { ?>
                    <option value="<?php echo isset($dieta['id_dieta']) ? $dieta['id_dieta'] : 0 ?>"><?php echo isset($dieta['nombre_dieta']) ? $dieta['nombre_dieta'] : '' ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="field">
            <label for="porcent_breakfast">Porcentaje Desayuno(%)</label>
            <input type="number" id="porcent_breakfast" name="porcent_breakfast" min="1" max="100" value="<?php echo isset($input['porcent_breakfast']) ? $input['porcent_breakfast'] : 0; ?>" required>
            <p><?php echo isset($errores['porcent_breakfast']) ? $errores['pòrcent_breakfast'] : '' ?></p>
        </div>
        <div class="field">
            <label for="porcent_brunch">Porcentaje Brunch(%)</label>
            <input type="number" id="porcent_brunch" name="porcent_brunch" min="0" max="100" value="<?php echo isset($input['porcent_brunch']) ? $input['porcent_brunch'] : 0; ?>">
            <p><?php echo isset($errores['porcent_brunch']) ? $errores['pòrcent_brunch'] : '' ?></p>
        </div>
        <div class="field">
            <label for="porcent_lunch">Porcentaje Comida(%)</label>
            <input type="number" id="porcent_lunch" name="porcent_lunch" min="1" max="100" value="<?php echo isset($input['porcent_lunch']) ? $input['porcent_lunch'] : 0; ?>" required>
            <p><?php echo isset($errores['porcent_lunch']) ? $errores['pòrcent_lunch'] : '' ?></p>
        </div>
        <div class="field">
            <label for="porcent_snack">Porcentaje Snack(%)</label>
            <input type="number" id="porcent_snack" name="porcent_snack" min="0" max="100" value="<?php echo isset($input['porcent_snack']) ? $input['porcent_snack'] : 0; ?>">
            <p><?php echo isset($errores['porcent_snack']) ? $errores['pòrcent_snack'] : '' ?></p>
        </div>
        <div class="field">
            <label for="porcent_dinner">Porcentaje Cena(%)</label>
            <input type="number" id="porcent_cena" name="porcent_dinner" min="1" max="100" value="<?php echo isset($input['porcent_dinner']) ? $input['porcent_dinner'] : 0; ?>" required>
            <p><?php echo isset($errores['porcent_dinner']) ? $errores['pòrcent_dinner'] : '' ?></p>
        </div>

        <input type="submit" value="ENVIAR">
    </form>
</div>