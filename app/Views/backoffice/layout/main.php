<!DOCTYPE html>
<html lang="en">

<?= $this->include('backoffice/partials/inc_head') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<body>
    <div id="app">
        <?php if (session()->getFlashdata('message')): ?>
            <input type="hidden" id="message" value="<?= esc(session()->getFlashdata('message')) ?>">
            <input type="hidden" id="message_type" value="<?= esc(session()->getFlashdata('message_type')) ?>">
        <?php endif; ?>
        <?= $this->include('backoffice/partials/inc_sidebar') ?>

        <div id="main" class='layout-navbar'>
            <!-- PAGE Navbar -->
            <?= $this->include('backoffice/partials/inc_navbar') ?>

            <div id="main-content">
                <!-- PAGE CONTENT -->
                <?= $this->renderSection('content') ?>
                <!-- PAGE FOOTER -->
                <?= $this->include('backoffice/partials/inc_footer') ?>
            </div>
        </div>

    </div>
    <!-- MAIN SCRIPT -->




    <?= $this->include('backoffice/partials/inc_script') ?>
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script src="<?= base_url('assets/js/csrf.js') ?>"></script>
    <?= $this->renderSection('js') ?>
    
</body>

</html>