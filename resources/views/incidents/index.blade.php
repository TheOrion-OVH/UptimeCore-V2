<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidents - UptimeCore</title>
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
                <h2 class="text-2xl font-bold text-gray-900">Incidents</h2>
                <a href="{{ route('incidents.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    + Nouvel Incident
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($incidents as $incident)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($incident->impact === 'critical')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">CRITIQUE</span>
                                            @elseif($incident->impact === 'major')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">MAJEUR</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">MINEUR</span>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $incident->title }}</div>
                                            <div class="text-sm text-gray-500">
                                                Statut: {{ ucfirst($incident->status) }} - 
                                                Démarré: {{ $incident->started_at->format('d/m/Y H:i') }}
                                                @if($incident->resolved_at)
                                                    - Résolu: {{ $incident->resolved_at->format('d/m/Y H:i') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('incidents.show', $incident) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Voir</a>
                                        <a href="{{ route('incidents.edit', $incident) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Modifier</a>
                                        <form method="POST" action="{{ route('incidents.destroy', $incident) }}" class="inline">
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
                            Aucun incident. <a href="{{ route('incidents.create') }}" class="text-indigo-600 hover:text-indigo-900">Créer le premier</a>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </main>
</body>
</html>

