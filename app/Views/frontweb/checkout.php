<?= $this->extend('frontweb/layout/main') ?>

<?= $this->section('content') ?>

<div class="product-page">

    <!-- Page Heading -->
    <div class="page-heading">
        <h3>สรุปรายการสินค้า</h3>
        <p class="text-subtitle text-muted mb-0">
            ตรวจสอบสินค้าและยอดรวมก่อนดำเนินการต่อ
        </p>
    </div>

    <div class="row g-4">

        <!-- รายการสินค้า -->
        <div class="col-12 col-lg-8">
            <div class="card product-panel h-100">

                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bag me-2"></i>
                        รายการสินค้าที่เลือก
                    </h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table align-middle checkout-table mb-0">

                            <thead>
                                <tr>
                                    <th>สินค้า</th>
                                    <th class="text-end">ราคา</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-end">รวม</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="product-name">
                                            <?= esc($item['name']) ?>
                                        </td>

                                        <td class="text-end text-nowrap">
                                            ฿<?= number_format($item['price'], 2) ?>
                                        </td>

                                        <td class="text-center">
                                            <?= (int) $item['quantity'] ?>
                                        </td>

                                        <td class="text-end text-nowrap fw-semibold">
                                            ฿<?= number_format($item['subtotal'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-12 col-lg-4">
            <!-- สรุปยอด -->
            <div class="col-12 col-lg-12">
                <div class="card product-panel summary-panel">

                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-receipt me-2"></i>
                            สรุปยอด
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="summary-row">
                            <span class="text-muted">จำนวนสินค้า</span>
                            <strong><?= (int) $totalQuantity ?> ชิ้น</strong>
                        </div>

                        <div class="summary-row">
                            <span class="text-muted">ยอดรวมสินค้า</span>
                            <strong>฿<?= number_format($total, 2) ?></strong>
                        </div>

                        <?php foreach ($discountDetails as $detail): ?>
                            <div class="summary-row">
                                <div>
                                    <div><?= esc($detail['name']) ?></div>
                                    <small class="text-muted">
                                        <?= esc($detail['type']) ?>
                                    </small>
                                </div>

                                <strong class="text-danger">
                                    -฿<?= number_format($detail['discount'], 2) ?>
                                </strong>
                            </div>
                        <?php endforeach; ?>

                        <div class="summary-row">
                            <span class="text-muted">ส่วนลดรวม</span>
                            <strong class="text-danger">
                                -฿<?= number_format($totalDiscount, 2) ?>
                            </strong>
                        </div>

                        <hr class="summary-divider">

                        <div class="summary-total">
                            <span>ยอดสุทธิ</span>
                            <strong class="text-primary">
                                ฿<?= number_format($netTotal, 2) ?>
                            </strong>
                        </div>

                        <a href="<?= route_to('index') ?>"
                            class="btn btn-outline-secondary w-100 mt-3">
                            <i class="bi bi-arrow-left me-1"></i>
                            กลับไปเลือกสินค้า
                        </a>

                    </div>
                </div>
            </div>
            <!-- Campaign Selection -->
            <div class="col-12 col-lg-12">
                <div class="card product-panel mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-tags me-2"></i>
                            เลือกส่วนลด
                        </h5>
                    </div>

                    <div class="card-body">
                        <form method="post"
                            action="<?= site_url('checkout/apply-campaigns') ?>">

                            <?= csrf_field() ?>

                            <div class="row g-3">

                                <!-- Coupon -->
                                <div class="col-12 col-lg-4">
                                    <label for="coupon_id" class="form-label fw-semibold">
                                        Coupon
                                    </label>

                                    <select name="coupon_id"
                                        id="coupon_id"
                                        class="form-select">
                                        <option value="0">ไม่ใช้ Coupon</option>

                                        <?php foreach ($campaignGroups['Coupon'] as $campaign): ?>
                                            <option
                                                value="<?= (int) $campaign['id'] ?>"
                                                <?= (int) ($selectedCampaigns['coupon_id'] ?? 0)
                                                    === (int) $campaign['id'] ? 'selected' : '' ?>>
                                                <?= esc($campaign['name']) ?>
                                                (<?= esc($campaign['subtype']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- On Top -->
                                <div class="col-12 col-lg-4">
                                    <label for="on_top_id" class="form-label fw-semibold">
                                        On Top
                                    </label>

                                    <select name="on_top_id"
                                        id="on_top_id"
                                        class="form-select">
                                        <option value="0">ไม่ใช้ On Top</option>

                                        <?php foreach ($campaignGroups['On Top'] as $campaign): ?>
                                            <option
                                                value="<?= (int) $campaign['id'] ?>"
                                                data-subtype="<?= esc($campaign['subtype'], 'attr') ?>"
                                                <?= (int) ($selectedCampaigns['on_top_id'] ?? 0)
                                                    === (int) $campaign['id'] ? 'selected' : '' ?>>
                                                <?= esc($campaign['name']) ?>
                                                (<?= esc($campaign['subtype']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Seasonal -->
                                <div class="col-12 col-lg-4">
                                    <label for="seasonal_id" class="form-label fw-semibold">
                                        Seasonal
                                    </label>

                                    <select name="seasonal_id"
                                        id="seasonal_id"
                                        class="form-select">
                                        <option value="0">ไม่ใช้ Seasonal</option>

                                        <?php foreach ($campaignGroups['Seasonal'] as $campaign): ?>
                                            <option
                                                value="<?= (int) $campaign['id'] ?>"
                                                <?= (int) ($selectedCampaigns['seasonal_id'] ?? 0)
                                                    === (int) $campaign['id'] ? 'selected' : '' ?>>
                                                <?= esc($campaign['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Points -->
                                <div class="col-12 col-lg-4"
                                    id="points-wrapper"
                                    style="display: none;">

                                    <label for="points" class="form-label fw-semibold">
                                        จำนวน Point ที่ต้องการใช้
                                    </label>

                                    <input type="number"
                                        name="points"
                                        id="points"
                                        class="form-control"
                                        min="0"
                                        step="1"
                                        value="<?= (int) ($selectedCampaigns['points'] ?? 0) ?>"
                                        placeholder="กรอกจำนวน Point">

                                    <small class="text-muted">
                                        ส่วนลดไม่เกิน 20%
                                    </small>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check2-circle me-1"></i>
                                        ใช้ส่วนลด
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>



    </div>
</div>
<script src="<?= base_url('assets/vendors/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/checkout.js') ?>"></script>
<?= $this->endSection() ?>