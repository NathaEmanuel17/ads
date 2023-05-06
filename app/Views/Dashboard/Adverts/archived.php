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
						<div class="col-md-12">

                            <a href="<?php echo route_to('my.adverts'); ?>" class="btn btn-main-sm btn-outline-primary float-right mb-4"><?php echo lang('App.btn_back'); ?></a>
						
                        </div>
						<div class=" col-md-12">
							<table class="table table-borderless table-striped" id="dataTable">
								<thead>
									<tr>
										<th scope="col" class="all"><?php echo lang('Adverts.label_title'); ?></th>
										<th scope="col" class="none"><?php echo lang('Adverts.label_code'); ?></th>
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

<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>

<script src="https://cdn.datatables.net/v/bs4/dt-1.13.4/r-2.4.1/datatables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php echo $this->include('Dashboard/Adverts/Scripts/_datatable_all_archived') ?>
<?php echo $this->include('Dashboard/Adverts/Scripts/_recover_advert') ?>
<?php echo $this->include('Dashboard/Adverts/Scripts/_delete_advert.php') ?>

<script>
	function refreshCSRFToken(token) {

		$('[name="<?php echo csrf_token(); ?>"]').val(token);
		$('meta[name="<?php echo csrf_token(); ?>"]').attr('content', token);

	}
</script>


<?= $this->endSection() ?>