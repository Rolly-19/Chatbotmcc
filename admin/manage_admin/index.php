<?php if($_settings->chk_flashdata('success')): ?>
<script>
	Swal.fire({
		icon: 'success',
		title: 'Success',
		text: "<?php echo $_settings->flashdata('success') ?>",
		showConfirmButton: false,
		timer: 2000
	});
</script>
<?php endif; ?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Users</h3>
		<div class="card-tools">
			<a href="?page=manage_admin/adduser" class="btn btn-flat btn-info"><span class="fas fa-plus"></span>  Add New User</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-bordered table-stripped" id="userTable">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Avatar</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Date Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="userTableBody">
					<!-- Rows will be populated by AJAX -->
				</tbody>
			</table>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Function to load users
    function loadUsers() {
        $.ajax({
            url: _base_url_ + "classes/Adduser.php?f=fetch&timestamp=" + new Date().getTime(),
            method: 'GET',
            dataType: 'json',
            cache: false, // Ensure fresh data
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    let tbody = $('#userTableBody');
                    tbody.html(''); // Clear existing rows

                    response.data.forEach(function(user) {
                        let avatarUrl = _base_url_ + (user.avatar || 'uploads/default.png');
                        let row = `
                        <tr>
                            <td class="text-center">${user.index}</td>
                            <td class="text-center">
                                <img src="${avatarUrl}" 
                                    alt="${user.name}" 
                                    class="img-avatar img-thumbnail rounded-circle" 
                                    style="width: 50px; height: 50px; object-fit: cover;"
                                    onerror="this.src='${_base_url_}uploads/default.png'">
                            </td>
                            <td>${user.name || ''}</td>
                            <td>${user.username || ''}</td>
                            <td>${user.phone || ''}</td>
                            <td>${user.date_added || ''}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton${user.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuButton${user.id}">
                                        <a class="dropdown-item" href="?page=manage_admin/edit_user&id=${user.id}">
                                            <span class="fa fa-edit text-primary"></span> Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="${user.id}">
                                            <span class="fa fa-trash text-danger"></span> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                        tbody.append(row);
                    });

                    // Use DataTable API for better lifecycle management
                    if ($.fn.DataTable.isDataTable('#userTable')) {
                        let dataTable = $('#userTable').DataTable();
                        dataTable.clear();
                        dataTable.rows.add(tbody.find('tr'));
                        dataTable.draw();
                    } else {
                        $('#userTable').DataTable({
                            "responsive": true,
                            "lengthChange": false,
                            "autoWidth": false,
                            "pageLength": 10,
                            "order": [[0, "asc"]],
                            "columnDefs": [
                                { "orderable": false, "targets": [1, 6] } // Disable sorting for avatar and actions columns
                            ]
                        });
                    }

                    attachDeleteEventHandlers(); // Reattach event handlers
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error loading users',
                        text: response.message || 'Unknown error',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching users',
                    confirmButtonText: 'Okay'
                });
            }
        });
    }

    // Function to attach delete event handlers
    function attachDeleteEventHandlers() {
        $('.delete_user').off('click').on('click', function() {
            const userId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _base_url_ + "classes/Adduser.php?f=delete&id=" + userId,
                        method: 'GET',
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'User has been deleted.',
                                    confirmButtonText: 'Okay'
                                });
                                loadUsers(); // Reload the user table
                            } else if (response == 2) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while deleting the user.',
                                    confirmButtonText: 'Okay'
                                });
                            } else if (response == 3) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Not Found',
                                    text: 'User not found.',
                                    confirmButtonText: 'Okay'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Unknown Error',
                                    text: 'An unknown error occurred.',
                                    confirmButtonText: 'Okay'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error deleting user.',
                                confirmButtonText: 'Okay'
                            });
                        }
                    });
                }
            });
        });
    }

    // Load users when page loads
    loadUsers();
});
</script>
