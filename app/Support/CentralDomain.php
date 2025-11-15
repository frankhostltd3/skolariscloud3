<?php

namespace App\Support;

use Illuminate\Http\Request;

class CentralDomain
{
    public static function base(?Request $request = null): ?string
    {
        $request ??= app()->bound('request') ? request() : null;

        $central = self::normalize(config('tenancy.central_domain'));
        $host = $request?->getHost();
        $appHost = self::normalize(parse_url(config('app.url'), PHP_URL_HOST) ?: null);

        if ($host && self::isLocalHost($host)) {
            return 'localhost';
        }

        if ($central && ! self::isLocalHost($central)) {
            return $central;
        }

        if ($host) {
            $normalizedHost = self::normalize($host);

            if ($normalizedHost) {
                return $normalizedHost;
            }
        }

        if ($central) {
            return $central;
        }

        if ($appHost) {
            if (self::isLocalHost($appHost)) {
                return 'localhost';
            }

            return $appHost;
        }

        return null;
    }

    public static function tenantDomain(string $subdomain, ?Request $request = null): ?string
    {
        $base = self::base($request);

        return $base ? $subdomain . '.' . $base : null;
    }

    public static function scheme(?Request $request = null): string
    {
        $request ??= app()->bound('request') ? request() : null;

        if ($request) {
            $host = $request->getHost();
            $scheme = $request->getScheme();

            if (self::isLocalHost($host)) {
                return 'http';
            }

            return $scheme ?: 'https';
        }

        $appUrl = config('app.url');
        $parsedScheme = $appUrl ? parse_url($appUrl, PHP_URL_SCHEME) : null;

        if ($parsedScheme) {
            return $parsedScheme;
        }

        return 'https';
    }

    public static function port(?Request $request = null): ?int
    {
        $request ??= app()->bound('request') ? request() : null;

        if ($request) {
            $scheme = $request->getScheme();
            $port = $request->getPort();

            if (! $port) {
                return null;
            }

            if ($scheme === 'https' && $port === 443) {
                return null;
            }

            if ($scheme === 'http' && $port === 80) {
                return null;
            }

            return $port;
        }

        $appUrl = config('app.url');
        $parsedPort = $appUrl ? parse_url($appUrl, PHP_URL_PORT) : null;

        return $parsedPort ?: null;
    }

    public static function isLocalHost(?string $host): bool
    {
        if (! $host) {
            return false;
        }

        $host = strtolower($host);

        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            return true;
        }

        return str_ends_with($host, '.localhost');
    }

    public static function normalize(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (str_contains($value, '://')) {
            $parsed = parse_url($value, PHP_URL_HOST);
            $value = $parsed ?: $value;
        }

        return ltrim($value, '.');
    }
}
