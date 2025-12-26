
<?= $this->extend('template/layout/main') ?>

<?= $this->section('content') ?>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- titilebar -->
    <div class="flex items-center justify-between">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h1 class="h3 mb-0"><?= $page ?? '' ?></h1>
                
            </div>
        </div>
    </div><!-- closee titilebar -->

    <!-- body -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-4">
         <h1>404</h1>
        <h2>The page you are looking for doesn't exist.</h2>
        <a class="btn" href="<?=base_url('dashboard');?>">Back to home</a>
        <img src="<?=base_url('public/assets/img/not-found.svg');?>" class="img-fluid py-5" alt="Page Not Found">
</div><!-- body -->
<?= $this->endSection(); ?>


