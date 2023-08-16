<?= $this->extend('Manager/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo lang('Adverts.title_index'); ?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>

<link href="https://cdn.datatables.net/v/bs4/dt-1.13.4/r-2.4.1/datatables.min.css" rel="stylesheet" />


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
					<a href="<?php echo route_to('adverts.manager'); ?>" class="btn btn-main-sm btn-outline-info mb-4"><?php echo lang('App.btn_back'); ?></a>

					<table class="table table-borderless table-striped" id="dataTable">
						<thead>
							<tr>
								<th scope="col" class="all"><?php echo lang('Adverts.label_title'); ?></th>
								<th scope="col" class="all"><?php echo lang('Adverts.label_code'); ?></th>
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

<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>

<script src="https://cdn.datatables.net/v/bs4/dt-1.13.4/r-2.4.1/datatables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php echo $this->include('Manager/Adverts/Scripts/_datatable_all_archived') ?>
<?php echo $this->include('Manager/Adverts/Scripts/_recover_advert') ?>
<?php echo $this->include('Manager/Adverts/Scripts/_delete_advert.php') ?>

<script>
	function refreshCSRFToken(token) {

		$('[name="<?php echo csrf_token(); ?>"]').val(token);
		$('meta[name="<?php echo csrf_token(); ?>"]').attr('content', token);

	}
</script>


<?= $this->endSection() ?>