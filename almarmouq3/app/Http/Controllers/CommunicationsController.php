<?php

namespace App\Http\Controllers;

use App\Models\CommunicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CommunicationsController extends Controller
{
    public function index()
    {
        $recentMessages = CommunicationLog::whereIn('channel', ['whatsapp', 'email'])
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('modules.communications.index', [
            'recentMessages' => $recentMessages,
            'userId' => auth()->id(),
        ]);
    }

    public function whatsapp()
    {
        $contacts = CommunicationLog::where('channel', 'whatsapp')
            ->with('sender:id,name,avatar')
            ->select('id', 'sender_id', 'sender_phone', 'content', 'created_at')
            ->distinct('sender_phone')
            ->get();

        $activeContact = request('contact', $contacts->first()?->sender_phone ?? null);

        $messages = [];
        if ($activeContact) {
            $messages = CommunicationLog::where('channel', 'whatsapp')
                ->where('sender_phone', $activeContact)
                ->with('sender:id,name,avatar')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $user_id = auth()->id();

        return view('modules.communications.whatsapp', [
            'contacts' => $contacts,
            'messages' => $messages,
            'activeContact' => $activeContact,
            'userId' => $user_id,
        ]);
    }

    public function sendWhatsApp(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $user_id = auth()->id();
        $phone = preg_replace('/[^0-9]/', '', $validated['to']);

        CommunicationLog::create([
            'client_id' => null,
            'user_id' => $user_id,
            'sender_id' => $user_id,
            'channel' => 'whatsapp',
            'direction' => 'outbound',
            'recipient' => $phone,
            'sender_phone' => $phone,
            'subject' => 'WhatsApp Message',
            'content' => $validated['message'],
            'status' => 'sent',
        ]);

        return back()->with('success', 'Message sent');
    }

    public function webmails()
    {
        $emails = CommunicationLog::where('channel', 'email')
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('modules.communications.webmails', [
            'emails' => $emails,
            'userId' => auth()->id(),
        ]);
    }

    public function majlisLogs()
    {
        $logs = CommunicationLog::whereIn('channel', ['whatsapp', 'majlis'])
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('modules.communications.majlis-logs', [
            'logs' => $logs,
            'userId' => auth()->id(),
        ]);
    }

    public function documents()
    {
        $documents = CommunicationLog::where('channel', 'document')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('modules.communications.documents', [
            'documents' => $documents,
            'userId' => auth()->id(),
        ]);
    }
}
