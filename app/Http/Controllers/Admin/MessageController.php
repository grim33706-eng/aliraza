<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        $messages = ContactMessage::query()
            ->when($filter === 'unread', fn ($q) => $q->unread())
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%")))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.messages.index', compact('messages', 'search', 'filter'));
    }

    public function show(ContactMessage $message): View
    {
        if (! $message->isRead()) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', [
            'message' => $message,
            'previous' => ContactMessage::query()->where('id', '>', $message->id)->orderBy('id')->value('id'),
            'next' => ContactMessage::query()->where('id', '<', $message->id)->orderByDesc('id')->value('id'),
        ]);
    }

    public function toggleRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => $message->isRead() ? null : now()]);

        return back()->with('toast', [
            'type' => 'info', 'icon' => 'mail-open',
            'title' => $message->isRead() ? 'Marked as read' : 'Marked as unread',
            'message' => "Message from {$message->name}.",
        ]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return to_route('admin.messages.index')->with('toast', [
            'type' => 'deleted',
            'title' => 'Message deleted',
            'message' => "The message from {$message->name} was permanently removed.",
        ]);
    }
}
