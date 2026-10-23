/* Ro'yxatni namunadagi (namuna.docx) jadval ko'rinishida yuklab olish: Word (docx) yoki PDF, har bir mahsulotga QR kod.
   Sahifadagi DATA, DIRS, activeDir, match, labOf, tagOf, splitName, orgLines, first, imgSrc, toast, loadScript
   dan foydalanadi. Bu fayl va kutubxonalar faqat "Yuklab olish" bosilganda yuklanadi. */
(() => {
const CDN = {
  docx: 'https://cdn.jsdelivr.net/npm/docx@9.14.0/+esm',
  jspdf: 'https://cdn.jsdelivr.net/npm/jspdf@3.0.4/dist/jspdf.umd.min.js',
  autotable: 'https://cdn.jsdelivr.net/npm/jspdf-autotable@5.0.9/dist/jspdf.plugin.autotable.min.js',
  qr: 'https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js',
  // PDF uchun kirill va o‘ ʻ belgilari bor shrift
  font: 'https://cdn.jsdelivr.net/npm/dejavu-fonts-ttf@2.37.3/ttf/DejaVuSans.ttf',
  fontBold: 'https://cdn.jsdelivr.net/npm/dejavu-fonts-ttf@2.37.3/ttf/DejaVuSans-Bold.ttf',
};
// namunadagi sarlavha va ustunlar ("Joyi" va "QR kod" qo'shilgan)
const TITLE = 'InnoWeek–2026 haftaligi doirasida ko‘rgazmada ishtirok etadigan loyihalar va mahsulotlar to‘g‘risida';
const TITLE2 = 'MA’LUMOT';
const HEAD = ['№', 'Mahsulot nomi', 'Qisqacha ma’lumot\n(innovatsionligi)', 'Tashkilot nomi\n(OTM/ITI/MCHJ)', 'Yo‘nalishi', 'Joyi', 'Rasm', 'QR kod'];
const IMG_SIDE = 400;   // hujjatdagi rasm shu o'lchamgacha kichraytiriladi (px) — fayl yengil bo'lsin

// "HUDUDIY INNOVATSIYALAR" -> "Hududiy innovatsiyalar" (namunadagidek); "7TECH" o'zgarmaydi
const dirName = t => t !== t.toUpperCase() ? t : (t[0] + t.slice(1).toLowerCase()).replace(/\b\d+tech\b/gi, s => s.toUpperCase());

// "Ishlanma haqida" dagi raqamli qatorlar: {1: '…', 2: '…'}
function numbered(info) {
  const out = {};
  String(info || '').split('\n').forEach(s => { const m = s.trim().match(/^(\d+)[.)]\s*(.+)$/); if (m) out[m[1]] = m[2]; });
  return out;
}

// Ro'yxatda ko'rinib turganlar (tanlangan yo'nalish / qidiruv): har bir mahsulot — bitta qator
function collectRows() {
  const q = $('#q').value.trim().toLowerCase();
  const dir = activeDir != null ? new Set(DIRS[activeDir].s) : null;
  const rows = [];
  DATA.filter(p => p.org && !p.cont && (!dir || dir.has(p.stand)) && (!q || match(p, q))).forEach(p => {
    const base = {org: orgLines(p.org).trim(), dir: dirName(labOf(p.stand, p.place).t), place: tagOf(p.stand, p.place),
      url: `${location.origin}/m/${p.stand}-${p.place}`};
    const byNo = numbered(p.info);
    if (!p.products.length) {
      const lines = String(p.info).split('\n');
      rows.push({...base, name: first(p.info) || '—', about: lines.slice(1).join(' ').trim(), img: null});
      return;
    }
    const solo = p.products.length === 1;
    p.products.forEach((x, k) => {
      let [name, about] = splitName(x.name);
      // nomini ajratib bo'lmaydigan uzun matn: boshi — nom, to'liq matn — ma'lumot
      if (!name && about) name = about.slice(0, 60).replace(/\s+\S*$/, '') + '…';
      if (!name) name = byNo[k + 1] || (solo ? first(p.info) : '') || `${k + 1}-mahsulot`;
      if (!about) about = byNo[k + 1] && byNo[k + 1] !== name ? byNo[k + 1] : solo && p.info && p.info !== name ? p.info : '';
      rows.push({...base, name, about, img: x.img[0] || null, url: `${base.url}/${k + 1}`});
    });
  });
  return rows;
}

const loadImage = src => new Promise((ok, fail) => {
  const im = new Image();
  im.onload = () => ok(im); im.onerror = () => fail(new Error(src)); im.src = src;
});
// rasm kichraytiriladi va JPEG qilinadi (Word va PDF ikkalasi uchun)
async function shrink(src) {
  const im = await loadImage(src);
  const k = Math.min(1, IMG_SIDE / Math.max(im.naturalWidth, im.naturalHeight));
  const c = document.createElement('canvas');
  c.width = Math.max(1, Math.round(im.naturalWidth * k)); c.height = Math.max(1, Math.round(im.naturalHeight * k));
  const g = c.getContext('2d');
  g.fillStyle = '#fff'; g.fillRect(0, 0, c.width, c.height); g.drawImage(im, 0, 0, c.width, c.height);
  return {url: c.toDataURL('image/jpeg', .82), w: c.width, h: c.height};
}
async function loadImages(files) {
  const list = [...new Set(files)], total = list.length, out = new Map();
  let done = 0;
  const work = async () => {
    while (list.length) {
      const f = list.shift();
      out.set(f, await shrink(imgSrc(f)).catch(() => null));   // ochilmagan rasm — katak bo'sh qoladi
      toast(`Rasmlar tayyorlanmoqda: ${++done} / ${total}`, false, 60000);
    }
  };
  await Promise.all(Array.from({length: 6}, work));
  return out;
}

// QR kod — oq fonli PNG (atrofida 2 modul bo'sh joy)
function qrImage(text) {
  const q = qrcode(0, 'M');
  q.addData(text); q.make();
  const n = q.getModuleCount(), cell = 8, pad = cell * 2, c = document.createElement('canvas');
  c.width = c.height = n * cell + pad * 2;
  const g = c.getContext('2d');
  g.fillStyle = '#fff'; g.fillRect(0, 0, c.width, c.height); g.fillStyle = '#000';
  for (let r = 0; r < n; r++) for (let col = 0; col < n; col++) if (q.isDark(r, col)) g.fillRect(pad + col * cell, pad + r * cell, cell, cell);
  return {url: c.toDataURL('image/png'), w: c.width, h: c.height};
}

const bytes = dataUrl => Uint8Array.from(atob(dataUrl.slice(dataUrl.indexOf(',') + 1)), ch => ch.charCodeAt(0));
const fit = (im, w, h) => { const k = Math.min(w / im.w, h / im.h); return [im.w * k, im.h * k]; };

async function makeDocx(rows, imgs, qrs) {
  const {Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell, WidthType, AlignmentType, VerticalAlign,
    ImageRun, PageOrientation, ShadingType, HeightRule} = await import(CDN.docx);
  const W = [520, 2050, 3650, 2350, 1700, 1250, 1800, 1250];   // ustunlar (DXA), jami 14570 = albom A4 eni - chetlar
  const font = 'Times New Roman';
  const text = (s, o = {}) => String(s).split('\n').map(line => new Paragraph({alignment: AlignmentType.CENTER, spacing: {after: 0},
    children: [new TextRun({text: line, font, size: o.size || 22, bold: !!o.bold})]}));
  const pic = (im, type, w, h) => {
    const [pw, ph] = fit(im, w, h);
    return [new Paragraph({alignment: AlignmentType.CENTER, spacing: {after: 0},
      children: [new ImageRun({type, data: bytes(im.url), transformation: {width: Math.round(pw), height: Math.round(ph)}})]})];
  };
  const cell = (children, i, fill) => new TableCell({width: {size: W[i], type: WidthType.DXA}, verticalAlign: VerticalAlign.CENTER,
    margins: {top: 60, bottom: 60, left: 80, right: 80}, ...(fill ? {shading: {type: ShadingType.CLEAR, color: 'auto', fill}} : {}), children});
  // namunadagidek: sarlavha qatori och ko'k, har sahifada takrorlanadi
  const head = new TableRow({tableHeader: true, height: {value: 520, rule: HeightRule.ATLEAST},
    children: HEAD.map((h, i) => cell(text(h, {size: 24, bold: true}), i, '8EAADB'))});
  const body = rows.map((r, i) => new TableRow({cantSplit: true, height: {value: 948, rule: HeightRule.ATLEAST}, children: [
    cell(text(`${i + 1}.`, {bold: true}), 0),
    cell(text(r.name), 1),
    cell(text(r.about, {size: 18}), 2),
    cell(text(r.org), 3),
    cell(text(r.dir), 4),
    cell(text(r.place), 5),
    cell(r.img && imgs.get(r.img) ? pic(imgs.get(r.img), 'jpg', 110, 90) : text(''), 6),
    cell(pic(qrs[i], 'png', 74, 74), 7),
  ]}));
  const doc = new Document({
    creator: 'InnoWeek 2026', title: `${TITLE} ${TITLE2}`,
    styles: {default: {document: {run: {font, size: 22}}}},
    sections: [{
      properties: {page: {size: {width: 11906, height: 16838, orientation: PageOrientation.LANDSCAPE},
        margin: {top: 1134, right: 1134, bottom: 850, left: 1134}}},
      children: [
        new Paragraph({alignment: AlignmentType.CENTER, spacing: {after: 240}, children: [
          new TextRun({text: TITLE, font, size: 32, bold: true}), new TextRun({text: TITLE2, font, size: 32, bold: true, break: 1})]}),
        new Table({width: {size: 14570, type: WidthType.DXA}, columnWidths: W, rows: [head, ...body]}),
      ],
    }],
  });
  return Packer.toBlob(doc);
}

async function fontData(url) {
  const r = await fetch(url);
  if (!r.ok) throw new Error("Shriftni yuklab bo'lmadi");
  const buf = new Uint8Array(await r.arrayBuffer());
  let s = '';
  for (let i = 0; i < buf.length; i += 0x8000) s += String.fromCharCode.apply(null, buf.subarray(i, i + 0x8000));
  return btoa(s);
}

async function makePdf(rows, imgs, qrs) {
  await loadScript(CDN.jspdf);
  await loadScript(CDN.autotable);
  const doc = new window.jspdf.jsPDF({orientation: 'landscape', unit: 'mm', format: 'a4', compress: true});
  const [regular, bold] = await Promise.all([fontData(CDN.font), fontData(CDN.fontBold)]);
  doc.addFileToVFS('DejaVuSans.ttf', regular); doc.addFont('DejaVuSans.ttf', 'DejaVu', 'normal');
  doc.addFileToVFS('DejaVuSans-Bold.ttf', bold); doc.addFont('DejaVuSans-Bold.ttf', 'DejaVu', 'bold');
  const pageW = doc.internal.pageSize.getWidth(), M = 20;
  doc.setFont('DejaVu', 'bold'); doc.setFontSize(13);
  const title = [...doc.splitTextToSize(TITLE, pageW - 2 * M), TITLE2];
  doc.text(title, pageW / 2, 16, {align: 'center'});
  const W = [9, 36, 70, 40, 28, 16, 32, 26];   // mm, jami 257 = albom A4 eni - chetlar
  const autoTable = typeof doc.autoTable === 'function' ? o => doc.autoTable(o) : o => window.autoTable(doc, o);
  autoTable({
    startY: 16 + title.length * 6,
    margin: {left: M, right: M, top: 12, bottom: 12},
    theme: 'grid',
    head: [HEAD],
    body: rows.map((r, i) => [`${i + 1}.`, r.name, r.about, r.org, r.dir, r.place, '', '']),
    styles: {font: 'DejaVu', fontSize: 8.5, halign: 'center', valign: 'middle', textColor: 0, lineColor: 0, lineWidth: .2, cellPadding: 1.5},
    headStyles: {fillColor: [142, 170, 219], fontStyle: 'bold', fontSize: 9.5, minCellHeight: 10},
    bodyStyles: {minCellHeight: 27},
    columnStyles: Object.fromEntries(W.map((w, i) => [i, {cellWidth: w, ...(i === 0 ? {fontStyle: 'bold'} : i === 2 ? {fontSize: 7} : {})}])),
    rowPageBreak: 'avoid',
    // rasm va QR kod katak o'rtasiga chiziladi
    didDrawCell: d => {
      if (d.section !== 'body' || d.column.index < 6) return;
      const c = d.cell, r = rows[d.row.index], isQr = d.column.index === 7;
      const im = isQr ? qrs[d.row.index] : r.img && imgs.get(r.img);
      if (!im) return;
      const [w, h] = isQr ? Array(2).fill(Math.min(22, c.height - 3)) : fit(im, c.width - 3, Math.min(c.height - 3, 25));
      doc.addImage(im.url, isQr ? 'PNG' : 'JPEG', c.x + (c.width - w) / 2, c.y + (c.height - h) / 2, w, h, isQr ? undefined : r.img);
    },
  });
  return doc.output('blob');
}

function fileName(ext) {
  const q = $('#q').value.trim();
  const part = activeDir != null ? DIRS[activeDir].n : q ? `qidiruv ${q}` : '';
  const slug = part.toLowerCase().replace(/[‘’ʻʼ'`]/g, '').replace(/[^a-z0-9а-яёўқғҳ]+/gi, '-').replace(/^-+|-+$/g, '');
  return `InnoWeek-2026-mahsulotlar${slug ? '-' + slug : ''}.${ext}`;
}

function save(blob, name) {
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob); a.download = name;
  document.body.appendChild(a); a.click(); a.remove();
  setTimeout(() => URL.revokeObjectURL(a.href), 10000);
}

window.expoExport = async kind => {
  const rows = collectRows();
  if (!rows.length) throw new Error("Yuklab olish uchun ro'yxat bo'sh");
  toast('Tayyorlanmoqda…', false, 60000);
  await loadScript(CDN.qr);
  const imgs = await loadImages(rows.map(r => r.img).filter(Boolean));
  const qrs = rows.map(r => qrImage(r.url));
  toast(kind === 'pdf' ? 'PDF yasalmoqda…' : 'Word fayl yasalmoqda…', false, 120000);
  const blob = kind === 'pdf' ? await makePdf(rows, imgs, qrs) : await makeDocx(rows, imgs, qrs);
  save(blob, fileName(kind));
  toast(`Yuklab olindi: ${rows.length} ta mahsulot ✓`);
};
})();
