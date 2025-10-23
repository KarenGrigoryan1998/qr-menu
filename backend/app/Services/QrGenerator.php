<?php

namespace App\Services;

use App\Models\Table as DiningTable;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrGenerator
{
    /**
     * Generate a QR code PNG for a table URL under public/qrcodes and return relative path.
     * Uses simplesoftwareio/simple-qrcode if installed; otherwise returns null.
     */
    public function generateForTable(DiningTable $table, int $size = 1024): ?string
    {
        $url = $table->qr_code_url;
        if (empty($url)) {
            return null;
        }

        $publicPath = public_path('qrcodes');
        if (! File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        $filename = "table-{$table->id}-{$size}.png";
        $fullPath = $publicPath . DIRECTORY_SEPARATOR . $filename;

        // Prefer Simple QrCode if available
        if (! class_exists(QrCode::class)) {
            return null; // package not installed
        }

        QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($url, $fullPath);

        // Return relative path from public root, e.g., qrcodes/table-1-1024.png
        return 'qrcodes/' . $filename;
    }

    public function getThumbUrl(DiningTable $table): ?string
    {
        if (empty($table->qr_code_url)) {
            return null;
        }
        $thumbRel = $table->qr_code_filename && str_contains($table->qr_code_filename, '-256')
            ? $table->qr_code_filename
            : ('qrcodes/table-' . $table->id . '-256.png');
        $thumbFull = public_path($thumbRel);
        if (! File::exists($thumbFull)) {
            $this->generateForTable($table, 256);
        }
        if (File::exists($thumbFull)) {
            $t = optional($table->updated_at)->timestamp ?? time();
            return asset($thumbRel) . '?t=' . $t;
        }
        return null;
    }

    public function getDownloadUrl(DiningTable $table): ?string
    {
        if (empty($table->qr_code_url)) {
            return null;
        }
        $size = (int)($table->qr_code_size ?? 1024);
        $rel = $table->qr_code_filename ?: ('qrcodes/table-' . $table->id . '-' . $size . '.png');
        $full = public_path($rel);
        if (! File::exists($full)) {
            $this->generateForTable($table, $size);
        }
        if (File::exists($full)) {
            $t = optional($table->updated_at)->timestamp ?? time();
            return asset($rel) . '?t=' . $t;
        }
        return null;
    }
}
