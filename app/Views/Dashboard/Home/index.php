<?= $this->extend('Dashboard/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo $title ?? ''; ?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>


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
					<h3 class="widget-header"><?php echo lang('App.sidebar.dashboard.dashboard'); ?></h3>

					<div class="card-deck">

						<div class="card">
							<div class="card-body text-center">
								<i class="fa fa-database text-primary fa-2x mb-2"></i>
								<p class="card-text"><?php echo lang('Adverts.text_total_adverts'); ?></p><br />
								<span class="badge badge-primary p-2 px-3 mt-2"><?php echo $totalUSerAdverts;?></span>
							</div>
						</div>

						<div class="card">
							<div class="card-body text-center">
								<i class="fa fa-check-circle text-success fa-2x mb-2"></i>
								<p class="card-text"><?php echo lang('Adverts.text_total_adverts_published'); ?></p><br />
								<span class="badge badge-success p-2 px-3 mt-2"><?php echo $totalPublishedAdverts ?></span>
							</div>
						</div>

						<div class="card">
							<div class="card-body text-center">
								<i class="fa fa-lock text-warning fa-2x mb-2"></i>
								<p class="card-text"><?php echo lang('Adverts.text_total_waiting_approval'); ?></p><br />
								<span class="badge badge-warning p-2 px-3 mt-2"><?php echo $totalUserAdvertsWaitingApproval ?></span>
							</div>
						</div>

						<div class="card">
							<div class="card-body text-center">
								<i class="fa fa-archive text-info fa-2x mb-2"></i>
								<p class="card-text"><?php echo lang('Adverts.text_total_archive'); ?></p><br />
								<span class="badge badge-primary p-2 px-3 mt-2"><?php echo $totalUserArchivedAdverts?></span>
							</div>
						</div>

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


<?= $this->endSection() ?>