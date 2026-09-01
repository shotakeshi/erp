<?php
    use App\Exceptions\ServiceException;
    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;
    
    return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReportDuplicates();
        $exceptions->dontReport([
            ServiceException::class,
        ]);
        $exceptions->render(function (ServiceException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], Response::HTTP_CONFLICT);
            }
            
            return back()->with('error', $e->getMessage());
        });
    })
    ->withEvents(discover: [
        app_path('Listeners'),
    ])->create();
