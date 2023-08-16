<?= $this->extend('Manager/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo lang('Adverts.title_index'); ?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>

<link href="https://cdn.datatables.net/v/bs4/dt-1.13.4/r-2.4.1/datatables.min.css" rel="stylesheet" />

<style>

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

<div class="container-fluid">
	<!-- Row Start -->
	<div class="row">

		<div class="col-md-12">

			<div class="card shadow-lg">
				<div class="card-header">
					<h5><?php echo lang('Adverts.title_index'); ?></h5>
				</div>

				<div class="card-body">
					<a href="<?php echo route_to('adverts.manager.archived'); ?>" class="btn btn-main-sm btn-outline-info mb-4"><?php echo lang('App.btn_all_archive'); ?></a>

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
	<!-- Row End -->
</div>

<?php echo $this->include('Manager/Adverts/_modal_advert') ?>

<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>

<script src="https://cdn.datatables.net/v/bs4/dt-1.13.4/r-2.4.1/datatables.min.js"></script>

<script src="<?php echo site_url('manager_assets/mask/jquery.mask.min.js') ?>"></script>
<script src="<?php echo site_url('manager_assets/mask/app.js') ?>"></script>

<?php echo $this->include('Manager/Adverts/Scripts/_datatable_all') ?>
<?php echo $this->include('Manager/Adverts/Scripts/_get_manager_advert') ?>
<?php echo $this->include('Manager/Adverts/Scripts/_show_modal_to_create') ?>
<?php echo $this->include('Manager/Adverts/Scripts/_submit_modal_create_update') ?>
<?php echo $this->include('Manager/Adverts/Scripts/_viacep') ?>
<?php echo $this->include('Manager/Adverts/Scripts/_archive_advert') ?>

<script>
	function refreshCSRFToken(token) {

		$('[name="<?php echo csrf_token(); ?>"]').val(token);
		$('meta[name="<?php echo csrf_token(); ?>"]').attr('content', token);

	}
</script>


<?= $this->endSection() ?>