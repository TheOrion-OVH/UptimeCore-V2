<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UptimeCore - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">UptimeCore</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/dashboard" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                        <a href="/monitors" class="text-gray-700 hover:text-gray-900">Monitors</a>
                        <a href="/incidents" class="text-gray-700 hover:text-gray-900">Incidents</a>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Total Monitors</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900" id="total-monitors">-</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Monitors UP</div>
                        <div class="mt-2 text-3xl font-bold text-green-600" id="monitors-up">-</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Monitors DOWN</div>
                        <div class="mt-2 text-3xl font-bold text-red-600" id="monitors-down">-</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Uptime Global</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900" id="overall-uptime">-</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Monitors</h3>
                    </div>
                    <div class="p-6">
                        <div id="monitors-list">
                            <p class="text-gray-500">Chargement...</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Charger les statistiques
        fetch('/api/dashboard/stats', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-monitors').textContent = data.data.total_monitors;
            document.getElementById('monitors-up').textContent = data.data.monitors_up;
            document.getElementById('monitors-down').textContent = data.data.monitors_down;
            document.getElementById('overall-uptime').textContent = data.data.overall_uptime + '%';
        });

        // Charger les monitors
        fetch('/api/monitors', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('monitors-list');
            if (data.data.length === 0) {
                list.innerHTML = '<p class="text-gray-500">Aucun monitor configuré</p>';
            } else {
                list.innerHTML = data.data.map(monitor => `
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div>
                            <div class="font-medium text-gray-900">${monitor.name}</div>
                            <div class="text-sm text-gray-500">${monitor.type} - ${monitor.url || monitor.host || monitor.domain}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${
                                monitor.status === 'up' ? 'bg-green-100 text-green-800' :
                                monitor.status === 'down' ? 'bg-red-100 text-red-800' :
                                'bg-gray-100 text-gray-800'
                            }">
                                ${monitor.status.toUpperCase()}
                            </span>
                            ${monitor.response_time ? `<span class="text-sm text-gray-500">${monitor.response_time}ms</span>` : ''}
                        </div>
                    </div>
                `).join('');
            }
        });
    </script>
</body>
</html>

