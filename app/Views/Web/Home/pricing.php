<?= $this->extend('Web/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo $title ?? ''; ?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>


<?= $this->endSection() ?>

<!--Envio para o template principal o conteudo dessa view-->
<?= $this->section('content') ?>

<section class="popular-deals section bg-gray">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h2><?php echo $title ?? 'Conheça os nossos planos' ?></h2>
                </div>
            </div>
        </div>
        <div class="row">

            <?php if (empty($plans)) : ?>
                <div class="col-lg-12">
                    <div class="alert alert-info text-center">No momento não há Planos disponiveis</div>
                </div>
            <?php else : ?>

                <?php foreach ($plans as $plan) : ?>

                    <div class="col-md-3">
                        <!-- product card -->
                        <div class="product-item bg-light">
                            <div class="card">
                                <div class="thumb-content">
                                    
                                </div>
                                <div class="card-body">
                                    <h4 class="card-title"><a href="<?php echo route_to('choice', $plan->id)?>"><?php echo $plan->name?></a></h4>
                                    <ul class="list-inline product-meta">
                                        <li class="list-inline-item">
                                            <?php if($plan->is_highlighted): ?>
                                                <p class="text-primary">Uma das melhores opções</p>
                                            <?php endif; ?>
                                        </li>
                                        <hr>
                                        <li class="list-inline-item">
                                            <i class="fa fa-money fa-lg text-success"></i><?php echo $plan->details()?>
                                        </li>
                                    </ul>
                                    <p class="card-text"><?php echo $plan->description ?></p>
                                    <div class="product-ratings">
                                        <ul class="list-inline">
                                            <li class="list-inline-item selected">Anúncios permitidos <?php echo $plan->adverts()?></li>
                                        </ul>
                                    </div>
                                                
                                    <hr>
                                    
                                    <a href="<?php echo route_to('choice', $plan->id)?>" class="btn btn-main-sm mt-2"><?php echo lang('Plans.btn_choice') ?></a>

                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>



        </div>
    </div>
</section>
<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>


<?= $this->endSection() ?>