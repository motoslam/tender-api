<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\TenderFilter;
use App\Http\Resources\TenderResource;
use App\Models\Tender;
use App\Http\Requests\StoreTenderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class TenderController extends Controller
{
    public function index(TenderFilter $filter): AnonymousResourceCollection
    {
        $tenders = Tender::filter($filter)
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        return TenderResource::collection($tenders);
    }

    public function store(StoreTenderRequest $request): JsonResponse
    {
        $tender = Tender::create($request->validated());

        return response()->json([
            'message' => 'Тендер успешно создан',
            'data' => new TenderResource($tender)
        ], 201);
    }

    // Получение тендера происходит по внутреннему ID вместо внешнего кода
    // сознательно, в задаче использован термин "идентификатор" а не "внешний код",
    // хотя возможно бизнес задача предполагала получение по внешнему коду.
    public function show(Tender $tender): TenderResource
    {
        return new TenderResource($tender);
    }
}
