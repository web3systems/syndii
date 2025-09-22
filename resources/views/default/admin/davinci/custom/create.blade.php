@extends('layouts.app')
@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
<!-- PAGE HEADER -->
<div class="page-header mt-5-7">
	<div class="page-leftheader">
		<h4 class="page-title mb-0">{{ __('Create Custom Template') }}</h4>
		<ol class="breadcrumb mb-2">
			<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-feather mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
			<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.davinci.dashboard') }}"> {{ __('Template Settings') }}</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Create Custom Template') }}</a></li>
		</ol>
	</div>
	<div class="page-rightheader">
		<a href="{{ route('admin.davinci.custom.category') }}" class="btn btn-primary mt-1 ripple">{{ __('Category Manager') }}</a>
	</div>
</div>
<!-- END PAGE HEADER -->
@endsection
@section('content')	
	<div class="row">
		<div class="col-sm-12">
			<div class="card border-0">				
				<div class="card-body pt-5">
					<form class="w-100" action="{{ route('admin.davinci.custom.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="row">	

							<div class="col-lg-6 col-md-12 col-sm-12">													
								<div class="input-box">								
									<h6>{{ __('Template Name') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('name') is-danger @enderror" id="name" name="name" placeholder="{{ __('Provide Template Name') }}">
										@error('name')
											<p class="text-danger">{{ $errors->first('name') }}</p>
										@enderror
									</div> 
								</div> 
							</div>

							<div class="col-lg-6 col-md-12 col-sm-12">													
								<div class="input-box">								
									<h6>{{ __('Template Description') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('description') is-danger @enderror" id="description" name="description" placeholder="{{ __('Provide Template Description') }}">
										@error('description')
											<p class="text-danger">{{ $errors->first('description') }}</p>
										@enderror
									</div> 
								</div> 
							</div>
								
							<div class="col-lg-6 col-md-12 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Template Category') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select id="image-feature-user" name="category" class="form-select" data-placeholder="{{ __('Select template category') }}">
										@foreach ($categories as $category)
											<option value="{{ $category->code }}"> {{ __(ucfirst($category->name)) }}</option>
										@endforeach																																																													
									</select>
								</div>
							</div>	

							<div class="col-lg-6 col-md-12 col-sm-12">													
								<div class="input-box">								
									<h6>{{ __('Template Icon') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('You will need to get it from fontawesome.com') }}"></i></h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('icon') is-danger @enderror" id="icon" name="icon" placeholder="ex: <i class='fa-solid fa-books'></i>">
										@error('icon')
											<p class="text-danger">{{ $errors->first('icon') }}</p>
										@enderror
									</div> 
								</div> 
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Templates Package') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<select id="templates-admin" name="package" class="form-select" data-placeholder="{{ __('Set Templates Package') }}">
										<option value="free">{{ __('Free Package') }}</option>
										<option value="all" selected>{{ __('Standard Package') }}</option>
										<option value="professional"> {{ __('Professional Package') }}</option>																															
										<option value="premium"> {{ __('Premium Package') }}</option>																																																																																																									
									</select>
								</div>
							</div>
						</div>

						<hr class="text-center mb-4 mt-2">

						<div class="row">
							<div class="col-md-12 col-sm-12 mt-2 mb-4">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="model_mode" class="custom-switch-input">
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Use Fixed Model for this Template') }} <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('By turning this option on, this template will be fixed to use only the model that is set below and user will not be able to change the model during template usage') }}"></i></span>
									</label>
								</div>
							</div>

							<div class="col-lg-6 col-md-12 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Set Model for this Template') }} </h6>
									<select id="model" name="model" class="form-select">
										<option value='gpt-3.5-turbo-0125' selected>{{ __('GPT 3.5 Turbo') }}</option>
										<option value='gpt-4'>{{ __('GPT 4') }}</option>																																																																																																																																																																																																																																																																																																																																																			
										<option value='gpt-4o'>{{ __('GPT 4o') }}</option>																																																																																																																																																																																																																																																																																																																																																			
										<option value='gpt-4o-mini'>{{ __('GPT 4o mini') }}</option>																																																																																																																																																																																																																																																																																																																																																			
										<option value='gpt-4-0125-preview'>{{ __('GPT 4 Turbo') }}</option>																																																																																																																											
										<option value='gpt-4-turbo-2024-04-09'>{{ __('GPT 4 Turbo with Vision') }}</option>										
										{{-- <option value="claude-3-opus-20240229">{{ __('Claude 3 Opus') }}</option>											
										<option value="claude-3-sonnet-20240229">{{ __('Claude 3 Sonnet') }}</option>									
										<option value="claude-3-haiku-20240307">{{ __('Claude 3 Haiku') }}</option>																																																																																																																																																																																						 --}}
										@foreach ($models as $model)
											<option value="{{ $model->model }}"> {{ $model->description }} ({{ __('Fine Tune Model')}})</option>
										@endforeach																																																										
									</select>
								</div>
							</div>
						</div>

						<hr class="text-center mt-2 mb-4">

						<div class="row">
							<div class="col-lg-6 col-md-12 col-sm-12 mt-2">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="activate" class="custom-switch-input" checked>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Activate Template') }}</span>
									</label>
								</div>
							</div>

							<div class="col-lg-6 col-md-12 col-sm-12 mt-2">
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="tone" class="custom-switch-input">
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Include Tone of Voice field') }}</span>
									</label>
								</div>
							</div>
						</div>

						<hr class="text-center mb-1 mt-4">

						<div class="row">
							<div class="col-sm-12 mt-4 mb-5">
								<div class="form-group">								
									<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('User Input Fields') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="field input-group mb-4">
										<input type="hidden" name="code[]" value="input-field-1">
										<input type="text" class="form-control" name="names[]" placeholder="{{ __('Enter Input Field Title (Required)') }}" id="input-field-1">
										<input type="text" class="form-control" name="placeholders[]" placeholder="{{ __('Enter Input Field Description') }} ({{ __('Required') }})">
										<select class="form-select mr-4" name="input_field[]" onchange="notifyUser(this)">
											<option value="input" selected>{{ __('Input Field') }}</option>
											<option value="textarea">{{ __('Textarea Field') }}</option>
											<option value="select">{{ __('Select List Field') }}</option>
											<option value="checkbox">{{ __('Checkbox List Field') }}</option>
											<option value="radio">{{ __('Radio Buttons Field') }}</option>
										</select>
										<select class="form-select" name="status_field[]">
											<option value="optional" selected>{{ __('Optional') }}</option>
											<option value="required">{{ __('Required') }}</option>
										</select>
										<span onclick="addField(this)" class="btn btn-primary">
											<i class="fa fa-btn fa-plus"></i>
										</span>
										<span onclick="removeField(this)" class="btn btn-primary">
											<i class="fa fa-btn fa-minus"></i>
										</span>										
									</div>
									<div id="field-container"></div>
								</div>
							</div>

							<div class="col-sm-12">								
								<div class="input-box">								
									<h6 class="fs-11 mb-2 font-weight-semibold">{{ __('Custom Prompt') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">
										<div id="field-buttons"></div>							    
										<textarea type="text" rows=10 class="form-control @error('prompt') is-danger @enderror" id="prompt" name="prompt"></textarea>
										@error('prompt')
											<p class="text-danger">{{ $errors->first('prompt') }}</p>
										@enderror
									</div> 
								</div> 
							</div>
				
							<div class="col-md-12 col-sm-12 text-center mb-2">
								<button type="submit" class="btn btn-primary pl-7 pr-7 ripple">{{ __('Create') }}</button>	
							</div>	
							
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";

		});

		let i = 2;
		function addField(plusElement, k){

			if (k == 2) {
				i = 3;
			}

			let required_type = plusElement.previousElementSibling;
			let field_type = required_type.previousElementSibling;
			let placeholder = field_type.previousElementSibling;	
			let name = placeholder.previousElementSibling;	
		
			// Stopping the function if the input field has no value.
			if(placeholder.previousElementSibling.value.trim() === ""){
				return false;
			}

			createButton(name.id);
			
			let new_field ='<div class="field input-group mb-4">' +
								'<input type="hidden" name="code[]" value="input-field-' + i + '">' +
								'<input type="text" class="form-control" name="names[]" id="input-field-' + i + '" placeholder="{{ __('Enter Input Field Title (Required)') }}">' +
								'<input type="text" class="form-control" placeholder="{{ __('Enter Input Field Description') }} ({{ __('Required') }})" name="placeholders[]">' +								
								'<select class="form-select mr-4" name="input_field[]" onchange="notifyUser(this)">' +
									'<option value="input" selected>{{ __('Input Field') }}</option>' +
									'<option value="textarea">{{ __('Textarea Field') }}</option>' +
									'<option value="select">{{ __('Select List Field') }}</option>' +
									'<option value="checkbox">{{ __('Checkbox List Field') }}</option>' +
									'<option value="radio">{{ __('Radio Buttons Field') }}</option>' +
								'</select>' +
								'<select class="form-select" name="status_field[]">' +
									'<option value="optional" selected>{{ __('Optional') }}</option>' +
									'<option value="required">{{ __('Required') }}</option>' +
								'</select>' +
								'<span onclick="addField(this)" class="btn btn-primary">' +
									'<i class="fa fa-btn fa-plus"></i>' +
								'</span>' +
								'<span onclick="removeField(this)" class="btn btn-primary">' +
									'<i class="fa fa-btn fa-minus"></i>' +
								'</span>' +
							'</div>';
			i++;
   			$("#field-container").append(new_field);

			// Un hiding the minus sign.
			plusElement.nextElementSibling.style.display = "block"; 
			// Hiding the plus sign.
			plusElement.style.display = "none"; 
		}

		function removeField(minusElement){
			let plusElement = minusElement.previousElementSibling;
			let field_type = plusElement.previousElementSibling;
			let placeholder = field_type.previousElementSibling;	
			let name = placeholder.previousElementSibling;	

			deleteButton(name.id);

			minusElement.parentElement.remove();
		}

		function createButton(id) {
			let new_button = '<span onclick="insertText(this)" id="' + id+'-button" class="btn btn-primary mr-4 mb-2">' + id + '</span>';
   			$("#field-buttons").append(new_button);
		}

		function deleteButton(id) {
			let button = document.getElementById(id + '-button');
			button.remove();
		}

		function insertText(value) {
			insertToPrompt(" ###" + value.innerHTML + "### ");
		}

		function insertToPrompt(text) {
			var curPos = document.getElementById("prompt").selectionStart;
			let x = $("#prompt").val();
			$("#prompt").val(x.slice(0, curPos) + text + x.slice(curPos));
		}

		function notifyUser(input) {
			let placeholder = input.previousElementSibling;

			switch (input.value) {
				case 'input':
				case 'textarea':
					placeholder.setAttribute('placeholder', 'Enter Input Field Description (Required)');
					break;
				case 'select':
					placeholder.setAttribute('placeholder', 'Enter Comma separated Options for Select List (Required)');
					break;
				case 'checkbox':
					placeholder.setAttribute('placeholder', 'Enter Comma separated Values for Checkboxes (Required)');
					break;
				case 'radio':
					placeholder.setAttribute('placeholder', 'Enter Comma separated Values for Radio Buttons (Required)');
					break;
				default:
					placeholder.setAttribute('placeholder', 'Enter Input Field Description (Required)');
					break;
			}
		}
	</script>
@endsection