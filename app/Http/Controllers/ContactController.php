<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect('/#contact');
    }

    public function store(Request $request): RedirectResponse
    {
        // Rate limiting: 5 requests per minute per IP
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:5000',
            '_hp' => 'nullable|string', // Honeypot field for bot detection
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
}
