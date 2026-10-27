const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const csrfName  = document.querySelector('meta[name="csrf-name"]').getAttribute('content');

const tableCampaign = $('#table-campaign').DataTable({
    processing: true,
    // serverSide: true,
    ajax: {
        url: 'get_campaign',
        type: 'POST',
        data: function(d) {
            d[csrfName] = csrfToken;
            return d;
        }
    },
    columns: [
        { data: 'no' },
        { data: 'name' },
        { data: 'type' },
        { data: 'subtype' },
        { data: 'amount' },
        { data: 'percentage' },
        { data: 'category_id' },
        { data: 'threshold_amount' },
        { data: 'max_discount_percentage' },
        { data: 'points_per_currency' },
        { data: 'action', orderable: false }
    ]
});

$(document).ready(function () {

    // กำหนด Sub Type ของแต่ละ Campaign Type
    const subTypes = {
        "Coupon": [
            { value: "FIXED_AMOUNT", text: "Fixed Amount" },
            { value: "PERCENTAGE", text: "Percentage Discount" }
        ],
        "On Top": [
            { value: "CATEGORY_PERCENTAGE", text: "Discount by Category" },
            { value: "POINTS", text: "Discount by Point" }
        ],


        "Seasonal": [
            { value: "THRESHOLD_FIXED", text: "Threshold Amount" },
        ]
    };

    // ทำงานเมื่อเลือก Campaign Type
    $('#type').on('change', function () {

        const type = $(this).val();
        const $subType = $('#sub_type');

        // ล้าง Sub Type เดิม
        $subType.empty();

        //clear form
        $('#form_fixed_amount, #form_percentage, #form_category_percent, #form_points_per_currency, #form_threshold_fixed').hide();

        // เพิ่ม Option เริ่มต้น
        $subType.append(
            $('<option>', {
                value: '',
                text: '-- Please Select Sub Type --'
            })
        );

        // ถ้าไม่ได้เลือก Type ให้ปิด Sub Type
        if (!type || !subTypes[type]) {
            $subType.prop('disabled', true);
            return;
        }

        // เพิ่ม Sub Type ตาม Type ที่เลือก
        $.each(subTypes[type], function (index, item) {
            $subType.append(
                $('<option>', {
                    value: item.value,
                    text: item.text
                })
            );
        });

        // เปิดให้เลือก Sub Type
        $subType.prop('disabled', false);
    });

    // แสดงฟอร์มตาม Campaign Sub Type
    $('#sub_type').on('change', function () {
        

        const subType = $(this).val();
        console.log(subType);

        // ซ่อนฟอร์มทั้งหมดก่อน
        $('#form_fixed_amount, #form_percentage, #form_category_percent, #form_points_per_currency, #form_threshold_fixed').hide();

        // แสดงฟอร์มตาม Sub Type ที่เลือก
        switch (subType) {
            case 'FIXED_AMOUNT':
                $('#form_fixed_amount').show();
                break;
            case 'PERCENTAGE':
                $('#form_percentage').show();
                break;
            case 'CATEGORY_PERCENTAGE':
                $('#form_category_percent').show();
                break;
            case 'POINTS':
                $('#form_points_per_currency').show();
                break;
            case 'THRESHOLD_FIXED':
                $('#form_threshold_fixed').show();
                break;
        }

    });

});

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
        select.add(new Option('-- เลือกประเภท --', '', false, selectedId === ''));

        data.forEach((item) => {
            const isSelected = String(item.id) === selectedId;
            select.add(new Option(item.name, item.id, false, isSelected));
        });

        select.value = selectedId;
    } catch (err) {
        console.error('โหลดประเภทสินค้าไม่สำเร็จ', err);
    }
};

loadCategories(document.getElementById('p_category')); 

const formAddCampaign = document.getElementById('form_add_campaign');
const btnSubmit = document.getElementById('btn-submit-form-add-campaign');

btnSubmit?.addEventListener('click', () => {
    formAddCampaign?.requestSubmit();
});

formAddCampaign?.addEventListener('submit', async function (e) {
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
        const res = await fetch('add_campaign', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            },
            body: new FormData(formAddCampaign)
        });

        const result = await res.json();
        tostAlert(result.status, result.message);

        const modalEl = formAddCampaign.closest('.modal');
        if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
        formAddCampaign.reset();
        $('#form_fixed_amount, #form_percentage, #form_category_percent, #form_points_per_currency, #form_threshold_fixed').hide();
        tableCampaign.ajax.reload();

        
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

$('#table-campaign').on('click', '.btn-delete-campaign', async function () {
    const id = this.dataset.id;

    const result = await Swal.fire({
        title: 'Confirm Deletion?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        await delete_campaign(id);
    }
});

async function delete_campaign(id) {
    const formData = new FormData();
    formData.append('id', id);

    try {
        const res = await fetch('delete_campaign', {
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
        tableCampaign.ajax.reload(null, false);
    } catch (err) {
        console.error(err);
        tostAlert('error', 'Failed to delete. Please try again.');
    }
}