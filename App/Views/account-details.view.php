

          <div class='row'>
            <div class='col-md-6 mt-4'>

              <div class='card'>
                <div class='card-header'>
                  <h4>Hola, <?php echo isset($_SESSION['usuario']['nombre_completo']) ?$_SESSION['usuario']['nombre_completo']: 'usuario' ?><strong></strong>!</h4>
                </div>
                <div class='card-body'>

                  <div class='d-flex w-100 justify-content-between'>
                    <p class='mb-1'>
                        Username:<?php echo isset($_SESSION['usuario']['username']) ?$_SESSION['usuario']['username']: 'usuario' ?> <strong></strong>
                    </p>
                  </div>

                  <div class='d-flex w-100 justify-content-between'>
                    <p class='mb-1'>
                      Email:<?php echo isset($_SESSION['usuario']['email']) ?$_SESSION['usuario']['email']: 'email' ?> <strong></strong>
                    </p>
                  </div>

                  <div class='d-flex w-100 justify-content-between'>
                    <p class='mb-1'>
                      Ultimo login: <?php echo isset($_SESSION['usuario']['last_login']) ?$_SESSION['usuario']['last_login']: '' ?><strong></strong>
                    </p>
                  </div>

                </div>
              </div>

              <div class='card bg-secondary mb-3'>

                <div class='card-header'>
                  <h4>Añadir peso</h4>
                  
                </div>
                <div class='card-body'>

                  <div class='form-group'>

                    <form action="/account" method='POST'>
                      
                      <div class='form-group'>
                        <div class='row'>
                          <div class='col-5'>
                            <label for='fecha' class='form-label mt-2'>
                              Fecha
                            </label>
                              <input class='form-control' type='date' name='fecha' id='fecha' value="<?php echo isset($input['fecha']) ? $input['fecha'] : ''?>">
                            <p class='text-danger'><?php echo isset($errores['fecha']) ? $errores['fecha'] :''?></p>
                          </div>
                          <div class='col-5'>
                            <label for='peso' class='form-label mt-2'>
                              Peso en kg
                            </label>
                            <input class='form-control' type='number' name=peso id='peso' placeholder='0.00'
                              required name='weight' min='0' value='<?php echo isset($input['peso']) ? $input['peso'] : '0'?>' step='1' title='Weight'
                              pattern='^\d*(\.\d{0,2})?$'>
                            <p class="text-danger"><?php echo isset($errores['peso']) ? $errores['peso'] :''?></p>
                          </div>
                          <div class='col-2'>
                            <button type='submit' class='btn btn-primary' href="<?php echo isset($editar) ? "/account/edit-peso/$id_peso": '/account'?>">
                              Añadir
                            </button>
                          </div>
                        </div>
                      </div>
                    </form>

                  </div>

                </div>
              </div>
                 <?php if($pesos!=null){?>
              <div class='card bg-secondary mb-3'>
                <div class='card-header'>
                  <h4>Registros de Pesos</h4>
                </div>
                <div class='card-body'>
                    <?php
                     if(isset($pesos)) {    
                      
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
                        <td class='col-md-4'><?php echo $peso['peso']?></td>
                        <td class='col-md-4'><?php echo $peso['fecha']?></td>
                        <td class='col-md-4'>
                          <a class='btn btn-danger' href="account/deletePeso/<?php echo $peso['id_peso']?>">
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
              <div class='card bg-secondary mb-3'>
                <div class='card-header'>
                  <h4>Historial de Pesos</h4>
                </div>
                <div class='card-body justify-content-center'>
                  <div class='chart-bar'>
                    <canvas id='myChart'></canvas>
                  </div>
                </div>
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
        const datosPeso = {
            label: "Progresion peso",
            // Pasar los datos igualmente desde PHP
            data: <?php echo json_encode($pesos_chart) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de fondo
            borderColor: 'rgba(54, 162, 235, 1)', // Color del borde
            borderWidth: 1, // Ancho del borde
        };
        new Chart($grafica, {
            type: 'line', // Tipo de gráfica
            data: {
                labels: etiquetas,
                datasets: [
                    datosPeso,
                    // Aquí más datos...
                ]
            },
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
