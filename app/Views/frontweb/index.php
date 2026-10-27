<?= $this->extend('frontweb/layout/main') ?>
<?= $this->section('content') ?>

<div class="product-page">

    <!-- <div class="page-heading">
        <h3>เลือกสินค้า</h3>
        <p class="text-subtitle text-muted">
            เลือกสินค้าที่ต้องการและเพิ่มลงในตะกร้า
        </p>
    </div> -->

    <div class="row g-4 align-items-start">

        <!-- 1. LEFT: PRODUCT FILTER -->
        <div class="col-12 col-lg-3">
            <div class="card product-panel">
                <div class="card-header">
                    <h5 class="card-title mb-0">ตัวกรองสินค้า</h5>
                </div>

                <div class="card-body">
                    <form method="get" action="<?= site_url('products') ?>">

                        <label class="form-label">ค้นหาสินค้า</label>
                        <input type="text" name="keyword" class="form-control mb-3"
                            placeholder="ชื่อสินค้า หรือ Barcode">

                        <label class="form-label">ประเภทสินค้า</label>
                        <select name="category" class="form-select mb-3">
                            <option value="">ทุกประเภท</option>
                            <option value="1">เครื่องดื่ม</option>
                            <option value="2">ขนมขบเคี้ยว</option>
                            <option value="3">ของใช้ในครัวเรือน</option>
                        </select>

                        <label class="form-label">ราคาต่ำสุด</label>
                        <input type="number" name="min_price" class="form-control mb-3" min="0" placeholder="0">

                        <label class="form-label">ราคาสูงสุด</label>
                        <input type="number" name="max_price" class="form-control mb-3" min="0" placeholder="1,000">

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            กรองสินค้า
                        </button>

                        <a href="<?= site_url('products') ?>" class="btn btn-light w-100">
                            ล้างตัวกรอง
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2. CENTER: PRODUCT LIST -->
        <div class="col-12 col-lg-6">
            <div class="card product-panel">
                <div class="card-header">
                    <h5 class="card-title mb-0">สินค้าแนะนำ</h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <?php foreach (($products ?? []) as $product): ?>
                            <div class="col-6 col-xl-4">
                                <div class="card h-100 mb-0">

                                    <div class="card-body d-flex flex-column">
                                        <div class="text-center mb-3">
                                            <img src="<?= esc(
                                                            $product['image_url']
                                                                ?? 'assets/images/placeholder.png'
                                                        ) ?>" alt="<?= esc($product['name']) ?>" class="img-fluid"
                                                style="height:120px;object-fit:contain">
                                        </div>

                                        <h6>
                                            <?= esc($product['name']) ?>
                                        </h6>

                                        <p class="text-muted small mb-2">
                                            <?= esc($product['category_name'] ?? '-') ?>
                                        </p>

                                        <h6 class="text-primary mt-auto">
                                            ฿<?= number_format(
                                                    (float) $product['price'],
                                                    2
                                                ) ?>
                                        </h6>

                                        <button type="button" class="btn btn-primary btn-sm w-100"
                                            onclick="addToCart(<?= (int) $product['id'] ?>)">
                                            <i class="bi bi-cart-plus"></i>
                                            เพิ่มลงตะกร้า
                                        </button>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($products)): ?>
                            <div class="col-12 text-center text-muted py-5">
                                ไม่พบสินค้า
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- 3. RIGHT: SHOPPING CART -->
        <div class="col-12 col-lg-3">
            <div class="card product-panel cart-panel">

                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cart3"></i>
                        ตะกร้าสินค้า

                        <span class="badge bg-primary" id="cart-count">
                            0
                        </span>
                    </h5>
                </div>

                <div class="card-body">
                    <div id="cart-items">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-cart3 fs-1"></i>
                            <p class="mt-2 mb-0">
                                ยังไม่มีสินค้าในตะกร้า
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-3">
                        <strong>ยอดรวม</strong>

                        <strong class="text-primary" id="cart-total">
                            ฿0.00
                        </strong>
                    </div>

                    <a href="<?= route_to('checkout.index') ?>"
                        id="checkout-btn"
                        class="btn btn-primary w-100">
                        <i class="bi bi-receipt"></i>
                        สรุปรายการสินค้า
                    </a>

                    <!-- <button type="button"
                        id="checkout-btn"
                        class="btn btn-primary w-100"
                        disabled>
                        ดำเนินการสั่งซื้อ
                    </button> -->

                </div>
            </div>
        </div>

    </div>

</div>

<script src="<?= base_url('assets/vendors/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/index.js') ?>"></script>
<?= $this->endSection() ?>