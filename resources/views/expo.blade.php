<!doctype html>
<html lang="uz">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Ko'rgazma xaritasi 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@verbatim
<style>
:root{
  --bg:#eef1f6; --surface:#fff; --ink:#0f172a; --ink2:#334155; --muted:#64748b; --line:#e2e8f0;
  --brand:#0b3d91; --brand2:#1d6fe0; --sel:#facc15; --hit:#ef4444;
  --radius:14px; --shadow:0 1px 2px rgba(15,23,42,.06),0 8px 24px rgba(15,23,42,.06);
}
*{box-sizing:border-box}
html,body{margin:0;height:100%}
body{background:var(--bg);color:var(--ink);font:14px/1.5 Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;display:flex;flex-direction:column;-webkit-font-smoothing:antialiased}
button{font:inherit}

/* ---------- header ---------- */
header{background:linear-gradient(180deg,#0b3d91,#0a347c);color:#fff;padding:14px 20px 0}
.hrow{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.logo{width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.14);display:grid;place-items:center;flex:none}
.brand h1{margin:0;font-size:18px;font-weight:800;letter-spacing:-.01em}
.brand p{margin:0;font-size:12px;opacity:.75}
.chips{display:flex;gap:8px;margin-left:auto;flex-wrap:wrap}
.chip{background:rgba(255,255,255,.12);border-radius:999px;padding:5px 12px;font-size:12px;white-space:nowrap}
.chip b{font-weight:700;font-size:13px;margin-right:3px}
.hrow2{display:flex;align-items:flex-end;gap:12px;margin-top:14px;flex-wrap:wrap}
.tabs{display:flex;gap:2px}
.tabs button{border:0;background:transparent;color:rgba(255,255,255,.75);padding:10px 16px;border-radius:10px 10px 0 0;cursor:pointer;font-weight:600}
.tabs button.on{background:var(--bg);color:var(--ink)}
.search{position:relative;flex:1;max-width:460px;margin:0 0 8px auto;min-width:220px}
.search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:.6}
#q{width:100%;padding:10px 12px 10px 38px;border:0;border-radius:10px;font:inherit;background:#fff;color:var(--ink);box-shadow:0 2px 8px rgba(0,0,0,.15)}
#q:focus{outline:3px solid rgba(250,204,21,.7)}

main{flex:1;min-height:0;display:flex;flex-direction:column}

#mapView{flex:1;min-height:0;overflow:hidden;padding:14px 20px 12px;display:flex;flex-direction:column;gap:10px}

/* ---------- yo'nalishlar legendasi ---------- */
.dirs{display:flex;gap:6px;flex-wrap:wrap}
.dir{display:flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--line);border-radius:999px;padding:4px 11px 4px 7px;font-size:12px;font-weight:500;color:var(--ink2);cursor:pointer;transition:.15s}
.dir i{width:14px;height:14px;border-radius:4px;background:var(--c);box-shadow:inset 0 0 0 1px rgba(0,0,0,.15)}
.dir:hover{border-color:#94a3b8}
.dir.on{background:var(--ink);color:#fff;border-color:var(--ink)}

/* ---------- xarita ---------- */
.mapCard{flex:1;min-height:0;background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);padding:12px;overflow:hidden;display:flex;align-items:center;justify-content:center}
.mapScale{position:relative;flex:none}
.map{transform-origin:0 0}
.map{display:grid;position:relative;width:max-content;
  /* chap zallar | 3 stend | yo'lak | 3 stend | o'ng zonalar; har bir stend: joylar, yorliq (2 ustun), joylar */
  grid-template-columns:86px 10px 50px 12px 44px 30px 30px 44px 12px 44px 30px 30px 44px 12px 44px 30px 30px 44px 56px 44px 30px 30px 44px 12px 44px 30px 30px 44px 12px 44px 30px 30px 44px 12px 104px 10px 86px;}
.zone{border:1.5px solid #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;text-align:center;font-weight:700;font-size:12px;letter-spacing:.06em;color:#475569;background:#f8fafc;padding:4px}
.zone.soft{background:#f1f5f9;border-style:dashed}
.zone.vert span{writing-mode:vertical-rl;transform:rotate(180deg)}
.aisle{background:repeating-linear-gradient(0deg,#f1f5f9 0 10px,#e8edf3 10px 20px);border-radius:8px}
.corr{grid-column:5/34;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;letter-spacing:.3em;font-weight:600}
.shdr{font-size:11px;font-weight:700;color:var(--muted);display:flex;align-items:flex-end;justify-content:center;cursor:pointer;padding-bottom:3px}
.shdr:hover{color:var(--brand2)}
.cell{position:relative;margin:1px;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:flex-start;padding:0 4px;font-size:10px;font-weight:700;color:rgba(0,0,0,.45);background:var(--c);z-index:1;transition:transform .12s,box-shadow .12s,opacity .15s}
.cell.dark{color:rgba(255,255,255,.7)}
.cell.empty{background:repeating-linear-gradient(135deg,var(--c) 0 4px,rgba(255,255,255,.75) 4px 8px);opacity:.75}
.cell:hover{transform:scale(1.12);box-shadow:0 4px 12px rgba(0,0,0,.3);z-index:5}
.cell.sel{box-shadow:0 0 0 3px var(--sel),0 0 0 5px var(--ink);z-index:6}
.cell.hit{box-shadow:0 0 0 3px var(--hit);z-index:4;animation:pulse 1.4s infinite}
.cell.dim{opacity:.18}
@keyframes pulse{50%{box-shadow:0 0 0 5px rgba(239,68,68,.35)}}
.slabel{margin:1px;border-radius:5px;background:var(--c);cursor:pointer;display:flex;align-items:center;justify-content:center;padding:4px 0;overflow:hidden;transition:opacity .15s}
.slabel span{writing-mode:vertical-rl;transform:rotate(180deg);font-weight:800;font-size:12.5px;letter-spacing:.02em;text-align:center;line-height:1.15;max-height:100%;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.35)}
.slabel.narrow span{font-size:11px}
.slabel.lighttxt span{color:#1e293b;text-shadow:none}
.slabel.dim{opacity:.18}
.maphint{display:flex;gap:18px;flex-wrap:wrap;color:var(--muted);font-size:12px;align-items:center}
.maphint i{display:inline-block;width:14px;height:14px;border-radius:3px;vertical-align:-3px;margin-right:6px}

#tip{position:fixed;pointer-events:none;background:var(--ink);color:#fff;padding:8px 10px;border-radius:8px;font-size:12px;max-width:280px;z-index:60;opacity:0;transition:opacity .1s;box-shadow:0 8px 20px rgba(0,0,0,.25)}
#tip b{display:block;font-size:11px;color:var(--sel);margin-bottom:2px}

/* ---------- list view ---------- */
#listView{flex:1;overflow:auto;padding:20px;display:none}
.tableCard{background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
table{border-collapse:collapse;width:100%;font-size:13px}
th,td{padding:10px 12px;vertical-align:top;text-align:left;border-bottom:1px solid var(--line)}
th{background:#f8fafc;position:sticky;top:0;z-index:1;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)}
tbody tr{cursor:pointer;transition:background .1s}
tbody tr:hover{background:#f1f6ff}
td.pre{white-space:pre-line}
.tag{display:inline-block;padding:2px 8px;border-radius:6px;font-weight:700;font-size:12px;background:var(--c);color:#fff;white-space:nowrap}
.tag.lt{color:#1e293b}
.muted{color:#94a3b8}

/* ---------- modal ---------- */
#overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:opacity .2s;z-index:30}
#overlay.open{opacity:1;pointer-events:auto}
#modal{position:fixed;left:50%;top:50%;transform:translate(-50%,-47%) scale(.97);opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;
  width:min(1000px,calc(100vw - 32px));max-height:calc(100vh - 40px);background:#fff;border-radius:20px;box-shadow:0 30px 80px rgba(2,6,23,.45);display:flex;flex-direction:column;z-index:40;overflow:hidden}
#modal.open{opacity:1;pointer-events:auto;transform:translate(-50%,-50%)}
.mh{position:relative;display:flex;align-items:center;gap:16px;padding:18px 18px 18px 22px;color:#fff;overflow:hidden;
  background:linear-gradient(120deg,var(--c,#0b3d91) 0%,color-mix(in srgb,var(--c,#0b3d91) 72%,#000) 100%)}
.mh::after{content:"";position:absolute;right:-60px;top:-80px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none}
.mh.lt{color:#1e293b;background:linear-gradient(120deg,var(--c) 0%,color-mix(in srgb,var(--c) 85%,#b45309) 100%)}
.mbadge{flex:none;min-width:64px;height:64px;padding:0 10px;border-radius:16px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1.05}
.mh.lt .mbadge{background:rgba(255,255,255,.45);border-color:rgba(0,0,0,.08)}
.mbadge b{font-size:22px;font-weight:800}
.mbadge span{font-size:11px;font-weight:600;opacity:.85;margin-top:3px;white-space:nowrap}
.mt{flex:1;min-width:0;position:relative;z-index:1}
.mh .kick{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;opacity:.85}
.mh h2{margin:4px 0 0;font-size:21px;line-height:1.25;font-weight:800;letter-spacing:-.01em;white-space:pre-line}
.mh .x{flex:none;align-self:flex-start;position:relative;z-index:2;width:40px;height:40px;border-radius:12px;border:0;background:rgba(255,255,255,.2);color:inherit;cursor:pointer;display:grid;place-items:center;transition:background .15s,transform .15s}
.mh.lt .x{background:rgba(0,0,0,.08)}
.mh .x:hover{background:rgba(255,255,255,.35);transform:rotate(90deg)}
.mh.lt .x:hover{background:rgba(0,0,0,.16)}
.mb{overflow:auto;padding:22px;flex:1;background:#fbfcfe}
.plist{display:flex;flex-direction:column;gap:14px}
.plist-h{display:flex;align-items:center;gap:10px;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700}
.plist-h i{flex:1;height:1px;background:var(--line)}
.plist-h b{background:var(--ink);color:#fff;border-radius:999px;padding:1px 9px;font-size:11px;letter-spacing:0}
.pcard{display:grid;grid-template-columns:260px minmax(0,1fr);background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden;transition:box-shadow .15s,border-color .15s}
.pcard:hover{border-color:#cbd5e1;box-shadow:0 8px 24px rgba(15,23,42,.08)}
.pcard .pic{position:relative;background:#f1f5f9;aspect-ratio:4/3;cursor:zoom-in;border-right:1px solid var(--line)}
.pcard .pic img{width:100%;height:100%;object-fit:cover;display:block}
.pcard .pic .zoom{position:absolute;right:8px;bottom:8px;width:30px;height:30px;border-radius:9px;background:rgba(15,23,42,.65);color:#fff;display:grid;place-items:center;pointer-events:none;opacity:0;transition:.15s}
.pcard:hover .pic .zoom{opacity:1}
.pcard .pic .more{position:absolute;left:8px;bottom:8px;background:rgba(15,23,42,.75);color:#fff;font-size:11px;font-weight:700;border-radius:7px;padding:2px 8px;pointer-events:none}
.pcard .pic.none{display:grid;place-items:center;color:#94a3b8;font-size:12px;cursor:default}
.pcard .txt{padding:16px 18px;display:flex;flex-direction:column;gap:8px;min-width:0}
.pcard .no{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:700;color:var(--brand2);text-transform:uppercase;letter-spacing:.06em}
.pcard .no span{width:26px;height:26px;border-radius:8px;background:var(--brand2);color:#fff;display:grid;place-items:center;font-size:13px;letter-spacing:0}
.pcard h3{margin:0;font-size:16px;line-height:1.35;font-weight:700;color:var(--ink)}
.pcard p{margin:0;font-size:13.5px;line-height:1.55;color:var(--ink2)}
.pcard p.by{display:flex;gap:6px;align-items:flex-start;color:var(--muted);font-size:13px}
.pcard p.by svg{flex:none;margin-top:2px}
.pcard .strip{display:flex;gap:6px;margin-top:auto;padding-top:4px}
.pcard .strip img{width:48px;height:38px;border-radius:7px;object-fit:cover;cursor:zoom-in;border:1px solid var(--line)}
.pwrap{display:flex;flex-direction:column;gap:18px}
.pwrap .side{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.pwrap .side>.box:only-child{grid-column:1/-1}
.side{display:flex;flex-direction:column;gap:14px}
.box{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px}
.box h4{margin:0 0 10px;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700;display:flex;align-items:center;gap:8px}
.box h4 i{flex:1;height:1px;background:var(--line)}
.desc{color:var(--ink2);font-size:14px;line-height:1.55;margin:0;padding-left:0;list-style:none}
.desc li{padding:7px 0 7px 30px;position:relative;border-bottom:1px dashed var(--line)}
.desc li:first-child{padding-top:0}.desc li:first-child span{top:0}
.desc li:last-child{border-bottom:0;padding-bottom:0}
.desc li span{position:absolute;left:0;top:7px;width:21px;height:21px;border-radius:7px;background:#eaf1ff;color:var(--brand2);font-size:11px;font-weight:800;display:grid;place-items:center}
.desc.plain li{padding-left:0}
.person{display:flex;gap:12px;align-items:flex-start;padding:10px 0;border-bottom:1px dashed var(--line)}
.person:first-of-type{padding-top:0}
.person:last-child{border-bottom:0;padding-bottom:0}
.person .ic{width:38px;height:38px;border-radius:11px;background:#eaf1ff;color:var(--brand2);display:grid;place-items:center;flex:none}
.person small{display:block;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-bottom:1px}
.person div{white-space:pre-line;font-size:13.5px;color:var(--ink);line-height:1.45}
.person a{display:inline-flex;align-items:center;gap:4px;margin-top:2px;color:var(--brand2);font-weight:700;text-decoration:none;background:#eaf1ff;border-radius:8px;padding:1px 8px}
.person a:hover{background:#dbe7ff}
.empty-state{text-align:center;padding:40px 10px;color:var(--muted)}
.empty-state .big{font-size:40px;margin-bottom:6px}
.mf{display:flex;justify-content:space-between;gap:10px;padding:12px 16px;border-top:1px solid var(--line);background:#fff}
.nav{border:1px solid var(--line);background:#fff;border-radius:12px;padding:9px 16px;cursor:pointer;font-weight:600;color:var(--ink2);display:flex;align-items:center;gap:6px}
.nav:hover:not(:disabled){border-color:var(--brand2);color:var(--brand2)}
.nav:disabled{opacity:.35;cursor:default}
.rows{display:flex;flex-direction:column;gap:8px}
.row{display:flex;align-items:center;gap:12px;width:100%;text-align:left;border:1px solid var(--line);background:#fff;border-radius:12px;padding:10px 12px;cursor:pointer;transition:.12s}
.row:hover{border-color:var(--brand2);background:#f8fbff}
.row .n{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;font-weight:800;font-size:13px;background:var(--c);color:#fff;flex:none}
.row .n.lt{color:#1e293b}
.row .t{flex:1;min-width:0}
.row .t b{display:block;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.row .t small{color:var(--muted)}
.row.off{opacity:.55}
.row .thumbs{display:flex;gap:4px}
.row .thumbs img{width:34px;height:34px;border-radius:6px;object-fit:cover}

/* ---------- lightbox ---------- */
#lb{position:fixed;inset:0;background:rgba(2,6,23,.92);display:none;align-items:center;justify-content:center;flex-direction:column;z-index:50;padding:24px}
#lb.open{display:flex}
#lb img{max-width:min(1000px,100%);max-height:78vh;border-radius:10px;background:#fff;box-shadow:0 20px 60px rgba(0,0,0,.5)}
#lb p{color:#e2e8f0;max-width:760px;text-align:center;margin:14px 0 0;font-size:15px}
#lb .cnt{color:#94a3b8;font-size:12px;margin-top:4px}
#lb .lbn{position:absolute;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;border:0;background:rgba(255,255,255,.12);color:#fff;font-size:24px;cursor:pointer}
#lb .lbn:hover{background:rgba(255,255,255,.25)}
#lb .prev{left:20px} #lb .next{right:20px}
#lb .lbx{position:absolute;right:20px;top:20px;width:44px;height:44px;border-radius:50%;border:0;background:rgba(255,255,255,.12);color:#fff;font-size:24px;cursor:pointer}

/* ---------- admin ---------- */
.adminBtn{display:flex;align-items:center;gap:6px;border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.1);color:#fff;border-radius:999px;padding:5px 12px;font-size:12px;font-weight:600;cursor:pointer}
.adminBtn:hover{background:rgba(255,255,255,.2)}
.adminBtn.on{background:#facc15;color:#0f172a;border-color:#facc15}
.listBar{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.listBar .cnt{color:var(--muted);font-size:13px;margin-right:auto}
.btn{border:1px solid var(--line);background:#fff;border-radius:12px;padding:9px 16px;cursor:pointer;font-weight:600;color:var(--ink2);display:inline-flex;align-items:center;gap:6px}
.btn:hover{border-color:var(--brand2);color:var(--brand2)}
.btn.primary{background:var(--brand2);border-color:var(--brand2);color:#fff}
.btn.primary:hover{background:#1559c0;color:#fff}
.btn.danger{color:#dc2626}
.btn.danger:hover{border-color:#dc2626;background:#fef2f2;color:#dc2626}
.btn:disabled{opacity:.5;cursor:wait}
.mf .grp{display:flex;gap:8px}
.form .fgrid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form .full{grid-column:1/-1}
.form .lt{display:flex;gap:6px;align-items:baseline;flex-wrap:wrap}
.form label{display:flex;flex-direction:column;gap:6px;font-size:12px;font-weight:600;color:var(--ink2)}
.form label em{color:#dc2626;font-style:normal}
.form input,.form select,.form textarea{font:inherit;font-size:14px;border:1px solid #cbd5e1;border-radius:10px;padding:9px 11px;background:#fff;color:var(--ink);width:100%}
.form textarea{min-height:110px;resize:vertical;line-height:1.5}
.form input:focus,.form select:focus,.form textarea:focus{outline:3px solid rgba(29,111,224,.2);border-color:var(--brand2)}
.form .hint{font-weight:400;color:var(--muted);font-size:11.5px}
.form .warn{grid-column:1/-1;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:10px;padding:8px 12px;font-size:13px}
.prodList{display:flex;flex-direction:column;gap:10px}
.prow{display:grid;grid-template-columns:96px 1fr 38px;gap:12px;align-items:center;background:#fff;border:1px solid var(--line);border-radius:12px;padding:8px}
.pimg{position:relative;width:96px;height:72px;border-radius:9px;overflow:hidden;background:#f1f5f9;border:1.5px dashed #cbd5e1;display:grid;place-items:center;cursor:pointer;color:var(--muted);font-size:11px;text-align:center}
.pimg:hover{border-color:var(--brand2);color:var(--brand2)}
.pimg img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.pimg input{display:none}
.pimg.busy::after{content:"Yuklanmoqda…";position:absolute;inset:0;background:rgba(255,255,255,.85);display:grid;place-items:center;font-size:11px;color:var(--ink2)}
.pdel{width:38px;height:38px;border-radius:10px;border:1px solid var(--line);background:#fff;cursor:pointer;color:#94a3b8;display:grid;place-items:center}
.pdel:hover{color:#dc2626;border-color:#dc2626;background:#fef2f2}
.addProd{border:1.5px dashed #cbd5e1;background:transparent;border-radius:12px;padding:10px;cursor:pointer;color:var(--ink2);font-weight:600}
.addProd:hover{border-color:var(--brand2);color:var(--brand2)}
.login{max-width:360px;margin:10px auto;display:flex;flex-direction:column;gap:14px}
#toast{position:fixed;left:50%;bottom:24px;transform:translate(-50%,20px);background:var(--ink);color:#fff;padding:10px 18px;border-radius:12px;font-weight:600;font-size:13.5px;opacity:0;pointer-events:none;transition:.2s;z-index:70;box-shadow:0 10px 30px rgba(0,0,0,.25)}
#toast.show{opacity:1;transform:translate(-50%,0)}
#toast.err{background:#dc2626}

@media (max-width:700px){
  .form .fgrid{grid-template-columns:1fr}
  header{padding:12px 14px 0}
  .chips{display:none}
  .search{margin:0 0 8px;max-width:none;order:-1;flex-basis:100%}
  #mapView,#listView{padding:12px}
  #modal{width:100vw;max-height:92vh;left:0;top:auto;bottom:0;border-radius:18px 18px 0 0;transform:translateY(30px)}
  #modal.open{transform:none}
  .mh h2{font-size:17px}
  .mbadge{min-width:52px;height:52px}.mbadge b{font-size:18px}
  .mb{padding:14px}
  .pcard{grid-template-columns:1fr}
  .pcard .pic{border-right:0;border-bottom:1px solid var(--line)}
  .pwrap .side{grid-template-columns:1fr}
  #lb .lbn{top:auto;bottom:24px;transform:none}
}
</style>
@endverbatim
</head>
<body>
<header>
  <div class="hrow">
    <div class="logo"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg></div>
    <div class="brand">
      <h1>Innovatsion ko'rgazma 2026</h1>
      <p>Ishtirokchilar joylashuvi xaritasi</p>
    </div>
    <div class="chips" id="chips"></div>
    <button class="adminBtn" id="adminBtn" hidden></button>
  </div>
  <div class="hrow2">
    <div class="tabs">
      <button id="tMap" class="on">Xarita</button>
      <button id="tList">Ro'yxat</button>
    </div>
    <label class="search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      <input id="q" type="search" placeholder="Tashkilot, mahsulot yoki stend nomini yozing…" autocomplete="off">
    </label>
  </div>
</header>

<main>
  <section id="mapView">
    <div class="dirs" id="dirs"></div>
    <div class="mapCard" id="mapCard">
      <div class="mapScale" id="mapScale"><div class="map" id="map"></div></div>
    </div>
    <div class="maphint">
      <span>👆 Katakchani bosing — shu joydagi ishtirokchi haqida ma'lumot chiqadi</span>
      <span><i style="background:repeating-linear-gradient(135deg,#94a3b8 0 4px,#fff 4px 8px)"></i>Ma'lumot kiritilmagan</span>
    </div>
  </section>
  <section id="listView">
    <div class="listBar"><span class="cnt" id="listCnt"></span><button class="btn primary" id="addBtn" hidden>＋ Yangi ishtirokchi</button></div>
    <div class="tableCard" id="tableCard"></div>
  </section>
</main>

<div id="tip"></div>
<div id="overlay"></div>
<div id="modal" role="dialog" aria-modal="true"></div>
<div id="toast"></div>
<div id="lb">
  <button class="lbx" aria-label="Yopish">×</button>
  <button class="lbn prev" aria-label="Oldingi">‹</button>
  <img alt="">
  <p></p><div class="cnt"></div>
  <button class="lbn next" aria-label="Keyingi">›</button>
</div>

<script>
window.EXPO = {
  places: @json($places),
  isAdmin: @json($isAdmin),
  stands: @json(config('expo.stands')),
  perStand: @json(config('expo.places_per_stand')),
  storage: @json(asset('storage')) + '/',
  routes: {
    data: @json(route('expo.data')),
    places: @json(url('expo/places')),
    upload: @json(route('expo.uploads.store')),
    login: @json(route('login')),
    logout: @json(route('logout')),
  },
};
</script>
@verbatim
<script>
let DATA = [], byStand = {};
function setData(arr) {
  DATA = arr; byStand = {};
  DATA.forEach(p => (byStand[p.stand] ||= []).push(p));
  Object.values(byStand).forEach(a => a.sort((x, y) => (+x.place) - (+y.place)));
}
// rasmlar "public" diskda: storage/expo/... (admin yuklaganlari storage/expo/uploads/...)
const imgSrc = f => EXPO.storage + f;

/* ---------- xarita ("2026" varag'i asosida) ---------- */
const PER = EXPO.perStand, HALF = PER / 2;   // har bir stend ikki ustun: 1..HALF va HALF+1..PER
/* B qatori tepada, A pastda. col = stendning birinchi grid ustuni; flip = o'ng yarimdagi stendlar (1..HALF chap tomonda) */
const STANDS = ['B', 'A'].flatMap(h => [1, 2, 3, 4, 5, 6].map(i => ({id: h + i, col: 5 * i, top: h === 'B', flip: i > 3})));
/* Stend bo'limlari: yorliq va rang. a..b — joylar oralig'i (yozilmasa butun stend) */
const SECTIONS = [
  {s:'B1', t:"QISHLOQ, SUV XO'JALIGI VA ATROF MUHIT", c:'#5fa83a'},
  {s:'B2', t:"XALQARO HAMKORLIK VA GLOBAL INNOVATSIYALAR", c:'#0ea5e9'},
  {s:'B3', t:"XALQARO HAMKORLIK VA GLOBAL INNOVATSIYALAR", c:'#0ea5e9'},
  {s:'B4', a:1, b:10, t:'7TECH', c:'#c0162c'},
  {s:'B4', a:11, b:20, t:'Universities of technology', c:'#c0162c'},
  {s:'B5', a:1, b:10, t:'MUDOFA SANOATI', c:'#ea7a2e'},
  {s:'B5', a:11, b:20, t:'SANOAT', c:'#ea7a2e'},
  {s:'B6', t:'SANOAT', c:'#ea7a2e'},
  {s:'A1', t:'HUDUDIY INNOVATSIYALAR', c:'#6b7280'},
  {s:'A2', a:1, b:10, t:'MUNIS', c:'#7e3bb5'},
  {s:'A2', a:11, b:20, t:'Ratsionalizatorlar va ixtirochilar', c:'#7e3bb5'},
  {s:'A3', a:1, b:10, t:'FANLAR AKADEMIYASI', c:'#7e3bb5'},
  {s:'A3', a:11, b:20, t:'XOTIN-QIZLAR', c:'#7e3bb5'},
  {s:'A4', t:'INNOVATSIYALAR OFISI', c:'#2b4f9c'},
  {s:'A5', a:1, b:10, t:'SPIN OFF', c:'#fde047', light:true},
  {s:'A5', a:11, b:20, t:'Texnologik startaplar', c:'#fde047', light:true},
  {s:'A6', a:1, b:10, t:'START UP', c:'#fde047', light:true},
  {s:'A6', a:11, b:20, t:'YASHNOBOD TEXNOPARKI', c:'#fde047', light:true},
];
/* Yo'nalishlar (legenda) */
const DIRS = [
  {n:"Qishloq xo'jaligi", c:'#5fa83a', s:['B1']},
  {n:'Xalqaro hamkorlik', c:'#0ea5e9', s:['B2','B3']},
  {n:'7TECH va universitetlar', c:'#c0162c', s:['B4']},
  {n:'Mudofaa va sanoat', c:'#ea7a2e', s:['B5','B6']},
  {n:'Hududiy innovatsiyalar', c:'#6b7280', s:['A1']},
  {n:'MUNIS, ixtirochilar, FA, xotin-qizlar', c:'#7e3bb5', s:['A2','A3']},
  {n:'Innovatsiyalar ofisi', c:'#2b4f9c', s:['A4']},
  {n:'Startaplar va texnopark', c:'#fde047', s:['A5','A6']},
];
// joyning bo'limi; butun stend uchun — bo'limlar nomi birga
const labOf = (id, n) => SECTIONS.find(x => x.s === id && (!x.a || (+n >= x.a && +n <= x.b)));
const standLab = id => { const l = SECTIONS.filter(x => x.s === id); return {...l[0], t: [...new Set(l.map(x => x.t))].join(' · ')}; };
const $ = s => document.querySelector(s);
const $$ = s => [...document.querySelectorAll(s)];
const esc = s => String(s ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const first = s => String(s || '').split('\n')[0].replace(/^\d+\.\s*/, '');
const place = (id, n) => (byStand[id] || []).find(x => +x.place === +n);
// Excel'dagi yo'nalish nomi — shu bo'limdagi boshqa joylardan
const sectionOf = (id, n) => (byStand[id] || []).find(x => x.section && labOf(id, x.place) === labOf(id, n))?.section || '';
const map = $('#map');

function el(cls, style, html) {
  const d = document.createElement('div');
  d.className = cls; d.style.cssText = style; if (html != null) d.innerHTML = html;
  map.appendChild(d); return d;
}

// Qatorlar: 1 = ALFA HALL, 3 = B sarlavha, keyin HALF ta B qator, yo'lak, A sarlavha, HALF ta A qator
function buildMap() {
  map.innerHTML = '';
  const rB = 3, rCorr = rB + HALF + 1, rA = rCorr + 1, rEnd = rA + HALF + 1;
  map.style.gridTemplateRows = `36px 10px 24px repeat(${HALF},27px) 40px 24px repeat(${HALF},27px)`;
  el('zone soft', 'grid-column:3/9;grid-row:1', 'PRESS ZONE');
  el('zone soft', 'grid-column:10/29;grid-row:1', 'ALFA HALL');
  el('zone soft', `grid-column:1;grid-row:${rCorr - 3}/${rA + 4}`, 'BETA<br>HALL');
  el('zone soft vert', `grid-column:3;grid-row:${rB + 1}/${rCorr}`, '<span>ILMIY TEXNIKA</span>');
  el('zone soft vert', `grid-column:3;grid-row:${rA + 1}/${rEnd}`, '<span>XARBIY TEXNIKA</span>');
  el('aisle', `grid-column:19;grid-row:${rB}/${rEnd}`);
  el('corr', `grid-row:${rCorr}`, "YO'LAK");
  el('zone soft', `grid-column:35;grid-row:${rB + 1}/${rCorr}`, 'B2B<br>ZONE');
  el('zone soft', `grid-column:35;grid-row:${rA + 1}/${rEnd}`, 'AR/VR<br>ZONE');
  el('zone soft', `grid-column:37;grid-row:${rCorr - 3}/${rA + 4}`, 'EVENT<br>ZONE');
  el('zone soft', `grid-column:37;grid-row:${rEnd - 4}/${rEnd}`, 'BABY<br>HALL');

  STANDS.forEach(s => {
    const r0 = s.top ? rB : rA;
    const h = el('shdr', `grid-column:${s.col}/${s.col + 4};grid-row:${r0}`, s.id);
    h.onclick = () => openStand(s.id);
    for (let n = 1; n <= PER; n++) {
      const p = place(s.id, n), lab = labOf(s.id, n), low = n <= HALF;
      // 1..HALF pastdan tepaga, HALF+1..PER tepadan pastga — yo'lak tomonda 1 va PER turadi
      const c = el('cell' + (p && p.org ? '' : ' empty') + (lab.light ? '' : ' dark'),
        `grid-column:${low === s.flip ? s.col : s.col + 3};grid-row:${r0 + 1 + (low ? HALF - n : n - HALF - 1)};--c:${lab.c}`, n);
      c.dataset.stand = s.id; c.dataset.place = n;
      c.onclick = () => openPlace(s.id, n);
    }
    // yorliq: bo'lim o'z joylari ustuni yonida turadi
    const parts = SECTIONS.filter(x => x.s === s.id);
    parts.forEach(l => {
      const low = !l.a || l.a <= HALF;
      const col = parts.length === 1 ? `${s.col + 1}/${s.col + 3}` : low === s.flip ? s.col + 1 : s.col + 2;
      const d = el('slabel' + (l.light ? ' lighttxt' : '') + (parts.length > 1 ? ' narrow' : ''),
        `grid-column:${col};grid-row:${r0 + 1}/${r0 + 1 + HALF};--c:${l.c}`, `<span>${esc(l.t)}</span>`);
      d.dataset.stand = s.id;
      d.onclick = () => openStand(s.id);
    });
  });
}
// Xaritani ekranga moslash
function fitMap() {
  // butun xarita scroll'siz ekranga sig'adi (faqat telefonda juda kichik bo'lib ketsa scroll)
  const card = $('#mapCard'), sc = $('#mapScale');
  const w = map.offsetWidth, h = map.offsetHeight;
  let k = Math.min((card.clientWidth - 24) / w, (card.clientHeight - 24) / h, 1.6);
  const small = innerWidth < 700 && k < .5;
  if (small) k = .5;
  card.style.overflow = small ? 'auto' : 'hidden';
  card.style.alignItems = card.style.justifyContent = small ? 'flex-start' : 'center';
  map.style.transform = `scale(${k})`;
  sc.style.width = w * k + 'px'; sc.style.height = h * k + 'px';
}

// Tooltip
const tip = $('#tip');
map.addEventListener('mousemove', e => {
  const c = e.target.closest('.cell');
  if (!c) { tip.style.opacity = 0; return; }
  const p = place(c.dataset.stand, c.dataset.place);
  tip.innerHTML = `<b>${c.dataset.stand} · ${c.dataset.place}-joy</b>${p && p.org ? esc(first(p.org)) : "Ma'lumot kiritilmagan"}`;
  tip.style.left = Math.min(e.clientX + 14, innerWidth - 300) + 'px';
  tip.style.top = e.clientY + 16 + 'px';
  tip.style.opacity = 1;
});
map.addEventListener('mouseleave', () => tip.style.opacity = 0);

/* ---------- legenda ---------- */
let activeDir = null;
function buildDirs() {
  $('#dirs').innerHTML = DIRS.map((d, i) => `<button class="dir" data-i="${i}" style="--c:${d.c}"><i></i>${esc(d.n)}</button>`).join('');
  $('#dirs').onclick = e => {
    const b = e.target.closest('.dir'); if (!b) return;
    activeDir = activeDir === +b.dataset.i ? null : +b.dataset.i;
    $('#q').value = ''; highlight();
  };
}
function highlight(keys) {
  $$('.dir').forEach(b => b.classList.toggle('on', +b.dataset.i === activeDir));
  const set = activeDir != null ? new Set(DIRS[activeDir].s) : null;
  $$('.cell').forEach(c => {
    c.classList.remove('hit', 'dim');
    if (keys) c.classList.add(keys.has(c.dataset.stand + '-' + c.dataset.place) ? 'hit' : 'dim');
    else if (set && !set.has(c.dataset.stand)) c.classList.add('dim');
  });
  $$('.slabel').forEach(l => l.classList.toggle('dim', !!keys || !!(set && !set.has(l.dataset.stand))));
}

/* ---------- modal ---------- */
const phoneRe = /(\+?998[\s-]?)?\(?\d{2}\)?[\s-]?\d{3}[\s-]?\d{2}[\s-]?\d{2}/g;
const withTel = s => esc(s).replace(phoneRe, m => `<a href="tel:${m.replace(/[^\d+]/g, '')}">${m}</a>`);
const icon = {
  user: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>',
  org: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 7h1M14 7h1M9 11h1M14 11h1M10 21v-4h4v4"/></svg>',
};
let gallery = [];   // lightbox uchun joriy rasmlar
let nav = null;     // joriy joy (oldingi/keyingi uchun)

const xIcon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>';
function header(lab, kick, title, badge) {
  return `<div class="mh${lab && lab.light ? ' lt' : ''}" style="--c:${lab ? lab.c : '#0b3d91'}">
    ${badge ? `<div class="mbadge">${badge}</div>` : ''}
    <div class="mt"><div class="kick">${kick}</div><h2>${title}</h2></div>
    <button class="x" data-close aria-label="Yopish" title="Yopish (Esc)">${xIcon}</button></div>`;
}
const zoomIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>';
// Nom katagiga to'liq tavsif yozilgan bo'lsa: birinchi gap — sarlavha, qolgani — tavsif
function splitName(s) {
  const name = String(s || '').replace(/^\d+\s*[.,)]\s*/, '').trim();
  const m = name.length > 120 && name.match(/^(.{3,120}?)(?:\s[-–—]\s|\.\s)([\s\S]+)$/);
  return m ? [m[1], m[2].trim()] : [name, ''];
}
function openPlace(id, n) {
  const lab = labOf(id, n);
  const p = place(id, n);
  const main = p && p.cont ? place(id, p.cont) : null;
  const d = main || p;
  nav = {id, n: +n};
  // bir tashkilot egallagan barcha joylar (asosiy joy va uning davomlari)
  const mainNo = main ? +main.place : +n;
  const group = [mainNo, ...(byStand[id] || []).filter(x => +x.cont === mainNo).map(x => +x.place)];
  const range = group.length > 1 ? `${Math.min(...group)}–${Math.max(...group)}-joylar` : `${n}-joy`;
  let body;
  gallery = [];
  if (!d || !d.org) {
    body = `<div class="empty-state"><div class="big">🗂️</div>Bu joy uchun hozircha ma'lumot kiritilmagan.</div>`;
  } else {
    const prods = d.products;
    prods.forEach(x => x.img.forEach(f => gallery.push({f, cap: splitName(x.name)[0]})));
    // "1. …" bilan boshlanmagan qator — oldingi bandning davomi (Excel'da bitta band ikki qatorga bo'lingan)
    const lines = d.info.split('\n').map(s => s.trim()).filter(Boolean).reduce((a, s) => {
      if (a.length && /^\d+[.)]/.test(a[0]) && !/^\d+[.)]/.test(s)) a[a.length - 1] += ' ' + s; else a.push(s);
      return a;
    }, []);
    const numbered = lines.length > 1 && lines.every(s => /^\d+[.)]/.test(s));
    const desc = lines.length ? `<div class="box"><h4>Ishlanma haqida<i></i></h4><ul class="desc${numbered ? '' : ' plain'}">${lines.map(s => {
      const m = s.match(/^(\d+)[.)]\s*(.*)$/);
      return numbered && m ? `<li><span>${m[1]}</span>${esc(m[2])}</li>` : `<li>${esc(s)}</li>`;
    }).join('')}</ul></div>` : '';
    const ppl = (d.contact || d.dept) ? `<div class="box"><h4>Aloqa<i></i></h4>
      ${d.contact ? `<div class="person"><div class="ic">${icon.user}</div><div><small>Mas'ul shaxs</small>${withTel(d.contact)}</div></div>` : ''}
      ${d.dept ? `<div class="person"><div class="ic">${icon.org}</div><div><small>Mas'ul boshqarma</small>${esc(d.dept)}</div></div>` : ''}
    </div>` : '';
    // Mahsulotlar ketma-ket: har biriga rasm + nom + "Ishlanma haqida"dagi mos raqamli qator
    const byNo = {};
    if (numbered) lines.forEach(s => { const m = s.match(/^(\d+)[.)]\s*(.*)$/); if (m) byNo[m[1]] = m[2]; });
    const norm = s => String(s || '').toLowerCase().replace(/[^a-zа-яёўқғҳ0-9]+/gi, '');
    const used = new Set();
    const cards = prods.map((x, i) => {
      const no = String(i + 1);
      const [name, more] = splitName(x.name);
      let about = byNo[no] || '';
      if (about) used.add(no);
      // nomsiz yagona ishlanma: qisqa tavsif sarlavha bo'ladi, uzuni pastda alohida ko'rsatiladi
      if (!name && !about && prods.length === 1 && !numbered && d.info && d.info.length <= 140) { about = d.info; used.add('all'); }
      const title = name || about || (prods.length > 1 ? `${no}-mahsulot` : first(d.org));
      // tavsif nomdan farq qilsa (masalan, qo'shimcha izoh yoki tashkilot nomi bo'lsa) ko'rsatiladi
      let extra = about && norm(about) !== norm(title) ? about : '';
      // tavsif nomni takrorlasa, faqat qolgan qismini qoldiramiz: "Samomoyka (Andijon yoshlar texnoparki)" -> "Andijon yoshlar texnoparki"
      if (extra && name) {
        const tail = extra.match(/\(([^()]*)\)[.\s]*$/);
        if (norm(extra).startsWith(norm(name).slice(0, 12))) extra = tail ? tail[1].trim() : '';
      }
      const g0 = gallery.findIndex(g => g.f === x.img[0]);
      const pic = x.img.length
        ? `<div class="pic"><img loading="lazy" src="${imgSrc(x.img[0])}" data-gi="${g0}" alt="${esc(title)}"><span class="zoom">${zoomIcon}</span>${x.img.length > 1 ? `<span class="more">+${x.img.length - 1} rasm</span>` : ''}</div>`
        : `<div class="pic none">Rasm yo'q</div>`;
      const strip = x.img.length > 1 ? `<div class="strip">${x.img.slice(1).map(f => `<img loading="lazy" src="${imgSrc(f)}" data-gi="${gallery.findIndex(g => g.f === f)}" alt="">`).join('')}</div>` : '';
      return `<article class="pcard">${pic}<div class="txt">
        ${prods.length > 1 ? `<div class="no"><span>${no}</span>mahsulot</div>` : ''}
        <h3>${esc(title)}</h3>${extra ? (extra === about ? `<p>${esc(extra)}</p>` : `<p class="by">${icon.org.replace(/18/g, '15')}${esc(extra)}</p>`) : ''}${more ? `<p>${esc(more)}</p>` : ''}${strip}</div></article>`;
    }).join('');
    // mahsulotlarga bog'lanmagan qolgan tavsif qatorlari
    const rest = used.has('all') ? [] : numbered ? lines.filter(s => !used.has((s.match(/^(\d+)/) || [])[1])) : lines;
    const restBox = rest.length ? `<div class="box"><h4>Ishlanma haqida<i></i></h4><ul class="desc${numbered ? '' : ' plain'}">${rest.map(s => {
      const m = s.match(/^(\d+)[.)]\s*(.*)$/);
      return numbered && m ? `<li><span>${m[1]}</span>${esc(m[2])}</li>` : `<li>${esc(s)}</li>`;
    }).join('')}</ul></div>` : '';
    body = prods.length
      ? `<div class="pwrap"><div class="plist"><div class="plist-h">Mahsulotlar va ishlanmalar <b>${prods.length}</b><i></i></div>${cards}</div>
         ${restBox || ppl ? `<div class="side">${restBox}${ppl}</div>` : ''}</div>`
      : `<div class="side">${desc}${ppl}</div>`;
  }
  const prev = +n > 1, next = +n < PER;
  const key = `${id}|${d && d.org ? d.place : n}`;
  const adminBtns = !isAdmin ? '' : d && d.org
    ? `<div class="grp"><button class="btn" data-edit="${key}">✎ Tahrirlash</button><button class="btn danger" data-del="${key}">O'chirish</button></div>`
    : `<button class="btn primary" data-edit="${key}">＋ Ma'lumot qo'shish</button>`;
  $('#modal').innerHTML = header(lab, esc(lab.t),
      d && d.org ? esc(d.org) : `${id} stend, ${n}-joy`, `<b>${id}</b><span>${range}</span>`) +
    `<div class="mb">${body}</div>
    <div class="mf">
      <button class="nav" data-go="-1" ${prev ? '' : 'disabled'}>‹ ${id}-${+n - 1 || ''}</button>
      <div class="grp">${adminBtns}<button class="nav" data-stand="${id}">Butun ${id} stend</button></div>
      <button class="nav" data-go="1" ${next ? '' : 'disabled'}>${id}-${next ? +n + 1 : ''} ›</button>
    </div>`;
  $$('.cell.sel').forEach(x => x.classList.remove('sel'));
  [...group, +n].forEach(g => document.querySelector(`.cell[data-stand="${id}"][data-place="${g}"]`)?.classList.add('sel'));
  openModal();
}

function openStand(id) {
  const lab = standLab(id);
  const list = byStand[id] || [];
  const filled = list.filter(p => p.org).length;
  nav = null;
  $('#modal').innerHTML = header(lab, `${filled} / ${list.length} joy band`, esc(lab.t), `<b>${id}</b><span>stend</span>`) +
    `<div class="mb"><div class="rows">${list.map(p => {
      const d = p.cont ? place(id, p.cont) : p;
      const imgs = d && !p.cont ? d.products.flatMap(x => x.img).slice(0, 3) : [];
      return `<button class="row${p.org ? '' : ' off'}" data-open="${p.place}">
        <span class="n${lab.light ? ' lt' : ''}" style="--c:${lab.c}">${p.place}</span>
        <span class="t"><b>${p.org ? esc(first(p.org)) : "Ma'lumot kiritilmagan"}</b><small>${p.cont ? `${id}-${p.cont} bilan birga` : p.org ? esc(first(p.info)).slice(0, 90) : ''}</small></span>
        <span class="thumbs">${imgs.map(f => `<img src="${imgSrc(f)}" alt="">`).join('')}</span>
      </button>`;
    }).join('')}</div></div>`;
  $$('.cell.sel').forEach(x => x.classList.remove('sel'));
  $$(`.cell[data-stand="${id}"]`).forEach(c => c.classList.add('sel'));
  openModal();
}

function openResults() {
  const q = $('#q').value.trim();
  if (!q) return;
  $('#modal').innerHTML = header(null, `Qidiruv · «${esc(q)}»`, `${hits.length} ta natija topildi`, `<b>${hits.length}</b><span>natija</span>`) +
    `<div class="mb">${hits.length ? `<div class="rows">${hits.map(p => {
      const lab = labOf(p.stand, p.place);
      return `<button class="row" data-s="${p.stand}" data-p="${p.place}">
        <span class="n${lab.light ? ' lt' : ''}" style="--c:${lab.c};width:auto;padding:0 8px">${p.stand}-${p.place}</span>
        <span class="t"><b>${esc(first(p.org))}</b><small>${esc(lab.t)}</small></span></button>`;
    }).join('')}</div>` : '<div class="empty-state"><div class="big">🔍</div>Hech narsa topilmadi.</div>'}</div>`;
  openModal();
}

function openModal() { $('#modal').classList.add('open'); $('#overlay').classList.add('open'); $('#modal .mb').scrollTop = 0; tip.style.opacity = 0; }
function closeModal() {
  $('#modal').classList.remove('open'); $('#overlay').classList.remove('open');
  $$('.cell.sel').forEach(x => x.classList.remove('sel'));
}

$('#modal').addEventListener('click', e => {
  const t = e.target;
  if (t.closest('form')) return;
  if (t.closest('[data-close]')) return closeModal();
  const ed = t.closest('[data-edit]');
  if (ed) { const [s, n] = ed.dataset.edit.split('|'); return openForm(s, n); }
  const del = t.closest('[data-del]');
  if (del) { const [s, n] = del.dataset.del.split('|'); return delPlace(s, n); }
  const go = t.closest('[data-go]');
  if (go && nav) return openPlace(nav.id, nav.n + +go.dataset.go);
  const st = t.closest('[data-stand]');
  if (st) return openStand(st.dataset.stand);
  const op = t.closest('[data-open]');
  if (op) return openPlace(document.querySelector('#modal .mbadge b').textContent, op.dataset.open);
  const r = t.closest('[data-s]');
  if (r) return openPlace(r.dataset.s, r.dataset.p);
  if (t.dataset.gi != null) openLb(+t.dataset.gi);
});

/* ---------- lightbox ---------- */
let gi = 0;
function openLb(i) {
  gi = (i + gallery.length) % gallery.length;
  const g = gallery[gi], lb = $('#lb');
  lb.querySelector('img').src = imgSrc(g.f);
  lb.querySelector('p').textContent = (g.cap || '').replace(/^\d+[.,]\s*/, '');
  lb.querySelector('.cnt').textContent = `${gi + 1} / ${gallery.length}`;
  lb.querySelectorAll('.lbn').forEach(b => b.style.display = gallery.length > 1 ? '' : 'none');
  lb.classList.add('open');
}
$('#lb').addEventListener('click', e => {
  if (e.target.classList.contains('prev')) return openLb(gi - 1);
  if (e.target.classList.contains('next')) return openLb(gi + 1);
  if (e.target.tagName !== 'IMG') $('#lb').classList.remove('open');
});
document.addEventListener('keydown', e => {
  const lbOpen = $('#lb').classList.contains('open');
  if (e.key === 'Escape') return lbOpen ? $('#lb').classList.remove('open') : closeModal();
  if (lbOpen && e.key === 'ArrowLeft') openLb(gi - 1);
  else if (lbOpen && e.key === 'ArrowRight') openLb(gi + 1);
  else if (!lbOpen && nav && $('#modal').classList.contains('open') && document.activeElement !== $('#q')) {
    if (e.key === 'ArrowLeft' && nav.n > 1) openPlace(nav.id, nav.n - 1);
    if (e.key === 'ArrowRight' && nav.n < PER) openPlace(nav.id, nav.n + 1);
  }
});
$('#overlay').onclick = closeModal;

/* ---------- qidiruv ---------- */
function match(p, q) {
  return [p.stand, p.stand + '-' + p.place, p.org, p.info, p.contact, p.dept, p.section, ...p.products.map(x => x.name)]
    .some(v => String(v).toLowerCase().includes(q));
}
let hits = [];
$('#q').addEventListener('input', () => {
  activeDir = null;
  const q = $('#q').value.trim().toLowerCase();
  hits = q ? DATA.filter(p => p.org && !p.cont && match(p, q)) : [];
  highlight(q ? new Set(hits.map(p => p.stand + '-' + p.place)) : null);
  renderList(q);
});
$('#q').addEventListener('keydown', e => { if (e.key === 'Enter') openResults(); });

/* ---------- ro'yxat ---------- */
function renderList(q = '') {
  const rows = DATA.filter(p => !q || match(p, q));
  $('#listCnt').textContent = `${rows.length} ta joy · ${rows.filter(p => p.org).length} tasi band`;
  $('#tableCard').innerHTML = `<table><thead><tr><th>Joy</th><th>Yo'nalish</th><th>Tashkilot</th><th>Ishlanma</th><th>Mas'ul</th></tr></thead><tbody>` +
    rows.map(p => {
      const lab = labOf(p.stand, p.place);
      return `<tr data-s="${p.stand}" data-p="${p.place}">
        <td><span class="tag${lab.light ? ' lt' : ''}" style="--c:${lab.c}">${p.stand}-${p.place}</span></td>
        <td>${esc(lab.t)}</td>
        <td class="pre">${p.org ? esc(p.org) : '<span class="muted">—</span>'}</td>
        <td>${p.cont ? `<span class="muted">${p.stand}-${p.cont} bilan birga</span>` : esc(first(p.info)).slice(0, 140)}</td>
        <td class="pre">${esc(p.contact)}</td></tr>`;
    }).join('') + '</tbody></table>';
}
$('#tableCard').addEventListener('click', e => { const tr = e.target.closest('tr[data-s]'); if (tr) openPlace(tr.dataset.s, tr.dataset.p); });
function showTab(list) {
  requestAnimationFrame(fitMap);
  $('#tMap').classList.toggle('on', !list); $('#tList').classList.toggle('on', list);
  $('#mapView').style.display = list ? 'none' : ''; $('#listView').style.display = list ? 'block' : 'none';
  if (!list) fitMap();
}
$('#tMap').onclick = () => showTab(false);
$('#tList').onclick = () => showTab(true);

/* ---------- admin ---------- */
let isAdmin = EXPO.isAdmin;
const csrf = document.querySelector('meta[name=csrf-token]').content;
async function api(method, url, body, isForm) {
  const headers = {'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'};
  if (!isForm && body) headers['Content-Type'] = 'application/json';
  const r = await fetch(url, {method, credentials: 'same-origin', headers, body: isForm ? body : body ? JSON.stringify(body) : undefined});
  const j = await r.json().catch(() => ({}));
  if (r.status === 401 || r.status === 419) { location.href = EXPO.routes.login; throw new Error('Qaytadan kiring'); }
  if (!r.ok) throw new Error(j.errors ? Object.values(j.errors)[0][0] : j.message || 'Xatolik yuz berdi');
  return j;
}
function toast(msg, err) {
  const t = $('#toast'); t.textContent = msg; t.className = 'show' + (err ? ' err' : '');
  clearTimeout(toast.t); toast.t = setTimeout(() => t.className = err ? 'err' : '', 2600);
}
function renderAdmin() {
  const b = $('#adminBtn');
  b.hidden = false;
  b.classList.toggle('on', isAdmin);
  b.innerHTML = isAdmin ? '● Admin · Chiqish' : '🔒 Admin kirish';
  $('#addBtn').hidden = !isAdmin;
}
$('#adminBtn').onclick = () => {
  if (!isAdmin) { location.href = EXPO.routes.login; return; }
  const f = document.createElement('form');
  f.method = 'POST'; f.action = EXPO.routes.logout;
  f.innerHTML = `<input type="hidden" name="_token" value="${csrf}">`;
  document.body.appendChild(f); f.submit();
};
$('#addBtn').onclick = () => openForm(null, null);

async function reload() {
  setData(await api('GET', EXPO.routes.data));
  buildMap(); renderList($('#q').value.trim().toLowerCase()); renderChips(); fitMap();
}

function prodRow(x = {name: '', img: []}) {
  const f = x.img[0];
  return `<div class="prow" data-img="${esc(x.img.join('|'))}">
    <label class="pimg">${f ? `<img src="${imgSrc(f)}" alt="">` : 'Rasm<br>yuklash'}<input type="file" accept="image/jpeg,image/png,image/webp"></label>
    <input name="pname" placeholder="Mahsulot / ishlanma nomi" value="${esc(x.name)}">
    <button type="button" class="pdel" title="Olib tashlash">${xIcon}</button></div>`;
}

function openForm(id, n) {
  const isNew = !id;
  const p = id ? place(id, n) : null;
  const d = p && p.org ? p : {section: '', org: '', info: '', contact: '', dept: '', products: []};
  if (!d.section && id) d.section = sectionOf(id, n);
  const lab = id ? labOf(id, n) : null;
  const opts = (arr, v) => arr.map(x => `<option ${String(x) === String(v) ? 'selected' : ''}>${x}</option>`).join('');
  $('#modal').innerHTML = header(lab, id ? esc(lab.t) : "Ro'yxatga qo'shish", p && p.org ? "Ma'lumotni tahrirlash" : 'Yangi ishtirokchi',
      id ? `<b>${id}</b><span>${n}-joy</span>` : '<b>＋</b>') +
    `<form class="mb form" id="pform" novalidate>
      <div class="fgrid">
        ${isNew ? `<label><span class="lt">Stend <em>*</em></span><select name="stand">${opts(EXPO.stands, EXPO.stands[0])}</select></label>
        <label><span class="lt">Joy <em>*</em></span><select name="place">${opts(Array.from({length: PER}, (_, i) => i + 1), 1)}</select></label>
        <div class="warn" id="occ" hidden></div>` : ''}
        <label class="full"><span class="lt">Tashkilot nomi <em>*</em></span><input name="org" value="${esc(d.org)}" required></label>
        <label class="full"><span class="lt">Yo'nalish (bo'lim)</span><input name="section" value="${esc(d.section)}"></label>
        <label class="full"><span class="lt">Ishlanma haqida <span class="hint">Har bir mahsulotni yangi qatorga yozing: 1. … 2. …</span></span><textarea name="info">${esc(d.info)}</textarea></label>
        <label><span class="lt">Mas'ul shaxs <span class="hint">F.I.Sh va telefon</span></span><textarea name="contact" style="min-height:64px">${esc(d.contact)}</textarea></label>
        <label><span class="lt">Mas'ul boshqarma</span><textarea name="dept" style="min-height:64px">${esc(d.dept)}</textarea></label>
        <div class="full"><label style="margin-bottom:8px">Mahsulotlar va rasmlar</label>
          <div class="prodList" id="prodList">${d.products.map(prodRow).join('')}</div>
          <button type="button" class="addProd" id="addProd" style="margin-top:10px;width:100%">＋ Mahsulot qo'shish</button></div>
      </div>
    </form>
    <div class="mf"><button class="btn" data-close>Bekor qilish</button><button class="btn primary" id="saveBtn">Saqlash</button></div>`;
  openModal();
  const form = $('#pform'), list = $('#prodList');
  $('#addProd').onclick = () => { list.insertAdjacentHTML('beforeend', prodRow()); list.lastElementChild.querySelector('input[name=pname]').focus(); };
  list.addEventListener('click', e => { const b = e.target.closest('.pdel'); if (b) b.closest('.prow').remove(); });
  list.addEventListener('change', async e => {
    if (e.target.type !== 'file' || !e.target.files[0]) return;
    const row = e.target.closest('.prow'), box = e.target.closest('.pimg'), file = e.target.files[0];
    if (file.size > 15 * 1024 * 1024) return toast('Rasm 15 MB dan katta bo\'lmasin', true);
    box.classList.add('busy');
    try {
      const fd = new FormData(); fd.append('image', file);
      const r = await api('POST', EXPO.routes.upload, fd, true);
      row.dataset.img = r.file;
      box.querySelector('img')?.remove();
      box.insertAdjacentHTML('afterbegin', `<img src="${imgSrc(r.file)}" alt="">`);
    } catch (err) { toast(err.message, true); }
    box.classList.remove('busy'); e.target.value = '';
  });
  if (isNew) {
    const chk = () => {
      const q = place(form.stand.value, form.place.value), o = $('#occ');
      o.hidden = !(q && q.org);
      if (q && q.org) o.textContent = `Diqqat: bu joy band — «${first(q.org)}». Saqlasangiz, ma'lumot almashtiriladi.`;
      // yo'nalishni tanlangan stenddan avtomatik olish (foydalanuvchi o'zi yozmagan bo'lsa)
      const auto = sectionOf(form.stand.value, form.place.value);
      if (!form.section.value || form.section.value === chk.auto) form.section.value = auto;
      chk.auto = auto;
    };
    form.stand.onchange = form.place.onchange = chk; chk();
  }
  $('#saveBtn').onclick = async () => {
    const s = isNew ? form.stand.value : id, num = isNew ? form.place.value : n;
    if (!form.org.value.trim()) { form.org.focus(); return toast('Tashkilot nomini kiriting', true); }
    const body = {
      org: form.org.value, section: form.section.value, info: form.info.value, contact: form.contact.value, dept: form.dept.value,
      products: [...list.children].map(r => ({name: r.querySelector('input[name=pname]').value, img: r.dataset.img ? r.dataset.img.split('|') : []})),
    };
    const btn = $('#saveBtn'); btn.disabled = true;
    try {
      await api('PUT', `${EXPO.routes.places}/${s}/${num}`, body);
      await reload(); toast('Saqlandi ✓'); openPlace(s, num);
    } catch (err) { toast(err.message, true); btn.disabled = false; }
  };
}

async function delPlace(id, n) {
  const p = place(id, n);
  if (!confirm(`${id}-${n} joydagi «${first(p?.org)}» ma'lumotini o'chirasizmi?\nBu joy bo'sh bo'lib qoladi.`)) return;
  try {
    await api('DELETE', `${EXPO.routes.places}/${id}/${n}`);
    await reload(); toast("O'chirildi"); openPlace(id, n);
  } catch (err) { toast(err.message, true); }
}

/* ---------- start ---------- */
function renderChips() {
  const orgs = new Set(DATA.filter(p => p.org && !p.cont).map(p => first(p.org)));
  const imgs = DATA.reduce((a, p) => a + p.products.reduce((b, x) => b + x.img.length, 0), 0);
  $('#chips').innerHTML = [
    [Object.keys(byStand).length, 'stend'], [DATA.filter(p => p.org).length, 'joy band'], [orgs.size, 'ishtirokchi'], [imgs, 'ishlanma rasmi'],
  ].map(([n, t]) => `<span class="chip"><b>${n}</b>${t}</span>`).join('');
}
function start() {
  buildMap(); buildDirs(); renderList(); renderChips(); fitMap(); renderAdmin();
}
addEventListener('resize', fitMap);
setData(EXPO.places);
start();
</script>
@endverbatim
</body>
</html>
