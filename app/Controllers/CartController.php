<?php

namespace App\Controllers;

use App\Models\CampaignModel;
use App\Models\ProductsModel;

class CartController extends BaseController
{
    protected ProductsModel $productsModel;
    protected CampaignModel $campaignModel;

    public function __construct()
    {
        $this->productsModel = new ProductsModel();
        $this->campaignModel = new CampaignModel();
    }

    public function index()
    {
        $cart = session()->get('cart') ?? [];

        if (empty($cart)) {
            return $this->response->setJSON([
                'success' => true,
                'items'   => [],
                'cart_count' => 0,
                'cart_total' => 0,
            ]);
        }

        $productIds = array_map('intval', array_keys($cart));

        $products = $this->productsModel
            ->whereIn('id', $productIds)
            ->findAll();

        $items = [];
        $cartTotal = 0;
        $cartCount = 0;

        foreach ($products as $product) {

            $productId = (int) $product['id'];
            $quantity = (int) ($cart[$productId]['quantity'] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $price = (float) $product['price'];
            $subtotal = $price * $quantity;

            $items[] = [
                'id'       => $productId,
                'name'     => $product['name'],
                'price'    => $price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];

            $cartTotal += $subtotal;
            $cartCount += $quantity;
        }

        return $this->response->setJSON([
            'success'    => true,
            'items'      => $items,
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
        ]);
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');

        $product = $this->productsModel->find($productId);

        //Check ProductID
        if (! $product) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Product not found.',
                ]);
        }

        $session = session();
        $cart = $session->get('cart') ?? [];

        // Set Cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity'   => 1,
            ];
        }

        $session->set('cart', $cart);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Added to cart successfully.',
            'cart_count' => array_sum(
                array_column($cart, 'quantity')
            ),
        ]);
    }

    public function updateQuantity()
    {
        $productId = (int) $this->request->getPost('product_id');
        $action = $this->request->getPost('action');

        if ($productId <= 0 || ! in_array($action, ['increase', 'decrease'], true)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Invalid request.',
                ]);
        }

        $cart = session()->get('cart') ?? [];

        // ตรวจสอบว่าสินค้ามีอยู่ในตะกร้าหรือไม่
        if (! isset($cart[$productId])) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Product not found in cart.',
                ]);
        }

        if ($action === 'increase') {
            $cart[$productId]['quantity']++;
        } else {
            // จำนวนสินค้าต้องไม่ต่ำกว่า 1
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            }
        }

        // บันทึกข้อมูลกลับเข้า Session
        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'status' => 'success',
            'message' => 'Cart updated successfully.',
        ]);
    }

    public function remove()
    {
        $productId = (int) $this->request->getPost('product_id');

        if ($productId <= 0) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Invalid product ID.',
                ]);
        }

        $cart = session()->get('cart') ?? [];

        if (! isset($cart[$productId])) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Product not found in cart.',
                ]);
        }

        // ลบสินค้าออกจากตะกร้า
        unset($cart[$productId]);

        // บันทึก Session ที่อัปเดตแล้ว
        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'status' => 'success',
            'message' => 'Product removed successfully.',
        ]);
    }

    public function checkout()
    {
        $cart = session()->get('cart') ?? [];

        if (empty($cart)) {
            return redirect()->to(site_url('products'))
                ->with('error', 'Your cart is empty.');
        }

        try {
            $items = $this->getCartItems();
        } catch (\RuntimeException $e) {
            session()->remove('selected_campaigns');

            return redirect()->to(site_url('products'))
                ->with('error', $e->getMessage());
        }

        if (empty($items)) {
            return redirect()->to(site_url('products'))
                ->with('error', 'Your cart is empty.');
        }

        $totalQuantity = 0;

        foreach ($items as $item) {
            $totalQuantity += $item['quantity'];
        }

        $campaigns = $this->campaignModel->findAll();

        $campaignGroups = [
            'Coupon'   => [],
            'On Top'   => [],
            'Seasonal' => [],
        ];

        foreach ($campaigns as $campaign) {
            if (isset($campaignGroups[$campaign['type']])) {
                $campaignGroups[$campaign['type']][] = $campaign;
            }
        }

        $selectedCampaigns = session()->get('selected_campaigns') ?? [];

        $calculation = $this->calculateDiscounts(
            $items,
            $selectedCampaigns
        );

        return view('frontweb/checkout', [
            'items'             => $items,
            'total'             => $calculation['subtotal'],
            'totalQuantity'     => $totalQuantity,
            'campaignGroups'    => $campaignGroups,
            'selectedCampaigns' => $selectedCampaigns,
            'discountDetails'   => $calculation['discountDetails'],
            'totalDiscount'     => $calculation['totalDiscount'],
            'netTotal'          => $calculation['netTotal'],
        ]);
    }

    private function getCartItems(): array
    {
        $cart = session()->get('cart') ?? [];

        if (empty($cart)) {
            return [];
        }

        $productIds = array_map('intval', array_keys($cart));

        $products = $this->productsModel
            ->whereIn('id', $productIds)
            ->findAll();

        // ตรวจสอบว่าสินค้าใน Session ยังอยู่ในฐานข้อมูลครบหรือไม่
        if (count($products) !== count($cart)) {
            throw new \RuntimeException(
                'Some products are no longer available.'
            );
        }

        $items = [];

        foreach ($products as $product) {
            $productId = (int) $product['id'];
            $quantity = (int) ($cart[$productId]['quantity'] ?? 0);

            if ($quantity <= 0) {
                throw new \RuntimeException('Invalid product quantity.');
            }

            $price = (float) $product['price'];
            $subtotal = round($price * $quantity, 2);

            $items[] = [
                'product_id' => $productId,
                'name'       => $product['name'],
                'category_id' => isset($product['category_id'])
                    ? (int) $product['category_id']
                    : null,
                'price'      => $price,
                'quantity'   => $quantity,
                'subtotal'   => $subtotal,
            ];
        }

        return $items;
    }

    public function applyCampaigns()
    {
        $couponId = (int) ($this->request->getPost('coupon_id') ?? 0);
        $onTopId = (int) ($this->request->getPost('on_top_id') ?? 0);
        $seasonalId = (int) ($this->request->getPost('seasonal_id') ?? 0);

        $pointsInput = $this->request->getPost('points');

        if (
            $pointsInput !== null
            && $pointsInput !== ''
            && (
                ! is_scalar($pointsInput)
                || ! ctype_digit((string) $pointsInput)
            )
        ) {
            return redirect()->to(site_url('checkout'))
                ->with('error', 'Please enter a valid point amount.');
        }

        $points = ($pointsInput === null || $pointsInput === '')
            ? 0
            : (int) $pointsInput;

        $selections = [
            'Coupon'   => $couponId,
            'On Top'   => $onTopId,
            'Seasonal' => $seasonalId,
        ];

        foreach ($selections as $type => $id) {
            if ($id < 0) {
                return redirect()->to(site_url('checkout'))
                    ->with('error', 'Invalid campaign selection.');
            }

            if ($id === 0) {
                continue;
            }

            $campaign = $this->campaignModel->find($id);

            if (! $campaign || $campaign['type'] !== $type) {
                return redirect()->to(site_url('checkout'))
                    ->with('error', 'Invalid campaign selection.');
            }

            $validSubtypes = [
                'Coupon'   => ['FIXED_AMOUNT', 'PERCENTAGE'],
                'On Top'   => ['CATEGORY_PERCENTAGE', 'POINTS'],
                'Seasonal' => ['THRESHOLD_FIXED'],
            ];

            if (! in_array($campaign['subtype'], $validSubtypes[$type], true)) {
                return redirect()->to(site_url('checkout'))
                    ->with('error', 'Invalid campaign subtype.');
            }

            if ($type === 'On Top' && $campaign['subtype'] === 'POINTS') {
                // POINTS campaign ต้องกรอกจำนวน Point เป็นจำนวนเต็มไม่ติดลบ
                continue;
            }

            if ($type === 'On Top' && $points > 0) {
                // ไม่อนุญาตให้กรอก Point สำหรับแคมเปญที่ไม่ใช่ POINTS
                return redirect()->to(site_url('checkout'))
                    ->with('error', 'Points can only be used with a POINTS campaign.');
            }
        }

        // หากไม่ได้เลือก POINTS จะไม่เก็บจำนวน Point
        if ($onTopId === 0) {
            $points = 0;
        } else {
            $onTopCampaign = $this->campaignModel->find($onTopId);

            if ($onTopCampaign && $onTopCampaign['subtype'] !== 'POINTS') {
                $points = 0;
            }
        }

        session()->set('selected_campaigns', [
            'coupon_id'   => $couponId,
            'on_top_id'   => $onTopId,
            'seasonal_id' => $seasonalId,
            'points'      => $points,
        ]);

        return redirect()->to(base_url('checkout'))
            ->with('success', 'Campaigns applied successfully.');
    }

    private function calculateDiscounts(array $items, array $selected): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['subtotal'];
        }

        $subtotal = round($subtotal, 2);
        $remaining = $subtotal;
        $details = [];

        $couponId = (int) ($selected['coupon_id'] ?? 0);
        $onTopId = (int) ($selected['on_top_id'] ?? 0);
        $seasonalId = (int) ($selected['seasonal_id'] ?? 0);
        $points = (int) ($selected['points'] ?? 0);

        // 1. Coupon
        if ($couponId > 0) {
            $campaign = $this->campaignModel->find($couponId);

            if ($campaign && $campaign['type'] === 'Coupon') {
                $discount = 0;

                if ($campaign['subtype'] === 'FIXED_AMOUNT') {
                    $discount = (float) ($campaign['amount'] ?? 0);
                } elseif ($campaign['subtype'] === 'PERCENTAGE') {
                    $discount = $remaining
                        * ((float) ($campaign['percentage'] ?? 0) / 100);
                }

                $discount = round(
                    min(max($discount, 0), $remaining),
                    2
                );

                $remaining = round($remaining - $discount, 2);

                $details[] = [
                    'name'     => $campaign['name'],
                    'type'     => 'Coupon',
                    'discount' => $discount,
                ];
            }
        }

        // 2. On Top
        if ($onTopId > 0 && $remaining > 0) {
            $campaign = $this->campaignModel->find($onTopId);

            if ($campaign && $campaign['type'] === 'On Top') {
                $discount = 0;

                if ($campaign['subtype'] === 'CATEGORY_PERCENTAGE') {
                    $categoryId = (int) ($campaign['category_id'] ?? 0);
                    $eligibleSubtotal = 0;

                    foreach ($items as $item) {
                        if ((int) ($item['category_id'] ?? 0) === $categoryId) {
                            $eligibleSubtotal += $item['subtotal'];
                        }
                    }

                    $discount = $eligibleSubtotal
                        * ((float) ($campaign['percentage'] ?? 0) / 100);

                    $discount = min($discount, $remaining);
                } elseif ($campaign['subtype'] === 'POINTS') {
                    // กติกา 1 Point = 1 บาท และจำกัดไม่เกิน 20%
                    $pointsPerCurrency = (float) (
                        $campaign['points_per_currency'] ?? 0
                    );

                    if ($pointsPerCurrency > 0 && $points > 0) {
                        // แปลง Point เป็นส่วนลดเงินบาท
                        $discount = $points / $pointsPerCurrency;

                        // จำกัดส่วนลดไม่เกิน 20% ของยอดคงเหลือ
                        $maxPointsDiscount = $remaining * 0.20;

                        $discount = min(
                            $discount,
                            $maxPointsDiscount
                        );
                    }
                }

                $discount = round(
                    min(max($discount, 0), $remaining),
                    2
                );

                $remaining = round($remaining - $discount, 2);

                $details[] = [
                    'name'     => $campaign['name'],
                    'type'     => 'On Top',
                    'discount' => $discount,
                ];
            }
        }

        // 3. Seasonal: ลดครั้งเดียวเมื่อยอดที่เหลือถึงเกณฑ์
        // if ($seasonalId > 0 && $remaining > 0) {
        //     $campaign = $this->campaignModel->find($seasonalId);

        //     if (
        //         $campaign
        //         && $campaign['type'] === 'Seasonal'
        //         && $campaign['subtype'] === 'THRESHOLD_FIXED'
        //     ) {
        //         $threshold = (float) ($campaign['threshold_amount'] ?? 0);
        //         $amount = (float) ($campaign['amount'] ?? 0);

        //         $discount = (
        //             $threshold > 0 && $remaining >= $threshold
        //         ) ? $amount : 0;

        //         $discount = round(
        //             min(max($discount, 0), $remaining),
        //             2
        //         );

        //         $remaining = round($remaining - $discount, 2);

        //         $details[] = [
        //             'name'     => $campaign['name'],
        //             'type'     => 'Seasonal',
        //             'discount' => $discount,
        //         ];
        //     }
        // }

        // 3. Seasonal: ลดซ้ำตามจำนวน threshold ที่ครบ
        if ($seasonalId > 0 && $remaining > 0) {
            $campaign = $this->campaignModel->find($seasonalId);

            if (
                $campaign
                && $campaign['type'] === 'Seasonal'
                && $campaign['subtype'] === 'THRESHOLD_FIXED'
            ) {
                $threshold = (float) ($campaign['threshold_amount'] ?? 0);
                $amount = (float) ($campaign['amount'] ?? 0);

                $discount = 0;

                if ($threshold > 0 && $amount > 0 && $remaining >= $threshold) {
                    // จำนวนครั้งที่ยอดคงเหลือครบเกณฑ์
                    $eligibleTimes = (int) floor($remaining / $threshold);

                    // ส่วนลดรวมตามจำนวนครั้งที่ครบเกณฑ์
                    $discount = $eligibleTimes * $amount;
                }

                // ส่วนลดต้องไม่เกินยอดคงเหลือ
                $discount = round(
                    min(max($discount, 0), $remaining),
                    2
                );

                $remaining = round($remaining - $discount, 2);

                $details[] = [
                    'name'     => $campaign['name'],
                    'type'     => 'Seasonal',
                    'discount' => $discount,
                ];
            }
        }

        $totalDiscount = 0;

        foreach ($details as $detail) {
            $totalDiscount += $detail['discount'];
        }

        return [
            'subtotal'       => $subtotal,
            'discountDetails' => $details,
            'totalDiscount'  => round($totalDiscount, 2),
            'netTotal'       => round($remaining, 2),
        ];
    }
}
