<?= $this->extend('backoffice/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Campaign</h3>
                <!-- <p class="text-subtitle text-muted">Navbar will appear in top of the page.</p> -->
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Campaign</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">List</h4>
                <button type="button" class="btn btn-sm btn-primary" id="btn_modal_add_campaign" data-bs-toggle="modal"
                    data-bs-target="#modal_add_campaign">
                    <i class="fa fa-plus"></i> Add Campaign
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <table class="table table-hover" id="table-campaign" style="width: 100%; white-space: nowrap;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Discount Rules</th>
                                <th>Amount</th>
                                <th>Percentage</th>
                                <th>Categoty</th>
                                <th>Threshold Amount</th>
                                <th>Max Discount Percentage</th>
                                <th>Point/Bath</th>
                                <th style="white-space: nowrap; width: 20%;">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Add -->
<div class="modal fade text-left" id="modal_add_campaign" tabindex="-1"
    role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Add Campaign</h4>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="form_add_campaign" enctype="multipart/form-data" method="post">
                    <div class="form-group">
                        <label>Campaign Name <code>*</code></label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Product Name" required>
                        <div class="invalid-feedback">
                            <i class="bx bx-radio-circle"></i>
                            Please enter valid information.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type">Category <code>*</code></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">-- Please Select Type --</option>
                            <option value="Coupon">Coupon</option>
                            <option value="On Top">On Top</option>
                            <option value="Seasonal">Seasonal</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter valid information.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sub_type">Discount Rules <code>*</code></label>
                        <select class="form-select" id="sub_type" name="sub_type" disabled required> 
                            <option value="">-- Please Select Sub Type --</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter valid information.
                        </div>
                    </div>

                    <!-- Fixed Amount -->
                    <div id="form_fixed_amount" style="display: none;">
                        <div class="form-group">
                            <label>Fixed Amount <code>*</code></label>
                            <input type="number" class="form-control" id="amount" name="amount"
                                placeholder="Price" min="0" step="any">
                        </div>
                    </div>

                    <!-- PERCENTAGE -->
                    <div id="form_percentage" style="display: none;">
                        <div class="form-group">
                            <label>Percentage <code>*</code></label>
                            <input type="number" class="form-control" id="percentage" name="percentage"
                                placeholder="Percentage" min="0" step="any">
                        </div>
                    </div>

                    <!-- CATEGORY_PERCENTAGE -->
                    <div id="form_category_percent" style="display: none;">
                        <div class="form-group">
                            <label for="p_category">Product Category <code>*</code></label> <small class="text-muted">To add a product category, <a href="<?= route_to('productsetting.category') ?>" target="_blank">click here.</a> </small>
                            <select class="form-select" id="p_category" name="p_category">
                                <option value="">-- Select Category --</option>
                            </select>
                            <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                Please enter valid information.
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Percentage <code>*</code></label>
                            <input type="number" class="form-control" id="category_percentage" name="category_percentage"
                                placeholder="Percentage" min="0" step="any">
                        </div>
                    </div>

                    <!-- POINTS -->
                    <div id="form_points_per_currency" style="display: none;">
                        <div class="form-group">
                            <label>Point/Bath <code>*</code></label>
                            <input type="number" class="form-control" id="points_per_currency" name="points_per_currency"
                                placeholder="Point/Bath" min="0" step="any">
                        </div>
                    </div>

                    <!-- THRESHOLD_FIXED -->
                    <div id="form_threshold_fixed" style="display: none;">
                        <div class="form-group">
                            <label>Threshold amount <code>*</code></label>
                            <input type="number" class="form-control" id="threshold_amount" name="threshold_amount"
                                placeholder="Threshold amount" min="0" step="any">
                        </div>
                        <div class="form-group">
                            <label>Discount amount <code>*</code></label>
                            <input type="number" class="form-control" id="discount_amount" name="discount_amount"
                                placeholder="Discount amount" min="0" step="any">
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary"
                    data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>
                <button type="button" class="btn btn-primary ml-1" id="btn-submit-form-add-campaign">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>



<script src="<?= base_url('assets/vendors/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/campaign.js') ?>"></script>
<?= $this->endSection() ?>