<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Incident - UptimeCore</title>
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
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Modifier Incident</h2>
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('incidents.update', $incident) }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titre</label>
                            <input type="text" name="title" value="{{ $incident->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Impact</label>
                            <select name="impact" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="minor" {{ $incident->impact === 'minor' ? 'selected' : '' }}>Mineur</option>
                                <option value="major" {{ $incident->impact === 'major' ? 'selected' : '' }}>Majeur</option>
                                <option value="critical" {{ $incident->impact === 'critical' ? 'selected' : '' }}>Critique</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="investigating" {{ $incident->status === 'investigating' ? 'selected' : '' }}>En investigation</option>
                                <option value="identified" {{ $incident->status === 'identified' ? 'selected' : '' }}>Identifié</option>
                                <option value="monitoring" {{ $incident->status === 'monitoring' ? 'selected' : '' }}>En surveillance</option>
                                <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Résolu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('incidents.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Annuler</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

