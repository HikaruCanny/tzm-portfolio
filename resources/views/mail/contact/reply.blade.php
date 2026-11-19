<x-mail::message>
# Hi {{ $visitorName }},

Thanks for reaching out through my portfolio. Here is my reply:

{{ $replyBody }}

---

**Your original message:**

{{ $originalMessage }}

Thanks,<br>
{{ $adminName }}<br>
{{ config('app.name') }}
</x-mail::message>