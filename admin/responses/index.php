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
    printWindow.document.write('<html><head><title>MCC CHAT - Responses List</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 20px; 
            line-height: 1.6;
            color: #333;
        }
        .print-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .print-header img {
            max-width: 120px;
            margin-right: 30px;
        }
        .print-header-text {
            text-align: center;
        }
        .print-header-text h1 {
            margin: 0;
            color: #000;
            font-size: 24px;
        }
        .print-header-text p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            margin: 20px 0; 
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        thead {
            background-color: #f2f2f2;
            color: #000;
        }
        th, td { 
            border: 1px solid #000; 
            padding: 12px; 
            text-align: left; 
        }
        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
        .truncate {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @media print { 
            body { margin: 0; padding: 0; } 
            table { 
                page-break-inside: avoid; 
                box-shadow: none;
            }
        }
        .print-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
    `);
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    
    // Enhanced header with logo and college name
    printWindow.document.write(`
        <div class="print-header">
            <img src="logo.png" alt="Madridejos Community College Logo">
            <div class="print-header-text">
                <h1>Madridejos Community College</h1>
                <p>Responses List</p>
            </div>
        </div>
    `);
    
    printWindow.document.write(tableHTML);
    
    // Add footer with date and page number
    printWindow.document.write(`
        <div class="print-footer">
            Generated on: ${new Date().toLocaleString()} | Page 1
        </div>
        <script>
            window.onload = function() {
                window.print();
            }
        <\/script>
    `);
    
    printWindow.document.write('</body></html>');
    printWindow.document.close();
}


</script>
