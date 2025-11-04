<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Incident - UptimeCore</title>
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
                    <a href="/monitors" class="text-gray-700 hover:text-gray-900">Monitors</a>
                    <a href="/incidents" class="text-indigo-600 font-medium">Incidents</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-6">
                <a href="{{ route('incidents.index') }}" class="text-indigo-600 hover:text-indigo-900">← Retour</a>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $incident->title }}</h2>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Impact</dt>
                        <dd class="mt-1">
                            @if($incident->impact === 'critical')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">CRITIQUE</span>
                            @elseif($incident->impact === 'major')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">MAJEUR</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">MINEUR</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($incident->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Démarré le</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $incident->started_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($incident->resolved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Résolu le</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->resolved_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
                @if($incident->updates->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Mises à jour</h3>
                        <div class="space-y-4">
                            @foreach($incident->updates as $update)
                                <div class="border-l-4 border-indigo-500 pl-4">
                                    <p class="text-sm text-gray-900">{{ $update->message }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $update->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('incidents.edit', $incident) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Modifier</a>
                    <form method="POST" action="{{ route('incidents.destroy', $incident) }}" class="inline">
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

