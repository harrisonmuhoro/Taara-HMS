# Theme (as of init — pre East African restyle)

## Compact token summary

### Colors (Tailwind `brand` — current SaaS blue)

- brand-50 `#eff6ff` … brand-500 `#3b82f6` … brand-600 `#2563eb` … brand-900 `#1e3a8a`
- surface `#ffffff` / dark `#0D1220`
- surface-card `#f8fafc` / card-dark `#141c2e`
- overlay CSS (`resources/css/app.css` `:root` `--gh-*`): linen `#f4f0e8`, gold `#9c773a`, onyx dark `#0b1117`

### Type

- Tailwind: Inter (sans), Outfit (headings in CSS)
- Layouts load: Cormorant Garamond + DM Sans + DM Mono
- Overlay forces body DM Sans, headings Cormorant

### Radius / shadow / motion

- `xl` 0.625rem, `2xl` 0.75rem, `3xl` 1rem
- `shadow-brand`, `shadow-card`, hover `-translate-y-0.5`
- fade-in-up / fade-in-down on page chrome

### Breakpoints

- Tailwind defaults; mobile sidebar overlay `lg:`

### Target (see `.superdesign/design-system.md`)

- paper `#F4EEE4`, terracotta `#B85A2A`, olive `#4A5C3A`, 4–6px radius, Source Sans 3 + Source Serif 4 wordmark

## Raw: tailwind.config.js

See `tailwind.config.js` in repo root (`darkMode: 'class'`, `@tailwindcss/forms`).

## Raw: CSS variables (current overlay)

```css
:root {
    --sidebar-width: 18rem;
    --topbar-height: 5rem;
    --brand: 37 99 235;
    --radius-card: 1rem;
    --gh-bg: #f4f0e8;
    --gh-surface: #fffdf8;
    --gh-gold: #9c773a;
}
```

Full file: `resources/css/app.css` (~544 lines) including `.card`, `.btn-primary`, `.form-input`, `.data-table`, glass/frosted/glow utilities, Grand Horizon overlay `!important` rules.
