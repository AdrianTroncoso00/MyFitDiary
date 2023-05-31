<div class="d-flex align-items-center justify-content-center">
<div class="card col-lg-8 col-md-8">
    <div class="card-header">
        <h4 class="title" style="text-align: center;">MACRONUTRIENTES SEMANA</h4>
    </div>
    <div class="content">
        <canvas id="chart"></canvas>

        <table class="table table-hover table-striped" >

            <tbody>
                <?php foreach ($nutrientesSemana as $nombreNutriente => $infoNutriente) { ?>
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
/

