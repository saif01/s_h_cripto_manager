<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HeaderSecurityMiddleware
{
  
    private $unwantedHeaderList = [
        'X-Powered-By',
        'Server',
    ];
    public function handle($request, Closure $next)
    {
         $this->removeUnwantedHeaders($this->unwantedHeaderList);
        $response = $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade', 'origin-when-cross-origin', 'origin-when-cross-origin');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Strict-Transport-Security', 'max-age=63072000', 'includeSubDomains', 'preload');
        $response->headers->set('Permissions-Policy', "accelerometer=(self), ambient-light-sensor=(self), autoplay=(self), battery=(self), camera=(self), cross-origin-isolated=(self), display-capture=(self), document-domain=*, encrypted-media=(self), execution-while-not-rendered=*, execution-while-out-of-viewport=*, fullscreen=(self), geolocation=(self), gyroscope=(self), magnetometer=(self), microphone=(self), midi=(self), navigation-override=(self), payment=(self), picture-in-picture=*, publickey-credentials-get=(self), screen-wake-lock=(self), sync-xhr=*, usb=(self), web-share=(self), xr-spatial-tracking=(self)");
        
        // $response->headers->set('Content-Security-Policy', 'upgrade-insecure-requests'); 
       
        // $response->headers->set("Content-Security-Policy", "default-src 'self' ;script-src 'self' 'unsafe-eval' ;style-src 'self' 'unsafe-eval' 'unsafe-inline' *.google.com *.googleapis.com ;img-src 'self' data: https: ;report-to csp-report ;object-src 'none' ;frame-src *.google.com ");
        
       
        // $response->headers->set('Content-Security-Policy', "default-src *; script-src * 'self'; style-src * 'self'");
        // $response->headers->set('Content-Security-Policy', "default-src *; script-src * 'self' 'unsafe-eval' 'unsafe-inline'; style-src * 'self' 'unsafe-eval' 'unsafe-inline' *.admin.cpbangladesh.com  *.google.com *.fonts.googleapis.com; img-src * 'self' data: https:;");

        $response->headers->set('Content-Security-Policy', "default-src *; script-src * 'self';");
        $response->headers->set('Content-Security-Policy', "script-src 'self' 'unsafe-eval' *.googleapis.com *.gstatic.com; style-src 'self' 'unsafe-eval' 'unsafe-inline' *.googleapis.com *.google.com *.gstatic.com img-src 'self' data: w3.org/svg/2000;");
        $response->headers->set('Content-Security-Policy', "report-to csp-report ;object-src 'none';");
        $response->headers->set("Content-Security-Policy", "frame-src *.google.com *.youtube.com");
        
        return $response;
    }
    private function removeUnwantedHeaders($headerList)
    {
        foreach ($headerList as $header)
            header_remove($header);
    }
            
}
