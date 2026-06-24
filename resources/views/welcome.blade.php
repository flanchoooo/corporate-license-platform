<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Corporate Vehicle Licensing Platform</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen">
            <header class="border-b border-gray-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded bg-gray-900 text-sm font-bold text-white">CV</div>
                        <div>
                            <div class="text-sm font-semibold uppercase tracking-wide text-gray-900">Corporate Vehicle Licensing</div>
                            <div class="text-xs text-gray-500">Zimbabwe fleet compliance portal</div>
                        </div>
                    </a>

                    <nav class="flex items-center gap-3 text-sm font-medium">
                        <a href="{{ route('bot.menu') }}" class="rounded-md px-4 py-2 text-gray-700 hover:text-gray-950">Vehicle Bot</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-md bg-gray-900 px-4 py-2 text-white">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-md px-4 py-2 text-gray-700 hover:text-gray-950">Log in</a>
                            <a href="{{ route('register') }}" class="rounded-md bg-gray-900 px-4 py-2 text-white">Register Company</a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main>
                <section class="bg-white">
                    <div class="mx-auto max-w-7xl px-6 py-16">
                        <div class="space-y-8">
                            <div class="inline-flex rounded border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-800">
                                Fleet licensing, checkout payments, and printable disks
                            </div>
                            <div class="space-y-5">
                                <h1 class="max-w-3xl text-4xl font-bold tracking-normal text-gray-950 sm:text-5xl">
                                    Manage corporate vehicle licensing from quote to disk.
                                </h1>
                                <p class="max-w-2xl text-lg leading-8 text-gray-600">
                                    Register company fleets, calculate Radio License, Motor Insurance, ZINARA, carbon tax and arrears, then pay by Mobile Money, Zimswitch, Visa, or Mastercard and generate license disks with QR verification.
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white">Open Dashboard</a>
                                @else
                                    <a href="{{ route('register') }}" class="rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white">Start Company Registration</a>
                                    <a href="{{ route('login') }}" class="rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-800">Log in</a>
                                @endauth
                                <a href="{{ route('bot.menu') }}" class="rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-800">Open Vehicle Bot</a>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </body>
</html>
