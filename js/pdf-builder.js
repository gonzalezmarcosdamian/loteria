/**
 * pdf-builder.js — Diseño de PDF monocromático centrado para cartones de lotería.
 * Requiere jsPDF 2.x en window.jspdf.
 *
 * Paleta: familia navy (un solo matiz) + grises neutros.
 * Layout calculado dinámicamente (sin posiciones hardcodeadas para el footer).
 */

// ─── Paleta monocromática navy ────────────────────────────────────────────────
const N = {
  n900:   [14,  18,  62],   // header principal, texto clave
  n700:   [38,  48, 118],   // elementos secundarios
  n500:   [72,  86, 158],   // badges ronda 3-4, bordes medios
  n300:   [148, 160, 210],  // bordes suaves
  n100:   [224, 228, 248],  // fondo celdas de grilla
  n50:    [242, 243, 253],  // fila alternada tabla
  white:  [255, 255, 255],
  textHi: [14,  18,  62],   // texto oscuro
  textMd: [88,  98, 150],   // texto medio
  textLo: [160, 168, 205],  // notas, pies
};

// Shade por ronda (mismo matiz, cuatro oscuridades)
const RONDA_SHADE = [N.n900, N.n700, N.n500, [98, 112, 178]];

// ─── Dimensiones página ───────────────────────────────────────────────────────
const PG  = { W: 210, H: 297, ML: 14, MR: 14, MT: 14, MB: 14 };
const MW  = PG.W - PG.ML - PG.MR;   // ancho útil: 182mm
const CX  = PG.W / 2;                // centro horizontal: 105mm

// ─── Grilla ───────────────────────────────────────────────────────────────────
const CELL   = 11;                    // mm por celda
const GRID_W = 5 * CELL;             // 55mm
const GRID_X = (PG.W - GRID_W) / 2; // centrado: 77.5mm

// ─── Utilidades de color ──────────────────────────────────────────────────────
const f = (doc, c) => doc.setFillColor(...c);
const s = (doc, c) => doc.setDrawColor(...c);
const t = (doc, c) => doc.setTextColor(...c);

function hLine(doc, y, color = N.n300, lw = 0.25) {
  s(doc, color);
  doc.setLineWidth(lw);
  doc.line(PG.ML, y, PG.ML + MW, y);
}

function filledRect(doc, x, y, w, h, color) {
  f(doc, color);
  doc.rect(x, y, w, h, 'F');
}

// ─── Formato de datos ─────────────────────────────────────────────────────────

/** Convierte YYYY-MM-DD → DD/MM/YYYY (soporta ambos formatos como input) */
function fmtDate(v) {
  if (!v) return '';
  if (/^\d{4}-\d{2}-\d{2}$/.test(v)) {
    const [y, m, d] = v.split('-');
    return `${d}/${m}/${y}`;
  }
  return v;
}

/** Formatea dinero con separador de miles (locale argentino) */
function fmtMoney(v) {
  const n = parseInt(v) || 0;
  return '$\u00a0' + n.toLocaleString('es-AR');
}

// ─── Logo ─────────────────────────────────────────────────────────────────────

/**
 * Dibuja el logo: imagen si existe, o monograma por defecto.
 * @param {number} x     posición X del cuadro del logo
 * @param {number} y     posición Y del cuadro del logo
 * @param {number} size  tamaño cuadrado en mm
 */
function drawLogo(doc, logo, x, y, size) {
  if (logo?.data) {
    try {
      doc.addImage(logo.data, logo.format || 'JPEG', x, y, size, size);
      return;
    } catch (_) { /* fallback al monograma */ }
  }

  // Monograma por defecto: círculo concéntrico con "L"
  const cx = x + size / 2;
  const cy = y + size / 2;
  const r  = size / 2;

  // Aro exterior
  f(doc, N.n900);
  doc.circle(cx, cy, r, 'F');

  // Aro blanco interior
  f(doc, N.white);
  doc.circle(cx, cy, r * 0.68, 'F');

  // Disco central navy
  f(doc, N.n700);
  doc.circle(cx, cy, r * 0.46, 'F');

  // Letra "L" en blanco
  t(doc, N.white);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(size * 0.42);
  doc.text('L', cx, cy + size * 0.14, { align: 'center' });
}

// ─── Indicador de ronda ───────────────────────────────────────────────────────

/**
 * Dibuja "── RONDA N ──" centrado con reglas decorativas.
 * @returns {number} Y siguiente
 */
