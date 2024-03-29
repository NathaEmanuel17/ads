<?= $this->extend('Dashboard/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo lang('Adverts.title_index'); ?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>

<link href="https://cdn.datatables.net/v/bs4/dt-1.13.4/r-2.4.1/datatables.min.css" rel="stylesheet" />

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
<section class="dashboard section">
	<!-- Container Start -->
	<div class="container">
		<!-- Row Start -->
		<div class="row">

			<?php echo $this->include('Dashboard/Layout/_sidebar'); ?>

			<div class="col-md-10 offset-md-1 col-lg-8 offset-lg-0">
				<!-- Recently Favorited -->
				<div class="widget dashboard-container my-adslist">
					<h3 class="widget-header"><?php echo lang('Adverts.title_index'); ?></h3>
					<div class="row">

						<?php if (user_reached_adverts_limit()) : ?>

							<div class="alert alert-info small">

								Você já cadastrou <?php echo count_all_user_adverts() ?> anúncios. Para continuar cadastrando, você precisará migrar de Plano.
								<a href="<?php echo route_to('pricing') ?>" class="btn btn-sm btn-info mt-3">Quero Migrar</a>

							</div>

						<?php else : ?>
							<div class="col-md-12">
								<button type="button" id="createAdvertBtn" class="btn btn-main-sm add-button float-right mb-4"> + <?php echo lang('App.btn_new'); ?></button>
							</div>
						<?php endif; ?>

						<div class=" col-md-12">
							<a href="<?php echo route_to('my.archived.adverts'); ?>" class="btn btn-main-sm btn-outline-info mb-4"><?php echo lang('App.btn_all_archive'); ?></a>
							<table class="table table-borderless table-striped" id="dataTable">
								<thead>
									<tr>
										<th scope="col"><?php echo lang('Adverts.label_image'); ?></th>
										<th scope="col" class="all"><?php echo lang('Adverts.label_title'); ?></th>
										<th scope="col" class="none"><?php echo lang('Adverts.label_code'); ?></th>
										<th scope="col" class="none text-center"><?php echo lang('Adverts.label_category'); ?></th>
										<th scope="col" class="all"><?php echo lang('Adverts.label_status'); ?></th>
										<th scope="col" class="none text-center"><?php echo lang('Adverts.label_address'); ?></th>

										<th scope="col"><?php echo lang('App.btn_actions'); ?></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Row End -->
	</div>
	<!-- Container End -->
</section>

<?php echo $this->include('Dashboard/Adverts/_modal_advert') ?>

<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>

<script src="https://cdn.datatables.net/v/bs4/dt-1.13.4/r-2.4.1/datatables.min.js"></script>

<script src="<?php echo site_url('manager_assets/mask/jquery.mask.min.js') ?>"></script>
<script src="<?php echo site_url('manager_assets/mask/app.js') ?>"></script>

<?php echo $this->include('Dashboard/Adverts/Scripts/_datatable_all') ?>
<?php echo $this->include('Dashboard/Adverts/Scripts/_get_my_advert') ?>
<?php echo $this->include('Dashboard/Adverts/Scripts/_show_modal_to_create') ?>
<?php echo $this->include('Dashboard/Adverts/Scripts/_submit_modal_create_update') ?>
<?php echo $this->include('Dashboard/Adverts/Scripts/_viacep') ?>
<?php echo $this->include('Dashboard/Adverts/Scripts/_archive_advert') ?>

<script>
	function refreshCSRFToken(token) {

		$('[name="<?php echo csrf_token(); ?>"]').val(token);
		$('meta[name="<?php echo csrf_token(); ?>"]').attr('content', token);

	}
</script>


<?= $this->endSection() ?>