<?= $this->extend('backoffice/layout/main') ?>
<?= $this->section('content') ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard</h3>
                <p class="text-subtitle text-muted">
                    Overview of your discount engine.
                </p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb"
                    class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">
                            Dashboard
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">

        <!-- Summary Cards -->
        <div class="row">

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body px-4 py-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon purple">
                                <i class="bi bi-bag"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted">
                                    Total Products
                                </h6>
                                <h6 class="font-extrabold mb-0">
                                    <?= esc($totalProducts ?? 0) ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body px-4 py-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon blue">
                                <i class="bi bi-collection"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted">
                                    Categories
                                </h6>
                                <h6 class="font-extrabold mb-0">
                                    <?= esc($totalCategories ?? 0) ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body px-4 py-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon green">
                                <i class="bi bi-tags"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted">
                                    Total Campaigns
                                </h6>
                                <h6 class="font-extrabold mb-0">
                                    <?= esc($totalCampaigns ?? 0) ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body px-4 py-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon blue">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted">
                                    Total Orders
                                </h6>
                                <h6 class="font-extrabold mb-0">
                                    <?= esc($totalOrders ?? 0) ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h4>Quick Actions</h4>
            </div>

            <div class="card-body">
                <p class="text-muted">
                    Manage products and discount campaigns.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= route_to('backofficeproducts.products') ?>"
                        class="btn btn-primary">
                        <i class="bi bi-bag me-1"></i>
                        Manage Products
                    </a>

                    <a href="<?= route_to('backofficeproducts.category') ?>"
                        class="btn btn-outline-primary">
                        <i class="bi bi-collection me-1"></i>
                        Manage Categories
                    </a>

                    <a href="<?= route_to('backoffice.campaign') ?>"
                        class="btn btn-outline-success">
                        <i class="bi bi-tags me-1"></i>
                        Manage Campaigns
                    </a>

                    <a href="<?= route_to('cart.index') ?>"
                        class="btn btn-outline-warning">
                        <i class="bi bi-cart3 me-1"></i>
                        Test Cart
                    </a>
                </div>
            </div>
        </div>

    </section>
</div>

<?= $this->endSection() ?>