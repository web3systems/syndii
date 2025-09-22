
<?php $__env->startSection('css'); ?>
	<!-- Sweet Alert CSS -->
	<link href="<?php echo e(URL::asset('plugins/sweetalert/sweetalert2.min.css')); ?>" rel="stylesheet" />
	<link href="<?php echo e(URL::asset('plugins/highlight/highlight.dark.min.css')); ?>" rel="stylesheet" />

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<form id="openai-form" action="" method="post" enctype="multipart/form-data" class="mt-24">		
	<?php echo csrf_field(); ?>
	<div class="row">	
		<div class="col-lg-4 col-md-12 col-sm-12">
			<div class="card border-0" id="template-input">
				<div class="card-body p-5 pb-0">

					<div class="row">
						<div class="template-view">
							<div class="template-icon mb-2 d-flex">
								<div>
									<i class="fa-solid fa-square-code blog-icon"></i>
								</div>
								<div>
									<h6 class="mt-1 ml-3 fs-16 number-font"><?php echo e(__('AI Code Generator')); ?></h6>
								</div>									
							</div>								
							<div class="template-info">
								<p class="fs-12 text-muted mb-4"><?php echo e(__('Create a various code by using only text commands')); ?></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<div class="text-left mb-5" id="balance-status">
								<?php if (isset($component)) { $__componentOriginale3df425532980655235957ec92e7e3b72c498067 = $component; } ?>
