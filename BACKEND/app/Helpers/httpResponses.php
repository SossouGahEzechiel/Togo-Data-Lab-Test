<?php

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

if (!function_exists("_404")) {
	/**
	 * Returns a 404 JSON response with a specified message.
	 *
	 * @param string $message The message to include in the response
	 * @return JsonResponse A JSON response with a 404 status code
	 */
	function _404(string $message = 'Resource not found'): JsonResponse
	{
		return response()->json(['message' => $message], SymfonyResponse::HTTP_NOT_FOUND);
	}
}

if (!function_exists("_400")) {
	/**
	 * Returns a 404 JSON response with a specified message.
	 *
	 * @param string $message The message to include in the response
	 * @return JsonResponse A JSON response with a 400 status code
	 */
	function _400(string $message = 'Requête invalide'): JsonResponse
	{
		return response()->json(['message' => $message], SymfonyResponse::HTTP_BAD_REQUEST);
	}
}

if (!function_exists("_403")) {
	/**
	 * Returns a 404 JSON response with a specified message.
	 *
	 * @param string $message The message to include in the response
	 * @return JsonResponse A JSON response with a 403 status code
	 */
	function _403(string $message = 'Accès refusé.'): JsonResponse
	{
		return response()->json(['message' => $message], SymfonyResponse::HTTP_FORBIDDEN);
	}
}

if (!function_exists("_500")) {
	/**
	 * Returns a 500 JSON response with a specified message.
	 *
	 * @param string $message The message to include in the response
	 * @return JsonResponse A JSON response with a 500 status code
	 */
	function _500(string $message = 'An expected error occurred on the server'): JsonResponse
	{
		return response()->json(['message' => $message], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
	}
}

if (!function_exists("_200")) {
	/**
	 * Returns a 500 JSON response with a specified message.
	 *
	 * @param string $message The message to include in the response
	 * @return JsonResponse A JSON response with a 500 status code
	 */
	function _200(string $message = 'Requête exécutée avec succès'): JsonResponse
	{
		return response()->json(['message' => $message], SymfonyResponse::HTTP_OK);
	}
}
