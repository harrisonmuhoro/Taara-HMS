# Extractable components

## AppSidebar
- Source: `resources/views/layouts/sidebar.blade.php`
- Category: layout
- Description: Left nav with wordmark, grouped links, user snippet
- Extractable props: activeItem (string, default: dashboard), userName (string, default: Amina Otieno), userEmail (string, default: amina@grandhorizon.co.ke)
- Hardcoded: nav labels, icons, Grand Horizon wordmark (text, no logo file)

## AppTopbar
- Source: `resources/views/layouts/topbar.blade.php`
- Category: layout
- Description: Search, branch chip, theme toggle, alerts, logout
- Extractable props: branchName (string, default: Nairobi Main), notificationCount (number, default: 2), searchQuery (string, default: "")
- Hardcoded: search placeholder, icon SVGs

## AppShell
- Source: `resources/views/components/app-layout.blade.php`
- Category: layout
- Description: Full viewport shell wrapping sidebar + topbar + main
- Extractable props: none beyond children
- Hardcoded: fonts, overflow-hidden h-screen

## StatusBadge
- Source: `resources/views/components/status-badge.blade.php`
- Category: basic
- Description: Pill status with optional dot
- Extractable props: color (string, default: gray), text (string, default: Pending)
- Hardcoded: palettes

## Card
- Source: `resources/views/components/card.blade.php`
- Category: basic
- Description: Bordered content panel
- Extractable props: title (string, default: ""), subtitle (string, default: "")
