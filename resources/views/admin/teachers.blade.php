<x-layouts.app :title="'Akun Guru - My Asssesmen'">
    <section class="card">
        <h2 style="margin-top:0;">Tambah Guru</h2>
        <form method="POST" action="{{ route('admin.teachers.store') }}" class="grid two">
            @csrf
            <label>Nama<input name="name" required></label>
            <label>Username<input name="username" required></label>
            <label>NIP<input name="nip" required></label>
            <label>Mata Pelajaran<input name="subject" required></label>
            <label>Password Awal<input name="password" required></label>
            <div style="display:flex;align-items:end;"><button class="btn btn-primary" type="submit">Simpan
                    Guru</button></div>
        </form>
    </section>

    <section class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>NIP</th>
                    <th>Mata Pelajaran</th>
                    <th>Perbarui</th>
                    <th>Reset Password</th>
                    <th>Hapus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                    <tr>
                        <td>{{ $teacher->user->name }}</td>
                        <td><small>{{ $teacher->user->username }}</small></td>
                        <td>{{ $teacher->nip ?? '-' }}</td>
                        <td>{{ $teacher->subject ?? '-' }}</td>
                        <td>
                            <div style="display:flex;justify-content:center;">
                                <button type="button" class="btn btn-primary open-update-modal"
                                    data-action="{{ route('admin.teachers.update', $teacher) }}"
                                    data-name="{{ $teacher->user->name }}"
                                    data-username="{{ $teacher->user->username }}" data-nip="{{ $teacher->nip }}"
                                    data-subject="{{ $teacher->subject }}">Perbarui</button>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;justify-content:center;">
                                <button type="button" class="btn btn-secondary open-reset-modal"
                                    data-action="{{ route('admin.teachers.reset_password', $teacher) }}"
                                    data-name="{{ $teacher->user->name }}">Reset</button>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;justify-content:center;">
                                <button type="button" class="btn btn-danger open-delete-modal"
                                    data-action="{{ route('admin.teachers.destroy', $teacher) }}"
                                    data-name="{{ $teacher->user->name }}">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada akun guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>


</x-layouts.app>
