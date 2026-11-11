<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

/**
 * Reads a student's printing request and returns a structured print spec.
 * It never returns prices, distances or opening hours: those come from PriceCalculator and the database.
 */
class PrintRequestAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
        You turn a student's printing request into a print specification.
        Return only the fields in the schema.
        - paper_size: a4, f4 or a3. Use a4 when the user does not say.
        - bw_pages and color_pages: how many pages to print in each mode. Count only pages the user asks for.
        - copies: how many copies. Use 1 when the user does not say.
        - binding: spiral_binding, tape_binding or hardcover_binding if the user asks for binding, otherwise null.
        Never guess prices, distances, or opening hours.
        PROMPT;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'paper_size' => $schema->string()->enum(['a4', 'f4', 'a3'])->required(),
            'bw_pages' => $schema->integer()->min(0)->required(),
            'color_pages' => $schema->integer()->min(0)->required(),
            'copies' => $schema->integer()->min(1)->required(),
            'binding' => $schema->string()->enum(['spiral_binding', 'tape_binding', 'hardcover_binding'])->nullable(),
        ];
    }
}
