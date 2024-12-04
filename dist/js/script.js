function start_loader(){
	$('body').append('<div id="preloader"><div class="loader-holder"><div></div><div></div><div></div><div></div>')
}
function end_loader(){
	 $('#preloader').fadeOut('fast', function() {
		$('#preloader').remove();
      })
}
// function 
window.alert_toast= function($msg = 'TEST',$bg = 'success' ,$pos=''){
	   	 var Toast = Swal.mixin({
	      toast: true,
	      position: $pos || 'top-end',
	      showConfirmButton: false,
	      timer: 5000
	    });
	      Toast.fire({
	        icon: $bg,
	        title: $msg
	      })
	  }

$(document).ready(function(){
	// Login
	$('#login-frm').submit(function(e) {
		e.preventDefault(); // Prevent traditional form submission
		start_loader(); // Start loader animation
		$('.err_msg').remove(); // Remove previous error messages
		
		$.ajax({
			url: _base_url_ + 'classes/Login.php?f=login', // Login handler URL
			method: 'POST',
			data: $(this).serialize(), // Serialize form data
			success: function(resp) {
				if (resp) {
					resp = JSON.parse(resp);
					if (resp.status === 'success') {
						Swal.fire({
							icon: 'success',
							title: 'Login Successful!',
							text: 'You will be redirected to the dashboard.',
							showConfirmButton: false,
							timer: 2000 // Auto close after 2 seconds
						}).then(() => {
							location.replace(_base_url_ + 'admin'); // Redirect after success
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Login Failed',
							text: resp.message, // Show error message
						});
						var _frm = $('#login-frm');
						var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> " + resp.message + "</div>";
						_frm.prepend(_msg); // Add the error message above the form
						_frm.find('input').addClass('is-invalid'); // Mark inputs as invalid
						$('[name="username"]').focus(); // Focus on the username field
					}
				}
				end_loader(); // Stop loader animation
			},
			error: function(xhr, status, error) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'An unexpected error occurred. Please try again later.'
				});
				var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Login failed. Please try again.</div>";
				$('#login-frm').prepend(_msg); // Add error message to the form
				end_loader(); // Stop loader animation
			}
		});
	});
	//Establishment Login
	$('#flogin-frm').submit(function(e){
		e.preventDefault()
		start_loader()
		if($('.err_msg').length > 0)
			$('.err_msg').remove()
		$.ajax({
			url:_base_url_+'classes/Login.php?f=flogin',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)

			},
			success:function(resp){
				if(resp){
					resp = JSON.parse(resp)
					if(resp.status == 'success'){
						location.replace(_base_url_+'faculty');
					}else if(resp.status == 'incorrect'){
						var _frm = $('#flogin-frm')
						var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Incorrect email or password</div>"
						_frm.prepend(_msg)
						_frm.find('input').addClass('is-invalid')
						$('[name="username"]').focus()
					}
						end_loader()
				}
			}
		})
	})

	//user login
	$('#slogin-frm').submit(function(e){
		e.preventDefault()
		start_loader()
		if($('.err_msg').length > 0)
			$('.err_msg').remove()
		$.ajax({
			url:_base_url_+'classes/Login.php?f=slogin',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)

			},
			success:function(resp){
				if(resp){
					resp = JSON.parse(resp)
					if(resp.status == 'success'){
						location.replace(_base_url_+'student');
					}else if(resp.status == 'incorrect'){
						var _frm = $('#slogin-frm')
						var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Incorrect email or password</div>"
						_frm.prepend(_msg)
						_frm.find('input').addClass('is-invalid')
						$('[name="username"]').focus()
					}
						end_loader()
				}
			}
		})
	})
	// System Info
	$('#system-frm').submit(function(e){
		e.preventDefault()
		start_loader()
		if($('.err_msg').length > 0)
			$('.err_msg').remove()
		$.ajax({
			url:_base_url_+'classes/SystemSettings.php?f=update_settings',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					// alert_toast("Data successfully saved",'success')
						location.reload()
				}else{
					$('#msg').html('<div class="alert alert-danger err_msg">An Error occured</div>')
					end_load()
				}
			}
		})
	})
})
