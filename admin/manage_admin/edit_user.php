<?php 
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    die("No user ID provided.");
}
?>
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid">
            <div id="msg"></div>
            <form action="" id="edit-user" enctype="multipart/form-data">
            <input type="hidden" name="id" id="userId" value="<?php echo isset($id) ? $id : ''; ?>">

                
                <!-- First Name -->
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" class="form-control" 
                           required pattern="[A-Za-z\s]+" 
                           title="Please enter a valid first name (letters and spaces only)"
                           value="<?php echo isset($firstname) ? $firstname : ''; ?>">
                </div>
                
                <!-- Last Name -->
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" 
                           required pattern="[A-Za-z\s]+" 
                           title="Please enter a valid last name (letters and spaces only)"
                           value="<?php echo isset($lastname) ? $lastname : ''; ?>">
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" 
                           required minlength="4" 
                           title="Username must be at least 4 characters long"
                           autocomplete="off"
                           value="<?php echo isset($username) ? $username : ''; ?>">
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" 
                           required maxlength="11" pattern="[0-9]{11}" 
                           title="Please enter a valid 11-digit phone number"
                           value="<?php echo isset($phone) ? $phone : ''; ?>">
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" 
                               minlength="8"
                               title="Password must be at least 8 characters long"
                               autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text" id="toggle-password" role="button">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        <i>Leave this blank if you don't want to change the password. 
                           New passwords must be at least 8 characters long.</i>
                    </small>
                </div>

                <!-- Avatar -->
                <div class="form-group">
                    <label for="customFile" class="control-label">Avatar</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input rounded-circle" id="customFile" 
                               name="img" onchange="displayImg(this, $(this))"
                               accept="image/*">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                
                <div class="form-group d-flex justify-content-center">
                    <img src="<?php echo validate_image(isset($avatar) ? $avatar : '') ?>" 
                         alt="" id="cimg" class="img-fluid img-thumbnail">
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <div class="row">
                <button class="btn btn-sm btn-primary" form="edit-user">Update</button>
            </div>
        </div>
    </div>
</div>

<script>
function displayImg(input, _this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    let userId = $('#userId').val();
    if (userId) {
        $.ajax({
            url: _base_url_ + "classes/Adduser.php?f=get_user", // Ensure correct endpoint
            method: 'POST',
            data: { id: userId },
            dataType: 'json', // Expect JSON response
            success: function(response) {
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
                console.error("Error: ", error);
                console.error(xhr.responseText); // Log detailed error for debugging
                alert_toast("An error occurred while fetching user data", 'error');
            }
        });
    }
});

    

    // Handle password toggle
    $('#toggle-password').click(function() {
        const passwordInput = $('#password');
        const icon = $(this).find('i');

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Handle form submission
    $('#edit-user').submit(function(e) {
    e.preventDefault();
    
    // Client-side validation
    let form = this;
    if (!form.checkValidity()) {
        form.reportValidity();
        return false;
    }

    // Show loading state
    start_loader();
    $('.btn-primary').attr('disabled', true);

    // Create FormData object
    let formData = new FormData($(this)[0]);
    
    // MODIFICATION: Separate first and last name
    let fullName = $('#firstname').val() + ' ' + $('#lastname').val();
    formData.set('name', fullName);

    $.ajax({
        url: _base_url_ + "classes/Adduser.php?f=update",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success: function(resp) {
                resp = resp.trim();
                switch(resp) {
                    case '1':
                        alert_toast("Data successfully updated.", 'success');
                        setTimeout(function() {
                            location.href = "?page=manage_admin/index";
                        }, 2000);
                        break;
                    case '2':
                        alert_toast("An error occurred while updating the data.", 'error');
                        break;
                    case '3':
                        alert_toast("Please fill in all required fields.", 'warning');
                        break;
                    case '4':
                        alert_toast("Username already exists.", 'warning');
                        break;
                    default:
                        alert_toast("An unknown error occurred.", 'error');
                        console.error(resp);
                        break;
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert_toast("An error occurred: " + error, 'error');
            },
            complete: function() {
                end_loader();
                $('.btn-primary').attr('disabled', false);
            }
        });
    });

</script>