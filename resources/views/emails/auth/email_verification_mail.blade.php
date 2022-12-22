@component('mail::message')
Hello{{$user->name}},

@component('mail::button',['url'=>'/'])
Click here to Verify your email address
@endcomponent
</a></p> 
Thanks,<br>
{{ config('app.name') }}
@endcomponent
