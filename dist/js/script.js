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
			e.preventDefault();
	
			
			// Remove any previous error message
			if ($('.err_msg').length > 0) {
				$('.err_msg').remove();
			}
	
			$.ajax({
				url: _base_url_ + 'classes/Login.php?f=login',
				method: 'POST',
				data: $(this).serialize(),
				error: function(err) {
					console.log(err);
				},
				success: function(resp) {
					if (resp) {
						console.log("Response:", resp);
						resp = JSON.parse(resp);
						console.log("Remaining time:", resp.remaining_time);
			
						// Successful login
						if (resp.status == 'success') {
							location.replace(_base_url_ + 'admin');
						} 
						// Incorrect username or password
						else if (resp.status == 'incorrect') {
							var _frm = $('#login-frm');
							var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Incorrect username or password.</div>";
							_frm.prepend(_msg);
							_frm.find('input').addClass('is-invalid');
							$('[name="username"]').focus();
							end_loader();
						} 
						// Account is locked, show remaining time
						else if (resp.status == 'locked') {
							var _frm = $('#login-frm');
							var _msg = "<div class='alert alert-danger text-white err_msg' id='lockout-msg'><i class='fa fa-exclamation-triangle'></i> " + resp.message + "</div>";
							_frm.prepend(_msg);
			
							// If remaining_time is not null, start the timer
							if (resp.remaining_time !== null) {
								var remaining_time = resp.remaining_time;
			
								var timer = setInterval(function() {
									if (remaining_time <= 0) {
										clearInterval(timer);
									} else {
										remaining_time--;
										var minutes = Math.floor(remaining_time / 60);
										var seconds = remaining_time % 60;
										$('#lockout-msg').html("<i class='fa fa-exclamation-triangle'></i> Your account is locked. Please try again in " + minutes + " minute(s) and " + seconds + " second(s).");
									}
								}, 1000); // Update every second
							}
							end_loader();
						}
					}
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
