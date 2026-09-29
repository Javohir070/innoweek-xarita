<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePlaceRequest;
use App\Models\Place;
use App\Services\ExpoImageStore;
use App\Services\PlaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpoController extends Controller
{
    public function __construct(private PlaceService $places) {}

    /** Xarita sahifasi — ma'lumot sahifaga birga beriladi */
    public function index(Request $request): View
    {
        return view('expo', [
            'places' => Place::forMap(),
            'isAdmin' => (bool) $request->user()?->is_admin,
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json(Place::forMap());
    }

    public function update(SavePlaceRequest $request, string $stand, int $place): JsonResponse
    {
        $this->assertPosition($stand, $place);

        return response()->json($this->places->save($stand, $place, $request->validated())->toMapArray());
    }

    public function destroy(string $stand, int $place): JsonResponse
    {
        $this->assertPosition($stand, $place);
        $this->places->clear($stand, $place);

        return response()->json(['ok' => true]);
    }

    public function upload(Request $request, ExpoImageStore $images): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:'.config('expo.upload_max_kb')],
        ], [
            'image.mimes' => 'Faqat JPG, PNG yoki WEBP rasm yuklash mumkin.',
            'image.max' => "Rasm 15 MB dan katta bo'lmasin.",
        ]);

        return response()->json(['file' => $images->store($request->file('image'))]);
    }

    private function assertPosition(string $stand, int $place): void
    {
        abort_unless(
            in_array($stand, config('expo.stands'), true) && $place >= 1 && $place <= config('expo.places_per_stand'),
            404
        );
    }
}
