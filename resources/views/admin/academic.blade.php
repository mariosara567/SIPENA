<x-layouts.app :title="'Data Akademik - My Asssesmen'">
    <section class="grid two">
        <article class="card">
            <h2 style="margin-top:0;">Kelas</h2>
            <form method="POST" action="{{ route('admin.classes.store') }}" style="display:flex;gap:8px;margin-bottom:12px;">
                @csrf
                <input name="name" placeholder="Nama kelas" required>
                <input type="number" name="year" placeholder="Tahun" min="1900" max="2100">
                <button class="btn btn-primary" type="submit">Tambah</button>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Kelas</th><th>Tahun</th><th>Jumlah Siswa</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('admin.classes.update', $class) }}" style="display:flex;gap:6px;align-items:center;">
                                    @csrf @method('PUT')
                                    <input name="name" value="{{ $class->name }}" required style="width:120px;">
                            </td>
                            <td>
                                    <input type="number" name="year" value="{{ $class->year }}" min="1900" max="2100" style="width:100px;">
                            </td>
                            <td>{{ $class->students_count }}</td>
                            <td>
                                    <button class="btn btn-secondary" type="submit">Simpan</button>
                                </form>
                                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Hapus kelas ini?')" style="display:inline;margin-left:8px;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Belum ada kelas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="card">
            <h2 style="margin-top:0;">Mata Pelajaran</h2>
            <form method="POST" action="{{ route('admin.subjects.store') }}" style="display:flex;gap:8px;margin-bottom:12px;">
                @csrf
                <input name="code" placeholder="Kode mapel">
                <input name="name" placeholder="Nama mata pelajaran" required>
                <select name="group">
                    <option value="">-- Kelompok --</option>
                    <option value="Wajib">Wajib</option>
                    <option value="Pilihan">Pilihan</option>
                </select>
                <button class="btn btn-primary" type="submit">Tambah</button>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Kode Mapel</th><th>Nama Mata Pelajaran</th><th>Kelompok</th><th>Jumlah Ujian</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" style="display:flex;gap:6px;align-items:center;">
                                    @csrf @method('PUT')
                                    <input name="code" value="{{ $subject->code }}" placeholder="Kode" style="width:120px;">
                            </td>
                            <td>
                                    <input name="name" value="{{ $subject->name }}" required>
                            </td>
                            <td>
                                    <select name="group">
                                        <option value="">-- Kelompok --</option>
                                        <option value="Wajib" {{ $subject->group === 'Wajib' ? 'selected' : '' }}>Wajib</option>
                                        <option value="Pilihan" {{ $subject->group === 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                                    </select>
                            </td>
                            <td>{{ $subject->exams_count }}</td>
                            <td>
                                    <button class="btn btn-secondary" type="submit">Simpan</button>
                                </form>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" onsubmit="return confirm('Hapus mapel ini?')" style="display:inline;margin-left:8px;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada mapel.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
</x-layouts.app>
