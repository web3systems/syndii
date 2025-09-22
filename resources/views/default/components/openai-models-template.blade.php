<select id="model" name="model" class="form-select" onchange="updateModel()">										
    @foreach ($models as $model)		
        @if (trim($model) == 'gpt-3.5-turbo-0125')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 3.5 Turbo') }}</option>								
        @elseif (trim($model) == 'gpt-4')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4') }}</option>
        @elseif (trim($model) == 'gpt-4o')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o') }}</option>
        @elseif (trim($model) == 'gpt-4o-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o mini') }}</option>
        @elseif (trim($model) == 'gpt-4o-search-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o Search Preview') }}</option>
        @elseif (trim($model) == 'gpt-4o-mini-search-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4o mini Search Preview') }}</option>
        @elseif (trim($model) == 'gpt-4-0125-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4 Turbo') }}</option>
        @elseif (trim($model) == 'gpt-4.5-preview')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.5') }}</option>
        @elseif (trim($model) == 'gpt-4.1')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.1') }}</option>
        @elseif (trim($model) == 'gpt-4.1-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.1 mini') }}</option>
        @elseif (trim($model) == 'gpt-4.1-nano')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | GPT 4.1 nano') }}</option>
        @elseif (trim($model) == 'o1')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o1') }}</option>
        @elseif (trim($model) == 'o1-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o1 mini') }}</option>
        @elseif (trim($model) == 'o1-pro')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o1 pro') }}</option>
        @elseif (trim($model) == 'o3-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o3 mini') }}</option>
        @elseif (trim($model) == 'o3')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o3') }}</option>
        @elseif (trim($model) == 'o4-mini')
            <option value="{{ trim($model) }}" @if (trim($model) == $default_model) selected @endif>{{ __('OpenAI | o4 mini') }}</option>
        @else
            @foreach ($fine_tunes as $fine_tune)
                @if (trim($model) == $fine_tune->model)
                    <option value="{{ trim($model) }}">{{ $fine_tune->description }} ({{ __('Fine Tune') }})</option>
                @endif
            @endforeach
        @endif
        
    @endforeach									
</select>	