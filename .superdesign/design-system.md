# Grand Horizon PMS — East African city hotel

Staff UI for a Nairobi city hotel. Daylight, terracotta, olive, paper. Practical front-desk density. Not generic SaaS blue, not brass-and-linen luxury theater.

## Brand

- Wordmark: **Grand Horizon** in Source Serif 4, weight 600. No letter-in-a-rounded-square logo. No invented SVG mark.
- Subline: Nairobi · Property desk (sentence case, never “Property Management System” in tiny tracking-widest caps).
- Voice: “Today’s arrivals”, “Check this guest in”, “Room 214 is dirty”. Never “Here’s what’s happening across your hotel today.”

## Color

| Token | Light | Dark | Use |
| --- | --- | --- | --- |
| paper | `#F4EEE4` | `#1A1612` | App background |
| surface | `#FFFBF4` | `#241E18` | Sidebar, tables, panels |
| ink | `#2C241C` | `#F0E6D8` | Body text |
| muted | `#6E6458` | `#A89884` | Secondary text |
| line | `#D9CFC0` | `#3A3228` | Borders |
| terracotta | `#B85A2A` | `#D47848` | Primary actions, active nav |
| terracotta-ink | `#FFF8F2` | `#1A1612` | Text on terracotta |
| olive | `#4A5C3A` | `#8FA678` | Available / occupied-healthy |
| dust | `#B08948` | `#C4A060` | Rare emphasis only (occupancy if needed) |
| danger | `#9B2C2C` | `#E07A6A` | Checkout blocked, errors |

Do not use Tailwind default blue (`#2563eb`) anywhere. Occupied rooms use terracotta wash, not sky-50. Available rooms use olive wash, not emerald-saas.

## Type

- UI: **Source Sans 3** (400/600). Tables, forms, nav, buttons.
- Hotel name only: **Source Serif 4**.
- Numbers on the desk (room numbers, KES, occupancy %): Source Sans 3 tabular, not a display serif.
- No Cormorant Garamond, Outfit, Inter, DM Sans, DM Mono as brand fonts.
- No uppercase + tracking-widest section labels. Use sentence case, 11–12px, muted.

## Shape and density

- Radius: 4px controls, 6px panels. Not `rounded-2xl` / `rounded-3xl`.
- Shadows: none on resting cards. 1px border (`line`) only.
- No hover-lift (`-translate-y`), no glow, no glass/frosted blur, no gradient brand fills.
- Padding: 12–16px in tables; 16–20px page gutters on desktop.
- Sidebar: 16rem, paper-adjacent surface, terracotta left bar on active item (2px), not a filled pill.

## Layout patterns

- Shell: left nav + solid top bar (no backdrop-blur) + scrollable main.
- Dashboard: shift briefing — arrivals, departures, dirty rooms, open tickets as a compact strip; then one arrivals table. Not four pastel KPI clones plus a duplicate KPI row.
- Login: split daylight. Left: Nairobi city-hotel photograph (warm morning light, not a stock lobby chandelier). Right: cream form, terracotta Sign in. Drop ISO/PCI/terminal theater.
- Front desk queues: dense tables, primary action on the right, guest name first.
- Room board: small numbered tiles, olive/terracotta/ochre status, no scale-on-hover.

## Do / don’t

- Do: KES amounts, room numbers, guest names, branch names as first-class.
- Don’t: “Grand Hotel” generic, “H” avatar logo, fake compliance badges, fade-in-up page chrome, uppercase tracking-widest.
