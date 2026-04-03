<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_portal_pages_render_successfully(): void
    {
        foreach ([
            'portal.dashboard',
            'portal.food-log',
            'portal.calendar',
            'portal.insights',
            'portal.meal-plans',
            'portal.feedback',
            'portal.profile',
        ] as $routeName) {
            $this->get(route($routeName))->assertSuccessful();
        }
    }

    public function test_admin_portal_pages_render_successfully(): void
    {
        foreach ([
            'admin.dashboard',
            'admin.users',
            'admin.dietitians',
            'admin.feedback',
            'admin.content',
            'admin.analytics',
        ] as $routeName) {
            $this->get(route($routeName))->assertSuccessful();
        }
    }
}
