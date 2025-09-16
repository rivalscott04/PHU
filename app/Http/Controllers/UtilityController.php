<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UtilityController extends Controller
{
	public function storageLink()
	{
		$publicStoragePath = public_path('storage');
		$targetPath = storage_path('app/public');

		try {
			// If the link already exists and is valid, return success
			if (is_link($publicStoragePath) || is_dir($publicStoragePath)) {
				return response()->json([
					'success' => true,
					'message' => 'Public storage already linked.',
				]);
			}

			// Try Laravel's artisan command first
			Artisan::call('storage:link');

			if (is_link($publicStoragePath) || is_dir($publicStoragePath)) {
				return response()->json([
					'success' => true,
					'message' => 'Storage link created via artisan.',
				]);
			}

			// Fallback: attempt to create symlink manually (common for cPanel)
			$relativeTarget = '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
			@symlink($relativeTarget, $publicStoragePath);

			if (is_link($publicStoragePath) || is_dir($publicStoragePath)) {
				return response()->json([
					'success' => true,
					'message' => 'Storage link created via symlink fallback.',
				]);
			}

			return response()->json([
				'success' => false,
				'message' => 'Failed to create storage link. Check hosting permissions.',
			], 500);
		} catch (\Throwable $e) {
			Log::error('Storage link error', [
				'error' => $e->getMessage(),
			]);

			return response()->json([
				'success' => false,
				'message' => 'Error: ' . $e->getMessage(),
			], 500);
		}
	}
}
