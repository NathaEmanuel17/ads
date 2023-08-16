<?= $this->extend('Manager/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo lang('Adverts.text_edit_images'); ?> - <?php echo $advert->title; ?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>


<?= $this->endSection() ?>

<!--Envio para o template principal o conteudo dessa view-->
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">

            <div class="card shadow-lg">

                <div class="card-header">
                    <a href="<?php echo route_to('adverts.manager'); ?>" class="btn btn-main-sm btn-outline-info "><?php echo lang('App.btn_back'); ?></a>
                </div>
                <?php if (empty($advert->images)) : ?>

                    <div class="alert alert-warning">
                        <?php echo lang('Adverts.text_no_images') ?>
                    </div>

                <?php else : ?>

                    <ul class="list-inline p-2">

                        <?php foreach ($advert->images as $image) : ?>

                            <li class="list-inline-item border p-2">

                                <img class="img-fluid" width="200" src="<?php echo route_to('web.image', $image->image, 'small') ?>" alt="<?php echo $advert->title; ?>">

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>


<?= $this->endSection() ?>