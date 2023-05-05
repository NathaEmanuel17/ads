<?= $this->extend('Dashboard/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo lang('Adverts.text_edit_images'); ?> - <?php echo $advert->title; ?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>

<style>
    /**
	* Para acompanhar o estilo dos inputs
	*/
    select {
        height: 50px !important;
    }

    #dataTable_filter .form-control {
        height: 30px !important;
    }

    /**
	* Criamos a classe .modal-xl que não tem nessa versão do bootstrap do template
	*/
    @media (min-width: 1200px) {
        .modal-xl {
            max-width: 1140px;
        }
    }
</style>

<?= $this->endSection() ?>

<!--Envio para o template principal o conteudo dessa view-->
<?= $this->section('content') ?>

<div class="container">
    <div class="row">

        <?php echo $this->include('Dashboard/Layout/_sidebar'); ?>

        <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-0">
            <!-- Edit Personal Info -->
            <div class="widget personal-info">
                <h3 class="widget-header user"><?php echo lang('Adverts.text_edit_images'); ?> - <?php echo $advert->title; ?></h3>

                <?php echo form_open_multipart(route_to('adverts.upload.my', $advert->id), hidden: $hiddens); ?>

                <div class="alert alert-info">
                    <?php echo lang('Adverts.text_images_info_upload') ?>
                </div>

                <!-- File chooser -->
                <div class="form-group choose-file">
                    <i class="fa fa-image text-center"></i>
                    <input type="file" name="images[]" multiple accept="image/*" class="form-control-file d-inline" id="input-file">
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-transparent btn-sm"><?php echo lang('App.btn_save') ?></button>

                <?php echo form_close(); ?>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php if (empty($advert->images)) : ?>

                        <div class="alert alert-warning">
                            <?php echo lang('Adverts.text_no_images') ?>
                        </div>

                    <?php else : ?>

                        <ul class="list-inline">

                            <?php foreach($advert->images as $image): ?>

                                <li class="list-inline-item">

                                    <?php echo form_open(route_to('adverts.delete.image', $image->image), ['id' => 'formDelete'], $hiddensDelete); ?>

                                        <button type="submit" class="btn bg-danger btn-main-sm d-block mx-auto mb-2"><i class="fa fa-trash fa-2x"></i></button>
                                    
                                    <?php echo form_close(); ?>

                                    <img class="img-fluid" width="80" src="<?php echo route_to('web.image', $image->image, 'small')?>" alt="<?php echo $advert->title; ?>">

                                </li>

                            <?php endforeach; ?>

                        </ul>
                        
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).on('click', '#formDelete', function(e) {

        e.preventDefault();
        
        var $form = $(this);

        Swal.fire({
            title:'<?php echo  lang('App.delete_confirmation'); ?>',
            text: '<?php echo  lang('App.info_delete_confirmation'); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?php echo  lang('App.btn_confirmed_delete'); ?>',
            cancelButtonText: '<?php echo  lang('App.btn_cancel'); ?>',
        }).then((result) => {
            if (result.isConfirmed) {
                $form.submit();
            }
        })
    })
</script>

<?= $this->endSection() ?>