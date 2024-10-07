<style>
	#chat_convo {
		max-height: 100vh;
		/* Increased height */
		background-color: #f0f0f0;
		/* Fallback background color */
		background-image: url('wave.png');
		/* Path to your background image */
		background-size: cover;
		/* Cover the entire area */
		background-position: center;
		/* Center the image */
		background-repeat: no-repeat;
		/* Do not repeat the image */
		color: #333333;
		/* Adjust text color for better readability */
	}

	#chat_convo .direct-chat-messages {
		min-height: 350px;
		/* Increased height */
		height: inherit;
		background-color: rgba(255, 255, 255, 0.8);
		/* Slightly transparent background for better text readability */
		border-radius: 10px;
		/* Optional: rounded corners */
		padding: 10px;
		/* Optional: add some padding */

	}

	.direct-chat-primary .right>.direct-chat-text {
		border-color: #d2d6de;
		color: #fff;
	}

	.direct-chat-msg .direct-chat-text {
		background-color: #ffffff;
		/* Chat bubble background color */
		color: #333333;
		/* Chat text color */
		border-radius: 10px;
		padding: 10px;
	}

	.direct-chat-msg.right .direct-chat-text {
		background-color: #ffffff;
		color: #333333;
		border-radius: 10px;
		padding: 10px;
	}

	.direct-chat-msg img {
		border: 1px solid red;
		/* Red border */
	}

	.card-footer {
		background-color: #e9ecef;
		/* Input area background color */
	}

	.input-group textarea {
		border: 1px solid #ced4da;
		color: #495057;
	}

	.input-group textarea::placeholder {
		color: #6c757d;
		/* Input placeholder text color */
	}

	.input-group-append .btn-primary {
		background-color: red;
		/* Send button background color */
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

	.typing-indicator span:nth-child(2) {
		animation-delay: 0.2s;
	}

	.typing-indicator span:nth-child(3) {
		animation-delay: 0.4s;
	}

	@keyframes typing {

		0%,
		60%,
		100% {
			transform: translateY(0);
		}

		30% {
			transform: translateY(-8px);
		}
	}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 <?php echo isMobileDevice() == false ? "offset-1" : ''; ?>">
            <div class="card direct-chat direct-chat-primary" id="chat_convo">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
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
                            <img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')); ?>" alt="message user image">
                            <div class="direct-chat-text">
                                <?php echo htmlspecialchars($_settings->info('intro'), ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="end-convo"></div>
                </div>
                <div class="card-footer">
                    <form id="send_chat" method="post">
                        <div class="input-group">
                            <textarea name="message" placeholder="Type Message ..." class="form-control" required=""></textarea>
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
        <img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('user_avatar')); ?>" alt="message user image">
        <div class="direct-chat-text"></div>
    </div>
</div>
<div class="d-none" id="bot_chat">
    <div class="direct-chat-msg mr-4">
        <img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')); ?>" alt="message user image">
        <div class="direct-chat-text"></div>
    </div>
</div>
<div class="d-none" id="typing_indicator">
    <div class="direct-chat-msg mr-4">
        <img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')); ?>" alt="message user image">
        <div class="direct-chat-text typing-indicator">
            <span></span><span></span><span></span>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Handle Enter key for message sending
        $('[name="message"]').keypress(function(e) {
            if (e.which === 13 && !e.originalEvent.shiftKey) {
                $('#send_chat').submit();
                return false;
            }
        });

        // Form submission handling
        $('#send_chat').submit(function(e) {
            e.preventDefault();
            var message = $('[name="message"]').val().trim();
            if (message === '') return;

            // Sanitize message for display
            var sanitizedMessage = $('<div>').text(message).html();
            var uchat = $('#user_chat').clone();
            uchat.find('.direct-chat-text').html(sanitizedMessage);
            $('#chat_convo .direct-chat-messages').append(uchat.html());
            $('[name="message"]').val('');
            scrollToBottom();

            // Show typing indicator
            var typingIndicator = $('#typing_indicator').clone();
            $('#chat_convo .direct-chat-messages').append(typingIndicator.html());
            scrollToBottom();

            // Send message to the server
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=get_response",
                method: 'POST',
                data: { message: message },
                error: function(err) {
                    console.log(err);
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                },
                success: function(resp) {
                    handleResponse(resp);
                }
            });
        });

        // Reset conversation
        $('#reset-convo').click(function() {
            resetChat();
        });

        // Scroll to the bottom of chat
        function scrollToBottom() {
            $("#chat_convo .card-body").animate({
                scrollTop: $("#chat_convo .card-body").prop('scrollHeight')
            }, "fast");
        }

        // Handle AJAX response
        function handleResponse(resp) {
            console.log(resp);
            if (resp) {
                resp = JSON.parse(resp);
                if (resp.status === 'success') {
                    if (resp.message === `I'm sorry, but the question you asked is not in my database yet. Please make sure your question is related to the school, try asking a different question, or check back later. Thank you!`) {
                        handleAIResponse();
                    } else {
                        displayBotMessage(resp.message, 2000);
                    }
                }
            }
        }

        // Handle AI response
        function handleAIResponse() {
            const message = $('[name="message"]').val().trim();
            $.ajax({
                url: _base_url_ + "classes/api_handler.php",
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ message: message }),
                success: function(response) {
                    let data;
                    try {
                        data = JSON.parse(response);
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        return;
                    }
                    let msgData = '';
                    if (data && data.text) {
                        msgData = $('<div>').text(data.text).html(); // Sanitize the AI response
                    }
                    displayBotMessage(msgData, 1000);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $('#response').text('An error occurred');
                }
            });
        }

        // Display bot message with a delay
        function displayBotMessage(message, delay) {
            setTimeout(() => {
                var bot_chat = $('#bot_chat').clone();
                bot_chat.find('.direct-chat-text').html(message);
                $('#chat_convo .direct-chat-messages').append(bot_chat.html());
                scrollToBottom();
                $('#chat_convo .direct-chat-messages .typing-indicator').parent().remove();
            }, delay);
        }

        // Reset chat function
        function resetChat() {
            $('.direct-chat-messages').empty();
            $('.direct-chat-messages').append(`
                <div class="direct-chat-msg mr-4">
                    <img class="direct-chat-img border-1 border-primary" src="<?php echo validate_image($_settings->info('bot_avatar')); ?>" alt="message user image">
                    <div class="direct-chat-text">
                        <?php echo htmlspecialchars($_settings->info('intro'), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>`);
        }
    });
</script>
