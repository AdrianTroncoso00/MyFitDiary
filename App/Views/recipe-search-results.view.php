
<?php if(isset($errorGuardar)){?>
    <div class="card bg-danger">
        <div class="card-body">
            <p><?php echo $errorGuardar ?></p>
        </div>
  </div>
<?php } ?>
<!-- Result -->
<div class="form col-12">
    <!-- Handle error -->
    <?php if (count($recetas) < 1) { ?>
        <div class="text-center">
            <div class="fs-1 fw-7 text-center mb-3">No result! <span class="noto">🙁</span></div>
            <div class="fs-5">We couldn't find recipes that match all of your requirements.</div>
            <div class="fs-5 mb-5">Please make sure there's no typo in your query.</div>
            <a href="/" class="fw-6 fs-4">Try Again?</a>
        </div>
    <?php } else { ?>
        <!-- Show result -->
        <div class="d-flex flex-wrap justify-content-evenly col-12">
            <?php foreach ($recetas as $receta) { ?>

                <!-- Cards -->
                <div class="position-relative border-0 card col-3" style="width: 13rem;">
                    <!-- Card Image -->

                    <img class="card-img-top" src="<?php echo $receta['image'] ?>" alt="<?php echo $receta['label'] ?>">
                    <!-- Card Body -->
                    <div class="card-body" style="text-align: left!important;">
                        <!-- Title -->
                        <div class="h5 lh-sm card-text text-capitalize mb-2"><?php echo $receta['label'] ?></div>
                        <!-- Input Label -->
                        <small class="lh-1 text-uppercase d-flex flex-wrap">


                            <?php foreach ($receta['dietLabels'] as $dietLabel) { ?>
                                <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $dietLabel ?></small>
                            <?php } ?>
                            <?php foreach ($receta['cuisineType'] as $cuisine) { ?>
                                <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $cuisine ?></small>
                            <?php } ?>
                        </small>
                    </div>
                    <button type="button" data-toggle="modal" data-target="<?php echo '#recipe' . $receta['position'] ?>">Abrir Receta</button>
                </div>
                <!-- Modal -->
                <div class="modal" id="<?php echo 'recipe' . $receta['position'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <!-- Modal body -->
                            <div class="modal-body p-5">
                                <!-- Visible -->
                                <div class="row mb-3">
                                    <!-- Image, Total Time, & Calories -->
                                    <div class="col-auto px-4">
                                        <!-- Image -->
                                        <div class="mb-3">
                                            <img src="<?php echo $receta['image'] ?>" alt="<?php echo $receta['label'] ?>">
                                            <p><?php echo strlen($receta['image'])?></p>
                                        </div>
                                        <!-- Total Time and Calories -->
                                        <div class="small mb-4 lh-lg d-flex flex-wrap justify-content-between">
                                            <!-- Time -->
                                            <small>
                                                <span class="bi bi-stopwatch"></span>
                                                <!-- Handle error if no data for totalTime -->
                                                <?php if ($receta['totalTime'] > 0) { ?>
                                                    <span><?php echo $receta['totalTime'] . ' Min' ?></span>
                                                <?php } else { ?>
                                                    No data.
                                                <?php } ?>
                                            </small>
                                            <!-- Calories -->
                                            <small>
                                                <span class="bi bi-fire"></span>
                                                <span><?php echo $receta['calories'] . ' kcal' ?></span>
                                            </small>
                                        </div>
                                        <!-- View recipe -->
                                        <div class="mb-3 text-center">
                                            <a href="<?php echo $receta['url'] ?>" target=”_blank”><button type="button" class=" yellow px-5 btn btn-primary">View Full Recipe <span class="bi bi-box-arrow-up-right"></span></button></a>
                                        </div>
                                    </div>
                                    <!-- Title, Source, & Ingredients -->
                                    <div class="col px-4">
                                        <!-- Title -->
                                        <div class="h3 pb-2 lh-sm text-capitalize"><?php echo $receta['label'] ?></div>
                                        <!-- Source -->

                                        <!-- Ingredients -->
                                        <div class="mb-3">
                                            <div class="lh-sm text-muted mb-2">Ingredients:</div>
                                            <small class="lh-1 text-lowercase">
                                                <ul class="list-group border-top border-bottom list-group-flush mb-3">
                                                    <?php foreach ($receta['ingredientLines'] as $ingredient) { ?>
                                                        <li class="list-group-item p-1"><?php echo $ingredient ?></li>
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
                                            <a href="javascript:void(0)" id="tag" class="toggler small text-muted ms-2 bi bi-chevron-down"></a>
                                        </div>
                                        <div class="mb-4 py-2 border-top border-bottom hide">
                                            <!-- Dish Tags -->

                                            <!-- Diet Tags -->
                                            <div class="mb-1">
                                                <div class="lh-sm small text-muted">Diet Type:</div>
                                                <small class="ps-2 lh-1 text-uppercase d-flex flex-wrap">
                                                    <?php foreach ($receta['dietLabels'] as $dietLabel) { ?>
                                                        <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $dietLabel ?></small>
                                                    <?php } ?>
                                                </small>
                                            </div>

                                            <!-- Cuisine Tags -->
                                            <div class="mb-1">
                                                <div class="lh-sm small text-muted">Cuisine Type:</div>
                                                <small class="ps-2 lh-1 text-uppercase d-flex flex-wrap">
                                                    <?php foreach ($receta['cuisineType'] as $cuisine) { ?>
                                                        <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $cuisine ?></small>
                                                    <?php } ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Bookmark -->
                                <form action="/add" method="POST">
                                    <div class="row center">
                                        <div>
                                            <input type="hidden" name="image" value="<?php echo $receta['image']?>">
                                            <input type="hidden" name="label" value="<?php echo $receta['label'] ?>">
                                            <input type="hidden" name="url" value="<?php echo $receta['url'] ?>">
                                            <input type="hidden" name="dietLabels" value="<?php echo implode(',',$receta['dietLabels']) ?>">
                                            <input type="hidden" name="ingredientLines" value="<?php echo implode(',',$receta['ingredientLines'])?>">
                                            <input type="hidden" name="calories" value="<?php echo $receta['calories'] ?>">
                                            <input type="hidden" name="totalTime" value="<?php echo $receta['totalTime'] ?>">
                                            <input type="hidden" name="cuisineType" value="<?php echo implode(',',$receta['cuisineType']) ?>">
    
                                        </div>
                                        <div class="col text-center">
                                            <input type="submit" name="bookmark" id="bookmark" value="Bookmark" class="px-5 btn btn-primary">
                                                   
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<div>
    <button type="button" class="btn btn-primary justify-content-center align-self-center">
        Mostrar Mas
    </button>
</div>

<!-- To top -->



<script>
    <script src="assets/js/toggle.js"></script>
<script src="assets/js/toTop.js"></script>
</script>