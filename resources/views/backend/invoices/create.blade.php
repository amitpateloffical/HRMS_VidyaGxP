@extends('layouts.backend')

@section('styles')
<!-- Select2 CSS -->
<link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.min.css')}}">
<!-- Datetimepicker CSS -->
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('page-header')
<div class="row align-items-center">
	<div class="col">
		<h3 class="page-title">Create Invoice</h3>
		<ul class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
			<li class="breadcrumb-item active">Create Invoice</li>
		</ul>
	</div>
	
</div>
@endsection


@section('content')
<div class="row">
	<div class="col-sm-12">
		<form action="{{route('invoices.store')}}" method="post" enctype="multipart/form-data">
			@csrf
			<div class="row">
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Client <span class="text-danger">*</span></label>
						<select class="select2" name="client">
							<option value="null">Select Client</option>
							@foreach (\app\Models\Client::get() as $client)
                                <option value="{{$client->id}}">{{$client->firstname.' '.$client->lastname}}</option>
                            @endforeach
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Project <span class="text-danger">*</span></label>
						<select class="select2" name="project" title="select project">
							<option value="null">Select Project</option>
							@foreach (\app\Models\Project::get() as $project)
								<option value="{{$project->id}}">{{$project->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Email <span class="text-danger">*</span></label>
						<input class="form-control" type="email" name="email">
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Tax <span class="text-danger">*</span></label>
						<select name="tax" class="select2" title="select tax" id="inv_tax">
							<option value="null">Select Tax</option>
							@foreach (\app\Models\Tax::get() as $tax)
							<option value="{{$tax->id}}">{{$tax->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Client Address <span class="text-danger">*</span></label>
						<textarea class="form-control" rows="3" name="client_address"></textarea>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Billing Address <span class="text-danger">*</span></label>
						<textarea class="form-control" rows="3" name="billing_address"></textarea>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Invoice date <span class="text-danger">*</span></label>
						<div class="cal-icon">
							<input class="form-control datetimepicker"   id="start_date_input" type="text" name="invoice_date">
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label>Due Date <span class="text-danger">*</span></label>
						<div class="cal-icon">
							<input class="form-control datetimepicker" type="text"  id="end_date_input" name="due_date">
						</div>
					</div>
				</div>
			</div>

			<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
			<script>
				$(document).ready(function() {
					console.log('jquery iniialized')
					// Initialize datetimepicker for start date
					$('#start_date_input').datetimepicker({
						format: 'YYYY-MM-DD',
						minDate: moment().startOf('day'), // Set min date to today
					});

					// Initialize datetimepicker for end date
					$('#end_date_input').datetimepicker({
						format: 'YYYY-MM-DD',
						useCurrent: false, // Do not automatically set to current date
					});

					// Set min date for end date based on start date
					$('#start_date_input').on('dp.change', function(e) {
						$('#end_date_input').data('DateTimePicker').minDate(e.date);
					});

					// Set max date for start date based on end date
					$('#end_date_input').on('dp.change', function(e) {
						$('#start_date_input').data('DateTimePicker').maxDate(e.date);
					});
				});
			</script>


			<div class="row">
				<div class="col-md-12 col-sm-12">
					<div class="table-responsive">
						<table class="table table-hover table-white repeater">
							<thead>
								<tr>
									<th>#</th>
									<th class="col-sm-2">Item</th>
									<th class="col-md-6">Description</th>
									<th style="width:100px;">Unit Cost<span class="text-danger">* in number</span></th>
									<th style="width:80px;">Qty<span class="text-danger">* in number</span></th>
									<th>Amount</th>
									<th><button type="button" class="btn btn-sm btn-success font-18 mr-1" title="Add" data-repeater-create>
										<i class="fa fa-plus"></i>
									</button> </th>
								</tr>
							</thead>
							<tbody data-repeater-list="items">
								<tr data-repeater-item>
									<td>
										<input type="text" name="id" class="form-control" style="min-width:50px" readonly value="1">
									</td>
									<td>
										<input class="form-control" name="name" type="text" style="min-width:150px">
									</td>
									<td>
										<input class="form-control" name="description" type="text" style="min-width:150px">
									</td>
									<td>
  									  <input class="form-control" name="unit_cost" id="unit_cost" style="width:100px" type="number" oninput="calculateAmount()">
															</td>
															<td>
    									<input class="form-control" name="quantity" id="quantity" style="width:80px" type="number" oninput="calculateAmount()">
															</td>
															<td>
    								<input class="form-control" name="amount" id="amount" readonly style="width:120px" type="text">
													</td>
									<td>
										<button type="button" class="btn btn-sm btn-danger font-18 ml-2" title="Delete" data-repeater-delete>
											<i class="fa fa-trash"></i>
										</button>
										
									</td>
								</tr>	
							</tbody>
						</table>
					</div>
				</div>
			</div>



			
			




<script>
    function calculateAmount() {
        // Get unit cost and quantity input values
        var unitCost = document.getElementById('unit_cost').value;
        var quantity = document.getElementById('quantity').value;

        // Check if both unit cost and quantity are provided
        if (unitCost !== '' && quantity !== '') {
            // Calculate the amount as the product of unit cost and quantity
            var amount = parseFloat(unitCost) * parseInt(quantity);
            // Update the amount field with the calculated value
            document.getElementById('amount').value = amount.toFixed(2); // Adjust the decimal places as needed
        } else {
            // If either unit cost or quantity is empty, clear the amount field
            document.getElementById('amount').value = '';
        }
    }
</script>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Discount <span class="text-danger">* must be a number</span></label>
						<input class="form-control text-right" type="number" name="discount" value="">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Status</label>
						<select name="status" class="select2 form-control">
							<option value="paid">Paid</option>
							<option value="pending">Pending</option>
						</select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label>Other Information</label>
						<textarea class="form-control" name="note"></textarea>
					</div>
				</div>	
			</div>
			<div class="submit-section">
				<button class="btn btn-primary submit-btn">Save</button>
			</div>
		</form>
	</div>
</div> 
@endsection


@section('scripts')
<!-- Select2 JS -->
<script src="{{asset('assets/plugins/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/plugins/jquery-repeater/jquery.repeater.min.js')}}"></script>
<script>
    $(document).ready(function(){
        'use strict';
		var index = 0;
		
		var tax = $('#inv_tax').val();
		
        $('table.repeater').repeater({

            show: function () {
				var id = $(`input[name="items[${index}][id]"]`);
				var name = $(`input[name="items[${index}][name]"]`);
				var unit_cost = $(`input[name="items[${index}][unit_cost]"]`);
				var quantity = $(`input[name="items[${index}][quantity]"]`);
				var amount = $(`input[name="items[${index}][amount]"]`);
				var item_amount = unit_cost.val() * quantity.val();
				amount.val(item_amount);
				
				index++;
				id.val(index).trigger('change');
				$(this).slideDown();
            },
			
            hide: function (deleteElement) {
				index--;
				$(this).slideUp(deleteElement);
            },
			
        });


    });
</script>
@endsection