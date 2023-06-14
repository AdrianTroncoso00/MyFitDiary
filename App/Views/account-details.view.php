<?php if (isset($_SESSION['exito'])) { ?>
    <div class="card bg-success">
        <div class="card-body">
            <p class="text-center"><?php echo $_SESSION['exito'] ?></p>
        </div>
    </div>
<?php } ?>
<?php if (isset($_SESSION['error'])) { ?>
    <div class="card bg-danger">
        <div class="card-body">
            <p class="text-center"><?php echo $_SESSION['error'] ?></p>
        </div>
    </div>
<?php } ?>

<div class='row'>
    <div class='col-md-6 mt-4'>

        <div class='card'>
            <div class='card-header'>
                <h4>Hola, <?php echo isset($_SESSION['usuario']['nombre_completo']) ? $_SESSION['usuario']['nombre_completo'] : 'usuario' ?><strong></strong>!</h4>
            </div>
            <div class='card-body'>

                <div class='d-flex w-100 justify-content-between'>
                    <p class='mb-1'>
                        Username:<?php echo isset($_SESSION['usuario']['username']) ? $_SESSION['usuario']['username'] : 'usuario' ?> <strong></strong>
                    </p>
                </div>

                <div class='d-flex w-100 justify-content-between'>
                    <p class='mb-1'>
                        Email:<?php echo isset($_SESSION['usuario']['email']) ? $_SESSION['usuario']['email'] : 'email' ?> <strong></strong>
                    </p>
                </div>

                <div class='d-flex w-100 justify-content-between'>
                    <p class='mb-1'>
                        Ultimo login: <?php echo isset($_SESSION['usuario']['last_login']) ? $_SESSION['usuario']['last_login'] : '' ?><strong></strong>
                    </p>
                </div>

            </div>
        </div>

        <div class='card mb-3 mt-4'>

            <div class='card-header'>
                <h4>Add Weigth</h4>

            </div>
            <div class='card-body'>
                <div class='form-group'>

                    <form action="/account" method='POST'>

                        <div class='form-group'>
                            <div class='row col-12 d-flex flex-row align-items-center justify-content-between'>
                                <div class='col-5'>
                                    <label for='fecha' class='form-label mt-2'>
                                        Fecha
                                    </label>
                                    <input class='form-control' type='date' name='fecha' id='fecha' value="<?php echo isset($input['fecha']) ? $input['fecha'] : '' ?>">
                                    <p class='text-danger'><?php echo isset($errores['fecha']) ? $errores['fecha'] : '' ?></p>
                                </div>
                                <div class='col-5'>
                                    <label for='peso' class='form-label mt-2'>
                                        Peso en kg
                                    </label>
                                    <input class='form-control' type='number' name=peso id='peso' placeholder='0.00'
                                           required name='weight' min='0' value='<?php echo isset($input['peso']) ? $input['peso'] : '0' ?>' step='1' title='Weight'
                                           pattern='^\d*(\.\d{0,2})?$'>
                                    <p class="text-danger"><?php echo isset($errores['peso']) ? $errores['peso'] : '' ?></p>
                                </div>
                                <button type='submit' class='btn btn-primary edit-peso' href="<?php echo isset($editar) ? "/account/edit-peso/$id_peso" : '/account' ?>">
                                    Añadir
                                </button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
        <?php if ($pesos != null) { ?>
            <div class='card mt-4 mb-4'>
                <div class='card-header'>
                    <h4>Weigth Records</h4>
                </div>
                <div class='card-body col-12'>
                    <?php
                    if (isset($pesos)) {
                        ?>

                        <table id='weightable' class='table'>
                            <thead>
                                <tr>
                                    <th scope='col' class='col-md-4'>Peso en kg</th>
                                    <th scope='col' class='col-md-4'>Fecha</th>
                                    <th scope='col' class='col-md-4'></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($pesos as $peso) {
                                    ?>
                                    <tr>
                                        <td class='col-md-4'><?php echo $peso['peso'] ?></td>
                                        <td class='col-md-4'><?php echo $peso['fecha'] ?></td>
                                        <td class='col-md-4'>
                                            <a class='btn btn-danger' href="account/deletePeso/<?php echo $peso['id_peso'] ?>">
                                                <i class='fas fa-trash-alt'></i>
                                            </a>
                                        </td>
                                    </tr>
            <?php
        }
        ?>

                            </tbody>
                        </table>
        <?php
    }
    ?>
                </div>
            </div>
                <?php } ?>
    </div>

    <div class='col-md-6 mt-4'>
        <div class='card mb-3'>
            <div class='card-header'>
                <h4>Weigth History</h4>
            </div>
            <div class='card-body justify-content-center'>
                <div class='chart-bar'>
                    <canvas id='myChart'></canvas>
                </div>
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">
    // Obtener una referencia al elemento canvas del DOM
    const $grafica = document.getElementById('myChart');
    // Pasaamos las etiquetas desde PHP
    const etiquetas = <?php echo json_encode($fechas) ?>;
    // Podemos tener varios conjuntos de datos. Comencemos con uno
    const data = {
        labels:<?php echo json_encode($fechas) ?>,
        datasets: [{
                label: 'Progresion peso',
                data: <?php echo json_encode($pesos_chart) ?>,
                backgroundColor:'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
            }]
    };
    new Chart($grafica, {
        type: 'line', // Tipo de gráfica
        data: data,
        options: {
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
            },
        }
    });
</script>
