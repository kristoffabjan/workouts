<?php

describe('Password Reset on Filament Panels', function () {
    it('shows password reset link on app panel login page', function () {
        $this->get('/app/login')
            ->assertSuccessful()
            ->assertSee('Forgot password?');
    });

    it('shows password reset link on admin panel login page', function () {
        $this->get('/admin/login')
            ->assertSuccessful()
            ->assertSee('Forgot password?');
    });

    it('can access password reset request page from app panel', function () {
        $this->get('/app/password-reset/request')
            ->assertSuccessful()
            ->assertSee('Forgot password?');
    });

    it('can access password reset request page from admin panel', function () {
        $this->get('/admin/password-reset/request')
            ->assertSuccessful()
            ->assertSee('Forgot password?');
    });

    it('password reset request page has email input on app panel', function () {
        $this->get('/app/password-reset/request')
            ->assertSuccessful()
            ->assertSee('Email address');
    });

    it('password reset request page has email input on admin panel', function () {
        $this->get('/admin/password-reset/request')
            ->assertSuccessful()
            ->assertSee('Email address');
    });
});
