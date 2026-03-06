/**
 * Genera un cartón aleatorio: grilla 3×5 con 15 números únicos del 1 al 90.
 * Garantiza al menos un número por cada decena (1-10, 11-20, ..., 81-90).
 *
 * @returns {number[][]} grilla 3×5
 */
function generateCard() {
  const numbers = [];

  // Un número por decena para distribución uniforme del 1-90
  for (let decade = 0; decade < 9; decade++) {
    numbers.push(Math.floor(Math.random() * 10) + decade * 10 + 1);
  }

  // 6 números extras únicos
  while (numbers.length < 15) {
    const n = Math.floor(Math.random() * 90) + 1;
    if (!numbers.includes(n)) numbers.push(n);
  }

  numbers.sort((a, b) => a - b);

  return [
    numbers.slice(0, 5),
    numbers.slice(5, 10),
    numbers.slice(10, 15),
  ];
}

/**
 * Genera `count` cartones únicos que no comparten ninguna fila con los de
 * `existing`. Usa un Set de firmas para detección O(n) en lugar de O(n²).
 *
 * @param {number}       count
 * @param {number[][][]} existing - cartones a evitar (vacío por defecto)
 * @param {Function}     [onProgress] - callback(done, total)
 * @returns {number[][][]}
 */
function generateList(count, existing = [], onProgress = null) {
  // Firma de fila: "posición|n1,n2,n3,n4,n5"
  const rowSigs = new Set();

  function registerCard(card) {
    for (let i = 0; i < 3; i++) {
      rowSigs.add(i + '|' + card[i].join(','));
    }
  }

  function isDuplicate(card) {
    for (let i = 0; i < 3; i++) {
      if (rowSigs.has(i + '|' + card[i].join(','))) return true;
    }
    return false;
  }

  existing.forEach(registerCard);

  const cards = [];
  const maxAttempts = count * 300; // válvula de seguridad
  let attempts = 0;

  while (cards.length < count) {
    if (++attempts > maxAttempts) {
      throw new Error(
        `Solo se pudieron generar ${cards.length} de ${count} cartones únicos. ` +
        'Reducí la cantidad e intentá de nuevo.'
      );
    }

    const candidate = generateCard();
    if (!isDuplicate(candidate)) {
      cards.push(candidate);
      registerCard(candidate);
      if (onProgress && cards.length % 50 === 0) {
        onProgress(cards.length, count);
      }
    }
  }

  return cards;
}

// ─── Persistencia en localStorage ────────────────────────────────────────────

function saveOriginals(cards) {
  try {
    localStorage.setItem('loteria_originales', JSON.stringify(cards));
  } catch (e) {
    console.warn('No se pudo guardar en localStorage:', e);
  }
}

function loadOriginals() {
  try {
    const saved = localStorage.getItem('loteria_originales');
    return saved ? JSON.parse(saved) : [];
  } catch (e) {
    return [];
  }
}

function countOriginals() {
  try {
    const saved = localStorage.getItem('loteria_originales');
    if (!saved) return 0;
    const arr = JSON.parse(saved);
    return Array.isArray(arr) ? arr.length : 0;
  } catch (e) {
    return 0;
  }
}
