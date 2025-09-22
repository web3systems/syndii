<div class="card-footer p-0">	
    <div class="row text-center pb-4 pt-4">
        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16">@if (auth()->user()->tokens == -1) {{ __('Unlimited') }} @else {{ App\Services\HelperService::userAvailableTokens() }} @endif</h4>
            <h6 class="fs-12">@if ($settings->model_credit_name == 'words') {{ __('Words Left') }} @else {{ __('Tokens Left') }} @endif</h6>
        </div>

        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16">@if (auth()->user()->images == -1) {{ __('Unlimited') }} @else {{ App\Services\HelperService::userAvailableImages() }} @endif</h4>
            <h6 class="fs-12">{{ __('Media Credits Left') }}</h6>
        </div>
    </div>   

    <div class="row text-center pb-4">
        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16">@if (auth()->user()->characters == -1) {{ __('Unlimited') }} @else {{ App\Services\HelperService::userAvailableChars() }} @endif</h4>
            <h6 class="fs-12">{{ __('Characters Left') }}</h6>
        </div>

        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16">@if (auth()->user()->minutes == -1) {{ __('Unlimited') }} @else {{ App\Services\HelperService::userAvailableMinutes() }} @endif</h4>
            <h6 class="fs-12">{{ __('Minutes Left') }}</h6>
        </div>
    </div>    															
</div>