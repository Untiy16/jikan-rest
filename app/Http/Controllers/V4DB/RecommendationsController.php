<?php

namespace App\Http\Controllers\V4DB;

use App\Http\HttpHelper;
use App\Http\HttpResponse;
use App\Http\Resources\V4\ResultsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jikan\Helper\Constants;
use Jikan\Request\Recommendations\RecentRecommendationsRequest;
use Jikan\Request\Reviews\RecentReviewsRequest;
use MongoDB\BSON\UTCDateTime;

class RecommendationsController extends Controller
{

    /**
     *  @OA\Schema(
     *      schema="recent recommendations",
     *      description="Recent Recommendations",
     *
     *      allOf={
     *          @OA\Schema(ref="#/components/schemas/pagination"),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="data",
     *                   type="array",
     *                   @OA\Items(
     *                      type="object",
     *                      anyOf={
     *                          @OA\Schema(ref="#/components/schemas/anime recommendation"),
     *                          @OA\Schema(ref="#/components/schemas/manga recommendation"),
     *                      },
     *                  ),
     *              ),
     *          ),
     *     }
     *  ),
     */

    /**
     *  @OA\Get(
     *     path="/recommendations/anime",
     *     operationId="getAnimeRecommendations",
     *     tags={"recommendations"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns Recent Anime Recommendations",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * ),
     *
     * @OA\Schema(
     *     schema="anime recommendation",
     *     description="Anime Recommendations",
     *     @OA\Property(
     *          property="anime",
     *          type="array",
     *          description="Similar Anime",
     *          @OA\Items(
     *               type="object",
     *               ref="#/components/schemas/mal_url",
     *          ),
     *     ),
     * ),
     */
    public function anime(Request $request)
    {
        $results = DB::table($this->getRouteTable($request))
            ->where('request_hash', $this->fingerprint)
            ->get();

        if (
            $results->isEmpty()
            || $this->isExpired($request, $results)
        ) {
            $page = $request->get('page') ?? 1;
            $anime = $this->jikan->getRecentRecommendations(new RecentRecommendationsRequest(Constants::RECENT_RECOMMENDATION_ANIME, $page));
            $response = \json_decode($this->serializer->serialize($anime, 'json'), true);

            if (HttpHelper::hasError($response)) {
                return HttpResponse::notFound($request);
            }

            if ($results->isEmpty()) {
                $meta = [
                    'createdAt' => new UTCDateTime(),
                    'modifiedAt' => new UTCDateTime(),
                    'request_hash' => $this->fingerprint
                ];
            }
            $meta['modifiedAt'] = new UTCDateTime();

            $response = $meta + $response;

            if ($results->isEmpty()) {
                DB::table($this->getRouteTable($request))
                    ->insert($response);
            }

            if ($this->isExpired($request, $results)) {
                DB::table($this->getRouteTable($request))
                    ->where('request_hash', $this->fingerprint)
                    ->update($response);
            }

            $results = DB::table($this->getRouteTable($request))
                ->where('request_hash', $this->fingerprint)
                ->get();
        }

        $response = (new ResultsResource(
            $results->first()
        ))->response();

        return $this->prepareResponse(
            $response,
            $results,
            $request
        );
    }

    /**
     *  @OA\Get(
     *     path="/recommendations/manga",
     *     operationId="getMangaRecommendations",
     *     tags={"recommendations"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns Recent Manga Recommendations",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * ),
     *
     * @OA\Schema(
     *     schema="manga recommendation",
     *     description="Manga Recommendations",
     *     @OA\Property(
     *          property="manga",
     *          type="array",
     *          description="Similar Manga",
     *          @OA\Items(
     *               type="object",
     *               ref="#/components/schemas/mal_url",
     *          ),
     *     ),
     * ),
     */
    public function manga(Request $request)
    {
        $results = DB::table($this->getRouteTable($request))
            ->where('request_hash', $this->fingerprint)
            ->get();

        if (
            $results->isEmpty()
            || $this->isExpired($request, $results)
        ) {
            $page = $request->get('page') ?? 1;
            $anime = $this->jikan->getRecentRecommendations(new RecentRecommendationsRequest(Constants::RECENT_RECOMMENDATION_MANGA, $page));
            $response = \json_decode($this->serializer->serialize($anime, 'json'), true);

            if (HttpHelper::hasError($response)) {
                return HttpResponse::notFound($request);
            }

            if ($results->isEmpty()) {
                $meta = [
                    'createdAt' => new UTCDateTime(),
                    'modifiedAt' => new UTCDateTime(),
                    'request_hash' => $this->fingerprint
                ];
            }
            $meta['modifiedAt'] = new UTCDateTime();

            $response = $meta + $response;

            if ($results->isEmpty()) {
                DB::table($this->getRouteTable($request))
                    ->insert($response);
            }

            if ($this->isExpired($request, $results)) {
                DB::table($this->getRouteTable($request))
                    ->where('request_hash', $this->fingerprint)
                    ->update($response);
            }

            $results = DB::table($this->getRouteTable($request))
                ->where('request_hash', $this->fingerprint)
                ->get();
        }

        $response = (new ResultsResource(
            $results->first()
        ))->response();

        return $this->prepareResponse(
            $response,
            $results,
            $request
        );
    }
}