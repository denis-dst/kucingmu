<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\CatCondolence;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatDeathNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_cat_to_deceased_triggers_notifications(): void
    {
        $owner = User::create([
            'name' => 'Pemilik Anabul',
            'email' => 'owner@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $fellowMember = User::create([
            'name' => 'Teman Komunitas',
            'email' => 'teman@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Mimi Cantik',
            'breed' => 'Domestik',
            'gender' => 'female',
            'status' => 'alive',
            'date_of_birth' => now()->subYears(2),
        ]);

        // Before update, no notification
        $this->assertEquals(0, UserNotification::where('user_id', $owner->id)->count());

        // Owner updates cat to deceased
        $response = $this->actingAs($owner)->put(route('cat.update', $cat->id), [
            'name' => 'Mimi Cantik',
            'breed' => 'Domestik',
            'gender' => 'female',
            'status' => 'deceased',
            'date_of_birth' => $cat->date_of_birth->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('cat.edit', $cat->id));
        $cat->refresh();
        $this->assertTrue($cat->isDeceased());

        // Check owner received notification
        $ownerNotif = UserNotification::where('user_id', $owner->id)->first();
        $this->assertNotNull($ownerNotif);
        $this->assertEquals('cat_memorial', $ownerNotif->type);
        $this->assertStringContainsString('Mimi Cantik', $ownerNotif->title);
        $this->assertStringContainsString('Belasungkawa', $ownerNotif->title);

        // Check fellow member received community notification
        $fellowNotif = UserNotification::where('user_id', $fellowMember->id)->first();
        $this->assertNotNull($fellowNotif);
        $this->assertEquals('cat_deceased', $fellowNotif->type);
        $this->assertStringContainsString('Mimi Cantik', $fellowNotif->title);

        // Owner visits /notifications page and sees the notification
        $notifPageResponse = $this->actingAs($owner)->get(route('notifications.index'));
        $notifPageResponse->assertStatus(200);
        $notifPageResponse->assertSee('Mimi Cantik');
        $notifPageResponse->assertSee('Belasungkawa');

        // Clicking the notification link marks it as read and redirects to memorial page
        $readResponse = $this->actingAs($owner)->get(route('notifications.read', $ownerNotif->id));
        $readResponse->assertRedirect(route('cat.memorial', $cat->id));
        $ownerNotif->refresh();
        $this->assertTrue($ownerNotif->isRead());
    }

    public function test_fellow_member_can_send_condolence_and_owner_is_notified(): void
    {
        $owner = User::create([
            'name' => 'Pemilik Anabul',
            'email' => 'owner2@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $fellowMember = User::create([
            'name' => 'Budi Pecinta Kucing',
            'email' => 'budi@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Oyen Gemoy',
            'breed' => 'Persia Mix',
            'gender' => 'male',
            'status' => 'deceased',
            'date_of_birth' => now()->subYears(3),
        ]);

        // Fellow member posts condolence
        $response = $this->actingAs($fellowMember)->post(route('cat.memorial.store', $cat->id), [
            'message' => 'Turut berduka cita sedalam-dalamnya. Semoga husnul khotimah dan bermain tenang di jannah kucing.',
            'prayer_text' => 'Semoga keluarga yang ditinggalkan diberikan ketabahan.',
        ]);

        $response->assertRedirect(route('cat.memorial', $cat->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cat_condolences', [
            'cat_id' => $cat->id,
            'user_id' => $fellowMember->id,
            'message' => 'Turut berduka cita sedalam-dalamnya. Semoga husnul khotimah dan bermain tenang di jannah kucing.',
        ]);

        // Owner should have received condolence notification
        $condolenceNotif = UserNotification::where('user_id', $owner->id)
            ->where('type', 'cat_condolence')
            ->first();

        $this->assertNotNull($condolenceNotif);
        $this->assertEquals($fellowMember->id, $condolenceNotif->sender_id);
        $this->assertStringContainsString('Budi Pecinta Kucing', $condolenceNotif->message);

        // Owner views /notifications and sees Budi's condolence
        $ownerPageResponse = $this->actingAs($owner)->get(route('notifications.index'));
        $ownerPageResponse->assertStatus(200);
        $ownerPageResponse->assertSee('Budi Pecinta Kucing');
        $ownerPageResponse->assertSee('Ucapan Belasungkawa untuk Oyen Gemoy');
    }

    public function test_toggling_cat_to_deceased_triggers_notification(): void
    {
        $owner = User::create([
            'name' => 'Pemilik Kucing',
            'email' => 'owner3@kucingmu.test',
            'password' => bcrypt('password'),
            'role' => 'member',
            'roles' => ['member'],
        ]);

        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => 'Milo Manis',
            'breed' => 'Kampung',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => now()->subYear(),
        ]);

        // Toggle to deceased
        $response = $this->actingAs($owner)->post(route('cat.toggle-status', $cat->id));
        $response->assertRedirect();

        $cat->refresh();
        $this->assertTrue($cat->isDeceased());

        // Owner received notification
        $ownerNotif = UserNotification::where('user_id', $owner->id)
            ->where('type', 'cat_memorial')
            ->first();

        $this->assertNotNull($ownerNotif);
        $this->assertStringContainsString('Milo Manis', $ownerNotif->title);
    }
}
