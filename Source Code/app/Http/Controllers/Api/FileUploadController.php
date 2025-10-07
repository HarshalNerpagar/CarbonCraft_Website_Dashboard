<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload files (images or audio) for order customization
     * Files are uploaded BEFORE order creation and linked later
     */
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'files' => 'required|array|max:5',
                'files.*' => 'required|file|mimes:jpg,jpeg,png,webp,mp3,wav,m4a,ogg,webm,mpeg|max:20480', // 20MB max
                'type' => 'required|in:image,audio'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $uploadedFiles = [];
            $type = $request->input('type');

            foreach ($request->file('files') as $file) {
                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(40) . '.' . $extension;

                // Store file in public disk under order_attachments/{type}s
                $path = $file->storeAs(
                    "order_attachments/{$type}s",
                    $filename,
                    'public'
                );

                // Create attachment record (without order_id for now)
                $attachment = OrderAttachment::create([
                    'order_id' => null, // Will be linked when order is created
                    'type' => $type,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => 'customer'
                ]);

                $uploadedFiles[] = [
                    'id' => $attachment->id,
                    'path' => $path,
                    'url' => Storage::url($path),
                    'original_name' => $attachment->original_name,
                    'size' => $attachment->file_size,
                    'human_size' => $attachment->human_file_size
                ];
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . '(s) uploaded successfully',
                'files' => $uploadedFiles
            ], 201);

        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'File upload failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Link uploaded files to an order
     * Called after order creation to associate files with order_id
     */
    public function linkToOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'attachment_ids' => 'required|array',
                'attachment_ids.*' => 'required|exists:order_attachments,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $orderId = $request->input('order_id');
            $attachmentIds = $request->input('attachment_ids');

            // Update all attachments to link them to the order
            OrderAttachment::whereIn('id', $attachmentIds)
                ->whereNull('order_id') // Only link if not already linked
                ->update(['order_id' => $orderId]);

            $linkedCount = OrderAttachment::where('order_id', $orderId)
                ->whereIn('id', $attachmentIds)
                ->count();

            // Get the linked files with URLs for Telegram
            $linkedFiles = OrderAttachment::where('order_id', $orderId)
                ->whereIn('id', $attachmentIds)
                ->get();

            $imageUrls = [];
            $audioUrls = [];

            foreach ($linkedFiles as $file) {
                $fullUrl = url('storage/' . $file->file_path);
                if ($file->type === 'image') {
                    $imageUrls[] = $fullUrl;
                } else if ($file->type === 'audio') {
                    $audioUrls[] = $fullUrl;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$linkedCount} file(s) linked to order successfully",
                'file_urls' => [
                    'images' => $imageUrls,
                    'audio' => $audioUrls
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('File linking error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to link files to order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get files for a specific order
     */
    public function getOrderFiles($orderId)
    {
        try {
            $attachments = OrderAttachment::where('order_id', $orderId)->get();

            return response()->json([
                'success' => true,
                'attachments' => $attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'type' => $attachment->type,
                        'url' => $attachment->url,
                        'original_name' => $attachment->original_name,
                        'mime_type' => $attachment->mime_type,
                        'size' => $attachment->human_file_size,
                        'uploaded_at' => $attachment->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve files',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an uploaded file
     */
    public function delete($attachmentId)
    {
        try {
            $attachment = OrderAttachment::findOrFail($attachmentId);

            // Delete the physical file
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }

            // Delete the database record
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete file',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
