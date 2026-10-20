@component('mail::message')
# Nieuw contactformulier

**Naam:** {{ $contactRequest->name }}
**E-mailadres:** {{ $contactRequest->email }}
@if($contactRequest->phone)
**Telefoonnummer:** {{ $contactRequest->phone }}
@endif
@if($contactRequest->company)
**Bedrijf:** {{ $contactRequest->company }}
@endif
@if($contactRequest->subject)
**Onderwerp:** {{ $contactRequest->subject }}
@endif

@if($contactRequest->message)
**Bericht:**

{{ $contactRequest->message }}
@endif

@if($contactRequest->source)
_Verstuurd vanaf: {{ $contactRequest->source }}_
@endif

@component('mail::button', ['url' => config('app.url').'/admin/contact-requests/'.$contactRequest->id])
Bekijk in het admin
@endcomponent
@endcomponent
