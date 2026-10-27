const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const csrfName  = document.querySelector('meta[name="csrf-name"]').getAttribute('content');

const tableProducts = $('#table-products').DataTable({
    processing: true,
    // serverSide: true,
    ajax: {
        url: 'get_products',
        type: 'POST',
        data: function(d) {
            d[csrfName] = csrfToken;
            return d;
        }
    },
    columns: [
        { data: 'no' },
        { data: 'image' },
        { data: 'name' },
        { data: 'category' },
        { data: 'price', className: 'text-end' },
        { data: 'action', className: 'text-end', orderable: false }
    ]
});

const pondOptions = {
        allowImagePreview: true,
        imagePreviewHeight: 170, // กำหนดความสูงของ preview
        allowImageResize: true,
        imageResizeTargetWidth: 200,
        imageResizeTargetHeight: 200,
        imageResizeMode: 'cover',
        imageResizeUpscale: false,
        acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
        storeAsFile: true,
        fileValidateTypeDetectType: (source, type) => new Promise((resolve) => {
            resolve(type);
        })
    };

if (typeof FilePond !== 'undefined') {
    FilePond.registerPlugin(
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType,
        FilePondPluginImagePreview,
        FilePondPluginImageResize
    );

    

    document.querySelectorAll('.image-resize-filepond').forEach((el) => {
        FilePond.create(el, pondOptions);
    });
}


const categorySelect = document.getElementById('p_category');

const loadCategories = async (select) => {
    if (!select) return;

    try {
        const res = await fetch('dropdown_get_category', {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);

        const data = await res.json();
        const selectedId = String(select.dataset.selected || '');

        select.innerHTML = '';
        select.add(new Option('-- Select Category --', '', false, selectedId === ''));

        data.forEach((item) => {
            const isSelected = String(item.id) === selectedId;
            select.add(new Option(item.name, item.id, false, isSelected));
        });

        select.value = selectedId;
    } catch (err) {
        console.error('Failed to load product categories.', err);
    }
};

loadCategories(document.getElementById('p_category'));       // หน้า add
loadCategories(document.getElementById('p_edit_category'));  // หน้า edit

    // loadCategories();


function checkInvalid(){
    document.querySelectorAll('.form-control').forEach(el => {
        el.classList.remove('is-invalid');
    });

    let p_name = document.getElementById("p_name").value;
    let p_category = document.getElementById("p_category").value;
    let p_price = document.getElementById("p_price").value;

    p_name == '' ? document.getElementById("p_name").className = "form-control is-invalid" : '';
    p_category == '' ? document.getElementById("p_category").className = "form-control is-invalid" : '';
    p_price == '' ? document.getElementById("p_price").className = "form-control is-invalid" : '';

    if(p_name == '' || p_category == '' || p_price == ''){
        return false;
    }

    return true;

}

const formAddProduct = document.getElementById('form_add_product');
const btnSubmit = document.getElementById('btn-submit-form-add-product');

// requestSubmit() fires the 'submit' event; submit() does not
btnSubmit?.addEventListener('click', () => {
    if (checkInvalid() === false) return;
    formAddProduct?.requestSubmit();
});

formAddProduct?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const result = await Swal.fire({
        title: 'Are you sure you want to proceed?',
        text: 'Would you like to save the changes?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    });
    if (!result.isConfirmed) return;

    btnSubmit.disabled = true;

    try {
        const res = await fetch('add_product', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            },
            body: new FormData(formAddProduct)
        });

        const result = await res.json();
        // console.log(result.status);
        // if (!res.ok) throw result;
        tostAlert(result.status, result.message);
        

        const modalEl = formAddProduct.closest('.modal');
        if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
        formAddProduct.reset();
        formAddProduct.querySelectorAll('.image-resize-filepond').forEach((input) => {
            FilePond.find(input)?.removeFiles();
        });

        tableProducts.ajax.reload();

        
    } catch (err) {
        // console.error(err);
        console.log(err)
        tostAlert('error', err.message || 'Failed to save. Please try again.');
    } finally {
        btnSubmit.disabled = false;
    }
});

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


