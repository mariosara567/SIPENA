<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Support\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherProfileController extends Controller
{
    public function edit(Request $request): View
    {
        abort_unless($request->user()->isTeacher() && $request->user()->teacher, 403);
        return view('teacher.profile', ['teacher' => $request->user()->teacher->load('user')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $teacher = $user->teacher;
        abort_unless($user->isTeacher() && $teacher, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,' . $user->id],
            'nip' => ['nullable', 'digits:18', 'unique:teachers,nip,' . $teacher->id],
            'subject' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
        ]);
        $teacher->update([
            'nip' => $validated['nip'] ?: null,
            'subject' => $validated['subject'] ?: null,
        ]);
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        AuditLogger::log($user, 'teacher.profile_updated', Teacher::class, $teacher->id);
        return back()->with('status', 'Profil guru berhasil diperbarui.');
    }
}
