<?php

namespace App\Http\Resources\V4;

use Illuminate\Http\Resources\Json\JsonResource;

class AnimeCharactersResource extends JsonResource
{
    /**
     *  @OA\Schema(
     *      schema="anime characters",
     *      description="Anime Characters Resource",
     *
     *     @OA\Property(
     *          property="data",
     *          type="array",
     *          @OA\Items(
     *               type="object",
     *
     *               @OA\Property(
     *                   property="mal_id",
     *                   type="integer",
     *                   description="MyAnimeList ID"
     *               ),
     *               @OA\Property(
     *                   property="url",
     *                   type="string",
     *                   description="MyAnimeList URL"
     *               ),
     *               @OA\Property(
     *                   property="image_url",
     *                   type="string",
     *                   description="Image URL"
     *               ),
     *               @OA\Property(
     *                   property="name",
     *                   type="string",
     *                   description="Character Name"
     *               ),
     *               @OA\Property(
     *                   property="role",
     *                   type="string",
     *                   description="Character's Role"
     *               ),
     *               @OA\Property(
     *                   property="voice_actors",
     *                   type="array",
     *                   @OA\Items(
     *                       type="object",
     *                       allOf={
     *                           @OA\Schema(ref="#/components/schemas/mal_url"),
     *                           @OA\Schema(
     *                               @OA\Property(
     *                                   property="image_url",
     *                                   type="string",
     *                               ),
     *                               @OA\Property(
     *                                   property="language",
     *                                   type="string",
     *                               ),
     *                           ),
     *                       },
     *                   ),
     *               ),
     *          ),
     *     ),
     *  )
     */

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this['characters'];
    }
}