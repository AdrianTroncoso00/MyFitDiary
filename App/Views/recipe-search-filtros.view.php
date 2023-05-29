<div class="my-3 text-left">
    <h2 class="mb-3 fw-6 text-capitalize">hi,<?php echo $_SESSION['usuario']['nombre_completo']?> <span class="wave noto">👋</span></h2>
    <h5>Let's get cooking good looking!</h5>
</div>

<div class="card col-10 align-self-center">
    <form class=" d-flex flex-column justify-content-center" action="/recipes" method="get" autocomplete="off">
        <!-- Ingredients -->
        <div class="query col-12" id="ingredientsWrapper">
            <p class="fw-normal" style="text-align: left!important;">Enter the first query or ingredient before adding more.<span class="ms-1 small text-muted">(optional)</span></p>
            <div class="col-7 d-flex align-items-center ">
                <input type="text" class="border-0 me-2 form-control form-control-sm" name="ingredients[]"
                       placeholder="Enter query or ingredient" autofocus />
                <span onclick="addField(this, 'ingredients')" class=" btn px-3 btn-primary">+</span>
               <span onclick="removeField(this)" class=" btn px-3 btn-primary">−</span>

            </div>
        </div>
        <!-- Excluded element -->
        <div class="query col-12 " id="excludedWrapper">
            <p class="fw-normal" style="text-align: left!important;">Enter the first query or ingredient before adding more.<span class="ms-1 small text-muted">(optional)</span></p>
            <div class="col-7 d-flex align-items-center justify-content-between">
                <input type="text" class="border-0 me-2 form-control form-control-sm" name="excluded[]" value=""
                       placeholder="Enter query or ingredient" autofocus />
                <span onclick="addField(this, 'excluded')" class=" btn px-3 btn-primary">+</span>
               <span onclick="removeField(this)" class=" btn px-3 btn-primary">−</span>

            </div>
        </div>
        <div class="query col-12 " id="excludedWrapper">
            <p class="fw-normal" style="text-align: left!important;">Enter the Min Calories<span class="ms-1 small text-muted">(optional)</span></p>
            <div class="col-7 d-flex align-items-center justify-content-between">
                <input type="number" class="border-0 me-2 form-control form-control-sm" name="excluded[]" value=""
                       placeholder="Enter query or ingredient" autofocus />
            </div>
        </div>
        <div class="query col-12 " id="excludedWrapper">
            <p class="fw-normal" style="text-align: left!important;">Enter the Min Calories<span class="ms-1 small text-muted">(optional)</span></p>
            <div class="col-7 d-flex align-items-center justify-content-between">
                <input type="number" class="border-0 me-2 form-control form-control-sm" name="excluded[]" value=""
                       placeholder="Enter query or ingredient" autofocus />
            </div>
        </div>
        
        <div class="query col-12" id="caloriesMaxWrapper">
            <label for="maxCalories" style="text-align: left!important;">Enter the Max Calories<span class="ms-1 small text-muted">(optional)</span></label>
            <input type="number" class="border-0 me-2 form-control form-control-sm" name="maxCalories" value=" "
                       placeholder="Enter max calories" />
        </div>
        <!-- Time -->
        <div class="query col-12" id="timeMinWrapper">
            <label for="time" style="text-align: left!important;">Enter Min Time cook<span class="ms-1 small text-muted">(optional)</span></label>
            <input type="number" class="border-0 me-2 form-control form-control-sm" name="timeMin"
                       placeholder="Enter time"/>
        </div>
        <div class="query col-12" id="timeMaxWrapper">
            <label for="time" style="text-align: left!important;">Enter Max Time cook<span class="ms-1 small text-muted">(optional)</span></label>
            <input type="number" class="border-0 me-2 form-control form-control-sm" name="timeMax"
                       placeholder="Enter time"/>
        </div>
        <!-- Dish Type  -->
        <div class="query col-12" id="mealTypeWrapper">
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
        <div class="query col-12" id="dishTypeWrapper">
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
        <div class="query col-12" id="dietLabelsWrapper">
            <p class="fw-normal" style="text-align: left!important;">Diet Labels<span class="ms-1 small text-muted">(optional)</span></p>
            <div id="dietListWrapper" class="hide">
                <div class="px-5 small d-flex flex-wrap justify-content-between mb-5">
                        <?php foreach ($dietas as $dieta) { ?>
                            <?php foreach ($dieta as $tipoDieta) { ?>
                                <div class="mb-3 col-sm-6">
                                    <input type="checkbox" name="dietLabels[]" value="<?php echo $tipoDieta['nombre_dieta']?>" id="<?php echo $tipoDieta['nombre_dieta']?>">
                                    <label class="form-check-label" for="<?php echo $tipoDieta['nombre_dieta']?>"><?php echo $tipoDieta['nombre_dieta']?></label>
                                </div>
                                
                           <?php } ?>
                        <?php } ?>
                </div>
            </div>
        </div>
        <!-- Health Labels  -->
        <div class="query col-12" id="healthLabelsWrapper">
            <p class="fw-normal" style="text-align: left!important;">Allergies / Restrictions<span class="ms-1 small text-muted">(optional)</span></p>
            <div id="healthListWrapper" class="hide">
                <div class="px-8 small d-flex flex-wrap justify-content-between mb-5" style="text-align: left!important;">
                    <?php foreach ($alergenos as $value) { ?>                        
                    <div class="d-flex flex-column">
                        <?php foreach ($value as $alergeno) {?>
                        <div class="mb-3 flex-column">
                            <input type="checkbox" name="healthLabels[]" value="<?php echo $alergeno['nombre_alergeno']?>" id="<?php echo $alergeno['nombre_alergeno']?>">
                            <label class="form-check-label" for="<?php echo $alergeno['nombre_alergeno']?>"><?php echo $alergeno['nombre_alergeno']?></label>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Cuisine Type Labels  -->
        <div class="query col-12" id="cuisineTypeWrapper">
            <p class="fw-normal" style="text-align: left!important;">Cuisine Type<span class="ms-1 small text-muted">(optional)</span></p>
            <div id="cuisineListWrapper" class="hide">
                <div class="px-8 small d-flex flex-wrap justify-content-between mb-5" style="text-align: left!important;">
                    <?php foreach ($tipoCocina as $value) { ?>                        
                    <div class="d-flex flex-column">
                        <?php foreach($value as $cocina){ ?>
                        <div class="mb-3 flex-column">
                            <input type="checkbox" name="cuisineType[]" value="<?php echo $cocina['nombre_tipo_cocina']?>" id="<?php echo $cocina['nombre_tipo_cocina']?>">
                            <label class="form-check-label" for="<?php echo $cocina['nombre_tipo_cocina']?>"><?php echo $cocina['nombre_tipo_cocina']?></label>
                        </div>
                        <?php } ?>
                    </div><!-- comment -->
                    <?php } ?>
                </div>
                </div>
            </div>
        </div>
        <!-- Submit -->
        <div class="form-footer d-flex align-items-center justify-content-center">
            <input type="submit" id="search" value="Show Recipes" class="px-5 py-3 btn btn-primary"/>
        </div>
    </form>
</div>

 <script src="assets/js/dynamicTextField.js"></script>