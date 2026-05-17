<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kurir;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KurirController extends Controller
{
    /**
     * Display a listing of the kurir.
     */
    public function index()
    {
        $kurirs = Kurir::latest()->get();
        return view('admin.kurirs.index', compact('kurirs'));
    }

    /**
     * Show the form for creating a new kurir.
     */
    public function create()
    {
        return view('admin.kurirs.create');
    }

    /**
     * Store a newly created kurir in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'plate_number' => 'required|string|unique:kurirs,plate_number',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $data = $request->all();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('kurir', $photoName, 'public');
            $data['photo'] = $photoName;
        }

        Kurir::create($data);
        return redirect()->route('admin.kurirs.index')->with('success', 'Kurir berhasil ditambahkan.');
    }

    /**
     * Display the specified kurir.
     */
    public function show(Kurir $kurir)
    {
        return view('admin.kurirs.show', compact('kurir'));
    }

    /**
     * Show the form for editing the specified kurir.
     */
    public function edit(Kurir $kurir)
    {
        return view('admin.kurirs.edit', compact('kurir'));
    }

    /**
     * Update the specified kurir in storage.
     */
    public function update(Request $request, Kurir $kurir)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'plate_number' => 'required|string|unique:kurirs,plate_number,' . $kurir->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $data = $request->all();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($kurir->photo && \Storage::disk('public')->exists('kurir/' . $kurir->photo)) {
                \Storage::disk('public')->delete('kurir/' . $kurir->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('kurir', $photoName, 'public');
            $data['photo'] = $photoName;
        }

        $kurir->update($data);
        return redirect()->route('admin.kurirs.index')->with('success', 'Kurir berhasil diperbarui.');
    }

    /**
     * Remove the specified kurir from storage.
     */
    public function destroy(Kurir $kurir)
    {
        if ($kurir->photo && \Storage::disk('public')->exists('kurir/' . $kurir->photo)) {
            \Storage::disk('public')->delete('kurir/' . $kurir->photo);
        }

        $kurir->delete();
        return redirect()->route('admin.kurirs.index')->with('success', 'Kurir berhasil dihapus.');
    }
}
