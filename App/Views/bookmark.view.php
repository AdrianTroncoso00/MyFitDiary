<?php if (isset($_SESSION['good'])) { ?>
    <div class="card bg-success">
        <div class="card-body">
            <p class="text-center"><?php echo $_SESSION['good'] ?></p>
        </div>
    </div>
<?php } ?>
<?php if (isset($_SESSION['bad'])) { ?>
    <div class="card bg-danger">
        <div class="card-body">
            <p class="text-center"><?php echo $_SESSION['bad'] ?></p>
        </div>
    </div>
<?php } ?>
<!-- Result -->
<div class="card col-12">
    <!-- Handle error -->
    <?php if (count($recetas) < 1) { ?>
        <div class="text-center">
            <div class="fs-1 fw-7 text-center mb-3">Nothing here!</div>
            <div class="fs-5">You haven't bookmarked any recipe so far.</div>
            <div class="fs-5 mb-5">Start browsing to have a list of your favorite recipes!</div>
            <a href="/recipe-search" class="fw-6 fs-4">Start now!</a>
        </div>
    <?php } else { ?>
        <div class="my-3 text-left">
            <h5>Bookmarked recipes:</h5>
        </div>
        <!-- Show result -->
        <div class="d-flex flex-wrap justify-content-evenly col-12">
            <?php foreach ($recetas as $key => $receta) { ?>

                <!-- Cards -->
                <div class="receta position-relative border-0 card col-3">
                    <!-- Card Image -->
                    <div class="card-body" data-toggle="modal" data-target="<?php echo '#recipe' . $key ?>">
                        <img class="card-img-top" src="<?php echo $receta['image'] ?>" alt="<?php echo $receta['label'] ?>">
                        <!-- Card Body -->
                        <div class="card-info" style="text-align: left!important;">
                            <!-- Title -->
                            <div class="h5 lh-sm card-text text-capitalize mb-2"><?php echo $receta['label'] ?></div>
                            <!-- Input Label -->
                            <small class="lh-1 text-uppercase d-flex flex-wrap">
                                <?php foreach ($receta['dietLabels'] as $dietLabel) { ?>
                                    <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $dietLabel ?></small>
                                <?php } ?>
                                <small class="fw-4 p-2 mb-1 me-1 form"><?php echo isset($receta['cuisinetype']) ? $receta['cuisinetype'] : '' ?></small>
                            </small>
                        </div>
                    </div>
                    <div class='card-footer d-flex align-items-center justify-content-center'>
                        <a href="/eliminar-receta-fav/<?php echo $receta['id_receta_fav']; ?>"><i class='fas fa-trash-alt'></i></a>
                        
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal" id="<?php echo 'recipe' . $key ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                <small class="fw-4 p-2 mb-1 me-1 form"><?php echo isset($receta['cuisineType']) ? $receta['cuisineType'] : '' ?></small>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php echo $pager->links()?>    
    <?php } ?>
</div>