<style>
/* Rotated Horizontal Feedback Button */
#feedback_button {
    display: flex;
    width: fit-content;
    height: 38px;
    padding: 0px 20px;
    white-space: nowrap;
    justify-content: center;
    align-items: center;
    border-radius: 6px 6px 0px 0px;
    border: 0px;
    cursor: pointer;
    font-size: 1em;
    bottom: 50%; /* Align vertically in the center */
    gap: 10px; /* Spacing between elements inside the button */
    transform-origin: center bottom; /* Rotate around bottom center */
    pointer-events: all; /* Ensure the button is clickable */
    position: fixed;
    left: auto;
    right: 0px; /* Fixed to the right side */
    transform: translate(50%, 0px) rotate(-90deg); /* Rotate into position */
    background: rgb(159, 239, 0) !important; /* Greenish background */
    color: rgb(2, 2, 0) !important; /* Black text color */
    fill: rgb(2, 2, 0) !important; /* For SVG or icons inside */
    z-index: 1000; /* Ensure it’s above other content */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3); /* Subtle shadow */
}

/* Hover Effect */
#feedback_button:hover {
    background: rgb(135, 202, 0) !important; /* Darker green on hover */
    color: rgb(2, 2, 0) !important; /* Retain black text color */
    fill: rgb(2, 2, 0) !important;
}

/* Modal header styling */
.modal-header {
    background-color: white;
    color: #000;
    font-weight: bold;
}

/* Modal footer styling */
.modal-footer {
    justify-content: center;
}
/* Modal background styling */
.modal-content {
    background-image: url('wave.png'); /* Add your background image here */
    background-size: cover; /* Ensure the image covers the modal */
    background-repeat: no-repeat; /* Avoid repeating */
    background-position: center center; /* Center the image */
    color: black; /* Adjust text color to contrast with background */
}

/* Optional: Add transparency to the modal background */
.modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
    pointer-events: none; /* Allow interactions with underlying content */
}

.modal-body {
    position: relative; /* Ensure content is above the overlay */
    z-index: 2;
}



</style>

<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('config.php'); ?>
 <?php require_once('inc/header.php') ?>
  <body class="hold-transition layout-top-nav" >
    <div class="wrapper">
     <?php require_once('inc/topBarNav.php') ?>
              
     <?php $page = isset($_GET['page']) ? $_GET['page'] : 'portal';  ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper" style="min-height: 567.854px;">
        <!-- Content Header (Page header) -->
        <div class="content-header">
         
          <!-- /.container-fluid -->
          <?php require_once('inc/topBarNav.php') ?>
    <div class="content-wrapper">
      <section class="content">
        <div class="container">
          <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : 'portal';
            if (!file_exists($page . ".php") && !is_dir($page)) {
              include '404.html';
            } else {
              if (is_dir($page))
                include $page . '/index.php';
              else
                include $page . '.php';
            }
          ?>
        </div>
      </section>
    </div>
  </div>
   <!-- Sticky Feedback Button -->
   <!-- Sticky Feedback Button -->
   <button id="feedback_button" data-bs-toggle="modal" data-bs-target="#feedback_modal">
   <img src="images.png" alt="Feedback" style="width: 20px; height: 20px; transform: rotate(90deg);">
    Feedback
</button>

       <!-- Feedback Modal -->
      

  
        <!-- /.gg -->
        <!-- /.content -->
        
        <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
      </div>
      
      <!-- /.content-wrapper -->
      <?php require_once('inc/footer.php') ?>
 <!-- JavaScript -->
 
 <script>
    $(document).ready(function() {
      // Ensure Swal is available
      if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded');
        return;
      }

      // Initialize the modal
      var feedbackModal = new bootstrap.Modal(document.getElementById('feedback_modal'), {
        keyboard: false
      });

      // Feedback button click handler (optional, but can be useful)
      $('#feedback_button').on('click', function() {
        console.log('Feedback button clicked');
        feedbackModal.show();
      });

      // Form submission handler
      $('#feedback_form').on('submit', function(e) {
        e.preventDefault();

        const feedbackData = {
          feedback_text: $('#feedback_text').val(),
          rating: $('#rating').val()
        };

        $.ajax({
          url: 'save_feedback.php', // Ensure this path is correct
          type: 'POST',
          dataType: 'json', // Expect JSON response
          data: feedbackData,
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Thank you!',
              text: response.message || 'Your feedback has been submitted successfully.'
            });
            
            // Hide the modal
            feedbackModal.hide();

            // Reset the form
            $('#feedback_form')[0].reset();
          },
          error: function(xhr, status, error) {
            Swal.fire({
              icon: 'error',
              title: 'Oops!',
              text: 'An error occurred: ' + (xhr.responseText || error)
            });
          }
        });
      });
    });
  </script>
</body>
</html>


