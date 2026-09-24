<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms & Conditions · {{ config('app.name', 'Taara HMS') }}</title>
    
    <link rel="icon" href="{{ asset('taara-hms-mark.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-sans">
    
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <div class="text-center mb-10">
                <a href="/" class="inline-block">
                    <img src="{{ asset('taara-hms-mark.svg') }}" alt="Taara HMS" class="h-16 w-16 mx-auto rounded-xl shadow-sm mb-4">
                </a>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Staff Acceptable Use Policy & Terms</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Last Updated: {{ now()->format('F j, Y') }}</p>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-8 sm:p-12 prose dark:prose-invert max-w-none">
                
                <h2>1. Introduction</h2>
                <p>Welcome to the Taara Hotel Management System (Taara HMS). This system is the exclusive property of <strong>Taara Hotel Limited</strong>. By logging in or using this system, you agree to comply with the terms and conditions outlined in this Acceptable Use Policy. These rules are designed to protect guest data, maintain financial integrity, and ensure the security of hotel operations.</p>

                <h2>2. Authorized Access</h2>
                <p>Access to Taara HMS is strictly restricted to authorized employees and contractors of <strong>Taara Hotel Limited</strong>. You are solely responsible for all activities occurring under your account.</p>
                <ul>
                    <li>You must not share your login credentials, passwords, or multi-factor authentication tokens with anyone, including other staff members.</li>
                    <li>If you suspect your account has been compromised, you must immediately notify the IT or Security department.</li>
                    <li>Accounts are monitored, and suspicious activity (such as off-hours logins or repeated failed attempts) will be logged and audited.</li>
                </ul>

                <h2>3. Handling of Guest Data & Privacy</h2>
                <p>As a user of this system, you will have access to Personally Identifiable Information (PII) and Payment Card Industry (PCI) data belonging to hotel guests.</p>
                <ul>
                    <li>Guest data must only be accessed on a "need-to-know" basis to perform your specific job duties.</li>
                    <li>You must not export, download, or physically print guest lists, reservation details, or financial reports unless expressly required for an authorized operational task.</li>
                    <li>Under no circumstances should sensitive payment data be written down, photographed, or stored outside the secure encrypted fields within the system.</li>
                </ul>

                <h2>4. Financial Integrity & Fraud Prevention</h2>
                <p>Taara HMS tracks all financial mutations, including POS orders, stay folios, invoices, and refunds.</p>
                <ul>
                    <li>All transactions must be accurately recorded in real-time.</li>
                    <li>Applying unauthorized discounts, comping rooms without managerial approval, or processing fraudulent refunds is strictly prohibited and constitutes grounds for immediate termination and potential legal action.</li>
                    <li>The system maintains immutable audit logs. Every action (including edits and deletions) is permanently tied to your user ID and IP address.</li>
                </ul>

                <h2>5. System Usage & Restrictions</h2>
                <p>The system is provided solely for hotel management purposes.</p>
                <ul>
                    <li>You may not use automated scripts, bots, or scrapers to extract data from the system.</li>
                    <li>You may not attempt to bypass security controls, rate limiters, or access routes outside your assigned role permissions.</li>
                    <li>You may not use the system's email or notification services to send unauthorized or non-business-related communications.</li>
                </ul>

                <h2>6. Termination of Access</h2>
                <p><strong>Taara Hotel Limited</strong> reserves the right to suspend, lock, or permanently revoke your access to Taara HMS at any time, with or without notice, if we believe you have violated these terms or represent a security risk to the property or our guests.</p>

                <h2>7. Acknowledgment</h2>
                <p>By continuing to the login page and accessing Taara HMS, you acknowledge that you have read, understood, and agree to be bound by this Acceptable Use Policy.</p>
                
                <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700 text-center">
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-medium rounded-xl shadow-sm transition-colors">
                        I Agree, Proceed to Login
                    </a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
