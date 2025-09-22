<x-mail::message>

<div style="text-align: center">
   <h6 style="font-size: 18px; font-weight: 600">{{__('Wallet Balance Transfer Completed')}}</h6>
</div>

<div style="text-align: center">
    <span style="font-size: 14px; text-align: center">{{ __('You have succcessfully transfered') }} <span style="font-weight: 600">{{$amount}}{{$currency}}</span> {{__('to')}} {{ $user->name }}</span>
</div>

</x-mail::message>
