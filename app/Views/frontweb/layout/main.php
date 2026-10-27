<!DOCTYPE html>
<html lang="en">

<head>
    <!-- head Start -->
    <?= $this->include('frontweb/partials/inc_head') ?>
    <link rel="stylesheet" href="<?= base_url('assets/vendors/toastify/toastify.css') ?>">
    <!-- head End -->
</head>

<body>
    <!-- navbar Start -->
    <?= $this->include('frontweb/partials/inc_navbar') ?>
    <!-- navbar End -->

    <!-- PAGE CONTENT -->
    <?= $this->renderSection('content') ?>
    <!-- PAGE FOOTER -->

    <?= $this->include('frontweb/partials/inc_script') ?>
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script src="<?= base_url('assets/js/csrf.js') ?>"></script>
    <?= $this->renderSection('js') ?>
    

</body>

</html>