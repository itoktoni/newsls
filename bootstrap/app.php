<?php

use App\Http\Middleware\AccessMiddleware;
use App\Http\Middleware\EnsureRsAccess;
use App\Http\Middleware\VerifyVerified;
use App\Providers\ModelAliasServiceProvider;
use Ibex\CrudGenerator\CrudServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Milon\Barcode\BarcodeServiceProvider;
use Plugins\Notes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        ModelAliasServiceProvider::class,
        CrudServiceProvider::class,
        BarcodeServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'access' => AccessMiddleware::class,
            'verified' => VerifyVerified::class,
            'rs.access' => EnsureRsAccess::class,
            // 'skip_verified' => SkipVerifiedCheck::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'wms/forklift/*',
        ]);

        $middleware->append([
            HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Semua respons error untuk request API memakai envelope Plugins\Notes
        // supaya bentuknya identik dengan respons sukses (lihat plugins/Notes.php).
        // HTTP status-nya tetap 200, kode aslinya ada di body.code.
        $isApi = fn (Request $request): bool => $request->expectsJson() || $request->is('api/*');

        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $isApi($request));

        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                // Pesan pertama yang paling relevan untuk desktop (sudah Indonesia via lang/id)
                $first = $e->validator->errors()->first() ?: 'Data yang diberikan tidak valid.';

                return Notes::validation(
                    $first,
                    $e->validator->errors()->getMessages()
                );
            }

            // Web: keep Laravel's default redirect-back (inline field errors)
            // but also push a toast notification so validation failures are visible.
            if (! $e->validator->errors()->isEmpty()) {
                flash()->error($e->validator->errors()->first());
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return Notes::failed(401, 'Unauthenticated.');
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return Notes::notFound();
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return Notes::failed(405, 'Method tidak diizinkan untuk url ini.');
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return Notes::failed(429, 'Terlalu banyak permintaan. Coba lagi nanti.');
            }
        });

        // Sisa HttpException lain (mis. 403 Forbidden) ikut envelope yang sama.
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return Notes::failed($e->getStatusCode(), $e->getMessage());
            }
        });

        // Jaring pengaman terakhir. Didaftarkan paling akhir supaya tipe yang lebih
        // spesifik di atas selalu menang. Tidak memanggil report() lagi — Laravel
        // sudah melaporkan exception sebelum tahap render.
        $exceptions->render(function (Throwable $e, Request $request) use ($isApi) {
            if ($isApi($request)) {
                return Notes::failed(500, config('app.debug')
                    ? $e->getMessage()
                    : 'Terjadi kesalahan pada server.');
            }
        });
    })->create();
