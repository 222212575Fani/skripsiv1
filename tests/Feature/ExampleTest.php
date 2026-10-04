<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Tidak ada landing page: alamat utama mengarah ke halaman login.
     */
    public function test_alamat_utama_diarahkan_ke_halaman_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
