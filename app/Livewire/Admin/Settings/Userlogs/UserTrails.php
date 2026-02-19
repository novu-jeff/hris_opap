<?php

namespace App\Livewire\Admin\Settings\Userlogs;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;

class UserTrails extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap'; // optional

    public $perPage = 10;

    public function getTrails()
    {
        $directory = storage_path('logs/trails');

        if (!File::exists($directory)) {
            return collect([]);
        }

        $files = File::files($directory);

        return collect($files)
            ->sortByDesc(fn ($file) => $file->getFilename())
            ->map(function ($file) {
                return [
                    'filename' => $file->getFilename(),
                    'size' => number_format($file->getSize() / 1024, 2),
                    'updated_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            });
    }

    public function render()
    {
        $all = $this->getTrails();

        // Manual pagination
        $page = $this->getPage();
        $perPage = $this->perPage;

        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $all->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('livewire.admin.settings.userlogs.user-trails', [
            'trails' => $paginated,
        ]);
    }

    public function viewLog($filename)
    {
        $filename = basename($filename);
        if ($filename === '' || preg_match('/[\/\\\\]/', $filename)) {
            return session()->flash('error', 'Invalid filename.');
        }
        $baseDir = realpath(storage_path('logs/trails'));
        if ($baseDir === false) {
            return session()->flash('error', 'Log file not found.');
        }
        $path = $baseDir . DIRECTORY_SEPARATOR . $filename;
        $realPath = realpath($path);
        if ($realPath === false || !str_starts_with($realPath, $baseDir)) {
            return session()->flash('error', 'Log file not found.');
        }

        $content = file_get_contents($realPath);

        return response()->streamDownload(
            fn () => print($content),
            $filename
        );
    }

    public function downloadLog($filename)
    {
        $filename = basename($filename);
        if ($filename === '' || preg_match('/[\/\\\\]/', $filename)) {
            return session()->flash('error', 'Invalid filename.');
        }
        $baseDir = realpath(storage_path('logs/trails'));
        if ($baseDir === false) {
            return session()->flash('error', 'File not found.');
        }
        $path = $baseDir . DIRECTORY_SEPARATOR . $filename;
        $realPath = realpath($path);
        if ($realPath === false || !str_starts_with($realPath, $baseDir)) {
            return session()->flash('error', 'File not found.');
        }

        return response()->download($realPath, $filename);
    }
}
