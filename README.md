# 🎱 Generador de Cartones de Lotería

Aplicación web **100% estática** para generar cartones de lotería en PDF directamente desde el navegador.
Sin servidor, sin instalaciones, sin backend.

Desarrollada en La Adela, La Pampa — Idea: **Oscar Vilugron** — Código: **Damian Gonzalez**

---

## ✨ Funcionalidades

| Modo | Descripción |
|------|-------------|
| **Originales** | Genera el lote principal de cartones únicos. Se guardan en el navegador para evitar duplicados. |
| **Adicionales / Sorpresas** | Genera cartones que no repiten ninguna fila de los originales. |
| **Lotería Radial** | Cartones con datos de contacto y emisora de radio. |

- Cartón: **15 números únicos** (1-90) en grilla 3×5, con al menos uno por decena
- PDF generado completamente en el navegador (sin enviar datos a ningún servidor)
- Los cartones originales se guardan en `localStorage` para la sesión

---

## 🚀 Despliegue — sin servidor

### Opción 1 — Netlify Drop (más fácil, 30 segundos)

1. Entrá a [app.netlify.com/drop](https://app.netlify.com/drop)
2. Arrastrá la carpeta `loteria/` al recuadro
3. ¡Listo! Netlify te da una URL pública gratuita

### Opción 2 — GitHub Pages

1. Subí el proyecto a un repositorio de GitHub
2. En Settings → Pages → seleccioná `main` branch
3. La URL será `https://tuusuario.github.io/loteria`

### Opción 3 — Abrir directo en el navegador (sólo para pruebas locales)

Como la app carga scripts externos, algunos navegadores bloquean el CORS al abrir archivos locales.
La forma más simple de probarlo sin servidor:

```bash
# Si tenés Python instalado (viene con Windows a veces):
cd "C:\ruta\a\loteria"
python -m http.server 8080
```

Luego abrí `http://localhost:8080` en el navegador.

> **Python sí está instalado** en este equipo (se detectó en `AppData/Local/Programs`).
> Podés usarlo directamente.

---

## 📁 Estructura del proyecto

```
loteria/
│
├── index.html                       ← Página principal (menú)
│
├── pages/                           ← Formularios + lógica de generación
│   ├── formulario-originales.html
│   ├── formulario-adicionales.html
│   └── formulario-radio.html
│
├── js/                              ← Lógica JavaScript pura
│   ├── card-generator.js            ← Genera cartones únicos + localStorage
│   └── pdf-builder.js               ← Construye PDFs con jsPDF
│
└── assets/
    └── css/
        └── style.css                ← Mínimo (el diseño lo maneja Tailwind CDN)
```

### Archivos PHP (ignorados en despliegue estático)

Los siguientes archivos son la versión anterior con backend PHP.
No afectan la versión estática — Netlify/GitHub Pages los ignora.
Podés borrarlos si querés.

```
src/          ← Clases PHP (Card, CardGenerator, etc.)
original_v1.php
adicional_v1.php
radio_v1.php
fpdf.php
fpdf.css
font/
txt/
```

---

## 🛠️ Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Diseño | **Tailwind CSS** (CDN) — utility-first, mobile-first |
| Interactividad | **Alpine.js** (CDN) — spinner, validación, estados |
| PDF | **jsPDF** (CDN) — generación de PDF en el navegador |
| Lógica | JavaScript puro, sin frameworks ni build step |

No requiere Node.js, Composer, npm, ni ninguna herramienta de build.

---

## 📱 Diseño

- **Mobile-first**: funciona en celular, tablet y desktop
- Spinner con texto de progreso mientras se genera el PDF
- Validación de campos en el cliente antes de procesar
- Aviso automático si no hay cartones originales guardados (en adicionales)

---

## 📋 Flujo de uso

```
1. Generá los Cartones Originales
      ↓ (se guardan en localStorage del navegador)

2. Imprimí el PDF descargado

3. Si necesitás más → Adicionales y Sorpresas
      ↓ (lee los originales del localStorage para no repetir)

4. Imprimí el PDF adicional
```

> ⚠️ Los originales se guardan en el **navegador que usaste**.
> Si cambiás de dispositivo o borrás el historial, el aviso de "sin originales"
> aparecerá en la pantalla de adicionales.
