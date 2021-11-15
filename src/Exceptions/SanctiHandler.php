<?php

namespace Sancti\Exceptions;

use Throwable;
use Exception;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class SanctiHandler extends ExceptionHandler
{
	// A list of the exception types that are not reported.
	protected $dontReport = [];

	// A list of the inputs that are never flashed for validation exceptions.
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	// Register the exception handling callbacks for the application.
	public function register()
	{
		$this->reportable(function (Throwable $e) {
			//
		});

		$this->renderable(function (NotFoundHttpException $e, $request) {
			if ($request->is('api/*')) {
				return response()->json(['message' => 'Record not found.'], 404);
			}
		});

		$this->renderable(function (PostTooLargeException $e, $request) {
			if ($request->is('api/*')) {
				return response()->json(['message' => 'Post data too large.'], 422);
			}
		});

		$this->renderable(function (HttpResponseException $e, $request) {
			if ($request->is('api/*')) {
				return response()->json(['message' => 'Invalid response.'], 422);
			}
		});

		$this->renderable(function (ThrottleRequestsException $e, $request) {
			if ($request->is('api/*')) {
				return response()->json(['message' => 'Too many requests.'], 422);
			}
		});

		$this->renderable(function (RouteNotFoundException $e, $request) {
			if ($request->is('api/*')) {
				return response()->json(['message' => 'Unauthorized route.'], 404);
			}
		});

		$this->renderable(function (Exception $e, $request) {
			if ($request->is('api/*')) {
				return response()->json(['message' => $e->getMessage()], 402);
			}
		});
	}

	public function render($request, Throwable $e)
	{
		// if ($e instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
		// 	// Redirect to url: /redirect/error
		// 	// then with errors back to upload form url: /galeries/create
		// 	// return redirect()->to(route('upload.error'));
		// }

		// Force an application/json rendering on API calls for error page
		// if ($request->is('api/*')) {
		// 	$request->headers->set('Accept', 'application/json');
		// 	return response()->json([
		// 		'error' => 'Unauthorized.',
		// 		'message' => __($e->getMessage()) ?? 'Invalid route'
		// 	], 402);
		// }

		return parent::render($request, $e);
	}
}