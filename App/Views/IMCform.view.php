<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MyFitDiary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f2f2f2;
            }
            .content{
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%

            }
            h1 {
                text-align: center;
                margin-top: 30px;
                color: #4CAF50;
            }
            form {
                width: 40%;
                min-width: 300px;
                margin: 0 auto;
                background-color: #fff;
                border-radius: 10px;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
                display: flex;
                flex-direction: column;

            }
            .field {
                margin: 10px 0;
                display: flex;
                flex-direction: column;
            }
            label {
                font-size: 18px;
                margin-bottom: 5px;
            }
            input[type="text"], input[type="number"], select {
                padding: 10px;
                border-radius: 5px;
                border: none;
                border: 2px solid #ccc;
                width: 80%;
                font-size: 16px;
                color: #333;
            }
            input[type="submit"] {
                padding: 10px 20px;
                margin-top: 20px;
                border-radius: 5px;
                border: none;
                background-color: #4CAF50;
                color: white;
                font-size: 18px;
                cursor: pointer;
                align-self: center;
            }
            .result {
                text-align: center;
                font-size: 20px;
                margin-top: 30px;
                color: #4CAF50;
            }
            textarea{
                height: 100px;
            }
            /* Estilos para pantallas pequeñas */
            @media screen and (max-width: 480px) {
                form {
                    width: 90%;
                    margin: 0;
                    padding: 10px;
                    box-shadow: none;
                }
                input[type="submit"] {
                    margin-top: 10px;
                }
            }
        </style>
    </head>
    <body>
        <div class="content">
            <form action="/imc" method="post">

                <h1>DATOS PERSONALES</h1>
                <div class="field">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="field">
                    <label for="genero">Género</label>
                    <select id="genero" name="genero" value="<?php echo isset($input['genero']) ? $input['genero'] : ''; ?>" required>
                        <option value="" disabled selected>-</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                    </select>
                </div>
                <div class="field">
                    <label for="edad">Edad</label>
                    <input type="number" id="edad" name="edad" min="1" max="120" value="<?php echo isset($input['edad']) ? $input['edad'] : ''; ?>" required>
                </div>
                <div class="field">
                    <label for="peso">Peso (kg)</label>
                    <input type="number" id="peso" name="peso" min="20" max="500" value="<?php echo isset($input['peso']) ? $input['peso'] : ''; ?>" required>
                </div>
                <div class="field">
                    <label for="altura">Altura (cm)</label>
                    <input type="number" id="altura" name="altura" min="20" max="500" value="<?php echo isset($input['altura']) ? $input['altura'] : ''; ?>" required>
                </div>
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
                    <label for="meta">Meta</label>
                    <select id="meta" name="meta" value="<?php echo isset($input['meta']) ? $input['meta'] : ''; ?>" required>
                        <option value="" disabled selected>-</option>
                        <option value="Perder Peso">Perder Peso</option>
                        <option value="Mantener Peso">Mantener Peso</option>
                        <option value="Aumentar Masa Muscular">Aumentar Masa Muscular</option>
                    </select>
                </div>

                <h1>CONFIGURACIÓN DIETA</h1>
                <form action="/imc" method="post">
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
                        <label for="dietas[]">Dieta a seguir</label>
                        <select class="form-select" multiple aria-label=".form-select" id="dieta" name="dietas[]" value="<?php echo isset($input['dieta']) ? $input['dieta'] : ''; ?>" required>
                            <option value="" disabled selected>-</option>
                            <?php foreach ($dietas as $dieta) { ?>
                                <option value="<?php echo isset($dieta['id_dieta']) ? $dieta['id_dieta'] : 0 ?>"><?php echo isset($dieta['nombre_dieta']) ? $dieta['nombre_dieta'] : '' ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="alergenos">Alergenos</label>
                        <select class="form-select" multiple aria-label=".form-select" id="alergenos" name="alergenos[]" value="<?php echo isset($input['alergenos']) ? $input['alergenos'] : ''; ?>" required>
                            <option value="" disabled selected>-</option>
                            <?php foreach ($alergenos as $alergeno) { ?>
                                <option value="<?php echo $alergeno['id_alergenos']?>"><?php echo $alergeno['nombre_alergeno']?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="porcent_desayuno">Porcentaje Desayuno(%)</label>
                        <input type="number" id="porcent_desayuno" name="porcent_desayuno" min="1" max="100" value="<?php echo isset($input['porcent_desayuno']) ? $input['porcent_desayuno'] : ''; ?>" required>
                    </div>
                    <div class="field">
                        <label for="porcent_comida">Porcentaje Comida(%)</label>
                        <input type="number" id="porcent_comida" name="porcent_comida" min="1" max="100" value="<?php echo isset($input['porcent_comida']) ? $input['porcent_comida'] : ''; ?>" required>
                    </div>
                    <div class="field">
                        <label for="porcent_cena">Porcentaje Cena(%)</label>
                        <input type="number" id="porcent_cena" name="porcent_cena" min="1" max="100" value="<?php echo isset($input['porcent_cena']) ? $input['porcent_cena'] : ''; ?>" required>
                    </div>
                    
                    <input type="submit" value="ENVIAR">
                </form>
        </div>
    </body>
</html>


