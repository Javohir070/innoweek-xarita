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
[hidden]{display:none!important}
/* to'liq ekran: yon panel va izoh yashiriladi, xarita butun oynani egallaydi */
body.mapfull #side,body.mapfull .maphint,body.mapfull #listView{display:none!important}
body.mapfull #mapView{display:flex!important;padding:8px}
#zFull svg{display:block}
/* ---------- nom va bo'limlar (yon panel boshida) ---------- */
.sideTop{display:flex;flex-direction:column;gap:12px}
.brandRow{display:flex;align-items:center;gap:10px;min-width:0}
.logo{width:40px;height:40px;border-radius:11px;background:linear-gradient(140deg,var(--brand2),var(--brand));display:grid;place-items:center;flex:none;box-shadow:0 4px 12px rgba(11,61,145,.3)}
.brand{min-width:0}
.brand h1{margin:0;font-size:16px;font-weight:800;letter-spacing:-.01em;line-height:1.2}
.brand p{margin:1px 0 0;font-size:11.5px;color:var(--muted)}
.tabs{display:flex;gap:3px;background:#e2e8f0;border-radius:12px;padding:3px}
.tabs button{flex:1;border:0;background:transparent;color:var(--ink2);padding:8px 14px;border-radius:9px;cursor:pointer;font-weight:600;transition:.15s}
.tabs button:hover{color:var(--ink)}
.tabs button.on{background:#fff;color:var(--ink);box-shadow:0 1px 3px rgba(15,23,42,.15)}
.search{position:relative;display:block;flex:none}
.search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:.6}
#q{width:100%;padding:11px 12px 11px 38px;border:1px solid var(--line);border-radius:12px;font:inherit;background:#fff;color:var(--ink);box-shadow:var(--shadow)}
#q:focus{outline:3px solid rgba(29,111,224,.22);border-color:var(--brand2)}
/* yozish paytida chiqadigan natijalar */
#sug{position:absolute;left:0;right:0;top:calc(100% + 6px);background:#fff;color:var(--ink);border-radius:12px;box-shadow:0 14px 36px rgba(15,23,42,.28);overflow:hidden;z-index:20;display:none}
#sug.open{display:block}
#sug button{display:flex;gap:10px;align-items:center;width:100%;border:0;border-bottom:1px solid var(--line);background:#fff;padding:9px 12px;text-align:left;cursor:pointer;color:inherit}
#sug button:last-child{border-bottom:0}
#sug button:hover{background:#f1f6ff}
#sug .t{flex:1;min-width:0}
#sug .t b,#sug .t small{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
#sug .t b{font-weight:600;font-size:13.5px}
#sug .t small{color:var(--muted);font-size:12px}
#sug .more{justify-content:center;color:var(--brand2);font-weight:600;font-size:13px}
#sug .none{padding:14px;color:var(--muted);text-align:center;font-size:13px}

main{flex:1;min-height:0;display:flex}
/* yon panel: qidiruv, yo'nalishlar, statistika */
#side{flex:none;width:272px;padding:14px 0 12px 20px;display:flex;flex-direction:column;gap:12px;min-height:0}
.chips{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.chip{background:var(--surface);border-radius:12px;box-shadow:var(--shadow);padding:9px 12px;display:flex;flex-direction:column;font-size:11.5px;color:var(--muted);line-height:1.3}
.chip b{font-size:19px;font-weight:800;color:var(--ink);letter-spacing:-.01em}

#mapView{flex:1;min-width:0;min-height:0;overflow:hidden;padding:14px 20px 12px;display:flex;flex-direction:column;gap:10px}

/* ---------- yo'nalishlar legendasi ---------- */
.dirs{display:flex;gap:6px;flex-wrap:wrap}
.dir{display:flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--line);border-radius:999px;padding:4px 11px 4px 7px;font-size:12px;font-weight:500;color:var(--ink2);cursor:pointer;transition:.15s}
.dir i{width:14px;height:14px;border-radius:4px;background:var(--c);box-shadow:inset 0 0 0 1px rgba(0,0,0,.15)}
.dir:hover{border-color:#94a3b8}
.dir.on{background:var(--ink);color:#fff;border-color:var(--ink)}
.dir.all{padding-left:11px;font-weight:600}

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
/* yo'lak o'rtasidagi orolchalar */
.island{display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;letter-spacing:.06em;color:#0f172a;margin:7px 3px;z-index:1;border-radius:6px;background:#fff;border:1.5px solid #cbd5e1;box-shadow:0 1px 3px rgba(15,23,42,.08)}
.island.link{cursor:pointer;transition:transform .12s,box-shadow .12s,border-color .12s}
.island.link::after{content:"";width:13px;height:13px;margin-left:6px;opacity:.7;background:currentColor;
  -webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='16' rx='2.5'/%3E%3Ccircle cx='8.5' cy='9.5' r='1.6'/%3E%3Cpath d='M21 16l-5-5-8 8'/%3E%3C/svg%3E") center/contain no-repeat;
  mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='16' rx='2.5'/%3E%3Ccircle cx='8.5' cy='9.5' r='1.6'/%3E%3Cpath d='M21 16l-5-5-8 8'/%3E%3C/svg%3E") center/contain no-repeat}
.island.link:hover{transform:translateY(-2px);border-color:var(--brand2);color:var(--brand2);box-shadow:0 8px 18px rgba(15,23,42,.2)}
/* hamkor stendi rasmlari oynasi */
.igal{background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.06)}
.igal .pic{position:relative;display:grid;place-items:center;background:#05070d;cursor:zoom-in}
.igal .pic img{display:block;max-width:100%;max-height:62vh;object-fit:contain}
.igal .pic .zoom{position:absolute;right:12px;bottom:12px;width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.18);color:#fff;display:grid;place-items:center;pointer-events:none}
.igal .strip{display:flex;gap:8px;padding:10px;flex-wrap:wrap}
.igal .strip img{width:112px;height:63px;border-radius:9px;object-fit:cover;cursor:zoom-in;border:2px solid transparent;transition:border-color .15s}
.igal .strip img:hover{border-color:var(--brand2)}
.map.plan .island{border:2px solid #111827;border-radius:3px;box-shadow:0 2px 6px rgba(15,23,42,.14)}
.corr{grid-column:5/34;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;letter-spacing:.3em;font-weight:600}
.shdr{font-size:11px;font-weight:700;color:var(--muted);display:flex;align-items:flex-end;justify-content:center;cursor:pointer;padding-bottom:3px}
.shdr:hover{color:var(--brand2)}
.cell{position:relative;margin:1px;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:flex-start;padding:0 4px;font-size:10px;font-weight:700;color:rgba(0,0,0,.45);background:var(--c);z-index:1;transition:transform .12s,box-shadow .12s,opacity .15s}
.cell.dark{color:rgba(255,255,255,.7)}
.cell.empty{background:repeating-linear-gradient(135deg,var(--c) 0 4px,rgba(255,255,255,.75) 4px 8px);opacity:.75}
.cell:hover{transform:scale(1.12);box-shadow:0 4px 12px rgba(0,0,0,.3);z-index:5}
.cell.wide{font-size:10.5px}
.cell.wide:hover{transform:scale(1.05)}
.cell.sel{box-shadow:0 0 0 3px var(--sel),0 0 0 5px var(--ink);z-index:6}
.cell.hit{box-shadow:0 0 0 3px var(--hit);z-index:4;animation:pulse 1.4s infinite}
.cell.dim{opacity:.18}
@keyframes pulse{50%{box-shadow:0 0 0 5px rgba(239,68,68,.35)}}
.slabel{margin:1px;border-radius:5px;background:var(--c);cursor:pointer;display:flex;align-items:center;justify-content:center;padding:4px 0;overflow:hidden;transition:opacity .15s}
.slabel span{writing-mode:vertical-rl;transform:rotate(180deg);font-weight:800;font-size:12.5px;letter-spacing:.02em;text-align:center;line-height:1.15;max-height:100%;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.35)}
.slabel.narrow span{font-size:11px}
.slabel.lighttxt span{color:#1e293b;text-shadow:none}
.slabel.dim{opacity:.18}
/* ---------- ko'rinish: xarita va yon panel bezagi ---------- */
.mapCard{background:var(--surface) radial-gradient(#e4e9f0 1px,transparent 1.2px) 0 0/18px 18px}
/* har bir stend — alohida oq kartochka */
.sbg{margin:-2px -5px -6px;border-radius:14px;background:#fff;border:1px solid var(--line);box-shadow:0 2px 10px rgba(15,23,42,.06)}
.shdr{align-items:center;padding:0;z-index:1}
.shdr span{background:#eef2f7;border-radius:999px;padding:1px 12px;font-size:11.5px;font-weight:800;color:var(--ink2);letter-spacing:.03em;transition:.15s}
.shdr:hover span{background:var(--brand2);color:#fff}
.cell{justify-content:center;padding:0;border-radius:5px;font-size:10.5px;color:rgba(0,0,0,.55);
  background:linear-gradient(180deg,rgba(255,255,255,.2),rgba(255,255,255,0) 55%),var(--c);box-shadow:inset 0 0 0 1px rgba(0,0,0,.07)}
.cell.dark{color:rgba(255,255,255,.9)}
.cell.wide{font-size:12px;font-weight:800}
/* har bir katakni o'z bo'limi yorlig'iga bog'lovchi chiziqcha */
.cell.cl{margin-right:9px}
.cell.cr{margin-left:9px}
.cell::after{content:"";position:absolute;top:50%;width:9px;height:2px;margin-top:-1px;background:var(--c);pointer-events:none}
.cell.cl::after{left:100%}
.cell.cr::after{right:100%}
.cell.empty::after{background:color-mix(in srgb,var(--c) 42%,#fff)}
/* bo'sh joy: och rang va hoshiya */
.cell.empty{opacity:1;background:color-mix(in srgb,var(--c) 9%,#fff);box-shadow:inset 0 0 0 1.5px color-mix(in srgb,var(--c) 42%,#fff);color:color-mix(in srgb,var(--c) 70%,#334155)}
.slabel{z-index:1;border-radius:7px;background:linear-gradient(180deg,rgba(255,255,255,.16),rgba(0,0,0,.07)),var(--c)}
.zone{border-color:#d5dde8;color:#64748b;font-size:11px}
.zone.soft{background:rgba(241,245,249,.9)}
.corr{background:rgba(241,245,249,.92)}
/* yon panel */
.dirs-h{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);padding:0 2px 2px}
.dir .cnt{margin-left:auto;font-size:11px;font-weight:700;color:var(--muted);background:#f1f5f9;border-radius:999px;padding:1px 8px;transition:.15s}
.dir.on .cnt{background:rgba(255,255,255,.18);color:#fff}
.dir.on i{box-shadow:0 0 0 2px rgba(255,255,255,.35)}
.chip b{color:var(--brand)}
/* ---------- xarita ko'rinishlari: v1 — asosiy (zal rejasi), v0 — oddiy rangli xarita ---------- */
.viewCtl{position:absolute;left:12px;top:12px;z-index:8;display:flex;background:#fff;border:1px solid var(--line);border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(15,23,42,.14)}
.viewCtl button{border:0;background:#fff;color:var(--ink2);font-size:12px;font-weight:600;padding:7px 11px;cursor:pointer}
.viewCtl button+button{border-left:1px solid var(--line)}
.viewCtl button.on{background:var(--ink);color:#fff}
/* zal rejasi: devor, pol, xonalar, stend bloklari */
.map.plan{padding:22px;background:linear-gradient(180deg,#ecf2fb,#dfe8f5);border:6px solid #111827;border-radius:6px;box-shadow:0 12px 32px rgba(15,23,42,.2)}
.map.plan .zone{background:#fff;border:2px solid #111827;border-radius:3px;color:#111827;font-weight:800;font-size:11.5px;letter-spacing:.03em}
.map.plan .zone.hall{background:#fff radial-gradient(#a3afc0 1.5px,transparent 1.7px) 3px 3px/9px 9px}
.map.plan .zone b{background:#fff;padding:2px 8px;border-radius:3px;font-weight:800}
.map.plan .aisle{background:none}
.map.plan .corr{background:none;color:#7c8aa0;font-weight:700}
/* stend — yaxlit rangli blok: kod katagi, och rangli kataklar, bog'lovchi chiziqlarsiz */
.map.plan .sbg{margin:0 -1px -1px 0;border:0;border-radius:4px;background:#fff;box-shadow:0 4px 12px rgba(15,23,42,.2)}
.map.plan .shdr{align-items:stretch;justify-content:stretch;padding:0;margin:0 -1px 0 0}
.map.plan .shdr span{display:grid;place-items:center;width:100%;padding:0;border-radius:0;background:var(--c);color:#fff;font-size:13px;font-weight:800;letter-spacing:.02em;
  box-shadow:inset 0 0 0 1px rgba(0,0,0,.16)}
.map.plan .shdr.lt span{color:#1e293b}
.map.plan .shdr:hover span{background:var(--c);filter:brightness(.9)}
.map.plan .cell,.map.plan .cell.cl,.map.plan .cell.cr{margin:0 -1px -1px 0;border-radius:0}
.map.plan .cell{box-shadow:none;font-size:10px;font-weight:700;
  background:color-mix(in srgb,var(--c) 40%,#fff);border:1px solid color-mix(in srgb,var(--c) 82%,#334155);color:color-mix(in srgb,var(--c) 38%,#0f172a)}
.map.plan .cell.empty{background:repeating-linear-gradient(135deg,color-mix(in srgb,var(--c) 16%,#fff) 0 5px,#fff 5px 10px);color:color-mix(in srgb,var(--c) 55%,#64748b)}
.map.plan .cell::after{display:none}
.map.plan .cell:hover{transform:none;filter:brightness(.93);box-shadow:none;z-index:2}
.map.plan .cell.sel{box-shadow:0 0 0 3px var(--sel),0 0 0 5px var(--ink);z-index:6}
.map.plan .cell.hit{box-shadow:0 0 0 3px var(--hit);z-index:4}
/* v1: stendlar tik — o'rtada to'q rangli yorliq, ikki yonida kataklar */
.map.v1 .slabel{margin:0 -1px -1px 0;border-radius:0;box-shadow:inset 0 0 0 1px rgba(0,0,0,.14)}
.map.v1 .cell.wide{font-size:11px}
/* yo'laklardagi harakatlanuvchi uzuq chiziq */
@keyframes flowY{to{background-position:center 24px}}
@keyframes flowX{to{background-position:24px center}}
/* v1: kataklar — oq kartochka ustidagi alohida plitkalar: yumaloq burchak, yengil gradient, pastida rangli qirra */
.map.v1 .sbg{background:#fff}
.map.v1 .cell,.map.v1 .cell.cl,.map.v1 .cell.cr{margin:1.5px;border-radius:6px}
.map.v1 .cell{font-size:10.5px;font-weight:800;color:color-mix(in srgb,var(--c) 48%,#0f172a);
  background:linear-gradient(180deg,#fff 0%,color-mix(in srgb,var(--c) 34%,#fff) 100%);
  border:1px solid color-mix(in srgb,var(--c) 58%,#fff);
  box-shadow:0 1.5px 0 color-mix(in srgb,var(--c) 70%,#64748b),0 2px 4px rgba(15,23,42,.08);
  transition:transform .12s,box-shadow .12s,background .12s}
.map.v1 .cell.wide{font-size:11.5px}
.map.v1 .cell:hover{transform:translateY(-2px);filter:none;z-index:5;
  background:linear-gradient(180deg,#fff 0%,color-mix(in srgb,var(--c) 55%,#fff) 100%);
  box-shadow:0 2px 0 color-mix(in srgb,var(--c) 80%,#475569),0 6px 12px color-mix(in srgb,var(--c) 35%,transparent)}
/* bo'sh joy: tekis, uzuq hoshiyali, qirrasiz */
.map.v1 .cell.empty{font-weight:600;color:color-mix(in srgb,var(--c) 45%,#94a3b8);border-style:dashed;box-shadow:none;
  background:repeating-linear-gradient(135deg,#fff 0 5px,color-mix(in srgb,var(--c) 11%,#fff) 5px 10px)}
.map.v1 .cell.sel{box-shadow:0 0 0 3px var(--sel),0 0 0 5px var(--ink);z-index:6}
.map.v1 .cell.hit{box-shadow:0 0 0 3px var(--hit);z-index:4}
.map.v1 .slabel{margin:1.5px;border-radius:6px;box-shadow:0 1.5px 0 rgba(0,0,0,.18),inset 0 1px 0 rgba(255,255,255,.25)}
@media (hover:none){.map.v1 .cell:hover{transform:none}}
/* v1: yo'laklar — oq yo'lka, o'rtasida harakatlanuvchi uzuq chiziq; asosiy kirishdan boshlanib, zal o'rtasida kesishadi */
.map.v1 .corr{margin:7px 0;border-radius:999px;box-shadow:inset 0 0 0 1px #cbd6e4,0 1px 3px rgba(15,23,42,.06);color:#475569;font-size:11px;font-weight:800;letter-spacing:.38em;
  background:repeating-linear-gradient(90deg,#8fa0b8 0 12px,transparent 12px 24px) 0 center/100% 2px repeat-x,rgba(255,255,255,.78);animation:flowX 1.6s linear infinite}
.map.v1 .corr span{background:#fff;padding:4px 16px 4px 20px;border-radius:999px;box-shadow:0 0 0 1px #cbd6e4,0 2px 6px rgba(15,23,42,.1)}
.map.v1 .aisle{margin:0 13px -22px;border-radius:999px 999px 0 0;box-shadow:inset 0 0 0 1px #cbd6e4,0 1px 3px rgba(15,23,42,.06);
  background:repeating-linear-gradient(180deg,#8fa0b8 0 12px,transparent 12px 24px) center 0/2px 100% repeat-y,rgba(255,255,255,.78);animation:flowY 1.6s linear infinite reverse}
@media (prefers-reduced-motion:reduce){.map.v1 .corr,.map.v1 .aisle{animation:none}}
/* v1: kirish, chiqishlar va hojatxonalar — namunadagidek devorning tashqarisida turadi.
   Buning uchun devor xaritaning chetida emas, ichkariroqda chiziladi (::before), atrofida belgilar uchun joy qoladi. */
.map .door{display:none}
.map.v1{--wt:46px;--wx:40px;--wb:66px;padding:calc(var(--wt) + 30px) calc(var(--wx) + 30px) calc(var(--wb) + 30px);border:0;border-radius:0;background:none;box-shadow:none}
.map.v1::before{content:"";position:absolute;inset:var(--wt) var(--wx) var(--wb);z-index:-1;border:6px solid #111827;border-radius:3px;
  background:linear-gradient(180deg,#ecf2fb,#dfe8f5);box-shadow:0 12px 32px rgba(15,23,42,.2)}
.map.v1 .door{display:block;position:absolute;pointer-events:none;z-index:2;line-height:0}
/* devordagi eshik: kulrang tavaqa */
.door.leaf{background:#cbd5e1;border:1px solid #64748b}
/* asosiy kirish: och yashil tavaqalar */
.door.gate{background:repeating-linear-gradient(90deg,#d1fae5 0 30px,#16a34a 30px 32px);border:1.5px solid #16a34a;border-radius:2px}
.door.inlbl{line-height:1;font-size:13px;font-weight:800;letter-spacing:.14em;color:#15803d;white-space:nowrap}
/* yon eshiklar faqat chiqish: belgi va tashqariga qaragan strelka */
.door.out{display:flex!important;align-items:center;gap:2px}
.map:not(.v1) .door.out{display:none!important}
/* xaritani kattalashtirish tugmalari */
.mapWrap{flex:1;min-height:0;position:relative;display:flex}
.zoomCtl{position:absolute;right:12px;bottom:12px;display:flex;flex-direction:column;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(15,23,42,.18);border:1px solid var(--line);z-index:8}
.zoomCtl button{width:40px;height:40px;border:0;background:#fff;color:var(--ink2);font-size:20px;font-weight:600;cursor:pointer;display:grid;place-items:center}
.zoomCtl button+button{border-top:1px solid var(--line)}
.zoomCtl button:hover:not(:disabled){background:#f1f6ff;color:var(--brand2)}
.zoomCtl button:disabled{opacity:.35;cursor:default}
.maphint .hm{display:none}
.maphint{display:flex;gap:18px;flex-wrap:wrap;color:var(--muted);font-size:12px;align-items:center}
.maphint i{display:inline-block;width:14px;height:14px;border-radius:3px;vertical-align:-3px;margin-right:6px}

#tip{position:fixed;pointer-events:none;background:var(--ink);color:#fff;padding:8px 10px;border-radius:8px;font-size:12px;max-width:280px;z-index:60;opacity:0;transition:opacity .1s;box-shadow:0 8px 20px rgba(0,0,0,.25)}
#tip b{display:block;font-size:11px;color:var(--sel);margin-bottom:2px}

/* ---------- list view ---------- */
#listView{flex:1;min-width:0;overflow:auto;padding:20px;display:none}
/* ishtirokchilar kartochkalari, stend bo'limlari bo'yicha guruhlangan */
.tableCard{display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:12px}
.tableCard .empty-state{grid-column:1/-1;background:#fff;border-radius:var(--radius);box-shadow:var(--shadow)}
.lgroup{grid-column:1/-1;display:flex;align-items:center;gap:10px;margin-top:12px;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ink2)}
.lgroup:first-child{margin-top:0}
.lgroup b{background:var(--c);color:#fff;border-radius:8px;padding:3px 10px;font-size:13px;letter-spacing:0}
.lgroup b.lt{color:#1e293b}
.lgroup i{flex:1;height:1px;background:#d5dde8}
.lgroup small{color:var(--muted);font-weight:600;letter-spacing:0;text-transform:none;font-size:12px}
.lcard{display:flex;gap:12px;text-align:left;background:#fff;border:1px solid var(--line);border-radius:14px;padding:10px;cursor:pointer;min-width:0;color:inherit;
  box-shadow:0 1px 2px rgba(15,23,42,.04);transition:border-color .15s,box-shadow .15s,transform .15s}
.lcard:hover{border-color:var(--c);box-shadow:0 10px 24px rgba(15,23,42,.1);transform:translateY(-1px)}
.lpic{flex:none;width:86px;height:86px;border-radius:10px;overflow:hidden;display:grid;place-items:center;font-weight:800;font-size:15px;
  background:color-mix(in srgb,var(--c) 12%,#fff);color:color-mix(in srgb,var(--c) 70%,#334155)}
.lpic img{width:100%;height:100%;object-fit:cover;display:block}
.lbody{flex:1;min-width:0;display:flex;flex-direction:column;gap:4px}
.lbody b{font-size:14px;line-height:1.3;font-weight:700;color:var(--ink);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.labout{font-size:12.5px;line-height:1.45;color:var(--ink2);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.lmeta{margin-top:auto;padding-top:2px;display:flex;gap:4px 12px;flex-wrap:wrap;font-size:11.5px;color:var(--muted)}
.lmeta span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%}
.lmeta .pr{color:var(--brand2);font-weight:600}
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
.hpic .pic .more{display:none}
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
/* bir nechta mahsulot: kartochkalar to'ri — tepada rasm (kesilmasdan), ostida nom */
.plist{display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:14px}
.pcard{display:flex;flex-direction:column}
.pcard .pic{aspect-ratio:auto;height:200px;border-right:0;border-bottom:1px solid var(--line);background:#fff}
.pcard .pic img{object-fit:contain}
.pcard .pic.none{height:84px;background:#f8fafc}
.pcard .txt{padding:14px 16px 16px;flex:1}
/* yagona ishlanma: chapda rasm (kesilmasdan), o'ngda nom, tavsif va aloqa */
.hero{display:grid;grid-template-columns:minmax(0,5fr) minmax(0,6fr);gap:18px;align-items:start}
.hero.nopic{grid-template-columns:1fr}
.hpic{position:sticky;top:0;background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.06)}
.hpic .pic{position:relative;display:grid;place-items:center;min-height:180px;cursor:zoom-in;background:#f1f5f9 radial-gradient(circle at 50% 40%,#fff 0,#f1f5f9 75%)}
.hpic .pic img{display:block;max-width:100%;max-height:400px;object-fit:contain}
.hpic .pic .zoom{position:absolute;right:10px;bottom:10px;width:32px;height:32px;border-radius:9px;background:rgba(15,23,42,.6);color:#fff;display:grid;place-items:center;pointer-events:none}
.hpic .strip{display:flex;gap:6px;flex-wrap:wrap;padding:8px;border-top:1px solid var(--line)}
.hpic .strip img{width:58px;height:46px;border-radius:8px;object-fit:cover;cursor:zoom-in;border:1px solid var(--line);transition:border-color .15s}
.hpic .strip img:hover{border-color:var(--brand2)}
.hside{display:flex;flex-direction:column;gap:14px;min-width:0}
.htitle h3{margin:0;font-size:19px;line-height:1.3;font-weight:800;letter-spacing:-.01em;color:var(--ink)}
.htitle p{margin:8px 0 0;font-size:14px;line-height:1.6;color:var(--ink2)}
.htitle p.by{display:flex;gap:6px;align-items:flex-start;color:var(--muted);font-size:13px}
.htitle p.by svg{flex:none;margin-top:3px}
.hside .desc{font-size:14.5px;line-height:1.65}
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
.mh,.mf{flex:none}
.mf{display:flex;justify-content:space-between;gap:10px;padding:12px 16px;border-top:1px solid var(--line);background:#fff}
.nav{border:1px solid var(--line);background:#fff;border-radius:12px;padding:9px 16px;cursor:pointer;font-weight:600;color:var(--ink2);display:flex;align-items:center;gap:6px}
.nav:hover:not(:disabled){border-color:var(--brand2);color:var(--brand2)}
.nav:disabled{opacity:.35;cursor:default}
.nav[data-go]:disabled{visibility:hidden}
.rows{display:flex;flex-direction:column;gap:8px}
.row{display:flex;align-items:center;gap:12px;width:100%;text-align:left;border:1px solid var(--line);background:#fff;border-radius:12px;padding:10px 12px;cursor:pointer;transition:.12s}
.row:hover{border-color:var(--brand2);background:#f8fbff}
.row .n{min-width:34px;padding:0 6px;height:34px;border-radius:9px;display:grid;place-items:center;font-weight:800;font-size:13px;background:var(--c);color:#fff;flex:none}
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
.adminBtn{margin-top:auto;align-self:flex-start;display:flex;align-items:center;gap:6px;border:0;background:#facc15;color:#0f172a;border-radius:999px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer}
.adminBtn:hover{background:#eab308}
.listBar{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.listBar .cnt{color:var(--ink2);font-size:13px;font-weight:600;margin-right:auto}
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

/* keng ekranda yo'nalishlar yon panelda ustun bo'lib turadi */
@media (min-width:901px){
  .dirs{flex-direction:column;flex-wrap:nowrap;align-items:stretch;gap:8px;overflow-y:auto;min-height:0;
    background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);padding:12px}
  .dir{flex:none;border-radius:10px;padding:9px 12px 9px 10px;font-size:13px;text-align:left;gap:9px}
  .dir.all{justify-content:center;padding:9px 12px}
}
/* tor ekranda yon panel tepaga chiqadi: qidiruv va bitta qatorda yo'nalishlar */
@media (max-width:900px){
  main{flex-direction:column}
  #side{width:auto;padding:10px 12px 0;gap:8px}
  .sideTop{flex-direction:row;align-items:center;gap:10px}
  .brandRow{flex:1}
  .brand p{display:none}
  .brand h1{font-size:14px;line-height:1.15}
  .tabs{flex:none}
  .tabs button{padding:7px 12px}
  .chips,.dirs-h{display:none}
  .dir .cnt{margin-left:2px}
  .dirs{flex-wrap:nowrap;overflow-x:auto;margin:0 -12px;padding:0 12px 2px;scrollbar-width:none}
  .dirs::-webkit-scrollbar{display:none}
  .dir{flex:none}
  .adminBtn{align-self:flex-end;margin-top:0}
  #mapView,#listView{padding:10px 12px}
  /* brauzer paneli ochilib-yopilganda pastki qism kesilmasin */
  body{height:100dvh}
}
/* sensorli ekranda sichqoncha effektlari yopishib qolmasin */
@media (hover:none){
  #tip{display:none}
  .cell:hover,.cell.wide:hover{transform:none;box-shadow:none}
  .cell.sel:hover{box-shadow:0 0 0 3px var(--sel),0 0 0 5px var(--ink)}
  .cell.hit:hover{box-shadow:0 0 0 3px var(--hit)}
}
@media (max-width:700px){
  .form .fgrid{grid-template-columns:1fr}
  .logo{width:34px;height:34px;border-radius:9px}
  #mapView{gap:8px;padding-bottom:8px}
  .mapCard{padding:8px}
  /* o'ng chetdagi xiralik — xarita yon tomonga davom etishini bildiradi */
  .mapWrap::after{content:"";position:absolute;top:0;right:0;bottom:0;width:26px;border-radius:0 14px 14px 0;background:linear-gradient(90deg,rgba(255,255,255,0),rgba(255,255,255,.92));pointer-events:none;z-index:7}
  .maphint{flex-wrap:nowrap;justify-content:space-between;gap:10px;font-size:11.5px;white-space:nowrap}
  .maphint .hx{display:none}
  .maphint .hm{display:inline}
  /* kattalashtirish tugmalari kataklarni to'smasin: tepada, zallar qatorida */
  .zoomCtl{top:6px;right:6px;bottom:auto;flex-direction:row;border-radius:10px}
  .viewCtl{left:6px;top:6px}
  .viewCtl button{padding:6px 8px;font-size:11.5px}
  .zoomCtl button{width:34px;height:30px;font-size:18px}
  .zoomCtl button+button{border-top:0;border-left:1px solid var(--line)}
  /* oyna pastdan chiqadi; sarlavhadan pastga tortib yopiladi */
  .mh::before{content:"";position:absolute;top:6px;left:50%;width:38px;height:4px;margin-left:-19px;border-radius:999px;background:rgba(255,255,255,.55);z-index:2}
  .mh.lt::before{background:rgba(0,0,0,.25)}
  .row{padding:9px 10px;gap:10px}
  .row .t b{white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
  .row .thumbs img:nth-child(n+2){display:none}
  .row .thumbs img{width:40px;height:40px;border-radius:8px}  #modal{width:100vw;max-height:92vh;left:0;top:auto;bottom:0;border-radius:18px 18px 0 0;transform:translateY(30px)}
  #modal.open{transform:none}
  .mh{padding:18px 12px 14px 14px;gap:12px}
  .mh h2{font-size:16px}
  .mbadge{min-width:52px;height:52px}.mbadge b{font-size:18px}
  .mb{padding:12px}
  /* mahsulot: chapda kichik rasm, o'ngda matn */
  .plist{grid-template-columns:1fr;gap:10px}
  .pcard{display:grid;grid-template-columns:104px minmax(0,1fr);border-radius:14px}
  .pcard .pic,.pcard .pic.none{height:auto;aspect-ratio:1/1;align-self:start;border:0;border-radius:0 0 12px 0;background:#f1f5f9}
  .pcard .pic img{object-fit:cover}
  .pcard .txt{padding:10px 12px;gap:5px}
  .pcard h3{font-size:14.5px}
  .pcard p{font-size:13px}
  .pcard .strip img{width:40px;height:32px}
  .pwrap .side{grid-template-columns:1fr}
  .hero{grid-template-columns:1fr;gap:12px}
  .hpic{position:static}
  .hpic .pic img{max-height:280px}
  .htitle h3{font-size:17px}
  .mf{padding:10px 12px}
  .mf .nav,.mf .btn{padding:9px 12px}
  #lb .lbn{top:auto;bottom:24px;transform:none}
  /* ro'yxat: bitta ustun */
  .listBar{flex-wrap:wrap}
  .tableCard{grid-template-columns:1fr;gap:8px}
  .lpic{width:72px;height:72px}
}
</style>
@endverbatim
</head>
<body>
<main>
  <aside id="side">
    <div class="sideTop">
      <div class="brandRow">
        <div class="logo"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg></div>
        <div class="brand">
          <h1>Innovatsion ko'rgazma 2026</h1>
          <p>Ishtirokchilar joylashuvi xaritasi</p>
        </div>
      </div>
      <div class="tabs">
        <button id="tMap" class="on">Xarita</button>
        <button id="tList">Ro'yxat</button>
      </div>
    </div>
    <label class="search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      <input id="q" type="search" placeholder="Tashkilot yoki mahsulot…" autocomplete="off">
      <div id="sug"></div>
    </label>
    <div class="dirs" id="dirs"></div>
    <div class="chips" id="chips"></div>
    <button class="adminBtn" id="adminBtn" hidden></button>
  </aside>
  <section id="mapView">
    <div class="mapWrap">
      <div class="mapCard" id="mapCard">
        <div class="mapScale" id="mapScale"><div class="map" id="map"></div></div>
      </div>
      <div class="viewCtl" id="viewCtl">
        <button data-v="v1">Asosiy</button><button data-v="v0">1-variant</button>
      </div>
      <div class="zoomCtl">
        <button id="zIn" aria-label="Kattalashtirish" title="Kattalashtirish">+</button>
        <button id="zOut" aria-label="Kichraytirish" title="Kichraytirish">−</button>
        <button id="zFull" aria-label="To'liq ekran" title="To'liq ekran"></button>
      </div>
    </div>
    <div class="maphint">
      <span>👆 Katakchani bosing<span class="hx"> — shu joydagi ishtirokchi haqida ma'lumot chiqadi</span><span class="hm"> · ↔ yon tomonga suring</span></span>
      <span><i style="background:#f1f5f9;box-shadow:inset 0 0 0 1.5px #94a3b8"></i>Bo'sh joy</span>
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
  assets: @json(asset('img')) + '/',
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
const imgSrc = f => f.startsWith('img:') ? EXPO.assets + f.slice(4) : EXPO.storage + f;

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
const orgLines = s => /^\s*1[.)]\s/.test(s) ? String(s).replace(/\s+(?=\d{1,2}[.)]\s*\S)/g, '\n') : s;
const first = s => String(s || '').split('\n')[0].replace(/^\d+\.\s*/, '');
const place = (id, n) => (byStand[id] || []).find(x => +x.place === +n);
// bir tashkilot egallagan joylar: asosiy joy va uning davomlari
const groupOf = (id, n) => [+n, ...(byStand[id] || []).filter(x => +x.cont === +n).map(x => +x.place)];
const rangeOf = (id, n) => { const g = groupOf(id, n); return g.length > 1 ? `${Math.min(...g)}–${Math.max(...g)}` : `${+n}`; };
// yorliq: "A1-9" yoki bir necha joy uchun "A1 · 1–2"
const tagOf = (id, n) => groupOf(id, n).length > 1 ? `${id} · ${rangeOf(id, n)}` : `${id}-${+n}`;
// Excel'dagi yo'nalish nomi — shu bo'limdagi boshqa joylardan
const sectionOf = (id, n) => (byStand[id] || []).find(x => x.section && labOf(id, x.place) === labOf(id, n))?.section || '';
const map = $('#map');

// Ko'rinishlar: v1 — asosiy (zal rejasi), v0 — oddiy rangli xarita ("1-variant")
// Asosiy ko'rinish — v1 (zal rejasi). Kalit nomi yangilangan: avval tanlangan eski ko'rinish saqlanib qolmasin.
let view = 'v1';
try { view = ['v0', 'v1'].includes(localStorage.expoView2) ? localStorage.expoView2 : 'v1'; } catch (e) {}
function el(cls, style, html) {
  const d = document.createElement('div');
  d.className = cls; d.style.cssText = style; if (html != null) d.innerHTML = html;
  map.appendChild(d); return d;
}

// Qatorlar: 1 = ALFA HALL, 3 = B sarlavha, keyin HALF ta B qator, yo'lak, A sarlavha, HALF ta A qator
function buildMap() {
  map.innerHTML = '';
  const rB = 3, rCorr = rB + HALF + 1, rA = rCorr + 1, rEnd = rA + HALF + 1;
  map.className = 'map ' + view + (view === 'v0' ? '' : ' plan');
  $$('#viewCtl button').forEach(b => b.classList.toggle('on', b.dataset.v === view));
  map.style.gridTemplateRows = `36px 10px 24px repeat(${HALF},27px) 58px 24px repeat(${HALF},27px)`;
  el('zone soft hz zpress', 'grid-column:3/9;grid-row:1', '<b>PRESS ZONE</b>');
  el('zone soft hz hall zalfa', 'grid-column:10/29;grid-row:1', '<b>ALFA HALL</b>');
  el('zone soft hall zbeta', `grid-column:1;grid-row:${rCorr - 3}/${rA + 4}`, '<b>BETA<br> HALL</b>');
  el('zone soft vert zsci', `grid-column:3;grid-row:${rB + 1}/${rCorr}`, '<span>ILMIY TEXNIKA</span>');
  el('zone soft vert zmil', `grid-column:3;grid-row:${rA + 1}/${rEnd}`, '<span>XARBIY TEXNIKA</span>');
  el('aisle', `grid-column:19;grid-row:${rB}/${rEnd}`);
  el('corr', `grid-column:5/34;grid-row:${rCorr}`, "<span>YO'LAK</span>");
  // yo'lakdagi to'rtta orolcha (Excel "2026" varag'ida S34:V36, Y34:AB36, AG34:AJ36, AM34:AP36 — nomsiz to'rtburchaklar)
  // ikkitasi homiylar joyi; qolgan ikkitasi Excelda nomsiz
  ISLANDS.forEach((x, i) => {
    const d = el('island' + (x.imgs ? ' link' : ''), `grid-column:${x.col};grid-row:${rCorr}`, x.name || '');
    if (x.imgs) { d.title = `${x.title} — stend ko'rinishi`; d.onclick = () => openIsland(i); }
  });
  el('zone soft zb2b', `grid-column:35;grid-row:${rB + 1}/${rCorr}`, '<b>B2B<br> ZONE</b>');
  el('zone soft zarvr', `grid-column:35;grid-row:${rA + 1}/${rEnd}`, '<b>AR/VR<br> ZONE</b>');
  el('zone soft hall zevent', `grid-column:37;grid-row:${rCorr - 3}/${rA + 4}`, '<b>EVENT<br> ZONE</b>');
  el('zone soft hall zbaby', `grid-column:37;grid-row:${rEnd - 4}/${rEnd}`, '<b>BABY<br> HALL</b>');

  if (view === 'v1') {
    // Eshiklar o'rni devor bo'ylab ulushda (o'tgan yilgi zal rejasidan o'lchab olingan):
    // chap va o'ng devorda 4 tadan chiqish, tepada 2 ta chiqish + hojatxona, pastda o'rtada asosiy kirish
    const exit = `<svg width="20" height="20" viewBox="0 0 24 24"><rect x="1.2" y="1.2" width="21.6" height="21.6" rx="1.5" fill="#fff" stroke="#16a34a" stroke-width="2.2"/>
      <rect x="14" y="4.5" width="5.5" height="15" fill="none" stroke="#16a34a" stroke-width="1.5"/><circle cx="9.3" cy="6.4" r="1.8" fill="#16a34a"/>
      <path d="M4.6 11.6l3-2.3 2.7.6 1.5 2.5 2.2.5M9.8 10l-1.3 4.2 2.7 2.1.3 3.4M8.5 14.2l-1.6 2.3-2.9.5" fill="none" stroke="#16a34a" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
    const wc = `<svg width="26" height="20" viewBox="0 0 26 20" fill="#111827"><circle cx="6.5" cy="2.6" r="2.3"/><path d="M3.2 6h6.6v7.2H8.3V20H4.7v-6.8H3.2z"/>
      <circle cx="19.5" cy="2.6" r="2.3"/><path d="M17.2 6h4.6l2.4 8h-2.7v6h-4v-6h-2.7z"/></svg>`;
    const arrow = `<svg width="38" height="46" viewBox="0 0 22 28" fill="#16a34a"><path d="M11 0l9 11h-6v17H8V11H2z"/></svg>`;
    // tashqariga qaragan kichik strelka (o'ngga; chap devorda ko'zgu qilinadi)
    const out = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h15M13 5l7 7-7 7"/></svg>`;
    const y = p => `calc(var(--wt) + (100% - var(--wt) - var(--wb)) * ${p})`;   // devor bo'ylab tik
    const x = p => `calc(var(--wx) + (100% - 2 * var(--wx)) * ${p})`;           // devor bo'ylab yotiq
    // asosiy kirish tik yo'lakning o'rtasida: chap chet (--wx + 30px) + yo'lakkacha bo'lgan ustunlar (626px) + yo'lak yarmi (28px)
    const mid = 'var(--wx) + 684px';
    const side = (edge, p) => `<div class="door leaf" style="${edge}:calc(var(--wx) - 1px);top:calc(${y(p)} - 9px);width:8px;height:18px"></div>
      <div class="door out" style="${edge}:0;top:calc(${y(p)} - 10px);${edge === 'left' ? 'transform:scaleX(-1)' : ''}">${exit}${out}</div>`;
    const top = p => `<div class="door leaf" style="top:calc(var(--wt) - 1px);left:calc(${x(p)} - 13px);width:26px;height:8px"></div>
      <div class="door" style="top:0;left:calc(${x(p)} - 10px)">${exit}</div><div class="door" style="top:23px;left:calc(${x(p)} - 13px)">${wc}</div>`;
    map.insertAdjacentHTML('beforeend', [.127, .358, .741, .973].map(p => side('left', p)).join('') + [.099, .329, .737, .968].map(p => side('right', p)).join('')
      + [.337, .699].map(top).join('')
      + `<div class="door gate" style="bottom:calc(var(--wb) - 3px);left:calc(${mid} - 64px);width:128px;height:12px"></div>
         <div class="door" style="bottom:6px;left:calc(${mid} - 19px)">${arrow}</div>
         <div class="door inlbl" style="bottom:22px;left:calc(${mid} + 30px)">ASOSIY KIRISH</div>`);
  }
  STANDS.forEach(s => {
    const r0 = s.top ? rB : rA;
    el('sbg', `grid-column:${s.col}/${s.col + 4};grid-row:${r0}/${r0 + HALF + 1};--c:${standLab(s.id).c}`);
    const sl = standLab(s.id);
    const h = el('shdr' + (sl.light ? ' lt' : ''), `grid-column:${s.col}/${s.col + 4};grid-row:${r0};--c:${sl.c}`,
      `<span>${s.id}</span>`);
    h.onclick = () => openStand(s.id);
    // 1..HALF pastdan tepaga, HALF+1..PER tepadan pastga — yo'lak tomonda 1 va PER turadi
    const rowOf = n => r0 + 1 + (n <= HALF ? HALF - n : n - HALF - 1);
    for (let n = 1; n <= PER; n++) {
      const p = place(s.id, n);
      if (p && p.cont && place(s.id, p.cont)) continue;   // asosiy joy bilan birga chiziladi
      // bir tashkilotning ketma-ket joylari bitta katak ("11–14"); ustun almashsa yoki oraliq uzilsa — alohida bo'lak
      const runs = [];
      groupOf(s.id, n).sort((a, b) => a - b).forEach(k => {
        const last = runs[runs.length - 1];
        last && k === last[last.length - 1] + 1 && (k <= HALF) === (last[0] <= HALF) ? last.push(k) : runs.push([k]);
      });
      runs.forEach(run => {
        const a = run[0], b = run[run.length - 1], lab = labOf(s.id, a);
        const top = Math.min(rowOf(a), rowOf(b));
        const left = (a <= HALF) === s.flip;   // katak yorliqning chap tomonida
        const c = el('cell' + (left ? ' cl' : ' cr') + (a < b ? ' wide' : '') + (p && p.org ? '' : ' empty') + (lab.light ? '' : ' dark'),
          `grid-column:${left ? s.col : s.col + 3};grid-row:${top}/${top + run.length};--c:${lab.c}`, a < b ? `${a}–${b}` : a);
        c.dataset.stand = s.id; c.dataset.place = n;
        c.onclick = () => openPlace(s.id, n);
      });
    }
    // yorliq: bo'lim o'z joylari ustuni yonida turadi
    const parts = SECTIONS.filter(x => x.s === s.id);
    parts.forEach(l => {
      const low = !l.a || l.a <= HALF;
      const col = parts.length === 1 ? `${s.col + 1}/${s.col + 3}` : low === s.flip ? s.col + 1 : s.col + 2;
      const d = el('slabel' + (l.light ? ' lighttxt' : '') + (parts.length > 1 ? ' narrow' + (low === s.flip ? ' n1' : ' n2') : ''),
        `grid-column:${col};grid-row:${r0 + 1}/${r0 + 1 + HALF};--c:${l.c}`, `<span>${esc(l.t)}</span>`);
      d.dataset.stand = s.id;
      d.onclick = () => openStand(s.id);
    });
  });
}
// Xaritani ekranga moslash
let zoom = 1;
function fitMap() {
  const card = $('#mapCard'), sc = $('#mapScale');
  const pad = innerWidth < 700 ? 16 : 24;
  card.style.overflow = 'hidden';   // o'lchashda scroll chizig'i joy egallamasin
  const w = map.offsetWidth, h = map.offsetHeight, cw = card.clientWidth - pad, ch = card.clientHeight - pad;
  if (cw <= 0 || ch <= 0) return;
  // kompyuterda butun xarita sig'adi; telefonda balandlikka moslanadi va yon tomonga suriladi
  const base = innerWidth < 700 ? Math.max(ch / h, .55) : Math.min(cw / w, ch / h, 1.6);
  const k = base * zoom, wide = w * k > cw + 1, tall = h * k > ch + 1;
  card.style.overflow = wide || tall ? 'auto' : 'hidden';
  card.style.justifyContent = wide ? 'flex-start' : 'center';
  card.style.alignItems = tall ? 'flex-start' : 'center';
  map.style.transform = `scale(${k})`;
  sc.style.width = w * k + 'px'; sc.style.height = h * k + 'px';
  $('#zOut').disabled = zoom <= 1; $('#zIn').disabled = zoom >= 3;
}
function setZoom(z) {
  const card = $('#mapCard');
  // ko'rinib turgan markaz joyida qolsin
  const cx = (card.scrollLeft + card.clientWidth / 2) / card.scrollWidth, cy = (card.scrollTop + card.clientHeight / 2) / card.scrollHeight;
  zoom = Math.min(3, Math.max(1, z)); fitMap();
  card.scrollLeft = cx * card.scrollWidth - card.clientWidth / 2; card.scrollTop = cy * card.scrollHeight - card.clientHeight / 2;
}
$('#viewCtl').onclick = e => {
  const b = e.target.closest('button'); if (!b) return;
  view = b.dataset.v; try { localStorage.expoView2 = view; } catch (err) {}
  zoom = 1; buildMap(); highlight(); fitMap();
};
$('#zIn').onclick = () => setZoom(zoom * 1.4);
$('#zOut').onclick = () => setZoom(zoom / 1.4);
// To'liq ekran: brauzer ruxsat bersa haqiqiy to'liq ekran, bo'lmasa (masalan iPhone) sahifa ichida kengaytiriladi
const fullIcon = on => `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="${
  on ? 'M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5' : 'M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5'}"/></svg>`;
function setFull(on) {
  document.body.classList.toggle('mapfull', on);
  $('#zFull').innerHTML = fullIcon(on);
  $('#zFull').title = on ? "To'liq ekrandan chiqish" : "To'liq ekran";
  zoom = 1; requestAnimationFrame(fitMap);
}
$('#zFull').onclick = () => {
  const on = !document.body.classList.contains('mapfull');
  const root = document.documentElement;
  if (on && root.requestFullscreen) root.requestFullscreen().catch(() => {});
  if (!on && document.fullscreenElement) document.exitFullscreen().catch(() => {});
  setFull(on);
};
// Esc yoki brauzer tugmasi bilan chiqilganda holat mos qolsin
document.addEventListener('fullscreenchange', () => { if (!document.fullscreenElement && document.body.classList.contains('mapfull')) setFull(false); });
$('#zFull').innerHTML = fullIcon(false);
// Katak ekrandan tashqarida bo'lsa (telefon yoki kattalashtirilgan xarita) — o'rtaga suriladi
function revealCell(id, n) { revealEl(document.querySelector(`.cell[data-stand="${id}"][data-place="${n}"]`)); }
function revealEl(c) {
  const card = $('#mapCard');
  if (!c || card.style.overflow !== 'auto') return;
  const a = c.getBoundingClientRect(), b = card.getBoundingClientRect();
  card.scrollTo({left: card.scrollLeft + a.left - b.left - b.width / 2, top: card.scrollTop + a.top - b.top - b.height / 2, behavior: 'smooth'});
}
// Tooltip
const tip = $('#tip');
map.addEventListener('mousemove', e => {
  const c = e.target.closest('.cell');
  if (!c) { tip.style.opacity = 0; return; }
  const p = place(c.dataset.stand, c.dataset.place);
  const main = p && p.cont ? p.cont : c.dataset.place;
  tip.innerHTML = `<b>${c.dataset.stand} · ${rangeOf(c.dataset.stand, main)}-joy</b>${p && p.org ? esc(first(p.org)) : "Ma'lumot kiritilmagan"}`;
  tip.style.left = Math.min(e.clientX + 14, innerWidth - 300) + 'px';
  tip.style.top = e.clientY + 16 + 'px';
  tip.style.opacity = 1;
});
map.addEventListener('mouseleave', () => tip.style.opacity = 0);

/* ---------- legenda ---------- */
let activeDir = null;
function buildDirs() {
  // har bir yo'nalishdagi ishtirokchilar soni
  const cnt = d => DATA.filter(p => p.org && !p.cont && d.s.includes(p.stand)).length;
  $('#dirs').innerHTML = '<div class="dirs-h">Yo\'nalishlar</div><button class="dir all" data-all>Barchasi</button>'
    + DIRS.map((d, i) => `<button class="dir" data-i="${i}" style="--c:${d.c}"><i></i>${esc(d.n)}<span class="cnt">${cnt(d)}</span></button>`).join('');
  highlight();
  $('#dirs').onclick = e => {
    const b = e.target.closest('.dir'); if (!b) return;
    activeDir = b.dataset.all != null || activeDir === +b.dataset.i ? null : +b.dataset.i;
    $('#q').value = ''; hits = []; highlight(); renderList();
    // telefonda tanlangan yo'nalishning birinchi stendi ko'rinadigan joyga suriladi
    if (activeDir != null) revealEl(document.querySelector(`.slabel[data-stand="${DIRS[activeDir].s[0]}"]`));
  };
}
function highlight(keys) {
  $$('.dir').forEach(b => b.classList.toggle('on', b.dataset.all != null ? activeDir == null && !keys : +b.dataset.i === activeDir));
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
/* Yo'lakdagi orolchalar (hamkorlar joyi): col — grid ustunlari; imgs — public/img/ ichidagi stend rasmlari.
   Ikkitasi Excelda nomsiz. Rasmi bor orolcha bosilganda rasmlar oynasi ochiladi. */
const ISLANDS = [
  {col: '6/11'},
  {col: '13/18', name: 'TMK', title: "TMK — O'zbekiston texnologik metallar kombinati", c: '#1397b8',
   imgs: [1, 2, 3, 4, 5, 6].map(n => `img:partners/tmk/${n}.webp`)},
  {col: '21/26', name: 'ALOQABANK', title: 'AloqaBank', c: '#0b6bbf',
   imgs: ['img:partners/aloqabank/1.jpg', 'img:partners/aloqabank/2.jpg', 'img:partners/aloqabank/3.jpg']},
  {col: '28/33'},
];
function openIsland(i) {
  const x = ISLANDS[i];
  nav = null;
  gallery = x.imgs.map(f => ({f, cap: `${x.title} — stend ko'rinishi`}));
  $('#modal').innerHTML = header({c: x.c, t: x.title}, 'Hamkor · markaziy yo\'lak', esc(x.title), `<b>★</b><span>hamkor</span>`) +
    `<div class="mb"><div class="igal">
      <div class="pic"><img src="${imgSrc(x.imgs[0])}" data-gi="0" alt="${esc(x.title)}"><span class="zoom">${zoomIcon}</span></div>
      ${x.imgs.length > 1 ? `<div class="strip">${x.imgs.map((f, k) => `<img src="${imgSrc(f)}" data-gi="${k}" alt="">`).join('')}</div>` : ''}
    </div></div>`;
  $$('.cell.sel').forEach(c => c.classList.remove('sel'));
  openModal();
}
function header(lab, kick, title, badge) {
  return `<div class="mh${lab && lab.light ? ' lt' : ''}" style="--c:${lab ? lab.c : '#0b3d91'}">
    ${badge ? `<div class="mbadge">${badge}</div>` : ''}
    <div class="mt"><div class="kick">${kick}</div><h2>${title}</h2></div>
    <button class="x" data-close aria-label="Yopish" title="Yopish (Esc)">${xIcon}</button></div>`;
}
const zoomIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>';
// Nom katagiga tavsif ham yozilgan bo'lsa ("“Nom” tavsif", "Nom — tavsif", "Nom. Tavsif", "Nom  tavsif", "Nom\nTavsif"):
// nom — sarlavha, qolgani — tavsif. Nomini ajratib bo'lmaydigan uzun matn sarlavhasiz, tavsif bo'lib chiqadi.
// Qoida ExcelExpoReader::titleOf() bilan bir xil.
function splitName(s) {
  const name = String(s || '').replace(/^\d+\s*[.,)]\s*/, '').trim();
  const m = name.match(/^([“"«][^”"»\n]{2,128}[”"»])[.:]?\s+(\S[\s\S]{19,})$/)
    || name.match(/^(.{3,130}?)(?:\s+[-–—]\s+|\.\s+|\s{2,}|\s*\n\s*)(\S[\s\S]{19,})$/);
  if (m) return [m[1].trim(), m[2].trim()];
  return name.length > 120 ? ['', name] : [name, ''];
}
// qisqa nom (rasm izohi, qidiruv, ro'yxat uchun): sarlavha, u bo'lmasa tavsif boshi
function shortName(s) {
  const [t, d] = splitName(s);
  return t || (d.length > 90 ? d.slice(0, 90).replace(/\s+\S*$/, '') + '…' : d);
}
// joy haqida bir qator: "Ishlanma haqida" boshi, u bo'sh bo'lsa mahsulot nomlari
const aboutOf = p => first(p.info) || p.products.map(x => shortName(x.name)).filter(Boolean).join(', ');
function openPlace(id, n) {
  const lab = labOf(id, n);
  const p = place(id, n);
  const main = p && p.cont ? place(id, p.cont) : null;
  const d = main || p;
  nav = {id, n: +n};
  // bir tashkilot egallagan barcha joylar (asosiy joy va uning davomlari)
  const mainNo = main ? +main.place : +n;
  const group = groupOf(id, mainNo);
  const range = rangeOf(id, mainNo) + (group.length > 1 ? '-joylar' : '-joy');
  let body;
  gallery = [];
  if (!d || !d.org) {
    body = `<div class="empty-state"><div class="big">🗂️</div>Bu joy uchun hozircha ma'lumot kiritilmagan.</div>`;
  } else {
    const prods = d.products;
    prods.forEach(x => x.img.forEach(f => gallery.push({f, cap: shortName(x.name)})));
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
    let solo = null;   // yagona ishlanma qismlari (hero ko'rinishi uchun)
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
      const text = `${extra ? (extra === about ? `<p>${esc(extra)}</p>` : `<p class="by">${icon.org.replace(/18/g, '15')}${esc(extra)}</p>`) : ''}${more ? `<p>${esc(more)}</p>` : ''}`;
      // sarlavha faqat haqiqiy nom bo'lsa chiqadi — tashkilot nomi tepada, mahsulot raqami kartochkada bor
      const h3 = name || about ? `<h3>${esc(title)}</h3>` : '';
      if (prods.length === 1) solo = {pic: x.img.length ? pic : '', strip, head: h3 || text ? `<div class="htitle">${h3}${text}</div>` : ''};
      return `<article class="pcard">${pic}<div class="txt">
        ${prods.length > 1 ? `<div class="no"><span>${no}</span>mahsulot</div>` : ''}
        ${h3 || (text ? '' : `<h3>${esc(title)}</h3>`)}${text}${strip}</div></article>`;
    }).join('');
    // mahsulotlarga bog'lanmagan qolgan tavsif qatorlari
    // (mahsulot nomida allaqachon yozilgan qator takrorlanmaydi)
    const rest = used.has('all') ? [] : numbered ? lines.filter(s => !used.has((s.match(/^(\d+)/) || [])[1]))
      : lines.filter(s => !s.split(/[,;]/).map(norm).filter(Boolean).every(part => prods.some(x => norm(x.name).includes(part))));
    const restBox = rest.length ? `<div class="box"><h4>Ishlanma haqida<i></i></h4><ul class="desc${numbered ? '' : ' plain'}">${rest.map(s => {
      const m = s.match(/^(\d+)[.)]\s*(.*)$/);
      return numbered && m ? `<li><span>${m[1]}</span>${esc(m[2])}</li>` : `<li>${esc(s)}</li>`;
    }).join('')}</ul></div>` : '';
    body = solo
      ? `<div class="hero${solo.pic ? '' : ' nopic'}">${solo.pic ? `<div class="hpic">${solo.pic}${solo.strip}</div>` : ''}
         <div class="hside">${solo.head}${restBox}${ppl}</div></div>`
      : prods.length
      ? `<div class="pwrap"><div class="plist-h">Mahsulotlar va ishlanmalar <b>${prods.length}</b><i></i></div><div class="plist">${cards}</div>
         ${restBox || ppl ? `<div class="side">${restBox}${ppl}</div>` : ''}</div>`
      : `<div class="side">${desc}${ppl}</div>`;
  }
  const prev = +n > 1, next = +n < PER;
  const key = `${id}|${d && d.org ? d.place : n}`;
  const adminBtns = !isAdmin ? '' : d && d.org
    ? `<div class="grp"><button class="btn" data-edit="${key}">✎ Tahrirlash</button><button class="btn danger" data-del="${key}">O'chirish</button></div>`
    : `<button class="btn primary" data-edit="${key}">＋ Ma'lumot qo'shish</button>`;
  $('#modal').innerHTML = header(lab, esc(lab.t),
      d && d.org ? esc(orgLines(d.org)) : `${id} stend, ${n}-joy`, `<b>${id}</b><span>${range}</span>`) +
    `<div class="mb">${body}</div>
    <div class="mf">
      <button class="nav" data-go="-1" ${prev ? '' : 'disabled'}>‹ ${id}-${+n - 1 || ''}</button>
      <div class="grp">${adminBtns}<button class="nav" data-stand="${id}">Butun ${id} stend</button></div>
      <button class="nav" data-go="1" ${next ? '' : 'disabled'}>${id}-${next ? +n + 1 : ''} ›</button>
    </div>`;
  $$('.cell.sel').forEach(x => x.classList.remove('sel'));
  $$(`.cell[data-stand="${id}"][data-place="${mainNo}"]`).forEach(c => c.classList.add('sel'));
  openModal(`${id}-${n}`);
  revealCell(id, mainNo);
}

function openStand(id) {
  const lab = standLab(id);
  const list = byStand[id] || [];
  const filled = list.filter(p => p.org).length;
  nav = null;
  $('#modal').innerHTML = header(lab, `${filled} / ${list.length} joy band`, esc(lab.t), `<b>${id}</b><span>stend</span>`) +
    `<div class="mb"><div class="rows">${list.filter(p => !p.cont || !place(id, p.cont)).map(p => {
      const d = p.cont ? place(id, p.cont) : p;
      const imgs = d && !p.cont ? d.products.flatMap(x => x.img).slice(0, 3) : [];
      return `<button class="row${p.org ? '' : ' off'}" data-open="${p.place}">
        <span class="n${lab.light ? ' lt' : ''}" style="--c:${lab.c}">${rangeOf(id, p.place)}</span>
        <span class="t"><b>${p.org ? esc(first(orgLines(p.org))) : "Bo'sh joy"}</b><small>${p.cont ? `${id}-${p.cont} bilan birga` : p.org ? esc(aboutOf(p).slice(0, 90)) : ''}</small></span>
        <span class="thumbs">${imgs.map(f => `<img loading="lazy" src="${imgSrc(f)}" alt="">`).join('')}</span>
      </button>`;
    }).join('')}</div></div>`;
  $$('.cell.sel').forEach(x => x.classList.remove('sel'));
  $$(`.cell[data-stand="${id}"]`).forEach(c => c.classList.add('sel'));
  openModal(id);
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

// hash — ochilgan joy yoki stend havolasi (ulashish uchun); boshqa oynalarda bo'sh
function setHash(hash) { history.replaceState(null, '', hash ? '#' + hash : location.pathname + location.search); }
function openModal(hash = '') {
  $('#modal').classList.add('open'); $('#overlay').classList.add('open'); $('#modal .mb').scrollTop = 0; tip.style.opacity = 0;
  $('#sug').classList.remove('open');
  setHash(hash);
}
function closeModal() {
  $('#modal').classList.remove('open'); $('#overlay').classList.remove('open');
  $$('.cell.sel').forEach(x => x.classList.remove('sel'));
  setHash('');
}
function openFromHash() {
  const m = decodeURIComponent(location.hash).match(/^#([A-Z]\d+)(?:-(\d+))?$/i);
  if (!m || !EXPO.stands.includes(m[1].toUpperCase())) return;
  if (!m[2]) openStand(m[1].toUpperCase());
  else if (+m[2] >= 1 && +m[2] <= PER) openPlace(m[1].toUpperCase(), +m[2]);
}
addEventListener('hashchange', openFromHash);

let dragY = null;
$('#modal').addEventListener('touchstart', e => { dragY = e.target.closest('.mh') ? e.touches[0].clientY : null; }, {passive: true});
$('#modal').addEventListener('touchend', e => { if (dragY != null && e.changedTouches[0].clientY - dragY > 60) closeModal(); dragY = null; }, {passive: true});
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
function renderSug(q) {
  const s = $('#sug');
  if (!q) return s.classList.remove('open');
  s.innerHTML = !hits.length ? '<div class="none">Hech narsa topilmadi</div>' : hits.slice(0, 6).map(p => {
    const lab = labOf(p.stand, p.place);
    // qaysi mahsulot mos kelgan bo'lsa, o'sha ko'rsatiladi
    const prod = p.products.find(x => x.name.toLowerCase().includes(q));
    return `<button type="button" data-s="${p.stand}" data-p="${p.place}">
      <span class="tag${lab.light ? ' lt' : ''}" style="--c:${lab.c}">${tagOf(p.stand, p.place)}</span>
      <span class="t"><b>${esc(first(p.org))}</b><small>${esc(prod ? shortName(prod.name) : lab.t)}</small></span></button>`;
  }).join('') + (hits.length > 6 ? `<button type="button" class="more" data-more>Barcha ${hits.length} ta natijani ko'rish</button>` : '');
  s.classList.add('open');
}
$('#q').addEventListener('input', () => {
  activeDir = null;
  const q = $('#q').value.trim().toLowerCase();
  hits = q ? DATA.filter(p => p.org && !p.cont && match(p, q)) : [];
  highlight(q ? new Set(hits.flatMap(p => groupOf(p.stand, p.place).map(n => p.stand + '-' + n))) : null);
  renderList(q); renderSug(q);
  if (hits.length) revealCell(hits[0].stand, hits[0].place);
});
$('#q').addEventListener('keydown', e => {
  if (e.key === 'Enter') { $('#q').blur(); openResults(); }
  if (e.key === 'Escape') $('#sug').classList.remove('open');
});
$('#q').addEventListener('focus', () => { if ($('#q').value.trim()) $('#sug').classList.add('open'); });
$('#q').addEventListener('blur', () => setTimeout(() => $('#sug').classList.remove('open'), 200));
$('#sug').addEventListener('click', e => {
  e.preventDefault();   // <label> ichida — bosish qidiruv maydoniga qaytib ketmasin
  const b = e.target.closest('button'); if (!b) return;
  $('#q').blur();
  b.dataset.more != null ? openResults() : openPlace(b.dataset.s, b.dataset.p);
});

/* ---------- ro'yxat ---------- */
// Har bir ishtirokchi — bitta kartochka (egallagan joylari oraliq bilan), stend bo'limlari bo'yicha guruhlangan.
// Tanlangan yo'nalish ro'yxatga ham ta'sir qiladi; bo'sh joylar soni guruh sarlavhasida.
function renderList(q = '') {
  const dir = activeDir != null ? new Set(DIRS[activeDir].s) : null;
  const all = DATA.filter(p => !p.cont && (!dir || dir.has(p.stand)) && (!q || match(p, q)));
  const rows = all.filter(p => p.org);
  $('#listCnt').textContent = q || dir ? `${rows.length} ta yozuv topildi`
    : `${rows.length} ta yozuv · ${DATA.filter(p => p.org).length} ta joy band`;
  let last = null;
  $('#tableCard').innerHTML = !rows.length ? '<div class="empty-state"><div class="big">🔍</div>Hech narsa topilmadi.</div>' : rows.map(p => {
    const lab = labOf(p.stand, p.place);
    let head = '';
    if (lab !== last) {
      last = lab;
      const inSec = x => x.stand === p.stand && labOf(x.stand, x.place) === lab;
      const free = q ? 0 : DATA.filter(x => inSec(x) && !x.org).length;
      head = `<div class="lgroup" style="--c:${lab.c}"><b class="${lab.light ? 'lt' : ''}">${p.stand}</b>${esc(lab.t)}<i></i>
        <small>${rows.filter(inSec).length} ta yozuv${free ? ` · ${free} ta bo'sh joy` : ''}</small></div>`;
    }
    const about = aboutOf(p);
    const img = p.products.flatMap(x => x.img)[0];
    const who = first(p.contact).replace(phoneRe, '').trim();
    return head + `<button class="lcard" data-s="${p.stand}" data-p="${p.place}" style="--c:${lab.c}">
      <span class="lpic">${img ? `<img loading="lazy" src="${imgSrc(img)}" alt="">` : p.stand}</span>
      <span class="lbody">
        <span><span class="tag${lab.light ? ' lt' : ''}" style="--c:${lab.c}">${tagOf(p.stand, p.place)}</span></span>
        <b>${esc(first(orgLines(p.org)))}</b>
        ${about ? `<span class="labout">${esc(about.slice(0, 180))}</span>` : ''}
        <span class="lmeta">${p.products.length ? `<span class="pr">${p.products.length} ta ishlanma</span>` : ''}${who ? `<span>${esc(who)}</span>` : ''}</span>
      </span></button>`;
  }).join('');
}
$('#tableCard').addEventListener('click', e => { const c = e.target.closest('[data-s]'); if (c) openPlace(c.dataset.s, c.dataset.p); });
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
  b.hidden = !isAdmin;
  b.innerHTML = '● Admin · Chiqish';
  $('#addBtn').hidden = !isAdmin;
}
$('#adminBtn').onclick = () => {
  const f = document.createElement('form');
  f.method = 'POST'; f.action = EXPO.routes.logout;
  f.innerHTML = `<input type="hidden" name="_token" value="${csrf}">`;
  document.body.appendChild(f); f.submit();
};
$('#addBtn').onclick = () => openForm(null, null);

async function reload() {
  setData(await api('GET', EXPO.routes.data));
  buildMap(); buildDirs(); renderList($('#q').value.trim().toLowerCase()); renderChips(); fitMap();
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
if (innerWidth < 700) $('#mapCard').scrollLeft = $('#mapScale').offsetWidth * 96 / map.offsetWidth;
openFromHash();
</script>
@endverbatim
</body>
</html>
