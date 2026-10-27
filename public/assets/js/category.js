const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const csrfName  = document.querySelector('meta[name="csrf-name"]').getAttribute('content');

const tableCategory = $('#table-category').DataTable({
    processing: true,
    // serverSide: true,
    ajax: {
        url: 'get_category',
        type: 'POST',
        data: function(d) {
            d[csrfName] = csrfToken;
            return d;
        }
    },
    columns: [
        { data: 'no' },
        { data: 'name' },
        { data: 'action', orderable: false }
    ]
});

document.getElementById('form_add_category')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    // ปิด modal
    formAddCategory = document.getElementById('form_add_category');
    const modalEl = formAddCategory.closest('.modal');
    
    bootstrap.Modal.getInstance(modalEl)?.hide();
    document.getElementById('form_add_category').reset();

    try {
        const res = await fetch('add_category', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: formData
        });

        const result = await res.json();
        // console.log(result.status);
        // if (!res.ok) throw result;
        tostAlert(result.status, result.message);
        tableCategory.ajax.reload();
        

        // alert('บันทึกสำเร็จ');
    } catch (err) {
        console.error(err);
        // alert(err.message || 'Failed to save. Please try again.');
        tostAlert("error", "Failed to save. Please try again.");
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

$('#table-category')?.on('click', '.btn-edit-category', function () {
    const id   = this.dataset.id;
    const name = this.dataset.name;
    editCategory(id, name);
});

function editCategory(id, name){
    document.getElementById('edit-category-id').value = id;
    document.getElementById('edit-category-name').value = name;
}

document.getElementById('form_edit_category')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    // ปิด modal
    formEditCategory = document.getElementById('form_edit_category');
    const modalEl = formEditCategory.closest('.modal');
    
    bootstrap.Modal.getInstance(modalEl)?.hide();
    document.getElementById('form_edit_category').reset();

    try {
        const res = await fetch('update_category', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: formData
        });

        const result = await res.json();
        // console.log(result.status);
        tostAlert(result.status, result.message);
        tableCategory.ajax.reload();
        // alert('บันทึกสำเร็จ');
    } catch (err) {
        // console.error(err);
        // alert(err.message || 'Failed to save. Please try again.');
        tostAlert("error", "Failed to save. Please try again.");
    }
});

$('#table-category').on('click', '.btn-delete-category', async function () {
    const id = this.dataset.id;

    const result = await Swal.fire({
        title: 'Confirm Deletion?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        await delete_category(id);
    }
});

async function delete_category(id) {
    const formData = new FormData();
    formData.append('id', id);

    try {
        const res = await fetch('delete_category', {
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
        tableCategory.ajax.reload(null, false);
    } catch (err) {
        console.error(err);
        tostAlert('error', 'Failed to delete. Please try again.');
    }
}