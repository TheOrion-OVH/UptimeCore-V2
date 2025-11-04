<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Monitor - UptimeCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.getElementById('monitor-type').addEventListener('change', function() {
            const type = this.value;
            document.getElementById('url-field').style.display = (type === 'http' || type === 'ssl') ? 'block' : 'none';
            document.getElementById('host-field').style.display = (type === 'ping' || type === 'tcp') ? 'block' : 'none';
            document.getElementById('host-port-field').style.display = (type === 'tcp') ? 'block' : 'none';
            document.getElementById('domain-field').style.display = (type === 'dns') ? 'block' : 'none';
        });
    </script>
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
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Créer un Monitor</h2>
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('monitors.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" id="monitor-type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="http">HTTP/HTTPS</option>
                                <option value="ping">Ping</option>
                                <option value="tcp">TCP Port</option>
                                <option value="dns">DNS</option>
                                <option value="ssl">SSL/TLS</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div id="url-field">
                            <label class="block text-sm font-medium text-gray-700">URL</label>
                            <input type="text" name="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('url') }}" placeholder="https://example.com">
                            @error('url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div id="host-field" style="display:none;">
                            <label class="block text-sm font-medium text-gray-700">Host</label>
                            <input type="text" name="host" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('host') }}" placeholder="192.168.1.1">
                            @error('host')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div id="host-port-field" style="display:none;">
                            <label class="block text-sm font-medium text-gray-700">Port</label>
                            <input type="number" name="port" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('port') }}" placeholder="80">
                        </div>
                        <div id="domain-field" style="display:none;">
                            <label class="block text-sm font-medium text-gray-700">Domaine</label>
                            <input type="text" name="domain" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('domain') }}" placeholder="example.com">
                            @error('domain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Intervalle (secondes)</label>
                            <input type="number" name="interval" value="{{ old('interval', 60) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('interval')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Timeout (secondes)</label>
                            <input type="number" name="timeout" value="{{ old('timeout', 10) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('timeout')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Groupe (optionnel)</label>
                            <input type="text" name="group" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('group') }}" placeholder="Production, Staging, etc.">
                        </div>
                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('monitors.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Annuler</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Créer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

