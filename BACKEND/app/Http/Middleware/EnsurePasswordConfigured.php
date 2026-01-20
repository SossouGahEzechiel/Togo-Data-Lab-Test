<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordConfigured
{
	protected $except = [
		'api/auth/configure-password',
		'api/auth/logout',
	];

	public function handle(Request $request, Closure $next): Response
	{
		if (!auth()->check()) {
			return response()->json([
				'message' => 'Non authentifié',
			], 401);
		}

		// Vérifier si la route actuelle est dans les exceptions
		if ($this->inExceptArray($request)) {
			return $next($request);
		}

		// Vérifier si le mot de passe a été configuré
		if (!$request->user()->has_configured_password) {
			return _403('Vous devez configurer votre mot de passe avant de continuer');
		}

		return $next($request);
	}

	protected function inExceptArray(Request $request): bool
	{
		foreach ($this->except as $except) {
			if ($except !== '/') {
				$except = trim($except, '/');
			}

			if ($request->is($except)) {
				return true;
			}
		}

		return false;
	}
}