function drawRondaLine(doc, ronda, y) {
  const color = RONDA_SHADE[(ronda - 1) % 4];
  const label = `RONDA ${ronda}`;

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(8);
  const tw = doc.getTextWidth(label);
  const gap = 4;

  // Regla izquierda
  s(doc, color);
  doc.setLineWidth(0.35);
  doc.line(PG.ML, y + 3, CX - tw / 2 - gap, y + 3);

  // Regla derecha
  doc.line(CX + tw / 2 + gap, y + 3, PG.ML + MW, y + 3);

  // Texto
  t(doc, color);
  doc.text(label, CX, y + 5.5, { align: 'center' });

  return y + 9;
}

// ─── Grilla del cartón ────────────────────────────────────────────────────────

/**
 * Dibuja la grilla 3×5 centrada horizontalmente.
 * @returns {number} Y siguiente
 */
function drawGrid(doc, card, y) {
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);

  for (let row = 0; row < 3; row++) {
    for (let col = 0; col < 5; col++) {
      const cx = GRID_X + col * CELL;
      const cy = y + row * CELL;

      // Fondo celda
      f(doc, N.n100);
      doc.rect(cx, cy, CELL, CELL, 'F');

      // Borde celda
      s(doc, N.n300);
      doc.setLineWidth(0.2);
      doc.rect(cx, cy, CELL, CELL, 'S');

      // Número
      t(doc, N.textHi);
      doc.text(String(card[row][col]), cx + CELL / 2, cy + CELL * 0.64, { align: 'center' });
    }
  }

  return y + 3 * CELL;
}

// ─── Bloque de cabecera ───────────────────────────────────────────────────────

/**
 * Dibuja la cabecera completa (barra, logo, info evento).
 * @returns {number} Y donde empieza el contenido del cartón
 */
function drawHeader(doc, config, modo, cardIndex, logo) {
  const BAR_H   = 13;
  const LOGO_SZ = 10;
  const LOGO_X  = PG.ML + 1.5;
  const LOGO_Y  = PG.MT + (BAR_H - LOGO_SZ) / 2;
  const TEXT_X  = LOGO_X + LOGO_SZ + 3;

  // ── Barra oscura principal
  filledRect(doc, PG.ML, PG.MT, MW, BAR_H, N.n900);

  // Logo
  drawLogo(doc, logo, LOGO_X, LOGO_Y, LOGO_SZ);

  // Nombre organización
  t(doc, N.white);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(10.5);
  const maxOrgW = MW - LOGO_SZ - 6 - 30;
  const orgText = doc.splitTextToSize(config.organiza.toUpperCase(), maxOrgW)[0];
  doc.text(orgText, TEXT_X, PG.MT + 8.8);

  // N.° de cartón (alineado a la derecha)
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(8.5);
  doc.text(
    `N.\u00b0 ${String(cardIndex + 1).padStart(3, '0')}`,
    PG.ML + MW - 1, PG.MT + 8.8, { align: 'right' }
  );

  let y = PG.MT + BAR_H + 4;

  // ── Info del evento
  t(doc, N.textMd);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(8);

  const fechaHora = `Fecha: ${fmtDate(config.fecha)}   Hora: ${config.hora} hs   Costo: ${fmtMoney(config.costocarton)}`;
  doc.text(fechaHora, PG.ML, y); y += 5;

  if (modo === 'originales' && config.lugar) {
    doc.text(`Lugar: ${config.lugar}`, PG.ML, y); y += 5;
  }
  if (modo === 'radio') {
    t(doc, N.n700);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(7.5);
    doc.text(`Tel: ${config.contacto}   Radio: ${config.radio}`, PG.ML, y); y += 5;
  }

  y += 1;
  hLine(doc, y, N.n900, 0.7);
  return y + 4;
}

// ─── Tabla de premios ─────────────────────────────────────────────────────────

/**
 * Dibuja la tabla de premios en una posición Y calculada.
 * @returns {number} Y final de la tabla
 */
