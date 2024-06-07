<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePayFastIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->pfValidIP($request)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }

    protected function pfValidIP(Request $request): bool
    {
        $validHosts = [
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        ];

        $validIps = [];
        foreach ($validHosts as $pfHostname) {
            $ips = gethostbynamel($pfHostname);
            if ($ips !== false) {
                $validIps = array_merge($validIps, $ips);
            }
        }

        // Add the server's IP to the list of valid IPs
        $serverIp = $request->ip();
        if ($serverIp) {
            $validIps[] = $serverIp;
        }

        $validIps = array_unique($validIps);

        $referrerHost = parse_url($request->headers->get('referer'), PHP_URL_HOST);
        $referrerIp = gethostbyname($referrerHost);

        return in_array($referrerIp, $validIps, true);
    }
}
