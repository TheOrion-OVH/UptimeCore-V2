<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitors - UptimeCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">UptimeCore</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/dashboard" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                    <a href="/monitors" class="text-indigo-600 font-medium">Monitors</a>
                    <a href="/incidents" class="text-gray-700 hover:text-gray-900">Incidents</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-gray-900">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Monitors</h2>
                <a href="{{ route('monitors.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    + Nouveau Monitor
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($monitors as $monitor)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($monitor->status === 'up')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">UP</span>
                                            @elseif($monitor->status === 'down')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">DOWN</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ strtoupper($monitor->status) }}</span>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $monitor->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $monitor->type }} - {{ $monitor->url ?? $monitor->host ?? $monitor->domain ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        @if($monitor->response_time)
                                            <span class="text-sm text-gray-500">{{ $monitor->response_time }}ms</span>
                                        @endif
                                        <span class="text-sm text-gray-500">Uptime: {{ number_format($monitor->uptime_percentage, 2) }}%</span>
                                        <a href="{{ route('monitors.show', $monitor) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Voir</a>
                                        <a href="{{ route('monitors.edit', $monitor) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Modifier</a>
                                        <form method="POST" action="{{ route('monitors.destroy', $monitor) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-gray-500">
                            Aucun monitor configuré. <a href="{{ route('monitors.create') }}" class="text-indigo-600 hover:text-indigo-900">Créer le premier</a>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
