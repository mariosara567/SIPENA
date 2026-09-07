@props(['title' => 'My Asssesmen'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
      :root{--primary:#0f766e;--primary-light:#14b8a6;--bg:#f5f7fa;--surface:#fff;--text:#0f172a;--muted:#64748b;--border:#e2e8f0;--success:#16a34a;--danger:#dc2626;--warning:#ea580c;--shadow:0 4px 12px rgba(0,0,0,.07)}
      *{box-sizing:border-box;margin:0;padding:0} body{font-family:Poppins,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
      .student-top{height:68px;background:var(--surface);border-bottom:2px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:50}
      .brand{font-weight:900;color:var(--primary);font-size:20px;text-decoration:none}.student-user{display:flex;align-items:center;gap:12px;color:var(--muted);font-size:13px;font-weight:700}.logout{border:1px solid var(--border);background:#fff;border-radius:8px;padding:9px 13px;font-weight:700;cursor:pointer;color:var(--text)}
      main{width:min(1180px,100%);margin:0 auto;padding:28px 20px 50px}.card{background:var(--surface);border:2px solid var(--border);border-radius:14px;padding:24px;box-shadow:var(--shadow);margin-bottom:20px}.grid{display:grid;gap:16px}.grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 16px;border:0;border-radius:9px;text-decoration:none;font-weight:800;cursor:pointer}.btn-primary{background:var(--primary);color:#fff}.btn-secondary{background:#fff;color:var(--text);border:2px solid var(--border)}input,textarea,select{width:100%;padding:11px 12px;border:2px solid var(--border);border-radius:9px;background:#fff;font:inherit}label{display:grid;gap:7px;font-weight:700;font-size:13px}.alert{padding:14px 16px;border-radius:10px;margin-top:16px;font-weight:600}.alert.success{background:#ecfdf5;color:#166534}.alert.error{background:#fef2f2;color:#b91c1c}.badge{display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;font-weight:800;font-size:12px}@media(max-width:720px){.grid.two{grid-template-columns:1fr}.student-top{padding:0 14px}.student-user span{display:none}main{padding:18px 12px 36px}.card{padding:18px}}
    </style>
</head>
<body>
<header class="student-top">
  <a class="brand" href="{{ route('student.exams.index') }}">My Asssesmen</a>
  <div class="student-user"><span><i class="fas fa-user-graduate"></i> {{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit"><i class="fas fa-right-from-bracket"></i> Keluar</button></form></div>
</header>
<main>
  @if(session('status'))<div class="alert success">{{ session('status') }}</div>@endif
  @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
  {{ $slot }}
</main>
</body>
</html>
