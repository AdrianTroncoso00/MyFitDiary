<div class="my-3 text-left">
    <h2 class="mb-3 fw-6 text-capitalize">hi,<?php echo $_SESSION['usuario']['nombre_completo']?> <span class="wave noto">👋</span></h2>
    <h5>Let's get cooking good looking!</h5>
</div>

<div class="form p-5">
    <form action="/recipes" method="get" autocomplete="off">
        <!-- Ingredients -->
        <div class="mb-5" id="ingredientsWrapper">
            <p class="fw-normal" style="text-align: left!important;">Enter the first query or ingredient before adding more.<span class="ms-1 small text-muted">(optional)</span></p>
            <div class="px-5 field mb-3">
                <input type="text" class="border-0 me-2 form-control form-control-sm" name="ingredients[]"
                       placeholder="Enter query or ingredient" autofocus />
                <span onclick="addField(this, 'ingredients')" class=" btn px-3 btn-primary">+</span>
               <span onclick="removeField(this)" class=" btn px-3 btn-primary">−</span>

            </div>
        </div>
        <!-- Excluded element -->
        <div class="mb-5" id="excludedWrapper">
            <p class="fw-normal" style="text-align: left!important;">Enter the first query or ingredient before adding more.<span class="ms-1 small text-muted">(optional)</span></p>
            <div class="px-5 field mb-3">
                <input type="text" class="border-0 me-2 form-control form-control-sm" name="excluded[]" value=""
                       placeholder="Enter query or ingredient" autofocus />
                <span onclick="addField(this, 'excluded')" class=" btn px-3 btn-primary">+</span>
               <span onclick="removeField(this)" class=" btn px-3 btn-primary">−</span>

            </div>
        </div>
        <!-- Calories -->
        <div class="mb-5" id="caloriesMinWrapper">
            <label for="minCalories" style="text-align: left!important;">Enter the Min Calories<span class="ms-1 small text-muted">(optional)</span></label>
            <input type="number" class="border-0 me-2 form-control form-control-sm" name="minCalories" value=" "
                       placeholder="Enter min calories"/>
        </div>
        <div class="mb-5" id="caloriesMaxWrapper">
            <label for="maxCalories" style="text-align: left!important;">Enter the Max Calories<span class="ms-1 small text-muted">(optional)</span></label>
            <input type="number" class="border-0 me-2 form-control form-control-sm" name="maxCalories" value=" "
                       placeholder="Enter max calories" />
        </div>
        <!-- Time -->
        <div class="mb-5" id="timeMinWrapper">
            <label for="time" style="text-align: left!important;">Enter Min Time cook<span class="ms-1 small text-muted">(optional)</span></label>
            <input type="number" class="border-0 me-2 form-control form-control-sm" name="timeMin"
                       placeholder="Enter time"/>
        </div>
        <div class="mb-5" id="timeMaxWrapper">
            <label for="time" style="text-align: left!important;">Enter Max Time cook<span class="ms-1 small text-muted">(optional)</span></label>
            <input type="number" class="border-0 me-2 form-control form-control-sm" name="timeMax"
                       placeholder="Enter time"/>
        </div>
        <!-- Dish Type  -->
        <div class="mb-5" id="mealTypeWrapper">
            <p class="fw-normal" style="text-align: left!important;">Nombre Comidas<span class="ms-1 small text-muted">(optional)</span> </p>
            <div id="mealWrapper" class="hide">
                <div class="px-5 small d-flex flex-wrap justify-content-between mb-5">
                    <?php foreach($nombreComidas as $nombre_comida){ ?>
                    <div class="mb-3 col-sm-6">
                        <input type="checkbox" name="mealType[]" value="<?php echo $nombre_comida['nombre_comida'] ?>">
                        <label class="form-check-label" for="<?php echo $nombre_comida['nombre_comida']?>"><?php echo $nombre_comida['nombre_comida']?></label>
                    </div>
                    <?php } ?>
                </div>
            </div>
        <div class="mb-5" id="dishTypeWrapper">
            <p class="fw-normal" style="text-align: left!important;">Dish Type<span class="ms-1 small text-muted">(optional)</span> </p>
            <div id="dishListWrapper" class="hide">
                <div class="px-5 small d-flex flex-wrap justify-content-between mb-5">
                    <?php foreach($tipoComida as $nombre_comida){ ?>
                    <div class="mb-3 col-sm-6">
                        <input type="checkbox" name="dishType[]" value="<?php echo $nombre_comida['nombre_tipo_comida'] ?>">
                        <label class="form-check-label" for="<?php echo $nombre_comida['nombre_tipo_comida']?>"><?php echo $nombre_comida['nombre_tipo_comida']?></label>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Diet Labels  -->
        <div class="mb-5" id="dietLabelsWrapper">
            <p class="fw-normal" style="text-align: left!important;">Diet Labels<span class="ms-1 small text-muted">(optional)</span></p>
            <div id="dietListWrapper" class="hide">
                <div class="px-5 small d-flex flex-wrap justify-content-between mb-5">
                    <?php foreach ($dietas as $dieta) { ?>
                    <div class="mb-3 col-sm-6">
                        <input type="checkbox" name="dietLabels[]" value="<?php echo $dieta['nombre_dieta']?>" id="<?php echo $dieta['nombre_dieta']?>">
                        <label class="form-check-label" for="<?php echo $dieta['nombre_dieta']?>"><?php echo $dieta['nombre_dieta']?></label>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Health Labels  -->
        <div class="mb-5" id="healthLabelsWrapper">
            <p class="fw-normal" style="text-align: left!important;">Allergies / Restrictions<span class="ms-1 small text-muted">(optional)</span></p>
            <div id="healthListWrapper" class="hide">
                <div class="px-5 small d-flex flex-wrap justify-content-between mb-5" style="text-align: left!important;">
                   
                    <div class="flex-column">
                        <?php foreach ($alergenos as $alergeno) {?>
                        <div class="mb-3 col-sm-6">
                            <input type="checkbox" name="healthLabels[]" value="<?php echo $alergeno['nombre_alergeno']?>" id="<?php echo $alergeno['nombre_alergeno']?>">
                            <label class="form-check-label" for="<?php echo $alergeno['nombre_alergeno']?>"><?php echo $alergeno['nombre_alergeno']?></label>
                        </div>
                        <?php } ?>
                    </div>
                   
                </div>
            </div>
        </div>
        <!-- Cuisine Type Labels  -->
        <div class="mb-5" id="cuisineTypeWrapper">
            <p class="fw-normal" style="text-align: left!important;">Cuisine Type<span class="ms-1 small text-muted">(optional)</span></p>
            <div id="cuisineListWrapper" class="hide">
                <div class="px-5 small d-flex flex-wrap justify-content-between mb-5" style="text-align: left!important;">
                    
                    <div class="flex-column">
                        <?php foreach($tipoCocina as $cocina){ ?>
                        <div class="mb-3 col-sm-6">
                            <input type="checkbox" name="cuisineType[]" value="<?php echo $cocina['nombre_tipo_cocina']?>" id="<?php echo $cocina['nombre_tipo_cocina']?>">
                            <label class="form-check-label" for="<?php echo $cocina['nombre_tipo_cocina']?>"><?php echo $cocina['nombre_tipo_cocina']?></label>
                        </div>
                        <?php } ?>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- Submit -->
        <div class="text-center">
            <input type="submit" id="search" value="Show Recipes" class="px-5 py-3 btn btn-primary"/>
        </div>
    </form>
</div>

 <script src="assets/js/dynamicTextField.js"></script>