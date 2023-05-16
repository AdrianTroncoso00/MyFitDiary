<div class="content-body col-12 d-flex justify-content-between">
    <div class="card col-md-7 d-flex align-items-center">
        <div class="header">
            <h4 class="title" style="text-align: center;">PLAN ALIMENTICIO</h4>
        </div>
        <div class="content col-10 ">
                <?php if (isset($mealPlan)) { ?>
                    <?php foreach ($mealPlan as $nombre => $comida) { ?>
                    <div class="box">

                        <div class="box-header">
                            <div class="box-header title">
                                <h4 class="encabezado4"><?php echo strtoupper($nombre) ?></h4>
                            </div>
                            <div class="box-header-check">
                                <p>calorias</p>
                                <div class="iconos">
                                    <div class="input-icono">    
                                        <input class="completado" type="checkbox" value="completado"
                                               name="completado">
                                        <label for="completado" class="input-text">Completado</label>
                                    </div>
                                    <a><i class="fa-solid fa-rotate"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="box-content">

                            <table class="table table-hover table-striped">

                                <tbody>
                                    <?php foreach ($comida as $infoComida) { ?>
                                        <tr class="tr-general">


                                            <td class="td-img">
                                                <img class="img" 
                                                     src="<?php echo isset($infoComida['recipe']['image']) ? $infoComida['recipe']['image'] : '' ?>">
                                            </td>
                                            <td class="td-descript">
                                                <p><?php echo isset($infoComida['recipe']['label']) ? $infoComida['recipe']['label'] : '' ?></p>
                                                <span><?php echo isset($infoComida['recipe']['calories']) ? $infoComida['recipe']['calories'] . ' kcal' : '' ?> </span>

                                            </td>

                                            <td class="td-buttons">
                                                <a><i class="fa-solid fa-rotate"></i></a>
                                                <a href="/regenerar-receta/<?php echo $infoComida['recipe']['calories'] ?>/<?php echo $infoComida['recipe']['mealType'][0] ?>"><i class="fa-regular fa-thumbs-up"></i></a>
                                                <i class="fa-solid fa-eye" data-toggle="modal" data-target="<?php echo '#' . $nombre.$key ?>"></i>
                                            </td>
                                        </tr>


                                    <?php } ?>
                                </tbody>

                            </table>


                            </div>




                        </div>
                    <?php } ?>
            <?php } ?>
            </div>
    </div>





    <div class="card col-md-5">
        <div class="header">
            <h4 class="title" style="text-align: center;">MACRONUTRIENTES</h4>
        </div>
        <div class="content">
            <canvas id="chart"></canvas>

            <table class="table table-hover table-striped">

                <tbody>
                    <?php foreach ($nutrientesTotales as $nombreNutriente => $infoNutriente) { ?>
                        <tr>


                            <td><?php echo $nombreNutriente ?></td>
                            <td class="td-descript">
                                <p><?php echo round($infoNutriente['cantidadTotal'], 2) ?></p>
                            </td>
                            <td>    
                                <p><?php echo $infoNutriente['unidad'] ?> </p>
                            </td>
                        </tr>
                    <?php } ?>


                </tbody>
            </table>

        </div>
    </div>
</div>

<script type="text/javascript">
    // Obtener una referencia al elemento canvas del DOM
    const $grafica = document.getElementById('chart');
    // Pasaamos las etiquetas desde PHP

    const data = {
        labels:<?php echo json_encode($etiquetas) ?>,
        datasets: [{
                label: 'My First Dataset',
                data: <?php echo json_encode($valores_etiquetas) ?>,
                backgroundColor:
<?php echo json_encode($chart_colors) ?>

                ,
                hoverOffset: 4
            }]
    };
    new Chart($grafica, {
        type: 'pie', // Tipo de gráfica
        data: data


    });
</script>
