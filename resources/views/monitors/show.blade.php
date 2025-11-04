<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Monitor - UptimeCore</title>
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
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-6">
                <a href="{{ route('monitors.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour</a>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $monitor->name }}</h2>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            @if($monitor->status === 'up')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">UP</span>
                            @elseif($monitor->status === 'down')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">DOWN</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ strtoupper($monitor->status) }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $monitor->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">URL / Host</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $monitor->url ?? $monitor->host ?? $monitor->domain ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Temps de réponse</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $monitor->response_time ?? 'N/A' }}ms</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Uptime</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($monitor->uptime_percentage, 2) }}%</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Intervalle</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $monitor->interval }}s</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dernière vérification</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $monitor->last_check_at ? $monitor->last_check_at->format('d/m/Y H:i:s') : 'Jamais' }}</dd>
                    </div>
                </dl>
                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('monitors.edit', $monitor) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Modifier</a>
                    <form method="POST" action="{{ route('monitors.destroy', $monitor) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

