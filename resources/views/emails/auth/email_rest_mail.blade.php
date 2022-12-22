@component('mail::message')
Hello{{$user->name}},

@component('mail::button',['url'=>'/'])\
This is your new password {{$user->password}} used this for login your account
@endcomponent
</a></p> 
Thanks,<br>
{{ config('app.name') }}
@endcomponent
