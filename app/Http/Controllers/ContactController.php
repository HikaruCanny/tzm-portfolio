<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contact', [
            'profile' => Profile::instance(),
            'faqs' => $this->faqs(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:5000',
            '_hp' => 'nullable|string',
        ]);

        $isSpam = ! empty($request->input('_hp'));

        ContactMessage::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'ip_hash' => hash('sha256', $request->ip() ?? '127.0.0.1'),
            'is_spam' => $isSpam,
        ]);

        return back()->with('success', 'Your message has been received! I will respond shortly.');
    }

    /**
     * @return list<array{question: string, answer: string}>
     */
    private function faqs(): array
    {
        return [
            [
                'question' => 'Are you open to full-time or contract roles?',
                'answer' => 'Yes. I am open to remote full-time roles and focused contract work around Laravel, Vue, and API-heavy products. Share the role, stack, and timeline in the form and I will reply quickly.',
            ],
            [
                'question' => 'What kind of projects fit best?',
                'answer' => 'Greenfield or existing Laravel/Vue apps, admin panels, REST APIs, SaaS features, and portfolio or internal tools that need clean architecture and maintainable frontend UX.',
            ],
            [
                'question' => 'How soon can we start a conversation?',
                'answer' => 'Most messages get a reply within one to two business days. If the note includes a deadline or interview window, call that out in the subject line.',
            ],
            [
                'question' => 'Can I download a resume instead of emailing?',
                'answer' => 'Yes. Use the resume download on this page or the About page when a PDF is uploaded in Site Profile. You can still send a short note afterward if useful.',
            ],
            [
                'question' => 'Do you work across time zones?',
                'answer' => 'Yes for async collaboration. Overlap hours can be arranged once we agree on the engagement and communication cadence.',
            ],
        ];
    }
}