function drawPremiosTable(doc, premios, startY) {
  const COL = [22, 80, 80]; // anchos de columna (total = 182mm)
  let y = startY;

  // Línea separadora
  hLine(doc, y - 2, N.n900, 0.6);

  // Encabezado de tabla
  filledRect(doc, PG.ML, y, MW, 7.5, N.n900);
  t(doc, N.white);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(7.5);
  doc.text('RONDA',         PG.ML + 3,               y + 5);
  doc.text('QUINTINA',      PG.ML + COL[0] + 3,      y + 5);
  doc.text('CARTÓN LLENO',  PG.ML + COL[0] + COL[1] + 3, y + 5);
  y += 7.5;

  // Filas de premios
  premios.forEach((p, i) => {
    const rowBg = i % 2 === 0 ? N.n50 : N.white;
    filledRect(doc, PG.ML, y, MW, 9, rowBg);

    // Bordes de fila
    s(doc, N.n300);
    doc.setLineWidth(0.15);
    doc.rect(PG.ML, y, MW, 9, 'S');
    doc.line(PG.ML + COL[0],          y, PG.ML + COL[0],          y + 9);
    doc.line(PG.ML + COL[0] + COL[1], y, PG.ML + COL[0] + COL[1], y + 9);

    // Badge mini de ronda
    const shade = RONDA_SHADE[i % 4];
    filledRect(doc, PG.ML + 2, y + 1.8, 15, 5.5, shade);
    t(doc, N.white);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(7);
    doc.text(`R${i + 1}`, PG.ML + 9.5, y + 5.8, { align: 'center' });

    // Textos (primera línea si es largo)
    t(doc, N.textHi);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.5);
    doc.text(
      doc.splitTextToSize(p.quintina, COL[1] - 5)[0],
      PG.ML + COL[0] + 3, y + 5.8
    );
    doc.text(
      doc.splitTextToSize(p.lleno, COL[2] - 5)[0],
      PG.ML + COL[0] + COL[1] + 3, y + 5.8
    );

    y += 9;
  });

  // Nota al pie
  y += 3;
  t(doc, N.textLo);
  doc.setFont('helvetica', 'italic');
  doc.setFontSize(6.8);
  doc.text(
    'NOTA: Se entrega el premio, no su valor monetario. No somos responsables por pérdida del cartón.',
    PG.ML, y
  );

  return y + 5;
}

// ─── Calculador de posición del footer ───────────────────────────────────────

/**
 * Calcula dónde debe comenzar la tabla de premios para que quede al final
 * de la página con un margen mínimo elegante.
 *
 * Altura de la tabla: 7.5 (header) + 4×9 (rows) + 3 (gap) + 10 (note+padding) = 56.5mm
 */
function premiosStartY(contentEndY) {
  const TABLE_H = 57;
  const ideal   = PG.H - PG.MB - TABLE_H; // ~226mm
  // Si el contenido llega más abajo del ideal, empezar inmediatamente después
  return Math.max(ideal, contentEndY + 8);
}

// ─────────────────────────────────────────────────────────────────────────────
// API PÚBLICA
// ─────────────────────────────────────────────────────────────────────────────

function buildOriginalesPdf(cards, config, logo = null) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('p', 'mm', 'a4');

  cards.forEach((card, index) => {
    if (index > 0) doc.addPage();

    let y = drawHeader(doc, config, 'originales', index, logo);

    for (let r = 1; r <= 4; r++) {
      y = drawRondaLine(doc, r, y);
      y = drawGrid(doc, card, y);
      y += 3;
    }

    drawPremiosTable(doc, config.premios, premiosStartY(y));
  });

  doc.save('cartones-originales.pdf');
}

function buildAdicionalesPdf(cards, config, logo = null) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('p', 'mm', 'a4');

  const TICKETS_PER_PAGE = 5;
  const AVAIL_H = PG.H - PG.MT - PG.MB;        // 269mm
  const BLOCK_H = Math.floor(AVAIL_H / TICKETS_PER_PAGE); // 53mm per ticket

  cards.forEach((card, index) => {
    const pos = index % TICKETS_PER_PAGE;
    if (index > 0 && pos === 0) doc.addPage();

    const blockY = PG.MT + pos * BLOCK_H;
    let y = blockY;

    // Separador entre tickets (excepto el primero)
    if (pos > 0) {
      hLine(doc, y - 1, N.n300, 0.2);
    }

    // Mini header del ticket
    filledRect(doc, PG.ML, y, MW, 7, N.n900);
    t(doc, N.white);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(7.5);
    doc.text(`${config.tipo}  N.\u00b0 ${String(index + 1).padStart(3, '0')}`, PG.ML + 2, y + 4.8);
    doc.setFont('helvetica', 'normal');
    doc.text(
      `${fmtDate(config.fecha)}  ${config.hora} hs  |  ${fmtMoney(config.costo)}`,
      PG.ML + MW - 2, y + 4.8, { align: 'right' }
    );
    y += 9;

    // Logo pequeño si existe
    if (logo?.data) {
      try { doc.addImage(logo.data, logo.format || 'JPEG', PG.ML, y, 8, 8); } catch (_) {}
    }

    // Grilla centrada
    drawGrid(doc, card, y);
  });

  doc.save('cartones-adicionales.pdf');
}

function buildRadioPdf(cards, config, logo = null) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('p', 'mm', 'a4');

  cards.forEach((card, index) => {
    if (index > 0) doc.addPage();

    let y = drawHeader(doc, config, 'radio', index, logo);

    for (let r = 1; r <= 4; r++) {
      y = drawRondaLine(doc, r, y);
      y = drawGrid(doc, card, y);
      y += 3;
    }

    drawPremiosTable(doc, config.premios, premiosStartY(y));
  });

  doc.save('cartones-radio.pdf');
}
