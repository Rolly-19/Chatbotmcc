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
		<h3 class="card-title">List of Questions Responses</h3>
		<div class="card-tools">
			<a href="?page=responses/manage" class="btn btn-flat btn-info"><span class="fas fa-plus"></span>  Create New</a>
			<button class="btn btn-flat btn-secondary" onclick="printTable()"><span class="fas fa-print"></span> Print</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-bordered table-stripped" id="responseTable">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="60%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Question</th>
						<th>Response</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT q.id, r.response_message, q.question FROM `questions` q inner join `responses` r on q.response_id = r.id order by q.question asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['question'] ?></td>
							<td><span class="truncate"><?php echo $row['response_message'] ?></span></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=responses/manage&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this data?","delete_question",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})

	function delete_question($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_response",
			method:"POST",
			data:{id: $id},
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(resp == 1){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}

	function printTable() {
    var printWindow = window.open('', '', 'height=600,width=800');
    var table = document.getElementById('responseTable').cloneNode(true);

    // Remove the action column from the table
    var headers = table.getElementsByTagName('thead')[0].getElementsByTagName('tr')[0];
    var cells = headers.getElementsByTagName('th');
    var actionIndex = -1;

    for (var i = 0; i < cells.length; i++) {
        if (cells[i].innerText.trim() === 'Action') {
            actionIndex = i;
            break;
        }
    }

    if (actionIndex !== -1) {
        // Remove action column from headers
        headers.removeChild(cells[actionIndex]);

        // Remove action column from rows
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (var i = 0; i < rows.length; i++) {
            rows[i].removeChild(rows[i].getElementsByTagName('td')[actionIndex]);
        }
    }

    // Print the table without action column
    var tableHTML = table.outerHTML;
    printWindow.document.write('<html><head><title>Print Table</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 20px 0; }');
    printWindow.document.write('th, td { border: 1px solid black; padding: 8px; text-align: left; }');
    printWindow.document.write('th { background-color: #f2f2f2; }');
    printWindow.document.write('h1, p { text-align: center; margin: 0; padding: 0; }');
    printWindow.document.write('@media print { body { margin: 0; padding: 0; } table { page-break-inside: avoid; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div style="text-align: center;">');
    printWindow.document.write('<img src="logo.png" alt="Logo" style="max-width: 150px; margin-bottom: 10px;">'); // Add logo URL and style it
    printWindow.document.write('<h1>Madridejos Community College</h1>'); // Add a title to the print page
    printWindow.document.write('<p>Responses List</p>'); // Add a subtitle to the print page
    printWindow.document.write('</div>');
    printWindow.document.write(tableHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}


</script>
