<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            'name' => config('app.name'),

            'auth' => [
                'user' => $user
                    ? [
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name,

                        // Role Spatie
                        'roles' => $user->getRoleNames()->values(),

                        // Permission Spatie
                        'permissions' => $user
                            ->getAllPermissions()
                            ->pluck('name')
                            ->values(),
                    ]
                    : null,
            ],
        ];
    }
}