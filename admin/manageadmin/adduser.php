<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid">
            <div id="msg"></div>
            <form action="" id="manage-user" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" class="form-control" 
                           required pattern="[A-Za-z\s]+" 
                           title="Please enter a valid first name (letters and spaces only)">
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" 
                           required pattern="[A-Za-z\s]+" 
                           title="Please enter a valid last name (letters and spaces only)">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" 
                           required minlength="4" 
                           title="Username must be at least 4 characters long"
                           autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" 
                           required maxlength="11" pattern="[0-9]{11}" 
                           title="Please enter a valid 11-digit phone number">
                </div>
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
                <div class="form-group">
                    <label for="customFile" class="control-label">Avatar</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input rounded-circle" id="customFile" 
                               name="img" onchange="displayImg(this,$(this))"
                               accept="image/*">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center">
                    <img src="path/to/default/avatar.jpg" alt="" id="cimg" 
                         class="img-fluid img-thumbnail">
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <div class="row">
                <button class="btn btn-sm btn-primary" form="manage-user">Save</button>
            </div>
        </div>
    </div>
</div>

<style>
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100% 100%;
    }
    
    .custom-file-input:focus ~ .custom-file-label {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    #toggle-password {
        cursor: pointer;
    }
    
    .alert {
        margin-bottom: 1rem;
    }
</style>

<script>
$(document).ready(function() {
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
    $('#manage-user').submit(function(e) {
        e.preventDefault();
        
        // Show loading state
        $('.btn-primary').attr('disabled', true).html('Saving...');
        
        // Create FormData object to handle file uploads
        let formData = new FormData($(this)[0]);
        
        $.ajax({
            url: 'manageuser.php',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                let response = JSON.parse(resp);
                
                if (response.status == 'success') {
                    // Show success message
                    $('#msg').html(`
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>
                            ${response.msg}
                        </div>
                    `);
                    
                    // Reset form if it's an add operation
                    if (!formData.get('id')) {
                        $('#manage-user')[0].reset();
                        $('#cimg').attr('src', 'DEFAULT_AVATAR_URL');
                        $('.custom-file-label').html('Choose file');
                    }
                } else {
                    // Show error message
                    $('#msg').html(`
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            ${response.msg}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                // Show error message for ajax failure
                $('#msg').html(`
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                        An error occurred while processing your request.
                    </div>
                `);
            },
            complete: function() {
                // Reset button state
                $('.btn-primary').attr('disabled', false).html('Save');
            }
        });
    });
});

// Handle image preview
function displayImg(input, _this) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
        _this.siblings('.custom-file-label').html(input.files[0].name);
    } else {
        $('#cimg').attr('src', 'DEFAULT_AVATAR_URL');
        _this.siblings('.custom-file-label').html('Choose file');
    }
}

</script>