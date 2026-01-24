<?php

use App\Enums\AccessRequestStatus;
use App\Livewire\RequestAccess;
use App\Models\AccessRequest;
use App\Models\User;
use App\Notifications\AccessRequestNotification;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    RateLimiter::clear('request-access:127.0.0.1');
});

describe('Request Access Page', function () {
    it('can view request access page as guest', function () {
        $this->get('/request-access')
            ->assertSuccessful()
            ->assertSee('Request Access');
    });

    it('redirects authenticated users away from request access', function () {
        $user = User::factory()->create();

        actingAs($user)
            ->get('/request-access')
            ->assertRedirect();
    });

    it('shows request access form', function () {
        Livewire::test(RequestAccess::class)
            ->assertSee('Full Name')
            ->assertSee('Email Address')
            ->assertSee('Message')
            ->assertSee('Submit Request');
    });
});

describe('Request Access Form Validation', function () {
    it('validates name is required', function () {
        Livewire::test(RequestAccess::class)
            ->set('email', 'test@example.com')
            ->call('submit')
            ->assertHasErrors(['name' => 'required']);
    });

    it('validates email is required', function () {
        Livewire::test(RequestAccess::class)
            ->set('name', 'Test User')
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    });

    it('validates email format', function () {
        Livewire::test(RequestAccess::class)
            ->set('name', 'Test User')
            ->set('email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    });

    it('validates email is unique in access_requests', function () {
        AccessRequest::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(RequestAccess::class)
            ->set('name', 'Test User')
            ->set('email', 'existing@example.com')
            ->call('submit')
            ->assertHasErrors(['email']);
    });

    it('validates email is not already registered in users', function () {
        User::factory()->create(['email' => 'registered@example.com']);

        Livewire::test(RequestAccess::class)
            ->set('name', 'Test User')
            ->set('email', 'registered@example.com')
            ->call('submit')
            ->assertHasErrors(['email']);
    });

    it('validates message max length', function () {
        Livewire::test(RequestAccess::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('message', str_repeat('a', 1001))
            ->call('submit')
            ->assertHasErrors(['message' => 'max']);
    });
});

describe('Request Access Form Submission', function () {
    it('creates access request on valid submission', function () {
        Notification::fake();

        Livewire::test(RequestAccess::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('message', 'I would like to join.')
            ->call('submit')
            ->assertSet('submitted', true);

        expect(AccessRequest::where('email', 'john@example.com')->exists())->toBeTrue();

        $request = AccessRequest::where('email', 'john@example.com')->first();
        expect($request->name)->toBe('John Doe')
            ->and($request->message)->toBe('I would like to join.')
            ->and($request->status)->toBe(AccessRequestStatus::Pending);
    });

    it('creates access request without optional message', function () {
        Notification::fake();

        Livewire::test(RequestAccess::class)
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->call('submit')
            ->assertSet('submitted', true);

        $request = AccessRequest::where('email', 'jane@example.com')->first();
        expect($request->message)->toBeNull();
    });

    it('notifies system admins on new request', function () {
        Notification::fake();

        $admin1 = User::factory()->create(['is_admin' => true]);
        $admin2 = User::factory()->create(['is_admin' => true]);
        User::factory()->create(['is_admin' => false]); // Non-admin should not be notified

        Livewire::test(RequestAccess::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->call('submit');

        Notification::assertSentTo([$admin1, $admin2], AccessRequestNotification::class);
        Notification::assertCount(2);
    });

    it('shows success state after submission', function () {
        Notification::fake();

        Livewire::test(RequestAccess::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->call('submit')
            ->assertSee('Request Submitted')
            ->assertSee('Back to Login');
    });
});

describe('Request Access Rate Limiting', function () {
    it('rate limits requests per IP', function () {
        Notification::fake();

        // First 10 requests should succeed (rate limit is 10 per hour)
        for ($i = 1; $i <= 10; $i++) {
            Livewire::test(RequestAccess::class)
                ->set('name', "User $i")
                ->set('email', "user{$i}@example.com")
                ->call('submit')
                ->assertSet('submitted', true);
        }

        // 11th request should be rate limited
        Livewire::test(RequestAccess::class)
            ->set('name', 'User 11')
            ->set('email', 'user11@example.com')
            ->call('submit')
            ->assertHasErrors(['email']);
    });
});

describe('Access Request Login Footer Link', function () {
    it('shows request access link on app login page', function () {
        $this->get('/app/login')
            ->assertSuccessful()
            ->assertSee('Request Access');
    });

    it('shows request access link on admin login page', function () {
        $this->get('/admin/login')
            ->assertSuccessful()
            ->assertSee('Request Access');
    });
});

describe('AccessRequest Admin Approval', function () {
    it('sends invitation when approving access request for new user', function () {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $accessRequest = AccessRequest::factory()->create([
            'email' => 'newuser@example.com',
            'status' => AccessRequestStatus::Pending,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs($admin)
            ->test(\App\Filament\Admin\Resources\AccessRequests\Pages\ListAccessRequests::class)
            ->callTableAction('approve', $accessRequest);

        $accessRequest->refresh();
        expect($accessRequest->isApproved())->toBeTrue();

        expect(\App\Models\UserInvitation::where('email', 'newuser@example.com')->exists())->toBeTrue();
    });

    it('does not send invitation when user already exists', function () {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        User::factory()->create(['email' => 'existing@example.com']);
        $accessRequest = AccessRequest::factory()->create([
            'email' => 'existing@example.com',
            'status' => AccessRequestStatus::Pending,
        ]);

        $invitationCountBefore = \App\Models\UserInvitation::count();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs($admin)
            ->test(\App\Filament\Admin\Resources\AccessRequests\Pages\ListAccessRequests::class)
            ->callTableAction('approve', $accessRequest);

        $accessRequest->refresh();
        expect($accessRequest->isApproved())->toBeTrue();
        expect(\App\Models\UserInvitation::count())->toBe($invitationCountBefore);
    });
});

describe('AccessRequest Model', function () {
    it('has correct status enum casting', function () {
        $request = AccessRequest::factory()->create();

        expect($request->status)->toBeInstanceOf(AccessRequestStatus::class);
    });

    it('can check if pending', function () {
        $request = AccessRequest::factory()->create(['status' => AccessRequestStatus::Pending]);

        expect($request->isPending())->toBeTrue()
            ->and($request->isApproved())->toBeFalse()
            ->and($request->isRejected())->toBeFalse();
    });

    it('can approve request', function () {
        $request = AccessRequest::factory()->create(['status' => AccessRequestStatus::Pending]);
        $admin = User::factory()->create(['is_admin' => true]);

        $request->approve($admin);

        expect($request->isApproved())->toBeTrue()
            ->and($request->processed_by)->toBe($admin->id)
            ->and($request->processed_at)->not->toBeNull();
    });

    it('can reject request', function () {
        $request = AccessRequest::factory()->create(['status' => AccessRequestStatus::Pending]);
        $admin = User::factory()->create(['is_admin' => true]);

        $request->reject($admin);

        expect($request->isRejected())->toBeTrue()
            ->and($request->processed_by)->toBe($admin->id)
            ->and($request->processed_at)->not->toBeNull();
    });
});
