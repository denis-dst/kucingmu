<?php

namespace Tests\Unit;

use App\Models\Cat;
use App\Models\User;
use Tests\TestCase;

class CatModelAuthorizationTest extends TestCase
{
    public function test_can_be_edited_by_owner(): void
    {
        $owner = new User();
        $owner->id = 1;
        $owner->role = 'member';

        $cat = new Cat();
        $cat->user_id = 1;

        $this->assertTrue($cat->canBeEditedBy($owner));
    }

    public function test_cannot_be_edited_by_non_owner_member(): void
    {
        $otherMember = new User();
        $otherMember->id = 2;
        $otherMember->role = 'member';

        $cat = new Cat();
        $cat->user_id = 1;

        $this->assertFalse($cat->canBeEditedBy($otherMember));
    }
}
