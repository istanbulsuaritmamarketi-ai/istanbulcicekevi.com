<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\UtmTracking::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            'paytr/callback',
        ]);

        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // 419 Page Expired (CSRF) Hatasını Yakala ve Kullanıcıyı Beyaz Ekrana Düşürmeden Geri Yönlendir
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->is('login')) {
                    return redirect()->route('login')->with('error', 'Oturum süreniz doldu, lütfen tekrar giriş yapın.');
                }
                return redirect()->back()->with('error', 'Sayfa oturumu yenilendi, lütfen işlemi tekrar deneyin.');
            }
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->is('login')) {
                return redirect()->route('login')->with('error', 'Oturum süreniz doldu, lütfen tekrar giriş yapın.');
            }
            return redirect()->back()->with('error', 'Sayfa oturumu yenilendi, lütfen işlemi tekrar deneyin.');
        });
    })->create();
