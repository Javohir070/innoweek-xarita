<!doctype html>
<html lang="uz">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin kirish — Ko'rgazma 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;place-items:center;padding:16px;font:14px/1.5 Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:#0f172a;
  background:radial-gradient(1200px 600px at 80% -10%,#1d6fe0 0%,transparent 60%),linear-gradient(180deg,#0b3d91,#0a2a63)}
.card{width:100%;max-width:400px;background:#fff;border-radius:20px;box-shadow:0 30px 80px rgba(2,6,23,.45);overflow:hidden}
.top{padding:26px 28px 18px;display:flex;gap:14px;align-items:center}
.logo{width:48px;height:48px;border-radius:14px;background:#0b3d91;display:grid;place-items:center;flex:none}
h1{margin:0;font-size:19px;font-weight:800}
.top p{margin:2px 0 0;color:#64748b;font-size:13px}
form{padding:6px 28px 28px;display:flex;flex-direction:column;gap:14px}
label{display:flex;flex-direction:column;gap:6px;font-size:12px;font-weight:600;color:#334155}
input[type=email],input[type=password]{font:inherit;font-size:15px;border:1px solid #cbd5e1;border-radius:11px;padding:11px 12px}
input:focus{outline:3px solid rgba(29,111,224,.2);border-color:#1d6fe0}
.row{flex-direction:row;align-items:center;gap:8px;font-weight:500;color:#64748b}
button{border:0;border-radius:12px;padding:12px;background:#1d6fe0;color:#fff;font:inherit;font-weight:700;font-size:15px;cursor:pointer}
button:hover{background:#1559c0}
.err{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:10px;padding:9px 12px;font-size:13px}
.back{display:block;text-align:center;padding:14px;border-top:1px solid #e2e8f0;color:#1d6fe0;text-decoration:none;font-weight:600;font-size:13px}
.back:hover{background:#f8fafc}
</style>
</head>
<body>
<main class="card">
  <div class="top">
    <div class="logo"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg></div>
    <div>
      <h1>Admin kirish</h1>
      <p>Innovatsion ko'rgazma 2026</p>
    </div>
  </div>
  <form method="POST" action="{{ route('login') }}">
    @csrf
    @if ($errors->any())
      <div class="err">{{ $errors->first() }}</div>
    @endif
    <label>Email
      <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
    </label>
    <label>Parol
      <input type="password" name="password" required autocomplete="current-password">
    </label>
    <label class="row"><input type="checkbox" name="remember" value="1"> Eslab qolish</label>
    <button type="submit">Kirish</button>
  </form>
  <a class="back" href="{{ route('expo.index') }}">← Xaritaga qaytish</a>
</main>
</body>
</html>
