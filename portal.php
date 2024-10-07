<style>
	#chat_convo {
		max-height: 100vh;
		background-color: #f0f0f0;
		background-image: url('wave.png');
		background-size: cover;
		background-position: center;
		background-repeat: no-repeat;
		color: #333333;
	}

	#chat_convo .direct-chat-messages {
		min-height: 350px;
		height: inherit;
		background-color: rgba(255, 255, 255, 0.8);
		border-radius: 10px;
		padding: 10px;
	}

	.direct-chat-msg .direct-chat-text {
		background-color: #ffffff;
		color: #333333;
		border-radius: 10px;
		padding: 10px;
	}

	.card-footer {
		background-color: #e9ecef;
	}

	.input-group textarea {
		border: 1px solid #ced4da;
		color: #495057;
	}

	.input-group textarea::placeholder {
		color: #6c757d;
	}

	.input-group-append .btn-primary {
		background-color: red;
		border-color: red;
	}

	.typing-indicator {
		display: flex;
		align-items: center;
	}

	.typing-indicator span {
		display: inline-block;
		width: 8px;
		height: 8px;
		margin: 0 2px;
		background-color: #999;
		border-radius: 50%;
		opacity: 0.6;
		animation: typing 1.5s infinite;
	}

	@keyframes typing {
		0%, 60%, 100% { transform: translateY(0); }
		30% { transform: translateY(-8px); }
	}
</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-lg-10 <?php echo isMobileDevice() == false ? "offset-1" : '' ?>">
			<div class="card direct-chat direct-chat-primary" id="chat_convo">
				<div class="card-header">
					<h3 class="card-title">Ask Me</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>
						<button type="button" class="btn btn-tool" id="reset-convo">
							<i class="fas fa-sync-alt"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="direct-chat-messages">
						<div class="direct-chat-msg mr-4">
							<img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')) ?>" alt="message user image">
							<div class="direct-chat-text">
								<?php echo htmlspecialchars($_settings->info('intro'), ENT_QUOTES, 'UTF-8'); ?>
							</div>
						</div>
					</div>
					<div class="end-convo"></div>
				</div>
				<div class="card-footer">
					<form id="send_chat" method="post">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
						<div class="input-group">
							<textarea type="text" name="message" placeholder="Type Message ..." class="form-control" required=""></textarea>
							<span class="input-group-append">
								<button type="submit" class="btn btn-primary">Send</button>
							</span>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="d-none" id="user_chat">
	<div class="direct-chat-msg right ml-4">
		<img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('user_avatar')) ?>" alt="message user image">
		<div class="direct-chat-text"></div>
	</div>
</div>
<div class="d-none" id="bot_chat">
	<div class="direct-chat-msg mr-4">
		<img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')) ?>" alt="message user image">
		<div class="direct-chat-text"></div>
	</div>
</div>
<div class="d-none" id="typing_indicator">
	<div class="direct-chat-msg mr-4">
		<img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')) ?>" alt="message user image">
		<div class="direct-chat-text typing-indicator">
			<span></span><span></span><span></span>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('[name="message"]').keypress(function(e) {
			if (e.which === 13 && !e.originalEvent.shiftKey) {
				$('#send_chat').submit();
				return false;
			}
		});
		$('#send_chat').submit(function(e) {
			e.preventDefault();
			var message = $('[name="message"]').val().trim();
			if (message === '') return false;

			// Clone user chat
			var uchat = $('#user_chat').clone();
			uchat.find('.direct-chat-text').html(htmlspecialchars(message));
			$('#chat_convo .direct-chat-messages').append(uchat.html());
			$('[name="message"]').val('');

			// Scroll to bottom
			$("#chat_convo .card-body").animate({
				scrollTop: $("#chat_convo .card-body").prop('scrollHeight')
			}, "fast");

			// Typing indicator
			var typingIndicator = $('#typing_indicator').clone();
			$('#chat_convo .direct-chat-messages').append(typingIndicator.html());
			$("#chat_convo .card-body").animate({
				scrollTop: $("#chat_convo .card-body").prop('scrollHeight')
			}, "fast");

			// AJAX request
			const requestData = {
				message: message
			};

			$.ajax({
				url: _base_url_ + "classes/Master.php?f=get_response",
				method: 'POST',
				data: requestData,
				error: function(err) {
					console.error(err);
					alert_toast("An error occurred.", 'error');
					end_loader();
				},
				success: function(resp) {
					if (resp) {
						resp = JSON.parse(resp);
						if (resp.status == 'success') {
							handleResponse(resp);
						}
					}
				}
			});

			function handleResponse(resp) {
				if (resp.message === "I am sorry. I can't understand your question. Please rephrase your question and make sure it is related to this site. Thank you :)") {
					// Call AI bot
					callAiBot(requestData);
				} else {
					displayBotMessage(resp.message, 2000);
				}
			}

			function callAiBot(requestData) {
				$.ajax({
					url: _base_url_ + "classes/api_handler.php",
					type: 'POST',
					contentType: 'application/json',
					data: JSON.stringify(requestData),
					success: function(response) {
						let data;
						try {
							data = JSON.parse(response);
						} catch (e) {
							console.error('Error parsing response:', e);
							return;
						}
						let msgData = data && data.text ? replaceNewlinesWithBr(data.text) : '';
						displayBotMessage(msgData, 1000);
					},
					error: function(xhr, status, error) {
						console.error('Error:', error);
						$('#response').text('An error occurred');
					}
				});
			}

			function displayBotMessage(message, delay) {
				setTimeout(() => {
					var bot_chat = $('#bot_chat').clone();
					bot_chat.find('.direct-chat-text').html(htmlspecialchars(message));
					$('#chat_convo .direct-chat-messages').append(bot_chat.html());
					$("#chat_convo .card-body").animate({
						scrollTop: $("#chat_convo .card-body").prop('scrollHeight')
					}, "fast");
					$('#chat_convo .direct-chat-messages .typing-indicator').parent().remove();
				}, delay);
			}

			function replaceNewlinesWithBr(text) {
				return text.replace(/\n/g, '<br>');
			}

			function htmlspecialchars(text) {
				return text.replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#039;');
			}
		});

		$('#reset-convo').click(function() {
			$('.direct-chat-messages').empty();
			$('.direct-chat-messages').append(`<div class="direct-chat-msg mr-4">
				<img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')) ?>" alt="message user image">
				<div class="direct-chat-text">
					<?php echo htmlspecialchars($_settings->info('intro'), ENT_QUOTES, 'UTF-8'); ?>
				</div>
			</div>`);
		});
	});
</script>
