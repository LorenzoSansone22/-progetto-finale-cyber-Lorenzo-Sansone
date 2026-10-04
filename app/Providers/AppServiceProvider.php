<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function (Request $request) {
                Cache::put('blocked_ip:' . $request->ip(), true, now()->addMinutes(10));

                return response('Too many requests. IP temporarily blocked.', 429);
            });
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        Event::listen(Login::class, function ($event) {
            Log::info('Login', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Logout::class, function ($event) {
            Log::info('Logout', [
                'user_id' => $event->user?->id,
                'email' => $event->user?->email,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Registered::class, function ($event) {
            Log::info('Registrazione', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
            ]);
        });

        if (Schema::hasTable('categories')) {
            $categories = Category::all();
            View::share(['categories' => $categories]);
        }

        if (Schema::hasTable('tags')) {
            $tags = Tag::all();
            View::share(['tags' => $tags]);
        }
    }
}