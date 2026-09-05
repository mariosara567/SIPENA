<x-layouts.app :title="'Akun Siswa - SIPENA'">
    <section class="card" style="display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0 0 6px;">Impor Data Siswa</h1>
            <p>Tambahkan banyak akun sekaligus. Nama kelas pada Excel harus sama dengan data kelas di SIPENA.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('admin.students.template') }}"><i class="fas fa-file-arrow-down"></i> Download Template Excel</a>
        <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center;flex:1 1 100%;">
            @csrf
            <input type="file" name="students_file" accept=".xlsx,.xls,.csv" required>
            <button class="btn btn-primary" type="submit"><i class="fas fa-file-import"></i> Impor Otomatis</button>
        </form>
    </section>
    <section class="grid two">
        <article class="card">
            <h2 style="margin-top:0;">Tambah Siswa</h2>
            <form method="POST" action="{{ route('admin.students.store') }}" class="grid">
                @csrf
                <label>Nama<input name="name" required></label>
                <label>Username<input name="username" required></label>
                <label>Password Awal<input name="password" required></label>
                <label>NIS<input name="nis" required></label>
                <label>Kelas
                    <select name="class_id" required>
                        <option value="">Pilih kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->display_name }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="btn btn-primary" type="submit">Simpan Siswa</button>
            </form>
        </article>

        <article class="card">
            <h2 style="margin-top:0;">Generate Massal Siswa</h2>
            <form method="POST" action="{{ route('admin.students.generate') }}" class="grid">
                @csrf
                <label>Kelas
                    <select name="class_id" required>
                        <option value="">Pilih kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->display_name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid two">
                    <label>Prefix Username<input name="prefix" value="siswa" required></label>
                    <label>Prefix NIS<input name="nis_prefix" value="NIS" required></label>
                    <label>Start Number<input type="number" name="start_number" value="1" min="1" required></label>
                    <label>Jumlah Akun<input type="number" name="count" value="10" min="1" max="300" required></label>
                </div>
                <label>Password Default<input name="default_password" value="password" required></label>
                <button class="btn btn-primary" type="submit">Generate Akun</button>
            </form>
        </article>
    </section>

    <section class="card table-wrap">
        <table>
            <thead><tr><th>Nama/NIS</th><th>Perbarui</th><th>Reset Password</th><th>Hapus</th></tr></thead>
            <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->user->name }}<br><small>{{ $student->user->username }} | {{ $student->nis }} | {{ $student->schoolClass->display_name }}</small></td>
                    <td>
                        <form method="POST" action="{{ route('admin.students.update', $student) }}" class="grid">
                            @csrf @method('PUT')
                            <input name="name" value="{{ $student->user->name }}" required>
                            <input name="username" value="{{ $student->user->username }}" required>
                            <input name="nis" value="{{ $student->nis }}" required>
                            <select name="class_id" required>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" @selected($class->id === $student->class_id)>{{ $class->display_name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-secondary" type="submit">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.students.reset_password', $student) }}" class="grid">
                            @csrf
                            <input name="password" placeholder="Password baru" required>
                            <button class="btn btn-secondary" type="submit">Reset</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Hapus akun siswa ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada akun siswa.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
