<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Support\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAcademicController extends Controller
{
    public function index(): View
    {
        return view('admin.academic', [
            'classes' => SchoolClass::query()->withCount('students')->orderBy('name')->get(),
            'subjects' => Subject::query()->withCount('exams')->orderBy('name')->get(),
        ]);
    }

    public function storeClass(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:classes,name'],
            'level' => ['required', 'integer', 'in:10,11,12'],
        ]);

        $class = SchoolClass::query()->create($validated);
        AuditLogger::log($request->user(), 'class.created', SchoolClass::class, $class->id, ['name' => $class->name]);

        return back()->with('status', 'Kelas berhasil ditambahkan.');
    }

    public function updateClass(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:classes,name,'.$class->id],
            'level' => ['required', 'integer', 'in:10,11,12'],
        ]);

        $class->update($validated);
        AuditLogger::log($request->user(), 'class.updated', SchoolClass::class, $class->id, ['name' => $class->name]);

        return back()->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroyClass(Request $request, SchoolClass $class): RedirectResponse
    {
        $className = $class->name;
        $classId = $class->id;
        $class->delete();

        AuditLogger::log($request->user(), 'class.deleted', SchoolClass::class, $classId, ['name' => $className]);

        return back()->with('status', 'Kelas berhasil dihapus.');
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:subjects,name'],
            'code' => ['nullable', 'string', 'max:4', 'unique:subjects,code'],
            'group' => ['nullable', 'string', 'max:50'],
        ]);

        $subject = Subject::query()->create($validated);
        AuditLogger::log($request->user(), 'subject.created', Subject::class, $subject->id, ['name' => $subject->name]);

        return back()->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:subjects,name,'.$subject->id],
            'code' => ['nullable', 'string', 'max:4', 'unique:subjects,code,'.$subject->id],
            'group' => ['nullable', 'string', 'max:50'],
        ]);

        $subject->update($validated);
        AuditLogger::log($request->user(), 'subject.updated', Subject::class, $subject->id, ['name' => $subject->name]);

        return back()->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroySubject(Request $request, Subject $subject): RedirectResponse
    {
        $subjectName = $subject->name;
        $subjectId = $subject->id;
        $subject->delete();

        AuditLogger::log($request->user(), 'subject.deleted', Subject::class, $subjectId, ['name' => $subjectName]);

        return back()->with('status', 'Mata pelajaran berhasil dihapus.');
    }
}
