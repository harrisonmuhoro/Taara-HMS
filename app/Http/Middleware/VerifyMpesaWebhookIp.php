<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMpesaWebhookIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = config('services.mpesa.webhook_ips', []);

        if ($allowed === [] || ! $this->matches($request->ip(), $allowed)) {
            abort(403, 'Webhook source is not authorized.');
        }

        return $next($request);
    }

    private function matches(?string $ip, array $allowed): bool
    {
        if (! $ip) {
            return false;
        }

        foreach ($allowed as $candidate) {
            if ($ip === $candidate) {
                return true;
            }

            if (str_contains($candidate, '/') && filter_var($ip, FILTER_VALIDATE_IP)) {
                [$network, $bits] = explode('/', $candidate, 2);
                if (! filter_var($network, FILTER_VALIDATE_IP) || ! ctype_digit($bits)) {
                    continue;
                }

                $ipBinary = inet_pton($ip);
                $networkBinary = inet_pton($network);
                if ($ipBinary === false || $networkBinary === false || strlen($ipBinary) !== strlen($networkBinary)) {
                    continue;
                }

                $bytes = intdiv((int) $bits, 8);
                $remainder = (int) $bits % 8;
                if (substr($ipBinary, 0, $bytes) !== substr($networkBinary, 0, $bytes)) {
                    continue;
                }
                if ($remainder === 0 || (ord($ipBinary[$bytes]) >> (8 - $remainder)) === (ord($networkBinary[$bytes]) >> (8 - $remainder))) {
                    return true;
                }
            }
        }

        return false;
    }
}
