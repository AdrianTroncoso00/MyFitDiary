<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MyFitDiary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    </head>
    <body>
        <div class="content d-flex align-items-center justify-content-center">
            <form action="/imc" method="post" id="datosPersonales" class="form col-10 d-flex flex-row align-items-center justify-content-around">
                <div class="card col-6">
                    <h1>DATOS PERSONALES</h1>
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input class="form-control" type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select class="form-select" id="genero" name="genero" value="<?php echo isset($input['genero']) ? $input['genero'] : ''; ?>" required>
                            <option value="" disabled selected>-</option>
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edad">Edad</label>
                        <input class="form-control" type="number" id="edad" name="edad" min="1" max="120" value="<?php echo isset($input['edad']) ? $input['edad'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input class="form-control" type="number" id="peso" name="peso" min="20" max="500" value="<?php echo isset($input['peso']) ? $input['peso'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="altura">Altura (cm)</label>
                        <input class="form-control" type="number" id="altura" name="altura" min="20" max="500" value="<?php echo isset($input['altura']) ? $input['altura'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="actividad">Nivel de Actividad Fisica</label>
                        <select class="form-select" id="actividad" name="actividad" value="<?php echo isset($input['actividad']) ? $input['actividad'] : ''; ?>" required>
                            <option value="" disabled selected>-</option>
                            <?php foreach ($actFis as $act) { ?>
                                <option value="<?php echo $act['id_actividad'] ?>"><?php echo $act['descripcion_actividad'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="meta">Meta</label>
                        <select class="form-select" id="meta" name="meta" value="<?php echo isset($input['meta']) ? $input['meta'] : ''; ?>" required>
                            <option value="" disabled selected>-</option>
                            <option value="Perder Peso">Perder Peso</option>
                            <option value="Mantener Peso">Mantener Peso</option>
                            <option value="Aumentar Masa Muscular">Aumentar Masa Muscular</option>
                        </select>
                    </div>
                </div>

                <div class="card col-6 d-flex">
                    <h1>CONFIGURACIÓN DIETA</h1>

                    <div class="form-group">
                        <label for="num_comidas">Numero de comidas(diarias)</label>
                        <select class="form-select" id="num_comidas" name="num_comidas" value="<?php echo isset($input['num_comidas']) ? $input['num_comidas'] : ''; ?>" required>
                            <option value="" disabled selected>-</option>
                            <?php foreach ($num_comidas as $comida) { ?>
                                <option value="<?php echo $comida ?>"><?php echo $comida ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
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
                    <div class="form-group">
                        <label for="alergenos">Alergenos</label>
                        <select class="form-select" multiple aria-label=".form-select" id="alergenos" name="alergenos[]" value="<?php echo isset($input['alergenos']) ? $input['alergenos'] : ''; ?>" required>
                            <option value="" disabled selected>-</option>
                            <?php foreach ($alergenos as $alergeno) { ?>
                                <option value="<?php echo $alergeno['id_alergenos'] ?>"><?php echo $alergeno['nombre_alergeno'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="porcent_breakfast">Porcentaje Desayuno(%)</label>
                        <input class="form-control" type="number" id="porcent_breakfast" name="porcent_breakfast" min="1" max="100" value="<?php echo isset($input['porcent_breakfast']) ? $input['porcent_breakfast'] : 0; ?>" required>
                        <p><?php echo isset($errores['porcent_breakfast']) ? $errores['pòrcent_breakfast'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="porcent_brunch">Porcentaje Brunch(%)</label>
                        <input class="form-control" type="number" id="porcent_brunch" name="porcent_brunch" min="0" max="100" value="<?php echo isset($input['porcent_brunch']) ? $input['porcent_brunch'] : 0; ?>">
                        <p><?php echo isset($errores['porcent_brunch']) ? $errores['pòrcent_brunch'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="porcent_lunch">Porcentaje Comida(%)</label>
                        <input class="form-control" type="number" id="porcent_lunch" name="porcent_lunch" min="1" max="100" value="<?php echo isset($input['porcent_lunch']) ? $input['porcent_lunch'] : 0; ?>" required>
                        <p><?php echo isset($errores['porcent_lunch']) ? $errores['pòrcent_lunch'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="porcent_snack">Porcentaje Snack(%)</label>
                        <input class="form-control" type="number" id="porcent_snack" name="porcent_snack" min="0" max="100" value="<?php echo isset($input['porcent_snack']) ? $input['porcent_snack'] : 0; ?>">
                        <p><?php echo isset($errores['porcent_snack']) ? $errores['pòrcent_snack'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="porcent_dinner">Porcentaje Cena(%)</label>
                        <input class="form-control" type="number" id="porcent_cena" name="porcent_dinner" min="1" max="100" value="<?php echo isset($input['porcent_dinner']) ? $input['porcent_dinner'] : 0; ?>" required>
                        <p><?php echo isset($errores['porcent_dinner']) ? $errores['pòrcent_dinner'] : '' ?></p>
                    </div>

                </div>
                <input type="submit" value="ENVIAR">
            </form>
        </div>
    </body>
</html>


