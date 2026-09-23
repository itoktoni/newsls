<?php

namespace App\Flasher;

/**
 * Lightweight flash message helper.
 * Stores toast messages in the session for rendering on the next request.
 *
 * Usage:
 *   flash()->success('Data berhasil disimpan');
 *   flash()->error('Gagal menyimpan data');
 *   flash()->warning('Perhatian');
 *   flash()->info('Informasi');
 */
class Flash
{
    /**
     * Push a toast to the session.
     */
    protected function push(?string $message, string $type): static
    {
        if ($message === null || $message === '') {
            return $this;
        }

        $toasts = session('toasts', []);
        $toasts[] = [
            'message' => $message,
            'type' => $type,
            'heading' => null,
            'duration' => 5000,
        ];
        session()->flash('toasts', $toasts);

        return $this;
    }

    public function success(?string $message): static
    {
        return $this->push($message, 'success');
    }

    public function error(?string $message): static
    {
        return $this->push($message, 'danger');
    }

    public function danger(?string $message): static
    {
        return $this->push($message, 'danger');
    }

    public function warning(?string $message): static
    {
        return $this->push($message, 'warning');
    }

    public function info(?string $message): static
    {
        return $this->push($message, 'info');
    }

    /**
     * Push a raw toast with explicit type.
     */
    public function raw(?string $message, string $type = 'success', ?string $heading = null): static
    {
        return $this->push($message, $type);
    }
}
