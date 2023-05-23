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

                                <div class="iconos">
                                    <div class="input-icono">    
                                        <input class="completado" type="checkbox" value="completado"
                                               name="completado">
                                        <label for="completado" class="input-text">Completado</label>
                                    </div>
                                    <a href="/cambiar-comida/<?php echo $nombre ?>"><i class="fa-solid fa-rotate"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="box-content">

                            <table class="table table-hover table-striped">

                                <tbody>
                                        <tr class="tr-general">
                                        <?php foreach ($comida as $key => $infoComida) { ?>
                                            <div>   
                                                <td class="td-img">
                                                    <img class="img" 
                                                         src="<?php echo isset($infoComida['image']) ? $infoComida['image'] : '' ?>">
                                                </td>
                                                <td class="td-descript">
                                                    <p><?php echo isset($infoComida['label']) ? $infoComida['label'] : '' ?></p>
                                                    <span><?php echo isset($infoComida['calorias']) ? $infoComida['calorias'] . ' kcal' : '' ?> </span>

                                                </td>

                                                <td class="td-buttons">
                                                    <a href="/cambiar-comida-especifica/<?php echo $infoComida['id_receta']; ?>/<?php echo $infoComida['nombre_comida'] ?>/<?php echo $infoComida['calorias_comida'] ?>"><i class="fa-solid fa-rotate"></i></a>
                                                    <a href="/eliminar-receta/<?php echo $infoComida['id_receta']; ?>"><i class='fas fa-trash-alt'></i></a>
                                                    <i class="fa-solid fa-eye" data-toggle="modal" data-target="<?php echo '#' . $nombre . $key ?>"></i>
                                                </td>
                                            </div>
                                        <div class="modal" id="<?php echo $nombre.$key ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body p-5">
                                                        <!-- Visible -->
                                                        <div class="row mb-3">
                                                            <!-- Image, Total Time, & Calories -->
                                                            <div class="col-auto px-4">
                                                                <!-- Image -->
                                                                <div class="mb-3">
                                                                    <img src="<?php echo $infoComida['image'] ?>" alt="<?php echo $infoComida['label'] ?>">
                                                                </div>
                                                                <!-- Total Time and Calories -->
                                                                <div class="small mb-4 lh-lg d-flex flex-wrap justify-content-between">
                                                                    <!-- Time -->
                                                                    <small>
                                                                        <span class="bi bi-stopwatch"></span>
                                                                        <!-- Handle error if no data for totalTime -->

                                                                        <span><?php echo $infoComida['totalTime'] . ' Min' ?></span>


                                                                    </small>
                                                                    <!-- Calories -->
                                                                    <small>
                                                                        <span class="bi bi-fire"></span>
                                                                        <span><?php echo $infoComida['calorias'] . ' kcal' ?></span>
                                                                    </small>
                                                                </div>
                                                                <!-- View recipe -->
                                                                <div class="mb-3 text-center">
                                                                    <a href="<?php echo $infoComida['url'] ?>" target=”_blank”><button type="button" class=" yellow px-5 btn btn-primary">View Full Recipe <span class="bi bi-box-arrow-up-right"></span></button></a>
                                                                </div>
                                                            </div>
                                                            <!-- Title, Source, & Ingredients -->
                                                            <div class="col px-4">
                                                                <!-- Title -->
                                                                <div class="h3 pb-2 lh-sm text-capitalize"><?php echo $infoComida['label'] ?></div>
                                                                <!-- Source -->

                                                                <!-- Ingredients -->
                                                                <div class="mb-3">
                                                                    <div class="lh-sm text-muted mb-2">Ingredients:</div>
                                                                    <small class="lh-1 text-lowercase">
                                                                        <ul class="list-group border-top border-bottom list-group-flush mb-3">
                                                                            <?php foreach ($infoComida['ingredientes'] as $ingredient) { ?>
                                                                                <li class="list-group-item p-1"><?php echo $ingredient['stringIngrediente'] ?></li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </small>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <!-- Expanded Tags -->
                                                        <div class="row mb-3 px-4">
                                                            <div class="col-12 px-0">
                                                                <div class="lh-base text-muted py-2">Show Tags

                                                                </div>
                                                                <div class="mb-4 py-2 border-top border-bottom hide">

                                                                    <!-- Cuisine Tags -->
                                                                    <div class="mb-1">
                                                                        <div class="lh-sm small text-muted">Cuisine Type:</div>
                                                                        <small class="ps-2 lh-1 text-uppercase d-flex flex-wrap">
                                                                            <?php foreach ($infoComida['cuisineType'] as $cuisine) { ?>
                                                                                <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $cuisine ?></small>
                                                                            <?php } ?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        </tr>
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
