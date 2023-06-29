<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">


    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @livewireScripts
    @stack('scripts')
</head>
<body>


<div class="dui-drawer {{ $drawer_open ? ' dui-drawer-open' : '' }}">
    <input id="my-drawer-2" type="checkbox" class="dui-drawer-toggle"/>
    <div class="dui-drawer-content flex flex-col items-center justify-center">
        <!-- Page content here -->

        <div class="min-h-screen w-full bg-gray-100 dark:bg-gray-900">

            @livewire('navigation-menu')
            <!-- Page Heading -->
            @if (isset($header))
                <header class="w-full bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            {{ $slot }}

        </div>

    </div>
    <div class="dui-drawer-side">
        <label for="my-drawer-2" class="dui-drawer-overlay"></label>
        <ul class="dui-menu p-4 w-80 h-full bg-base-200 text-base-content">
            <!-- Sidebar content here -->

            <div class="my-2"><a href="{{ route('reports.inventory.status') }}"
                                 class="w-full dui-btn hover:dui-btn-outline {{
    request()->routeIs('reports.inventory.status') ? ' dui-btn-primary' : 'dui-btn-neutral' }}">Inventory Status</a></div>
            <div class="my-2"><a href="{{ route('reports.inventory.transactions') }}"
                                 class="w-full dui-btn hover:dui-btn-outline {{
    request()->routeIs('reports.inventory.transactions') ? ' dui-btn-primary' : 'dui-btn-neutral'  }}">Inventory Transactions</a></div>

    </div>
</div>
@livewire('notifications')


</body>
</html>
