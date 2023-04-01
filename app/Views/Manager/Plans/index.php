<?= $this->extend('Manager/Layout/main'); ?>

<?= $this->section('title') ?>

<?php echo lang('Plans.title_index');?>

<?= $this->endSection() ?>


<!--Envio para o template principal os arquivos css e styles dessa view-->
<?= $this->section('styles') ?>

<link href="https://cdn.datatables.net/v/bs5/dt-1.12.0/r-2.3.0/datatables.min.css" rel="stylesheet"/>

<?= $this->endSection() ?>

<!--Envio para o template principal o conteudo dessa view-->
<?= $this->section('content') ?>

<div class="container-fluid pt-3">

  <div class="row">

    <div class="col-md-12">

      <div class="card shadow-lg">

        <div class="card-header">

          <h5><?php echo lang('Plans.title_index');?></h5>

          <button id="createPlanBtn" class="btn btn-success btn-sm float-end"><?php echo lang('App.btn_new');?></button>
        </div>

        <div class="card-body">

          <a class="btn btn-info btn-sm mt-2 mb-4" href="<?php echo route_to('plans.archived'); ?>"><?php echo lang('App.btn_all_archive');?></a>

          <table class="table table-borderless table-striped" id="dataTable">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col"><?php echo lang('Plans.label_code');?></th>
                <th scope="col"><?php echo lang('Plans.label_name');?></th>
                <th scope="col"><?php echo lang('Plans.label_is_highlighted');?></th>
                <th scope="col"><?php echo lang('Plans.label_details');?></th>
                <th scope="col"><?php echo lang('App.btn_actions');?></th>
              </tr>
            </thead>

          </table>

        </div>

      </div>

    </div>

  </div>

</div>

<?php echo $this->include('Manager/Plans/_modal_plan'); ?>

<?= $this->endSection() ?>

<!--Envio para o template principal os arquivos scrpits dessa view-->
<?= $this->section('scripts') ?>

<script src="https://cdn.datatables.net/v/bs5/dt-1.12.0/r-2.3.0/datatables.min.js"></script>

<script src="<?php echo site_url('manager_assets/mask/jquery.mask.min.js')?>"></script>
<script src="<?php echo site_url('manager_assets/mask/app.js')?>"></script>

<?php echo $this->include('Manager/Plans/Scripts/_datatable_all'); ?>
<?php echo $this->include('Manager/Plans/Scripts/_show_modal_to_create'); ?>
<?php echo $this->include('Manager/Plans/Scripts/_submit_modal_create_update'); ?>
<?php  echo $this->include('Manager/Plans/Scripts/_get_plan_info') ?>
<?php  echo $this->include('Manager/Plans/Scripts/_archive_plan') ?>

<script>

  function refreshCSRFToken(token) {

    $('[name="<?php echo csrf_token();?>"]').val(token);
    $('meta[name="<?php echo csrf_token();?>"]').attr('content', token);

  }

</script>


<?= $this->endSection() ?>