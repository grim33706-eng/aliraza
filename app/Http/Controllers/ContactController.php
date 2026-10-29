<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessage as ContactMail;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(ContactRequest $request): JsonResponse|RedirectResponse
    {
        // Honeypot filled in: report success so bots learn nothing, but keep nothing.
        if ($request->isSpam()) {
            return $this->respond($request, true);
        }

        $data = $request->safe()->only(['name', 'email', 'subject', 'message']);

        // Saved to the admin inbox first, then emailed; either one is enough.
        $saved = $this->attempt(fn () => ContactMessage::query()->create($data + ['ip_address' => $request->ip()]), 'saving');
        $mailed = $this->attempt(fn () => Mail::to(config('portfolio.mail.inbox'))->send(new ContactMail($data)), 'emailing');

        return $this->respond($request, $saved || $mailed);
    }

    private function attempt(callable $action, string $what): bool
    {
        try {
            $action();

            return true;
        } catch (Throwable $e) {
            Log::error("Contact form: {$what} the message failed", ['exception' => $e->getMessage()]);

            return false;
        }
    }

    private function respond(ContactRequest $request, bool $sent): JsonResponse|RedirectResponse
    {
        $message = $sent
            ? config('portfolio.pages.contact.form.success')
            : 'Sorry, your message couldn\'t be sent right now. Please email me directly at '.config('portfolio.contact.email').'.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $sent ? 200 : 503);
        }

        return $sent
            ? to_route('contact')->with('contact_success', $message)
            : back()->withInput()->with('contact_error', $message);
    }
}
