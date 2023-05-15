<div class="my-3 text-left">
    <h2 class="mb-3 fw-6 text-capitalize">hi,Prueba <span class="wave noto">👋</span></h2>
    <h5>Let's get cooking good looking!</h5>
</div>

<div class="form p-5">
    <form action="/recipes" method="GET" autocomplete="off">
        <!-- Ingredients -->
        <div class="mb-5" id="ingredientsWrapper">
            <p class="fw-normal" style="text-align: left!important;">Enter the first query or ingredient before adding more.</p>
            <div class="px-5 field mb-3">
                <input type="text" class="border-0 me-2 form-control form-control-sm" name="ingredients" id="ingredients" 
                       placeholder="Enter query or ingredient" autofocus />

            </div>
        </div>
        <!-- Dish Type  -->
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
            <p class="fw-normal" style="text-align: left!important;">Diet Labels<span class="ms-1 small text-muted">(optional)</span>
                <a href="javascript:void(0)" id="diet" class="toggler small text-muted ms-2 bi bi-chevron-down"></a>
            </p>
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
            <p class="fw-normal" style="text-align: left!important;">Allergies / Restrictions<span class="ms-1 small text-muted">(optional)</span>
                <a href="javascript:void(0)" id="health" class="toggler small text-muted ms-2 bi bi-chevron-down"></a>
            </p>
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
            <p class="fw-normal" style="text-align: left!important;">Cuisine Type<span class="ms-1 small text-muted">(optional)</span>
                <a href="javascript:void(0)" id="cuisine" class="toggler small text-muted ms-2 bi bi-chevron-down"></a>
            </p>
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

