<?= $this->extend('Dashboard/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo lang('Adverts.text_edit_images'); ?>

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
                <h3 class="widget-header user"><?php echo lang('Adverts.text_edit_images'); ?></h3>

                <?php echo form_open_multipart(route_to('adverts.upload.my', $advert->id), hidden: $hiddens); ?>

                    <div class="alert alert-info">
                        <?php echo lang('Adverts.text_images_info_upload')?>
                    </div>

                    <!-- File chooser -->
                    <div class="form-group choose-file">
                        <i class="fa fa-image text-center"></i>
                        <input type="file" name="images[]" multiple accept="image/*" class="form-control-file d-inline" id="input-file">
                    </div>
                    
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-transparent btn-sm"><?php echo lang('App.btn_save')?></button>
                
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>


<?= $this->endSection() ?>