<?php $component = App\View\Components\BalanceTemplate::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('balance-template'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\BalanceTemplate::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3df425532980655235957ec92e7e3b72c498067)): ?>
<?php $component = $__componentOriginale3df425532980655235957ec92e7e3b72c498067; ?>
<?php unset($__componentOriginale3df425532980655235957ec92e7e3b72c498067); ?>
<?php endif; ?>
							</div>							
						</div>	
						<div class="col-sm-12">
							<div id="form-group" class="mb-5">
								<h6 class="fs-11 mb-2 font-weight-semibold"><?php echo e(__('Programming Language')); ?> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
								<select id="creativity" name="language" class="form-select" data-placeholder="<?php echo e(__('Specify Your Programming Language')); ?>">									
									<option value='c'><?php echo e(__('C')); ?></option>
									<option value='c#'><?php echo e(__('C#')); ?></option>
									<option value='c++'><?php echo e(__('C++')); ?></option>
									<option value='go'><?php echo e(__('Go')); ?></option>
									<option value='html'><?php echo e(__('HTML')); ?></option>
									<option value='javascript'> <?php echo e(__('JavaScript')); ?></option>																																																													
									<option value='java'> <?php echo e(__('Java')); ?></option>																																																													
									<option value='perl'> <?php echo e(__('Perl')); ?></option>																																																													
									<option value='php'> <?php echo e(__('PHP')); ?></option>																																																													
									<option value='python' selected> <?php echo e(__('Python')); ?></option>																																																													
									<option value='powershell'> <?php echo e(__('Powershell')); ?></option>																																																													
									<option value='ruby'> <?php echo e(__('Ruby')); ?></option>																																																													
									<option value='shell'> <?php echo e(__('Shell')); ?></option>																																																													
									<option value='swift'> <?php echo e(__('Swift')); ?></option>	
									<option value='flutter'> <?php echo e(__('flutter')); ?></option>																																																													
									<option value='typescript'> <?php echo e(__('TypeScript')); ?></option>		
									<option value='none'><?php echo e(__('None')); ?></option>																																																											
								</select>
							</div>
						</div>
						<div class="col-sm-12">								
							<div class="input-box">	
								<h6 class="fs-11 mb-2 font-weight-semibold"><?php echo e(__('Provide Instructions')); ?>  <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>									
								<div class="form-group">						    
									<textarea rows="15" cols="50" type="text" class="form-control <?php $__errorArgs = ['instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="instructions" name="instructions" placeholder="<?php echo e(__('Specify what kind of function or piece of code you want to generate')); ?>" required></textarea>
									<?php $__errorArgs = ['instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
										<p class="text-danger"><?php echo e($errors->first('instructions')); ?></p>
									<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
								</div> 
							</div> 
						</div>

						<div class="col-sm-12 mb-5">
							<div class="form-group">	
								<h6 class="fs-11 mb-2 font-weight-semibold"><?php echo e(__('AI Model')); ?></h6>								
								<select id="model" name="model" class="form-select" onchange="updateModel()">										
									<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>		
										<?php if(trim($model) == 'gpt-3.5-turbo-0125'): ?>
											<option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 3.5 Turbo')); ?></option>								
										<?php elseif(trim($model) == 'gpt-4'): ?>
											<option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4')); ?></option>
										<?php elseif(trim($model) == 'gpt-4o'): ?>
											<option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4o')); ?></option>
										<?php elseif(trim($model) == 'gpt-4o-mini'): ?>
											<option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4o mini')); ?></option>
										<?php elseif(trim($model) == 'gpt-4-0125-preview'): ?>
											<option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4 Turbo')); ?></option>
										<?php elseif(trim($model) == 'gpt-4-turbo-2024-04-09'): ?>
											<option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4 Turbo with Vision')); ?></option>
										<?php else: ?>
											<?php $__currentLoopData = $fine_tunes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fine_tune): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(trim($model) == $fine_tune->model): ?>
													<option value="<?php echo e(trim($model)); ?>"><?php echo e($fine_tune->description); ?> (<?php echo e(__('Fine Tune')); ?>)</option>
												<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
										
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>									
								</select>
							</div>
						</div>
					</div>						

					<div class="card-footer border-0 text-center p-0">
						<div class="w-100 pt-2 pb-2">
							<div class="text-center">
								<span id="processing" class="processing-image"><img src="<?php echo e(theme_url('img/svgs/upgrade.svg')); ?>" alt=""></span>
								<button type="submit" name="submit" class="btn btn-primary  pl-7 pr-7 fs-11 pt-2 pb-2" id="generate"><?php echo e(__('Generate Code')); ?></button>
							</div>
						</div>							
					</div>	
			
				</div>
			</div>			
		</div>

		<div class="col-lg-8 col-md-12 col-sm-12">
			<div class="card border-0" >
				<div class="card-body pb-2">
					<div class="row">						
						<div class="col-lg-4 col-md-12 col-sm-12">								
							<div class="input-box mb-2">								
								<div class="form-group">							    
									<input type="text" class="form-control <?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="document" name="document" value="<?php echo e(__('New Code')); ?>">
									<?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
										<p class="text-danger"><?php echo e($errors->first('document')); ?></p>
									<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
								</div> 
							</div> 
						</div>
						<div class="col-lg-4 col-md-12 col-sm-12">
							<div class="d-flex" id="template-buttons-group">	
								<div>
									<a id="export-txt" class="template-button mr-2" onclick="exportTXT();" href="#"><i class="fa-solid fa-file-lines table-action-buttons table-action-buttons-big view-action-button" data-tippy-content="<?php echo e(__('Download as Text File')); ?>"></i></a>
								</div>	
								<div>
									<a id="copy-button" class="template-button mr-2" onclick="copyText();" href="#"><i class="fa-solid fa-copy table-action-buttons table-action-buttons-big edit-action-button" data-tippy-content="<?php echo e(__('Copy Code')); ?>"></i></a>
								</div>
								<div>
									<a id="save-button" class="template-button" onclick="return saveText(this);" href="#"><i class="fa-solid fa-floppy-disk-pen table-action-buttons table-action-buttons-big delete-action-button" data-tippy-content="<?php echo e(__('Save Code')); ?>"></i></a>
								</div>					
							</div>
						</div>

					</div>
				</div>
			</div>

			<div class="card border-0" id="template-output">
				<div class="card-body">
					<div>	
						<div id="code-result"></div>	

						<div id="code-textarea">
							<pre>
								<code id="codetext"></code>
							</pre>						
						</div>				

						<div id="generating-message" class="text-muted"><span><?php echo e(__('Generate your code easily')); ?></span></div>	

						<div id="generating-status" class="text-muted text-center">
							<p class="mb-2"><?php echo e(__('Generating the code, please wait')); ?></p>
							<img src='<?php echo e(theme_url("img/svgs/code.svg")); ?>'>
						</div>	
						
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(URL::asset('plugins/richtext/jquery.richtext.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('plugins/sweetalert/sweetalert2.all.min.js')); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/markdown-it/13.0.1/markdown-it.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script type="text/javascript">
	$(function () {

		"use strict";

		
		// SUBMIT FORM
		$('#openai-form').on('submit', function(e) {

			e.preventDefault();

			let form = $(this);

			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'POST',
				url: 'code/process',
				data: form.serialize(),
				beforeSend: function() {
					$('#generate').html('');
					$('#generate').prop('disabled', true);	
					document.getElementById("generating-message").style.display="none";				
					document.getElementById("generating-status").style.display="block";				
					document.getElementById("code-result").style.opacity=0.1;				
					$('#processing').show().clone().appendTo('#generate'); 
					$('#processing').hide();          
				},
				complete: function() {
					$('#generate').prop('disabled', false);		
					document.getElementById("generating-status").style.display="none";	
					document.getElementById("code-result").style.opacity=1;				
					$('#processing', '#generate').empty().remove();
					$('#processing').hide();
					$('#generate').html('Generate Code');            
				},
				success: function (data) {		

					if (data['status'] == 'success') {
						let formatted_text=data['text'];
						let editor = document.getElementById('codetext');
						editor.innerHTML = '';

						var tempString = formatted_text.split(/(\```)(.+)(\```)/g);
						var isCode = 0;
						for (var i = 0; i < tempString.length; ++i) {
						  if (tempString[i].match(/(\```)/g)) {							
							isCode = 1;
						  } else if (tempString[i].match(/(\```)/g)) {							
							isCode = 0;
						  }
						  if (isCode == 1) {
							tempString[i] = tempString[i].replace(/<br\s*[\/]?>/gi, '\n').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
						  }
						}

						formatted_text = tempString.join("");
						
						editor.innerHTML += formatted_text;
						editor.innerHTML += '<br><br><br>';

						let save = document.getElementById('save-button');
						save.setAttribute('target', data['id']);

						// animateValue("available-words", data['old'], data['current'], 300);
						// animateValue("balance-number", data['old'], data['current'], 300);
					
						toastr.success('<?php echo e(__('Code was successfully generated')); ?>');
		
						updateChar();	
					} else {						
						Swal.fire('<?php echo e(__('Code Generation Error')); ?>', data['message'], 'warning');
					}

				},
				error: function(data) {
					$('#generate').prop('disabled', false);
            		$('#generate').html('Generate Code'); 
					console.log(data)
				}
			});
		});
	});

	function nl2br (str, is_xhtml) {
     	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
     	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
  	} 

	function updateChar(tCode) {
		var defaults = {
			html: true,
			xhtmlOut: false,
			breaks: false,
			langPrefix: 'language-',
			linkify: true,
			typographer: true,
			_highlight: true,
			_strict: false,
			_view: 'html'
		};
		defaults.highlight = function (str) {
			return '<pre class="hljs"><a class="copy-code" onclick="copyCode(this)" href="#"><?php echo e(__('Copy')); ?></a><code>' +
					   hljs.highlightAuto(str).value +
				   '</code></pre>'; 
		};
		var md = window.markdownit(defaults);
		$("#code-result").html(md.render($("#codetext")[0].innerText));
		
	}

	function copyText() {
		var r = document.createRange();
		r.selectNode(document.getElementById('code-result'));
		window.getSelection().removeAllRanges();
		window.getSelection().addRange(r);
		document.execCommand('copy');
		window.getSelection().removeAllRanges();

		toastr.success('<?php echo e(__('Code has been copied successfully')); ?>');
	}

	function exportTXT() {
		var elHtml = document.getElementById('codetext').innerText;
		var link = document.createElement('a');
		var mimeType = 'text/plain';

		link.setAttribute('download', 'document.txt');
		link.setAttribute('href', 'data:' + mimeType  +  ';charset=utf-8,' + encodeURIComponent(elHtml));
		link.click(); 
	}

	function animateValue(id, start, end, duration) {
		if (start === end) return;
		var range = end - start;
		var current = start;
		var increment = end > start? 1 : -1;
		var stepTime = Math.abs(Math.floor(duration / range));
		var obj = document.getElementById(id);
		var timer = setInterval(function() {
			current += increment;
			if (current > 0) {
				obj.innerHTML = current;
			} else {
				obj.innerHTML = 0;
			}
			
			if (current == end) {
				clearInterval(timer);
			}
		}, stepTime);
	}

	function saveText(event) {

		let textarea = document.getElementById('code-result').innerHTML;
		let title = document.getElementById('document').value;


		if (!event.target) {
			toastr.warning('<?php echo e(__('You will need to generate AI code first before saving')); ?>');
		} else {
			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'POST',
				url: '/app/user/code/save',
				data: { 'id': event.target, 'text': textarea, 'title': title},
				success: function (data) {					
					if (data['status'] == 'success') {
						toastr.success('<?php echo e(__('Code has been successfully saved')); ?>');
					} else {						
						toastr.warning('<?php echo e(__('There was an issue while saving your code')); ?>');
					}
				},
				error: function(data) {
					toastr.warning('<?php echo e(__('There was an issue while saving your code')); ?>');
				}
			});

			return false;
		}

	}

	function copyCode(e) {
		var input = document.createElement('textarea');
		input.innerHTML = $(e).siblings().clone().text();
		document.body.appendChild(input);
		input.select();
		var result = document.execCommand('copy');
		document.body.removeChild(input);
		toastr.success('<?php echo e(__('Code has been copied successfully')); ?>');
	}

	function updateModel() {
		let model = document.getElementById("model").value;

		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			method: 'POST',
			url: '/app/user/chat/model',
			data: { 'model': model},
			success: function (data) {					
				let balance = document.getElementById('balance-number');
				let model = document.getElementById('model-name');
				balance.innerHTML =  data['balance'];
				model.innerHTML =  data['model'];

			},
			error: function(data) {
			}
		});
	}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/user/codex/index.blade.php ENDPATH**/ ?>