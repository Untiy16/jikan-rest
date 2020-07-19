<?php

namespace App\Http\Controllers\V4DB;

use App\Anime;
use App\Character;
use App\Club;
use App\Http\HttpHelper;
use App\Http\HttpResponse;
use App\Http\Middleware\Throttle;
use App\Http\QueryBuilder\SearchQueryBuilderAnime;
use App\Http\QueryBuilder\SearchQueryBuilderCharacter;
use App\Http\QueryBuilder\SearchQueryBuilderClub;
use App\Http\QueryBuilder\SearchQueryBuilderManga;
use App\Http\QueryBuilder\SearchQueryBuilderPeople;
use App\Http\QueryBuilder\SearchQueryBuilderUsers;
use App\Http\Resources\V4\AnimeCharactersResource;
use App\Http\Resources\V4\AnimeCollection;
use App\Http\Resources\V4\CharacterCollection;
use App\Http\Resources\V4\ClubCollection;
use App\Http\Resources\V4\MangaCollection;
use App\Http\Resources\V4\PersonCollection;
use App\Http\Resources\V4\ResultsResource;
use App\Http\SearchQueryBuilder;
use App\Manga;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jikan\Jikan;
use Jikan\MyAnimeList\MalClient;
use Jikan\Request\Anime\AnimeCharactersAndStaffRequest;
use Jikan\Request\Search\AnimeSearchRequest;
use Jikan\Request\Search\MangaSearchRequest;
use Jikan\Request\Search\CharacterSearchRequest;
use Jikan\Request\Search\PersonSearchRequest;
use Jikan\Helper\Constants as JikanConstants;
use Jikan\Request\Search\UserSearchRequest;
use Jikan\Request\User\UsernameByIdRequest;
use JMS\Serializer\Serializer;
use MongoDB\BSON\UTCDateTime;
use phpDocumentor\Reflection\Types\Object_;

class SearchController extends Controller
{
    private $request;
    const MAX_RESULTS_PER_PAGE = 25;

