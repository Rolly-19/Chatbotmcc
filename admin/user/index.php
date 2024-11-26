<?php 
$user = $conn->query("SELECT * FROM users where id ='".$_settings->userdata('id')."'");
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo $_settings->userdata('id') ?>">
				<div class="form-group">
					<label for="name">First Name</label>
					<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
				</div>
				<div class="form-group">
					<label for="name">Last Name</label>
					<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
				</div>
				<div class="form-group">
    <label for="username">Email</label>
    <input type="email" name="username" id="username" class="form-control" 
           value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" 
           required autocomplete="off">
</div>

				<div class="form-group">
    <label for="phone">Phone Number</label>
    <input type="text" name="phone" id="phone" class="form-control" value="<?php echo isset($meta['phone']) ? $meta['phone']: '' ?>" maxlength="11" required>
</div>

				<div class="form-group">
					<label for="password">Password</label>
					<div class="input-group">
						<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
						<div class="input-group-append">
							<span class="input-group-text" id="toggle-password"><i class="fa fa-eye"></i></span>
						</div>
					</div>
					<small><i>Leave this blank if you don't want to change the password.</i></small>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Avatar</label>
					<div class="custom-file">
		              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		              <label class="custom-file-label" for="customFile">Choose file</label>
		            </div>
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary" form="manage-user">Update</button>
				</div>
			</div>
		</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$('#toggle-password').click(function(){
		const passwordField = $('#password');
		const passwordFieldType = passwordField.attr('type');
		const newType = passwordFieldType === 'password' ? 'text' : 'password';
		passwordField.attr('type', newType);
		$(this).find('i').toggleClass('fa-eye fa-eye-slash');
	});

	$('#manage-user').submit(function(e){
    e.preventDefault();
    
    // Client-side email validation
    var email = $('#username').val();
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        $('#msg').html('<div class="alert alert-danger">Please enter a valid email address.</div>');
        return false;
    }
    
    start_loader();
    $.ajax({
        url: _base_url_ + 'classes/Users.php?f=save',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success: function(resp) {
            if (resp == 1) {
                location.reload();
            } else {
                $('#msg').html('<div class="alert alert-danger">Username already exists</div>');
                end_loader();
            }
        }
    });
});
</script>
