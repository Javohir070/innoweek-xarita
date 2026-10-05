<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['is_admin' => true])->save();

        return $user;
    }

    private function seedPlace(array $attrs = [], array $products = []): Place
    {
        $place = Place::create(array_merge(['stand' => 'A1', 'place' => 1, 'section' => 'Hudud', 'org' => 'Toshkent MChJ'], $attrs));
        foreach ($products as $i => $p) {
            $place->products()->create(['position' => $i] + $p);
        }

        return $place;
    }

    public function test_map_page_is_public_and_contains_data(): void
    {
        $this->seedPlace();

        $this->get('/')->assertOk()->assertSee('Toshkent MChJ')->assertSee('csrf-token', false)
            ->assertDontSee('isAdmin: true', false);
        $this->getJson('/expo/places')->assertOk()->assertJsonPath('0.stand', 'A1')->assertJsonPath('0.org', 'Toshkent MChJ');
    }

    public function test_guest_and_non_admin_cannot_modify(): void
    {
        $this->seedPlace();

        $this->putJson('/expo/places/A1/1', ['org' => 'X'])->assertUnauthorized();
        $this->deleteJson('/expo/places/A1/1')->assertUnauthorized();
        $this->postJson('/expo/uploads')->assertUnauthorized();

        $user = User::factory()->create();
        $this->actingAs($user)->putJson('/expo/places/A1/1', ['org' => 'X'])->assertForbidden();
        $this->actingAs($user)->deleteJson('/expo/places/A1/1')->assertForbidden();
        $this->assertSame('Toshkent MChJ', Place::first()->org);
    }

    public function test_admin_can_create_and_update_place_with_products(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('expo/uploads/a.jpg', 'x');
        $admin = $this->admin();

        $this->actingAs($admin)->putJson('/expo/places/B4/3', [
            'org' => 'Yangi MChJ',
            'section' => '7TECH',
            'info' => "1. Birinchi\n2. Ikkinchi",
            'contact' => 'Ali (90) 123-45-67',
            'products' => [['name' => 'Sensor', 'img' => ['expo/uploads/a.jpg']], ['name' => '', 'img' => []], ['name' => str_repeat('Uzun tavsif. ', 80), 'img' => []]],
        ])->assertOk()->assertJsonPath('org', 'Yangi MChJ')->assertJsonCount(2, 'products');

        $place = Place::where(['stand' => 'B4', 'place' => 3])->firstOrFail();
        $this->assertSame(['expo/uploads/a.jpg'], $place->products->first()->images);

        // qayta saqlash — almashtiradi, ikki nusxa yaratmaydi
        $this->actingAs($admin)->putJson('/expo/places/B4/3', ['org' => 'Boshqa'])->assertOk()->assertJsonCount(0, 'products');
        $this->assertSame(1, Place::where('stand', 'B4')->count());
    }

    public function test_saving_main_place_renames_its_continuations(): void
    {
        $this->seedPlace(['place' => 3, 'org' => 'Eski nom']);
        $this->seedPlace(['place' => 4, 'cont' => 3, 'org' => 'Eski nom']);
        $this->seedPlace(['place' => 5, 'org' => 'Boshqa']);

        $this->actingAs($this->admin())->putJson('/expo/places/A1/3', ['org' => 'Yangi nom'])->assertOk();

        $this->assertSame('Yangi nom', Place::where('place', 4)->value('org'));
        $this->assertSame(3, Place::where('place', 4)->value('cont'));
        $this->assertSame('Boshqa', Place::where('place', 5)->value('org'));
    }

    public function test_validation_and_path_safety(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->putJson('/expo/places/A1/1', ['org' => ''])->assertUnprocessable()->assertJsonValidationErrors('org');
        foreach (['../.env', 'expo/../../.env', 'expo/missing.jpg', '/etc/passwd', 'expo/x.php'] as $bad) {
            $this->actingAs($admin)->putJson('/expo/places/A1/1', ['org' => 'A', 'products' => [['name' => 'n', 'img' => [$bad]]]])
                ->assertUnprocessable();
        }
        $this->actingAs($admin)->putJson('/expo/places/Z9/1', ['org' => 'A'])->assertNotFound();
        $this->actingAs($admin)->putJson('/expo/places/A1/21', ['org' => 'A'])->assertNotFound();
    }

    public function test_delete_clears_place_and_its_continuations(): void
    {
        $main = $this->seedPlace(['place' => 3], [['name' => 'P', 'images' => []]]);
        $this->seedPlace(['place' => 4, 'cont' => 3]);
        $other = $this->seedPlace(['place' => 5, 'org' => 'Qoladi']);

        $this->actingAs($this->admin())->deleteJson('/expo/places/A1/3')->assertOk();

        $this->assertSame('', $main->fresh()->org);
        $this->assertSame(0, $main->products()->count());
        $this->assertSame('Hudud', $main->fresh()->section);
        $this->assertSame('', Place::where('place', 4)->first()->org);
        $this->assertSame('Qoladi', $other->fresh()->org);
    }

    public function test_upload_stores_resized_jpeg_and_rejects_non_images(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $path = $this->actingAs($admin)->post('/expo/uploads', ['image' => UploadedFile::fake()->image('big.png', 3000, 2000)], ['Accept' => 'application/json'])
            ->assertOk()->json('file');
        $this->assertMatchesRegularExpression('#^expo/uploads/[a-z0-9_]+\.jpg$#', $path);
        Storage::disk('public')->assertExists($path);
        [$w, $h] = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame([1400, 933], [$w, $h]);

        $this->actingAs($admin)->post('/expo/uploads', ['image' => UploadedFile::fake()->createWithContent('x.php', '<?php echo 1;')], ['Accept' => 'application/json'])
            ->assertUnprocessable();
    }

    public function test_login_logout_and_no_registration(): void
    {
        $admin = $this->admin();

        $this->post('/login', ['email' => $admin->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($admin);
        $this->get('/')->assertSee('isAdmin: true', false);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->get('/register')->assertNotFound();
    }

    public function test_login_is_rate_limited(): void
    {
        $admin = $this->admin();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $admin->email, 'password' => 'wrong']);
        }
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_import_command_loads_json_and_images(): void
    {
        Storage::fake('public');
        $dir = sys_get_temp_dir().'/expo-test-'.uniqid();
        mkdir("$dir/img", 0777, true);
        file_put_contents("$dir/img/p001.jpg", 'img');
        file_put_contents("$dir/data.json", json_encode([
            ['stand' => 'A1', 'place' => '1', 'cont' => null, 'section' => 'S', 'org' => 'O', 'info' => 'I', 'contact' => 'C', 'dept' => 'D',
                'products' => [['name' => 'N', 'img' => ['p001.jpg']]]],
            ['stand' => 'A1', 'place' => '2', 'cont' => '1', 'section' => 'S', 'org' => 'O', 'info' => '', 'contact' => '', 'dept' => '', 'products' => []],
            ['stand' => 'ZZ', 'place' => '1', 'org' => 'skip', 'products' => []],
        ]));

        $this->artisan('expo:import', ['json' => "$dir/data.json", '--images' => "$dir/img"])->assertSuccessful();
        $this->assertSame(2, Place::count());
        $this->assertSame(1, Place::where('place', 2)->value('cont'));
        $this->assertSame(['expo/p001.jpg'], Place::first()->products->first()->images);
        Storage::disk('public')->assertExists('expo/p001.jpg');

        // qayta yuklash --force siz rad etiladi
        $this->artisan('expo:import', ['json' => "$dir/data.json", '--images' => "$dir/img"])->assertFailed();
    }
}