    /**
     *  @OA\Get(
     *     path="/anime",
     *     operationId="getAnimeSearch",
     *     tags={"anime"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns search results for anime",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function anime(Request $request)
    {
        $this->request = $request;
        $page = $this->request->get('page') ?? 1;
        $limit = $this->request->get('limit') ?? self::MAX_RESULTS_PER_PAGE;

        if (!empty($limit)) {
            $limit = (int) $limit;

            if ($limit <= 0) {
                $limit = 1;
            }

            if ($limit > self::MAX_RESULTS_PER_PAGE) {
                $limit = self::MAX_RESULTS_PER_PAGE;
            }
        }

        $results = SearchQueryBuilderAnime::query(
            $request,
            Anime::query()
        );

        $results = $results
            ->paginate(
                $limit,
                ['*'],
                null,
                $page
            );

        return new AnimeCollection(
            $results
        );
    }

    /**
     *  @OA\Get(
     *     path="/manga",
     *     operationId="getMangaSearch",
     *     tags={"manga"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns search results for manga",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function manga(Request $request)
    {
        $this->request = $request;
        $page = $this->request->get('page') ?? 1;
        $limit = $this->request->get('limit') ?? self::MAX_RESULTS_PER_PAGE;

        if (!empty($limit)) {
            $limit = (int) $limit;

            if ($limit <= 0) {
                $limit = 1;
            }

            if ($limit > self::MAX_RESULTS_PER_PAGE) {
                $limit = self::MAX_RESULTS_PER_PAGE;
            }
        }

        $results = SearchQueryBuilderManga::query(
            $request,
            Manga::query()
        );

        $results = $results
            ->paginate(
                $limit,
                ['*'],
                null,
                $page
            );

        return new MangaCollection(
            $results
        );
    }

    /**
     *  @OA\Get(
     *     path="/people",
     *     operationId="getPeopleSearch",
     *     tags={"people"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns search results for people",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function people(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $limit = $request->get('limit') ?? self::MAX_RESULTS_PER_PAGE;

        if (!empty($limit)) {
            $limit = (int) $limit;

            if ($limit <= 0) {
                $limit = 1;
            }

            if ($limit > self::MAX_RESULTS_PER_PAGE) {
                $limit = self::MAX_RESULTS_PER_PAGE;
            }
        }

        $results = SearchQueryBuilderPeople::query(
            $request,
            Person::query()
        );

        $results = $results
            ->paginate(
                $limit,
                ['*'],
                null,
                $page
            );

        return new PersonCollection(
            $results
        );
    }

    /**
     *  @OA\Get(
     *     path="/characters",
     *     operationId="getCharactersSearch",
     *     tags={"characters"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns search results for characters",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function character(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $limit = $request->get('limit') ?? self::MAX_RESULTS_PER_PAGE;

        if (!empty($limit)) {
            $limit = (int) $limit;

            if ($limit <= 0) {
                $limit = 1;
            }

            if ($limit > self::MAX_RESULTS_PER_PAGE) {
                $limit = self::MAX_RESULTS_PER_PAGE;
            }
        }

        $results = SearchQueryBuilderCharacter::query(
            $request,
            Character::query()
        );

        $results = $results
            ->paginate(
                $limit,
                ['*'],
                null,
                $page
            );

        return new CharacterCollection(
            $results
        );
    }

    /**
     *  @OA\Get(
     *     path="/users",
     *     operationId="getUsersSearch",
     *     tags={"users"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns search results for users",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * ),
     *
     *  @OA\Schema(
     *      schema="user collection",
     *      description="User Results",
     *
     *      allOf={
     *           @OA\Schema(ref="#/components/schemas/pagination"),
     *           @OA\Schema(
     *              @OA\Property(
     *                   property="data",
     *                   type="object",
     *
     *                   @OA\Schema(
     *                       @OA\Property(
     *                           property="url",
     *                           type="string",
     *                           description="MyAnimeList URL"
     *                       ),
     *                       @OA\Property(
     *                           property="username",
     *                           type="string",
     *                           description="MyAnimeList Username"
     *                       ),
     *                       @OA\Property(
     *                           property="image_url",
     *                           type="string",
     *                           description="Image URL"
     *                       ),
     *                       @OA\Property(
     *                           property="last_online",
     *                           type="string",
     *                           description="Last Online Date ISO8601"
     *                       ),
     *                   ),
     *              ),
     *          ),
     *      },
     *  ),
     */
    public function users(Request $request)
    {
        $results = DB::table($this->getRouteTable($request))
            ->where('request_hash', $this->fingerprint)
            ->get();

        if (
            $results->isEmpty()
            || $this->isExpired($request, $results)
        ) {
            $anime = $this->jikan->getUserSearch(
                SearchQueryBuilderUsers::query(
                    $request
                )
            );
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
     *     path="/userbyid",
     *     operationId="getUserById",
     *     tags={"user"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns username by ID search",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * ),
     *
     *  @OA\Schema(
     *      schema="user by id",
     *      description="User Meta By ID",
     *
     *      @OA\Property(
     *           property="data",
     *           type="object",
     *
     *           @OA\Property(
     *               property="url",
     *               type="string",
     *               description="MyAnimeList URL"
     *           ),
     *           @OA\Property(
     *               property="username",
     *               type="string",
     *               description="MyAnimeList Username"
     *           ),
     *      ),
     *  ),
     *
     */
    public function userById(Request $request, int $id)
    {
        $results = DB::table($this->getRouteTable($request))
            ->where('request_hash', $this->fingerprint)
            ->get();

        if (
            $results->isEmpty()
            || $this->isExpired($request, $results)
        ) {
            $anime = ['results'=>$this->jikan->getUsernameById(new UsernameByIdRequest($id))];
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
     *     path="/clubs",
     *     operationId="getClubsSearch",
     *     tags={"clubs"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Returns search results for clubs",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function clubs(Request $request)
    {
        $this->request = $request;
        $page = $this->request->get('page') ?? 1;
        $limit = $this->request->get('limit') ?? self::MAX_RESULTS_PER_PAGE;

        if (!empty($limit)) {
            $limit = (int) $limit;

            if ($limit <= 0) {
                $limit = 1;
            }

            if ($limit > self::MAX_RESULTS_PER_PAGE) {
                $limit = self::MAX_RESULTS_PER_PAGE;
            }
        }

        $results = SearchQueryBuilderClub::query(
            $request,
            Club::query()
        );

        $results = $results
            ->paginate(
                $limit,
                ['*'],
                null,
                $page
            );

        return new ClubCollection(
            $results
        );
    }
}