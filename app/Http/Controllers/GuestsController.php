<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\GuestDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GuestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Guest::class);
        $query = Guest::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $guests = $query->paginate(15)->withQueryString();

        return view('guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Guest::class);
        return view('guests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Guest::class);
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:guests,email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'id_type' => ['nullable', 'string', 'max:50'],
            'id_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'preferences' => ['nullable', 'array'],
        ]);

        do {
            $guestNumber = 'GST-' . strtoupper(Str::random(8));
        } while (Guest::where('guest_number', $guestNumber)->exists());

        $guest = Guest::create(array_merge($validated, [
            'branch_id' => auth()->user()->branch_id,
            'guest_number' => $guestNumber,
        ]));

        return redirect()->route('guests.show', $guest)
            ->with('success', 'Guest created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $guest)
    {
        $this->authorize('view', $guest);
        $guest->load(['reservations.room', 'reservations.branch', 'documents.uploader']);
        return view('guests.show', compact('guest'));
    }

    public function uploadDocument(Request $request, Guest $guest)
    {
        $this->authorize('update', $guest);
        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('document');
        $document = GuestDocument::create([
            'guest_id' => $guest->id,
            'uploaded_by' => auth()->id(),
            'name' => $validated['name'] ?: $file->getClientOriginalName(),
            'path' => $file->store('guest-documents', 'local'),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('success', "Document {$document->name} uploaded successfully.");
    }

    public function downloadDocument(Guest $guest, GuestDocument $document)
    {
        $this->authorize('view', $guest);
        abort_unless($document->guest_id === $guest->id && Storage::disk('local')->exists($document->path), 404);

        return Storage::disk('local')->download($document->path, $document->name);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $guest)
    {
        $this->authorize('update', $guest);
        return view('guests.edit', compact('guest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guest $guest)
    {
        $this->authorize('update', $guest);
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:guests,email,' . $guest->id, 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'id_type' => ['nullable', 'string', 'max:50'],
            'id_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'preferences' => ['nullable', 'array'],
        ]);

        $guest->update($validated);

        return redirect()->route('guests.show', $guest)
            ->with('success', 'Guest updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        $this->authorize('delete', $guest);
        if ($guest->reservations()->exists()) {
            return back()->with('error', 'Cannot delete guest with existing reservations.');
        }

        $guest->delete();

        return redirect()->route('guests.index')
            ->with('success', 'Guest deleted successfully.');
    }
}
