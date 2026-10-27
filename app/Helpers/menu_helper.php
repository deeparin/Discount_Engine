<?php

if (!function_exists('is_active_route_prefix')) {

    function is_active_route_prefix(array|string $prefixes): string
    {
        $currentRoute = service('router')->getMatchedRouteOptions()['as'] ?? '';

        $prefixes = (array) $prefixes;
        
        foreach ($prefixes as $prefix) {
            if (str_starts_with($currentRoute, $prefix)) {
                return 'active';
            }
        }

        return '';
    }
}