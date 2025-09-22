<x-mail::message>

<div style="text-align: center">
    <h6 style="font-size: 18px; font-weight: 600">{{__('New Fund Received Successfully')}}</h6>
 </div>
 
 <div style="text-align: center">
     <span style="font-size: 14px; text-align: center">{{ __('You have received') }} <span style="font-weight: 600">{{$amount}}{{$currency}}</span> {{__('from')}} {{ $user->name}}</span>
 </div>

</x-mail::message>


