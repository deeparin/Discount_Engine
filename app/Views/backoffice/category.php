<?= $this->extend('backoffice/layout/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Category</h3>
                <!-- <p class="text-subtitle text-muted">Navbar will appear in top of the page.</p> -->
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Category</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">List</h4>
                <button type="button" class="btn btn-sm btn-primary" id="btn_modal_add_category" data-bs-toggle="modal"
                    data-bs-target="#inlineForm">
                    <i class="fa fa-plus"></i> Add Category
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <table class="table table-hover" id="table-category" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อประเภท</th>
                                <th style="white-space: nowrap; width: 20%;">จัดการ</th>
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
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">เพิ่มประเภทสินค้า</h4>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <form action="#" id="form_add_category">
                <div class="modal-body">
                    <label>ชื่อประเภทสินค้า : </label>
                    <div class="form-group">
                        <input type="text" name="name" placeholder="ชื่อประเภทสินค้า"
                            class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary"
                        data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                    <button type="submit" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Confirm</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
 <div class="modal fade text-left" id="edit_category" tabindex="-1"
    role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">แก้ไขเภทสินค้า</h4>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <form action="#" id="form_edit_category">
                <input type="hidden" name="id" id="edit-category-id">
                <div class="modal-body">
                    <label>ชื่อประเภทสินค้า : </label>
                    <div class="form-group">
                        <input type="text" id="edit-category-name" name="name" placeholder="ชื่อประเภทสินค้า"
                            class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary"
                        data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                    <button type="submit" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Confirm</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/vendors/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/category.js') ?>"></script>
<?= $this->endSection() ?>