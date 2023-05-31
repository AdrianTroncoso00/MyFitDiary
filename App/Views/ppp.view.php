

<?php if (isset($mealPlanSemanal)) { ?>
    <?php foreach ($mealPlanSemanal as $dia => $comida) { ?>
        <div class="content-body col-12 d-flex justify-content-around">
            <div class="card row-cols-lg-6 col-lg-6 col-md-6 d-flex align-items-center">
                <div class="content col-10">
                    <div class="d-flex flex-row align-items-center justify-content-between">
                        <a><i class="fa-solid fa-square-caret-left"></i></a>
                        <p><?php echo $dia ?></p>
                        <a><i class="fa-solid fa-square-caret-right"></i></a>
                    </div>
                    <?php foreach ($comida as $nombre => $infoComida) { ?>
                        <div class="box col-12 col-lg-12">
                            <div class="box-header title d-flex flex-row align-items-start justify-content-between col-12">
                                <div class="box-header">
                                    <h4 class="encabezado4"><?php echo strtoupper($nombre) ?></h4>
                                </div>
                            </div>
                            <div class="box-content">
                                <?php foreach ($infoComida as $key=>$info) { ?>
                                    <div class="receta-box d-flex align-items-center justify-content-between">

                                        <div class="td-img">
                                            <img class="img" 
                                                 src="<?php echo isset($info['image']) ? $info['image'] : '' ?>">
                                        </div>
                                        <div class="td-descript align-self-center">
                                            <p class="label"><?php echo isset($info['label']) ? $info['label'] : '' ?></p>
                                            <span><?php echo isset($info['calorias']) ? $info['calorias'] . ' kcal' : '' ?> </span>

                                        </div>

                                        <div class="td-buttons">
                                            <i class="fa-solid fa-eye" data-toggle="modal" data-target="<?php echo '#' . $nombre . $key ?>"></i>
                                        </div>


                                    </div>
                                    <div class="modal" id="<?php echo $nombre . $key ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <!--Modal body--> 
                                                <div class="modal-body p-5">
                                                    <!--Visible--> 
                                                    <div class="row mb-3">
                                                        <!--Image, Total Time, & Calories--> 
                                                        <div class="col-auto px-4">
                                                            <!--Image--> 
                                                            <div class="mb-3">
                                                                <img src="<?php echo $info['image'] ?>" alt="<?php echo $info['label'] ?>">
                                                            </div>
                                                            <!--Total Time and Calories--> 
                                                            <div class="small mb-4 lh-lg d-flex flex-wrap justify-content-between">
                                                                Time: 
                                                                <span><?php echo $info['totalTime'] . ' Min' ?></span>
                                                                <!--Calories--> 
                                                                <small>
                                                                    <span class="bi bi-fire"></span>
                                                                    <span><?php echo $info['calorias'] . ' kcal' ?></span>
                                                                </small>
                                                            </div>
                                                            <!--View recipe--> 
                                                            <div class="mb-3 text-center">
                                                                <a href="<?php echo $info['url'] ?>" target=”_blank”><button type="button" class=" yellow px-5 btn btn-primary">View Full Recipe <span class="bi bi-box-arrow-up-right"></span></button></a>
                                                            </div>
                                                        </div>
                                                        <!--Title, Source, & Ingredients--> 
                                                        <div class="col px-4">
                                                            <!--Title--> 
                                                            <div class="h3 pb-2 lh-sm text-capitalize"><?php echo $info['label'] ?></div>
                                                            <!--Source 
                                                            
                                                            Ingredients -->
                                                            <div class="mb-3">
                                                                <div class="lh-sm text-muted mb-2">Ingredients:</div>
                                                                <small class="lh-1 text-lowercase">
                                                                    <ul class="list-group border-top border-bottom list-group-flush mb-3">
                                                                        <?php foreach ($info['ingredientes'] as $ingredient) { ?>
                                                                            <li class="list-group-item p-1"><?php echo $ingredient['stringIngrediente'] ?></li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </small>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <!--Expanded Tags--> 
                                                    <div class="row mb-3 px-4">
                                                        <div class="col-12 px-0">
                                                            <div class="lh-base text-muted py-2">Show Tags
                                                                <a href="javascript:void(0)" id="tag" class="toggler small text-muted ms-2 bi bi-chevron-down"></a>
                                                            </div>
                                                            <div class="mb-4 py-2 border-top border-bottom hide">

                                                                <!--                                                            Cuisine Tags -->
                                                                <div class="mb-1">
                                                                    <div class="lh-sm small text-muted">Cuisine Type:</div>
                                                                    <small class="ps-2 lh-1 text-uppercase d-flex flex-wrap">
                                                                        <small class="fw-4 p-2 mb-1 me-1 form"><?php echo $info['cuisineType'] ?></small>
                                                                    </small>
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




                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
