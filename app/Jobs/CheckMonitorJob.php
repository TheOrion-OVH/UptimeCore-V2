<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\Heartbeat;
use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckMonitorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Monitor $monitor)
    {
        $this->onQueue('monitoring');
    }

    public function handle(): void
    {
        if ($this->monitor->isPaused()) {
            return;
        }

        // Vérifier si une maintenance est en cours
        $inMaintenance = $this->monitor->maintenances()
            ->where('status', 'in_progress')
            ->where('disable_alerts', true)
            ->exists();

        if ($inMaintenance) {
            return;
        }

        $result = match ($this->monitor->type) {
            'http' => $this->checkHttp(),
            'ping' => $this->checkPing(),
            'tcp' => $this->checkTcp(),
            'dns' => $this->checkDns(),
            'ssl' => $this->checkSsl(),
            default => null,
        };

        if ($result === null) {
            return;
        }

        $this->saveHeartbeat($result);
        $this->updateMonitorStatus($result);
        $this->handleIncidents($result);
    }

    private function checkHttp(): array
    {
        try {
            $startTime = microtime(true);
            
            $request = Http::timeout($this->monitor->timeout)
                ->withHeaders($this->monitor->headers ?? []);

            if (!$this->monitor->follow_redirects) {
                $request->withoutRedirecting();
            }

            $response = $request->send($this->monitor->method, $this->monitor->url);

            $responseTime = round((microtime(true) - $startTime) * 1000);
            $statusCode = $response->status();

            $isUp = $statusCode === $this->monitor->expected_status_code;

            return [
                'status' => $isUp ? 'up' : 'down',
                'response_time' => $responseTime,
                'status_code' => $statusCode,
                'error_message' => $isUp ? null : "HTTP {$statusCode}",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function checkPing(): array
    {
        // Note: ICMP ping nécessite des permissions root ou des outils système
        // Pour l'instant, on simule avec un check TCP sur le port 22 (SSH) ou autre
        try {
            $startTime = microtime(true);
            $connection = @fsockopen($this->monitor->host, 22, $errno, $errstr, $this->monitor->timeout);
            
            if ($connection) {
                fclose($connection);
                $responseTime = round((microtime(true) - $startTime) * 1000);
                
                return [
                    'status' => 'up',
                    'response_time' => $responseTime,
                    'status_code' => null,
                    'error_message' => null,
                ];
            }

            return [
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => $errstr ?? 'Connection failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function checkTcp(): array
    {
        try {
            $startTime = microtime(true);
            $connection = @fsockopen(
                $this->monitor->host,
                $this->monitor->port,
                $errno,
                $errstr,
                $this->monitor->timeout
            );

            if ($connection) {
                fclose($connection);
                $responseTime = round((microtime(true) - $startTime) * 1000);

                return [
                    'status' => 'up',
                    'response_time' => $responseTime,
                    'status_code' => null,
                    'error_message' => null,
                ];
            }

            return [
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => $errstr ?? 'Connection failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function checkDns(): array
    {
        try {
            $startTime = microtime(true);
            $records = dns_get_record($this->monitor->domain, $this->getDnsRecordType());
            $responseTime = round((microtime(true) - $startTime) * 1000);

            if (empty($records)) {
                return [
                    'status' => 'down',
                    'response_time' => $responseTime,
                    'status_code' => null,
                    'error_message' => 'No DNS records found',
                ];
            }

            if ($this->monitor->expected_value) {
                $found = false;
                foreach ($records as $record) {
                    if (isset($record[$this->getDnsRecordKey()]) && 
                        $record[$this->getDnsRecordKey()] === $this->monitor->expected_value) {
                        $found = true;
                        break;
                    }
                }

                return [
                    'status' => $found ? 'up' : 'down',
                    'response_time' => $responseTime,
                    'status_code' => null,
                    'error_message' => $found ? null : 'Expected value not found',
                ];
            }

            return [
                'status' => 'up',
                'response_time' => $responseTime,
                'status_code' => null,
                'error_message' => null,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function checkSsl(): array
    {
        try {
            $url = parse_url($this->monitor->url);
            $host = $url['host'] ?? $url['path'];
            $port = $url['port'] ?? 443;

            $startTime = microtime(true);
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $socket = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                $this->monitor->timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                return [
                    'status' => 'down',
                    'response_time' => null,
                    'status_code' => null,
                    'error_message' => $errstr ?? 'SSL connection failed',
                ];
            }

            $params = stream_context_get_params($socket);
            $cert = $params['options']['ssl']['peer_certificate'];
            $certData = openssl_x509_parse($cert);
            $validTo = $certData['validTo_time_t'];
            $daysUntilExpiry = floor(($validTo - time()) / 86400);

            fclose($socket);
            $responseTime = round((microtime(true) - $startTime) * 1000);

            $isUp = $daysUntilExpiry > $this->monitor->days_before_alert;

            return [
                'status' => $isUp ? 'up' : 'down',
                'response_time' => $responseTime,
                'status_code' => null,
                'error_message' => $isUp ? null : "SSL certificate expires in {$daysUntilExpiry} days",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function getDnsRecordType(): int
    {
        return match ($this->monitor->record_type) {
            'A' => DNS_A,
            'AAAA' => DNS_AAAA,
            'CNAME' => DNS_CNAME,
            'MX' => DNS_MX,
            'TXT' => DNS_TXT,
            default => DNS_A,
        };
    }

    private function getDnsRecordKey(): string
    {
        return match ($this->monitor->record_type) {
            'A' => 'ip',
            'AAAA' => 'ipv6',
            'CNAME' => 'target',
            'MX' => 'target',
            'TXT' => 'txt',
            default => 'ip',
        };
    }

    private function saveHeartbeat(array $result): void
    {
        Heartbeat::create([
            'monitor_id' => $this->monitor->id,
            'status' => $result['status'],
            'response_time' => $result['response_time'],
            'status_code' => $result['status_code'],
            'error_message' => $result['error_message'],
            'checked_at' => now(),
        ]);
    }

    private function updateMonitorStatus(array $result): void
    {
        $wasUp = $this->monitor->isUp();
        $isUp = $result['status'] === 'up';

        $updateData = [
            'status' => $result['status'],
            'response_time' => $result['response_time'],
            'last_check_at' => now(),
        ];

        if ($isUp) {
            $this->monitor->increment('successful_checks');
        } else {
            $this->monitor->increment('failed_checks');
        }

        $this->monitor->increment('total_checks');

        // Recalculer l'uptime
        $uptimePercentage = $this->monitor->total_checks > 0
            ? ($this->monitor->successful_checks / $this->monitor->total_checks) * 100
            : 100;

        $updateData['uptime_percentage'] = round($uptimePercentage, 2);

        $this->monitor->update($updateData);

        // Nettoyer les anciens heartbeats (garder seulement 90 jours)
        Heartbeat::where('monitor_id', $this->monitor->id)
            ->where('checked_at', '<', now()->subDays(90))
            ->delete();
    }

    private function handleIncidents(array $result): void
    {
        $wasUp = $this->monitor->was('status', 'up');
        $isUp = $result['status'] === 'up';

        if (!$wasUp && $isUp) {
            // Monitor est revenu UP, résoudre l'incident
            $incident = Incident::whereHas('monitors', function ($q) {
                $q->where('monitors.id', $this->monitor->id);
            })
            ->where('status', '!=', 'resolved')
            ->first();

            if ($incident) {
                $incident->resolve();
                
                // Envoyer des notifications
                $this->sendNotifications('up', $result);
            }
        } elseif ($wasUp && !$isUp) {
            // Monitor est DOWN, créer un incident
            $incident = Incident::create([
                'user_id' => $this->monitor->user_id,
                'title' => "{$this->monitor->name} is DOWN",
                'impact' => 'major',
                'status' => 'investigating',
                'started_at' => now(),
            ]);

            $incident->monitors()->attach($this->monitor->id);
            
            // Envoyer des notifications
            $this->sendNotifications('down', $result);
        }
    }

    private function sendNotifications(string $event, array $result): void
    {
        $channels = $this->monitor->notificationChannels()
            ->wherePivot('notify_on_' . $event, true)
            ->where('enabled', true)
            ->get();

        foreach ($channels as $channel) {
            // TODO: Implémenter l'envoi de notifications
            // dispatch(new SendNotificationJob($channel, $this->monitor, $event, $result));
        }
    }
}

