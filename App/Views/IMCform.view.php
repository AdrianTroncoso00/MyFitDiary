<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MyFitDiary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="assets/css/form.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        
    </head>
    <body class="d-flex align-items-center justify-content-center">
        <div class="content col-8 d-flex align-items-center justify-content-center">
            <form action="<?php echo isset($editar) ? '/imc-edit' : '/imc' ?>" method="post" id="datosPersonales" class="card col-8 d-flex flex-column align-items-center justify-content-around">
                <div class="form-info">
                    <h1>DATOS PERSONALES</h1>
                    <div class="form-group">
                        <label for="nombre_completo">Nombre completo</label>
                        <input class="form-control" type="text" id="nombre_completo" name="nombre_completo" value="<?php echo isset($input['nombre_completo']) ? $input['nombre_completo'] : ''; ?>" required>
                        <p class="text-danger"><?php echo isset($errores['nombre_completo']) ? $errores['nombre_completo'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select class="form-select" id="genero" name="genero" value="<?php echo isset($input['genero']) ? $input['genero'] : ''; ?>" required>
                            <option value="" disabled selected>Choose One</option>
                            <?php foreach ($generos as $genero) { ?>
                                <option value="<?php echo $genero ?>" <?php echo isset($input['genero']) && $input['genero']==$genero ? 'selected' : ''?>><?php echo $genero ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <p class="text-danger"><?php echo isset($errores['genero']) ? $errores['genero'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="edad">Edad</label>
                        <input class="form-control" type="number" id="edad" name="edad" min="1" max="120" value="<?php echo isset($input['edad']) ? $input['edad'] : ''; ?>" required>
                        <p class="text-danger"><?php echo isset($errores['edad']) ? $errores['edad'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input class="form-control" type="number" id="peso" name="peso" min="20" max="500" value="<?php echo isset($input['peso']) ? $input['peso'] : ''; ?>" required>
                        <p class="text-danger"><?php echo isset($errores['peso']) ? $errores['peso'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="estatura">Altura (cm)</label>
                        <input class="form-control" type="number" id="estatura" name="estatura" min="20" max="500" value="<?php echo isset($input['estatura']) ? $input['estatura'] : ''; ?>" required>
                        <p class="text-danger"><?php echo isset($errores['estatura']) ? $errores['estatura'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="actividad_fisica">Nivel de Actividad Fisica</label>
                        <select class="form-select" id="actividad" name="actividad_fisica" value="<?php echo isset($input['actividad_fisica']) ? $input['actividad_fisica'] : ''; ?>" required>
                            <option value="" disabled selected>Choose One</option>
                            <?php foreach ($actFis as $act) { ?>
                                <option value="<?php echo $act['id_actividad'] ?>" <?php echo isset($input['actividad_fisica']) && $input['actividad_fisica']==$act['id_actividad'] ? 'selected' : ''?>><?php echo $act['descripcion_actividad'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <p class="text-danger"><?php echo isset($errores['actividad_fisica']) ? $errores['actividad_fisica'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="objetivo">Meta</label>
                        <select class="form-select" id="meta" name="objetivo" value="<?php echo isset($input['objetivo']) ? $input['objetivo'] : ''; ?>" required>
                            <option value="" disabled selected>Choose One</option>
                            <?php foreach ($metas as $meta) { ?>
                                <option value="<?php echo $meta ?>" <?php echo isset($input['objetivo']) && $input['objetivo']==$meta ? 'selected' : ''?>><?php echo $meta ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <p class="text-danger"><?php echo isset($errores['objetivo']) ? $errores['objetivo'] : '' ?></p>
                    </div>

                    <h1>CONFIGURACIÓN DIETA</h1>

                    <div class="form-group">
                        <label for="num_comidas">Numero de comidas(diarias)</label>
                        <select class="form-select" id="num_comidas" name="num_comidas" value="<?php echo isset($input['num_comidas']) ? $input['num_comidas'] : ''; ?>" required>
                            <option value="" disabled selected>Choose One</option>
                            <?php foreach ($num_comidas as $comida) { ?>
                                <option value="<?php echo $comida ?>" <?php echo isset($input['num_comidas']) && $input['num_comidas']==$comida ? 'selected' : ''?>><?php echo $comida ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <p class="text-danger"><?php echo isset($errores['num_comidas']) ? $errores['num_comidas'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="dieta">Dieta a seguir</label>
                        <select class="form-select" aria-label=".form-select" id="dieta" name="dieta" value="<?php echo isset($input['dieta']) ? $input['dieta'] : ''; ?>" required>
                            <option value="" disabled selected>Choose One</option>
                            <?php foreach ($dietas as $dieta) { ?>
                            <option value="<?php echo $dieta['id_dieta']?>" <?php echo isset($input['dieta']) && $input['dieta']==$dieta['id_dieta'] ? 'selected' : ''?>><?php echo isset($dieta['nombre_dieta']) ? $dieta['nombre_dieta'] : '' ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <p class="text-danger"><?php echo isset($errores['dieta']) ? $errores['dieta'] : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="alergenos">Alergenos</label>
                        <select class="form-control selectpicker" multiple data-live-search="true" id="alergenos" name="alergenos[]"  required>
                            <option value="" disabled selected>Choose One</option>
                            <?php if(isset($input['alergenos'])){?>
                                <?php foreach ($input['alergenos'] as $inpAlergeno) {?>
                                    <?php foreach ($alergenos as $alergeno) { ?>
                                        <option value="<?php echo $alergeno['id_alergenos'] ?>" <?php echo isset($inpAlergeno) && $inpAlergeno==$alergeno['id_alergenos'] ? 'selected' : ''?>> <?php echo $alergeno['nombre_alergeno'] ?></option>
                                        <?php
                                    }
                                    ?>                                   
                                <?php } ?>
                            <?php }else{ ?>
                                    <?php foreach ($alergenos as $alergeno) { ?>
                                        <option value="<?php echo $alergeno['id_alergenos'] ?>" <?php echo isset($inpAlergeno) && $inpAlergeno==$alergeno['id_alergenos'] ? 'selected' : ''?>> <?php echo $alergeno['nombre_alergeno'] ?></option>
                                        <?php
                                    }
                                    ?>                                   
                                
                            <?php } ?>
                        </select>
                        <p class="text-danger"><?php echo isset($errores['alergenos']) ? $errores['alergenos'] : '' ?></p>
                    </div>
                    <div class="form-group"  id="porcentDesayuno">
                        <label for="porcent_breakfast">Porcentaje Desayuno(%)</label>
                        <input class="form-control" type="number" id="porcent_breakfast" name="porcent_breakfast" min="1" max="100" value="<?php echo isset($input['porcent_breakfast']) ? $input['porcent_breakfast'] : 0; ?>" required>
                        <p><?php echo isset($errores['porcent_breakfast']) ? $errores['pòrcent_breakfast'] : '' ?></p>
                    </div>
                    <div class="form-group"  id="porcentBrunch">
                        <label for="porcent_brunch">Porcentaje Brunch(%)</label>
                        <input class="form-control" type="number" id="porcent_brunch" name="porcent_brunch" min="0" max="100" value="<?php echo isset($input['porcent_brunch']) ? $input['porcent_brunch'] : 0; ?>">
                        <p><?php echo isset($errores['porcent_brunch']) ? $errores['pòrcent_brunch'] : '' ?></p>
                    </div>
                    <div class="form-group"  id="porcentComida">
                        <label for="porcent_lunch">Porcentaje Comida(%)</label>
                        <input class="form-control" type="number" id="porcent_lunch" name="porcent_lunch" min="1" max="100" value="<?php echo isset($input['porcent_lunch']) ? $input['porcent_lunch'] : 0; ?>" required>
                        <p><?php echo isset($errores['porcent_lunch']) ? $errores['pòrcent_lunch'] : '' ?></p>
                    </div>
                    <div class="form-group" id="porcentSnack">
                        <label for="porcent_snack">Porcentaje Snack(%)</label>
                        <input class="form-control" type="number" id="porcent_snack" name="porcent_snack" min="0" max="100" value="<?php echo isset($input['porcent_snack']) ? $input['porcent_snack'] : 0; ?>">
                        <p><?php echo isset($errores['porcent_snack']) ? $errores['pòrcent_snack'] : '' ?></p>
                    </div>
                    <div class="form-group"  id="porcentCena">
                        <label for="porcent_dinner">Porcentaje Cena(%)</label>
                        <input class="form-control" type="number" id="porcent_cena" name="porcent_dinner" min="1" max="100" value="<?php echo isset($input['porcent_dinner']) ? $input['porcent_dinner'] : 0; ?>" required>
                        <p><?php echo isset($errores['porcent_dinner']) ? $errores['pòrcent_dinner'] : '' ?></p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <input class="btn btn-success" type="submit" value="<?php echo isset($editar) ? 'EDITAR' : 'ENVIAR'?>">
                    </div>
                </div>
            </form>
        </div>
    </body>
    <script src="assets/js/send2forms.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
</html>


