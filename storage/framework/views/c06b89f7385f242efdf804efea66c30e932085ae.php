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
        <?php elseif(trim($model) == 'gpt-4o-search-preview'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4o Search Preview')); ?></option>
        <?php elseif(trim($model) == 'gpt-4o-mini-search-preview'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4o mini Search Preview')); ?></option>
        <?php elseif(trim($model) == 'gpt-4-0125-preview'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4 Turbo')); ?></option>
        <?php elseif(trim($model) == 'gpt-4.5-preview'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4.5')); ?></option>
        <?php elseif(trim($model) == 'gpt-4.1'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4.1')); ?></option>
        <?php elseif(trim($model) == 'gpt-4.1-mini'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4.1 mini')); ?></option>
        <?php elseif(trim($model) == 'gpt-4.1-nano'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | GPT 4.1 nano')); ?></option>
        <?php elseif(trim($model) == 'o1'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | o1')); ?></option>
        <?php elseif(trim($model) == 'o1-mini'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | o1 mini')); ?></option>
        <?php elseif(trim($model) == 'o1-pro'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | o1 pro')); ?></option>
        <?php elseif(trim($model) == 'o3-mini'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | o3 mini')); ?></option>
        <?php elseif(trim($model) == 'o3'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | o3')); ?></option>
        <?php elseif(trim($model) == 'o4-mini'): ?>
            <option value="<?php echo e(trim($model)); ?>" <?php if(trim($model) == $default_model): ?> selected <?php endif; ?>><?php echo e(__('OpenAI | o4 mini')); ?></option>
        <?php else: ?>
            <?php $__currentLoopData = $fine_tunes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fine_tune): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(trim($model) == $fine_tune->model): ?>
                    <option value="<?php echo e(trim($model)); ?>"><?php echo e($fine_tune->description); ?> (<?php echo e(__('Fine Tune')); ?>)</option>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>									
</select>	<?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/components/openai-models-template.blade.php ENDPATH**/ ?>