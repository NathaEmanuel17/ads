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
                    <h2><?php echo $title ?? 'Anúncios recentes' ?></h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quas, magnam.</p>
                </div>
            </div>
        </div>

        <div class="row justity-content-center">

            <?php if (empty($adverts)) : ?>
                <div class="alert alert-info mx-auto">Não há anuncios cadastrados</div>
            <?php else : ?>

                <?php foreach ($adverts as $advert) : ?>

                    <div class="col-6 col-md-2 pl-1 pr-1 mb-2">

                        <div class="card h-100">

                            <div class="thumb-content mx-auto d-block">

                                <a href="<?php echo route_to('adverts.detail', $advert->code) ?>">
                                    <?php echo $advert->image(classImage: 'card-img-top', sizeImage: 'small'); ?>
                                </a>

                            </div>

                            <div class="card-body">

                                <p class="card-tile">
                                    <a href="<?php echo route_to('adverts.detail', $advert->code) ?>"></a>
                                    <?php echo word_limiter($advert->title, 5); ?>
                                </p>

                                <p class="card-text text-primary"><strong><?php echo $advert->price() ?></strong></p>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

                <div class="col-md-12 mt-4">

                    <div>
                        <?php echo $pager->links(); ?>
                    </div>

                </div>

            <?php endif; ?>
            <!-- offer 01 -->

        </div>
    </div>
</section>
<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>


<?= $this->endSection() ?>