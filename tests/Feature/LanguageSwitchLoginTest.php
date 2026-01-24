<?php

describe('Language Switcher on Login Pages', function () {
    it('shows language switcher on app login page', function () {
        $this->get('/app/login')
            ->assertSuccessful()
            ->assertSee('language-switch-component');
    });

    it('shows language switcher on admin login page', function () {
        $this->get('/admin/login')
            ->assertSuccessful()
            ->assertSee('language-switch-component');
    });

    it('shows language switcher on app password reset page', function () {
        $this->get('/app/password-reset/request')
            ->assertSuccessful()
            ->assertSee('language-switch-component');
    });

    it('shows language switcher on admin password reset page', function () {
        $this->get('/admin/password-reset/request')
            ->assertSuccessful()
            ->assertSee('language-switch-component');
    });
});