$('#table-products')?.on('click', '.btn-edit-product', async function () {
    const id = this.dataset.id;
    const p_edit_category = document.getElementById('p_edit_category');
    // editCategory(id, name);
    const btn = this;
    btn.disabled = true;
    try {
        const res = await fetch(`get_product/${encodeURIComponent(id)}`, {
            method: 'GET',   // ใส่หรือไม่ใส่ก็ได้ เป็นค่าเริ่มต้น
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await res.json();
        if (!res.ok || result.status !== 'success') throw result;

        const v = result.data;
        const form = document.getElementById('form_edit_product');

        form.querySelector('[name="p_edit_id"]').value = v.id;
        form.querySelector('[name="p_edit_name"]').value = v.name ?? '';
        form.querySelector('[name="p_edit_price"]').value = v.price ?? '';
        form.querySelector('[name="p_edit_category"]').value = v.category_id ?? '';
        form.querySelector('[name="p_edit_oldimg"]').value = v.image ?? '';
        form.querySelector('#current-image').src = v.show_image || form.querySelector('#current-image').dataset.default;
        const modalEl = document.getElementById('modal_edit_product');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    } catch (err) {
        console.error(err);
        tostAlert('error', err.message || 'โหลดข้อมูลไม่สำเร็จ');
    } finally {
        btn.disabled = false;
    }
});

document.getElementById('btn-change-image').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('gallery-input').click();
});

document.getElementById('gallery-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById('current-image').src = ev.target.result;
    };
    reader.readAsDataURL(file);
});

const formEditProduct = document.getElementById('form_edit_product');
const btnEditSubmit = document.getElementById('btn-submit-form-edit-product');

// requestSubmit() fires the 'submit' event; submit() does not
btnEditSubmit?.addEventListener('click', () => {
    if (checkEditInvalid() === false) return;
    formEditProduct?.requestSubmit();
});

function checkEditInvalid(){
    document.querySelectorAll('.form-control').forEach(el => {
        el.classList.remove('is-invalid');
    });

    let p_edit_name = document.getElementById("p_edit_name").value;
    let p_edit_category = document.getElementById("p_edit_category").value;
    let p_edit_price = document.getElementById("p_edit_price").value;

    p_edit_name == '' ? document.getElementById("p_edit_name").className = "form-control is-invalid" : '';
    p_edit_category == '' ? document.getElementById("p_edit_category").className = "form-control is-invalid" : '';
    p_edit_price == '' ? document.getElementById("p_edit_price").className = "form-control is-invalid" : '';

    if(p_edit_name == '' || p_edit_category == '' || p_edit_price == ''){
        return false;
    }

    return true;

}

formEditProduct?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const result = await Swal.fire({
        title: 'Are you sure you want to proceed?',
        text: 'Would you like to save the changes?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    });
    if (!result.isConfirmed) return;

    btnSubmit.disabled = true;

    try {
        const res = await fetch('update_product', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            },
            body: new FormData(formEditProduct)
        });

        const result = await res.json();
        // console.log(result.status);
        // if (!res.ok) throw result;
        tostAlert(result.status, result.message);
        

        const modalEl = formEditProduct.closest('.modal');
        if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
        formEditProduct.reset();
        formEditProduct.querySelectorAll('.image-resize-filepond').forEach((input) => {
            FilePond.find(input)?.removeFiles();
        });

        tableProducts.ajax.reload();

        
    } catch (err) {
        // console.error(err);
        console.log(err)
        tostAlert('error', err.message || 'Failed to save. Please try again.');
    } finally {
        btnSubmit.disabled = false;
    }
});

$('#table-products').on('click', '.btn-delete-product', async function () {
    const id = this.dataset.id;

    const result = await Swal.fire({
        title: 'Confirm Deletion?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        await delete_product(id);
    }
});


async function delete_product(id) {
    const formData = new FormData();
    formData.append('id', id);

    try {
        const res = await fetch('delete_product', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: formData
        });

        const result = await res.json();
        tostAlert(result.status, result.message);
        tableProducts.ajax.reload(null, false);
    } catch (err) {
        // console.error(err);
        tostAlert('error', 'Failed to delete. Please try again.');
    }
}

