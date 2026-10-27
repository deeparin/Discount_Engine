
const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
const csrfNameElement  = document.querySelector('meta[name="csrf-name"]');

const csrfToken = csrfTokenElement?.content ?? '';
const csrfName  = csrfNameElement?.content ?? '';

document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});

async function addToCart(productId) {

    const formData = new FormData();

    formData.append('product_id', productId);

    try {
        const response = await fetch('cart/add',
            {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            }
        );

        const result = await response.json();

        if (!response.ok || result.status != 'success') {
            tostAlert(result.status || 'Failed to add product to cart.');
            return;
        }

        // อัปเดตจำนวนสินค้าในตะกร้า
        const badge = document.querySelector('#cart-count');

        if (badge) {
            badge.textContent = result.cart_count;
        }

        tostAlert(result.status, result.message);
        await loadCart();


        console.log('Cart updated:', result);

    } catch (error) {

        console.error('Add to cart error:', error);

        tostAlert('error','An error occurred. Please try again.');
    }
}

async function loadCart() {
    try {
        const response = await fetch('cart',
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error('Failed to load cart.');
        }

        const cartItems = document.querySelector('#cart-items');
        const cartCount = document.querySelector('#cart-count');
        const cartTotal = document.querySelector('#cart-total');
        const checkoutBtn = document.querySelector('#checkout-btn');

        // แสดงจำนวนสินค้า
        cartCount.textContent = result.cart_count;

        // แสดงยอดรวม
        cartTotal.textContent = formatMoney(result.cart_total);

        // เปิด/ปิดปุ่มสั่งซื้อ
        checkoutBtn.disabled = result.items.length === 0;

        // ล้างรายการเดิมก่อนแสดงข้อมูลใหม่
        cartItems.replaceChildren();

        // กรณีไม่มีสินค้า
        if (result.items.length === 0) {

            const empty = document.createElement('div');
            empty.className = 'text-center text-muted py-4';

            const icon = document.createElement('i');
            icon.className = 'bi bi-cart3 fs-1';

            const text = document.createElement('p');
            text.className = 'mt-2 mb-0';
            text.textContent = 'ยังไม่มีสินค้าในตะกร้า';

            empty.append(icon, text);
            cartItems.appendChild(empty);

            return;
        }

        // แสดงสินค้าแต่ละรายการ
        // แสดงสินค้าแต่ละรายการ
        result.items.forEach(function(item) {

            const wrapper = document.createElement('div');
            wrapper.className = 'cart-item mb-3';

            // ชื่อสินค้า
            const name = document.createElement('div');
            name.className = 'fw-semibold';
            name.textContent = item.name;

            // ราคาและยอดรวม
            const priceInfo = document.createElement('div');
            priceInfo.className = 'small text-muted mt-1';
            priceInfo.textContent =
                `${formatMoney(item.price)} / ชิ้น`;

            // แถวควบคุมจำนวนและยอดรวม
            const controlRow = document.createElement('div');
            controlRow.className =
                'd-flex justify-content-between align-items-center mt-2 gap-2';

            // กลุ่มปุ่มเพิ่ม/ลดจำนวน
            const quantityGroup = document.createElement('div');
            quantityGroup.className = 'btn-group btn-group-sm';
            quantityGroup.setAttribute('role', 'group');
            quantityGroup.setAttribute(
                'aria-label',
                `จำนวนสินค้า ${item.name}`
            );

            // ปุ่มลดจำนวน
            const decreaseBtn = document.createElement('button');
            decreaseBtn.type = 'button';
            decreaseBtn.className = 'btn btn-outline-secondary';
            decreaseBtn.textContent = '−';
            decreaseBtn.setAttribute('aria-label', 'ลดจำนวนสินค้า');
            decreaseBtn.dataset.action = 'decrease';
            decreaseBtn.dataset.id = item.id;
            decreaseBtn.disabled = item.quantity <= 1;

            // จำนวนสินค้า
            const quantity = document.createElement('span');
            quantity.className =
                'border px-3 d-flex align-items-center';
            quantity.textContent = item.quantity;

            // ปุ่มเพิ่มจำนวน
            const increaseBtn = document.createElement('button');
            increaseBtn.type = 'button';
            increaseBtn.className = 'btn btn-outline-secondary';
            increaseBtn.textContent = '+';
            increaseBtn.setAttribute('aria-label', 'เพิ่มจำนวนสินค้า');
            increaseBtn.dataset.action = 'increase';
            increaseBtn.dataset.id = item.id;

            quantityGroup.append(
                decreaseBtn,
                quantity,
                increaseBtn
            );

            // ยอดรวมของรายการสินค้า
            const subtotal = document.createElement('span');
            subtotal.className = 'fw-semibold text-primary';
            subtotal.textContent = formatMoney(item.subtotal);

            controlRow.append(quantityGroup, subtotal);

            // ปุ่มลบสินค้า
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className =
                'btn btn-sm btn-outline-danger mt-2';
            removeBtn.innerHTML = '<i class="bi bi-trash"></i> ลบสินค้า';
            removeBtn.dataset.action = 'remove';
            removeBtn.dataset.id = item.id;
            removeBtn.setAttribute('aria-label', `ลบ ${item.name} ออกจากตะกร้า`);

            wrapper.append(
                name,
                priceInfo,
                controlRow,
                removeBtn
            );

            cartItems.appendChild(wrapper);
        });

    } catch (error) {

        console.error('Load cart error:', error);

    }
}

const cartItemsElement = document.querySelector('#cart-items');

cartItemsElement.addEventListener('click', async function(event) {

    const button = event.target.closest('button[data-action]');

    if (!button || !cartItemsElement.contains(button)) {
        return;
    }

    const productId = Number(button.dataset.id);
    const action = button.dataset.action;

    if (!Number.isInteger(productId) || productId <= 0) {
        return;
    }
    // ป้องกันการกดซ้ำระหว่างกำลังส่ง Request
    if (button.disabled) {
        return;
    }

    button.disabled = true;

    try {
        if (action === 'increase' || action === 'decrease') {

            await updateCartQuantity(productId, action);

        } else if (action === 'remove') {

            // const confirmed = confirm(
            //     'คุณต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?'
            // );
            // if (!confirmed) {
            //     return;
            // }
            // await removeCartItem(productId);
            const result = await Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            });

            if (!result.isConfirmed) {
                return;
            }

            await removeCartItem(productId);
        }

    } catch (error) {
        console.error('Cart action error:', error);
        alert(error.message || 'An error occurred. Please try again.');
    } finally {
        if (button.isConnected) {
            button.disabled = false;
        }

    }

});

async function updateCartQuantity(productId, action) {

    const formData = new FormData();

    formData.append('product_id', productId);
    formData.append('action', action);

    const response = await fetch('cart/update',
        {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        }
    );

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(
            result.message || 'Failed to update cart.'
        );
    }
    // โหลดรายการและยอดรวมใหม่
    await loadCart();
}

async function removeCartItem(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);

    const response = await fetch('cart/remove',
        {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        }
    );
    const result = await response.json();
    if (!response.ok || !result.success) {
        throw new Error(
            result.message || 'Failed to remove product.'
        );
    }
    await loadCart();
}


// จัดรูปแบบราคา
function formatMoney(amount) {

    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount);

}

function tostAlert(type, message){
    Toastify({
        text: message,
        duration: 4000,
        // close: true,
        gravity: "top",
        position: "center",
        backgroundColor: type == 'success' ? "#4fbe87" : type == 'warning' ? "#eaca4a" : "#f3616d"
        // style: {
        //     background: "#dc3545"
        // }
    }).showToast();
}