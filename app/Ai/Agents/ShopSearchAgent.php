<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

/**
 * Reads a student's sentence about which print shop they want and returns search filters.
 * It never searches, ranks or prices anything: the page applies the filters to data from the database.
 */
class ShopSearchAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
        You turn a student's sentence about finding a print shop into search filters. The student writes in Indonesian.
        Return only the fields in the schema. Use null, false or an empty list for everything the student does not ask for.
        Never guess, and never answer the sentence.
        - area: only the name of a street, neighbourhood or building the shop should be at, without words such as
          "sekitar", "daerah", "dekat" or "jalan". "di sekitar Ngagel" gives "Ngagel". Words about the student's own
          position, such as "dekat sini" or "terdekat", are not an area: use sort distance for them.
        - shop_name: only when the student names a particular print shop.
        - paper_size: a4, f4 or a3.
        - color_mode: bw or color.
        - services: print, photocopy, binding, scan or laminating. Every kind of jilid is binding. A paper size or
          colour for printing also means print.
        - sort: price for "murah" or "termurah", distance for "dekat" or "terdekat".
        - open_only: true when the shop must be open now.
        - max_km: only when the student states a distance. Pick 1, 2 or 5, the smallest one that covers it, or 5 when
          the distance is larger.
        Examples:
        "print f4 termurah di sekitar ngagel" gives area "Ngagel", paper_size f4, services [print], sort price.
        "fotokopi yang masih buka paling dekat" gives services [photocopy], open_only true, sort distance.
        PROMPT;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'area' => $schema->string()->nullable(),
            'shop_name' => $schema->string()->nullable(),
            'paper_size' => $schema->string()->enum(['a4', 'f4', 'a3'])->nullable(),
            'color_mode' => $schema->string()->enum(['bw', 'color'])->nullable(),
            'services' => $schema->array()->items(
                $schema->string()->enum(['print', 'photocopy', 'binding', 'scan', 'laminating']),
            )->required(),
            'sort' => $schema->string()->enum(['price', 'distance'])->nullable(),
            'open_only' => $schema->boolean()->required(),
            'max_km' => $schema->integer()->min(1)->max(5)->nullable(),
        ];
    }
}
