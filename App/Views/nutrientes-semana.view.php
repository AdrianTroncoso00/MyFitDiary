<div class="d-flex align-items-center justify-content-center">
    <div class="card col-lg-8 col-md-8">
        <?php if (count($nutrientesTotales) < 1) { ?>
            <div class="text-center">
                <div class="fs-1 fw-7 text-center mb-3">Nothing here!</div>
                <div class="fs-5">You haven't bookmarked any recipe so far.</div>
                <div class="fs-5 mb-5">Start browsing to have a list of your favorite recipes!</div>
                <a href="/recipe-search" class="fw-6 fs-4">Start now!</a>
            </div>
        <?php } else { ?>
            <div class="card-header bg-white"> 
                <h4 class="title" style="text-align: center;">NUTRIENTES SEMANA</h4>
            </div>
            <div class="content">
                <canvas id="chart"></canvas>

                <table class="table table-hover table-striped" >

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
        <?php } ?>
    </div>
</div>
<?php if (count($nutrientesTotales) > 1) { ?>
    <script type="text/javascript">
        // Obtener una referencia al elemento canvas del DOM
        const $grafica = document.getElementById('chart');
        // Pasaamos las etiquetas desde PHP

        const data = {
            labels:<?php echo json_encode($etiquetas) ?>,
            datasets: [{
                    label: 'Macronutrientes',
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

<?php } ?>


