<div class="modal" id="<?php echo $nombre ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
                            <a href="javascript:void(0)" id="tag" class="toggler small text-muted ms-2 bi bi-chevron-down"></a>
                        </div>
                        <div class="mb-4 py-2 border-top border-bottom hide">
                            <!-- Cuisine Tags -->
                            <div class="mb-1">
                                <div class="lh-sm small text-muted">Cuisine Type:</div>
                                <small class="ps-2 lh-1 text-uppercase d-flex flex-wrap">
                                    <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $infoComida['cuisineType'] ?></small>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

