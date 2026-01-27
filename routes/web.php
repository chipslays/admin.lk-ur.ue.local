<?php


use App\Http\Controllers\Auth\LoginController;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;


Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $query = Ticket::query();

        $query->when(request('status', null), function ($query, $status) {
            $status = strtolower($status);

            $allowedStatuses = ['all', 'open', 'in_progress', 'closed', 'cancelled'];

            if (!in_array($status, $allowedStatuses)) {
                $status = 'all';
            }

            if ($status === 'all') {
                return;
            }

            $query->where('status', $status);
        });

        $query->when(request('search', null), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', $search)
                    ->orWhereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            });
        });

        return Inertia::render('Tickets/List', [
            'tickets' => $query
                ->with(['user', 'firstMessage'])
                ->latest()
                ->paginate(10)
                ->onEachSide(1)
                ->withQueryString(),
            'filters' => request()->only(['search', 'status']),
            'counts' => [
                'all' => Ticket::count(),
                'open' => Ticket::where('status', 'open')->count(),
                'in_progress' => Ticket::where('status', 'in_progress')->count(),
                'closed' => Ticket::where('status', 'closed')->count(),
                'cancelled' => Ticket::where('status', 'cancelled')->count(),
            ]
        ]);
    })->name('tickets.list');

    Route::get('/ticket/{id}', function ($id) {
        $ticket = Ticket::with(['user', 'messages.user'])->findOrFail($id);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
        ]);
    })->name('tickets.show');

    Route::post('/ticket/{id}/status', function ($id) {
        $ticket = Ticket::with(['user', 'messages'])->findOrFail($id);

        $allowedStatuses = ['open', 'in_progress', 'closed', 'cancelled'];

        $status = request('status', null);

        if (!in_array($status, $allowedStatuses)) {
            return back();
        }

        $ticket->update([
            'status' => $status,
            'closed_at' => $status === 'closed' ? now() : null,
        ]);

        return back();
    })->name('tickets.set-status');

    Route::post('/ticket/{id}/message', function ($id) {
        $ticket = Ticket::findOrFail($id);

        $validated = request()->validate([
            'message' => ['required', 'string', 'max:4096'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['nullable', 'file'],
        ]);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'message' => $validated['message'],
            'attachments' => [],
            'manager_name' => user()->name,
        ]);

        $files = array_filter((array) request()->file('attachments'));
        $paths = [];

        $dir = "tickets/admin/ticket_{$ticket->id}/message_{$message->id}";

        foreach ($files as $file) {
            $original = $file->getClientOriginalName();
            $safeName = Str::slug(pathinfo($original, PATHINFO_FILENAME), language: 'ru');
            $ext = $file->getClientOriginalExtension();

            $filename = ($safeName ?: 'file') . '-' . Str::random(8) . '.' . $ext;

            // Используем storeAs() вместо put()
            $path = $file->storeAs($dir, $filename, 'lk');

            $paths[] = $path;
        }

        $message->update([
            'attachments' => $paths,
        ]);

        return redirect()->route('tickets.show', $ticket->id);
    })->name('tickets.message.store');


    Route::post('/ticket/{ticketId}/message/{messageId}', function ($ticketId, $messageId) {
        file_get_contents(env('LK_URL')."/ticket/{$ticketId}/message/{$messageId}/delete?from=admin");
        return back();
    })->name('tickets.message.delete');

    Route::post('/ticket/{id}/notes', function ($id) {
        $ticket = Ticket::findOrFail($id);

        $validated = request()->validate([
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $ticket->update([
            'notes' => $validated['notes'],
        ]);

        return back();
    })->name('tickets.notes.update');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});
