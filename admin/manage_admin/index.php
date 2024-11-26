<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

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

<script>
$(document).ready(function() {
    let userId = $('#userId').val();
    if (userId) {
        $.ajax({
            url: _base_url_ + "classes/Adduser.php?f=get_user",
            method: 'POST',
            data: { id: userId },
            dataType: 'json',
            success: function(response) {
                console.log("Response received:", response); // Debugging

                if (response.status === 'success') {
                    const user = response.data;
                    $('#firstname').val(user.firstname);
                    $('#lastname').val(user.lastname);
                    $('#username').val(user.username);
                    $('#phone').val(user.phone);
                    if (user.avatar) {
                        $('#cimg').attr('src', _base_url_ + user.avatar);
                    }
                } else {
                    alert_toast(response.message || "Error loading user data", 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
                console.error(xhr.responseText); // Debug response
                alert_toast("An error occurred while fetching user data", 'error');
            }
        });
    }


    // Function to format date
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }

    // Function to load users
    function loadUsers() {
        $.ajax({
            url: _base_url_+"classes/Adduser.php?f=fetch", // Make sure this URL is correct
            method: 'GET',
            dataType: 'json',
            cache: false,
            success: function(response) {
                console.log('AJAX Response:', response);  // Log the full response

                if(response.status === 'success' && response.data) {
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
                                    <button class="btn btn-info  dropdown-toggle" type="button" id="dropdownMenuButton${user.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

                    // Initialize or refresh DataTable
                    if (!$.fn.DataTable.isDataTable('#userTable')) {
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
                    } else {
                        $('#userTable').DataTable().draw();
                    }

                    // Add click event for delete button
                    $('.delete_user').click(function() {
                        const userId = $(this).data('id');
                        if (confirm("Are you sure you want to delete this user?")) {
                            $.ajax({
                                url: _base_url_ + "classes/Adduser.php?f=delete&id=" + userId,
                                method: 'GET',
                                success: function(response) {
                                    if (response == 1) {
                                        alert_toast("User deleted successfully", 'success');
                                        loadUsers(); // Reload the user table
                                    } else if (response == 2) {
                                        alert_toast("An error occurred while deleting the user", 'error');
                                    } else if (response == 3) {
                                        alert_toast("User not found", 'warning');
                                    } else {
                                        alert_toast("Unknown error occurred", 'error');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert_toast("Error deleting user", 'error');
                                    console.error("AJAX Error: ", error);
                                }
                            });
                        }
                    });
                } else {
                    console.error('Error in response:', response);  // Log error if response is not as expected
                    alert_toast("Error loading users: " + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr.responseText, status, error);  // Log full error details

                let errorMessage = 'An error occurred while fetching users';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch(e) {
                    // If we can't parse JSON, show the first 100 characters of the response
                    errorMessage += ': ' + xhr.responseText.substring(0, 100);
                }
                alert_toast(errorMessage, 'error');
            }
        });
    }

    // Load users when page loads
    loadUsers();


    
});


</script>