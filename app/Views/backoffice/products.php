<?= $this->extend('backoffice/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Products</h3>
                <!-- <p class="text-subtitle text-muted">Navbar will appear in top of the page.</p> -->
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Products</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">List</h4>
                <button type="button" class="btn btn-sm btn-primary" id="btn_modal_add_products" data-bs-toggle="modal"
                    data-bs-target="#inlineForm">
                    <i class="fa fa-plus"></i> Add Product
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <table class="table table-hover" id="table-products" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th class="text-end">Price</th>
                                <th class="text-end" style="white-space: nowrap; width: 20%;">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal Add -->
<div class="modal fade text-left" id="inlineForm" tabindex="-1"
    role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Add Product</h4>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="form_add_product" enctype="multipart/form-data" method="post">

                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" class="image-resize-filepond" id="p_image" name="p_image">
                    </div>
                    <div class="form-group">
                        <label>Product Name <code>*</code></label>
                        <input type="text" class="form-control" id="p_name" name="p_name"
                            placeholder="Product Name" required>
                        <div class="invalid-feedback">
                            <i class="bx bx-radio-circle"></i>
                            Please enter valid information.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="p_category">Category <code>*</code></label> <small class="text-muted">To add a product category, <a href="<?= route_to('backofficeproducts.category') ?>" target="_blank">click here.</a> </small>
                        <select class="form-select" id="p_category" name="p_category">
                            <option value="">-- Select Category --</option>
                        </select>
                        <div class="invalid-feedback">
                            <i class="bx bx-radio-circle"></i>
                            Please enter valid information.
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Price <code>*</code></label>
                        <input type="number" class="form-control" id="p_price" name="p_price"
                            placeholder="Price" min="0" step="any">
                        <div class="invalid-feedback">
                            <i class="bx bx-radio-circle"></i>
                            Please enter valid information.
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
                <button type="button" id="btn-submit-form-add-product" class="btn btn-primary ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Confirm</span>
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade text-left" id="modal_edit_product" tabindex="-1"
    role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Add Product</h4>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="form_edit_product" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="p_edit_id" id="p_edit_id">
                    <input type="hidden" name="p_edit_oldimg" id="p_edit_oldimg">
                    <div class="form-group">
                        <label>Product Image</label>

                        <div class="text-center" id="change_image">
                            <div class="position-relative d-inline-block mt-2" style="max-width:70%;">
                                <img id="current-image"
                                    src="<?= base_url('assets/images/unity/no_image_1-1.jpeg') ?>"
                                    data-default="<?= base_url('assets/images/unity/no_image_1-1.jpeg') ?>"
                                    class="d-block rounded w-100"
                                    style="aspect-ratio: 1 / 1; object-fit: cover;"
                                    alt="รูปสินค้า">

                                <button type="button" id="btn-change-image"
                                    class="btn btn-sm btn-light shadow position-absolute rounded-circle"
                                    style="right: 8px; bottom: 8px; width: 36px; height: 36px; padding: 0;"
                                    title="แก้ไขรูป">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                            </div>
                        </div>

                        <input type="file" id="gallery-input" name="p_edit_image" accept="image/*" style="display:none">
                    </div>
                    <div class="form-group">
                        <label>Product Name <code>*</code></label>
                        <input type="text" class="form-control" id="p_edit_name" name="p_edit_name"
                            placeholder="Product Name" required>
                        <div class="invalid-feedback">
                            <i class="bx bx-radio-circle"></i>
                            Please enter valid information.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="p_category">Category <code>*</code></label> <small class="text-muted">To add a product category, <a href="<?= route_to('backofficeproducts.category') ?>" target="_blank">click here.</a> </small>
                        <select class="form-select" id="p_edit_category" name="p_edit_category">
                            <option value="">-- Select Category --</option>
                        </select>
                        <div class="invalid-feedback">
                            <i class="bx bx-radio-circle"></i>
                            Please enter valid information.
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Price <code>*</code></label>
                        <input type="number" class="form-control" id="p_edit_price" name="p_edit_price"
                            placeholder="Price" min="0" step="any">
                        <div class="invalid-feedback">
                            <i class="bx bx-radio-circle"></i>
                            Please enter valid information.
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
                <button type="button" id="btn-submit-form-edit-product" class="btn btn-primary ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Confirm</span>
                </button>
            </div>

        </div>
    </div>
</div>

<!-- filepond validation -->
<script src="<?= base_url('assets/js/filepond-plugin-file-validate-size.js') ?>"></script>
<script src="<?= base_url('assets/js/filepond-plugin-file-validate-type.js') ?>"></script>


<!-- image editor -->
<script src="<?= base_url('assets/js/filepond-plugin-image-preview.js') ?>"></script>
<script src="<?= base_url('assets/js/filepond-plugin-image-resize.js') ?>"></script>

<!-- filepond -->
<script src="<?= base_url('assets/js/filepond.js') ?>"></script>

<script src="<?= base_url('assets/vendors/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/products.js') ?>"></script>
<?= $this->endSection() ?>