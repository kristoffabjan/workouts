<?php

namespace App\Livewire;

use App\Models\AccessRequest;
use App\Models\User;
use App\Notifications\AccessRequestNotification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RequestAccess extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $message = null;

    public bool $submitted = false;

    public function submit(): void
    {
        $this->checkRateLimit();

        $this->validate();

        $this->validateEmailUnique();

        $accessRequest = AccessRequest::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ]);

        $this->notifyAdmins($accessRequest);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.request-access')
            ->layout('layouts.auth', ['title' => __('auth.request_access.title')]);
    }

    protected function checkRateLimit(): void
    {
        $key = 'request-access:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => __('auth.request_access.rate_limited', ['seconds' => $seconds]),
            ]);
        }

        RateLimiter::hit($key, 3600);
    }

    protected function validateEmailUnique(): void
    {
        if (AccessRequest::where('email', $this->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('auth.request_access.email_already_requested'),
            ]);
        }

        if (User::where('email', $this->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('auth.request_access.email_already_registered'),
            ]);
        }
    }

    protected function notifyAdmins(AccessRequest $accessRequest): void
    {
        User::where('is_admin', true)
            ->each(function (User $admin) use ($accessRequest): void {
                $admin->notify(new AccessRequestNotification($accessRequest));
            });
    }
}
