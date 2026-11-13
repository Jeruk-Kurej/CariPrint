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
 * It names which pages are wanted but never counts them: PageSelector does that.
 */
class PrintRequestAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
        You turn a student's printing request into a print specification.
        Return only the fields in the schema.
        A note in square brackets after the request says whether a PDF document is attached.
        - paper_size: a4, f4 or a3. Use a4 when the user does not say.
        - page_ranges: fill this when the user points at pages by number or position, or wants a whole document
          printed ("file ini", "dokumen ini", "semua"). Leave it empty when the user only gives a number of pages.
          Each item covers the pages from..to, counted from 1. A negative number counts from the end:
          -1 is the last page and -2 is the page before it.
          mode is bw, color, skip or auto. skip means these pages are not printed. Use bw when the user does not say.
          auto means "colour only where it is needed": the application prints a page in colour only if it has colour
          on it and in black and white otherwise. Use auto only when the user asks for that, for example "hitam putih,
          warna hanya kalau perlu". Never decide yourself which pages have colour.
          A later item replaces an earlier one on the pages they share, so give the general rule first and the
          exceptions after it. Never work out how many pages a range holds and never do page arithmetic.
          The application does that.
          Examples:
          "halaman 1-10 warna" gives [{from: 1, to: 10, mode: color}]
          "print file ini" gives [{from: 1, to: -1, mode: bw}]
          "semua kecuali halaman terakhir" gives [{from: 1, to: -1, mode: bw}, {from: -1, to: -1, mode: skip}]
          "3 halaman terakhir" gives [{from: -3, to: -1, mode: bw}]
          "halaman 1-5 warna, sisanya hitam putih" gives [{from: 1, to: -1, mode: bw}, {from: 1, to: 5, mode: color}]
          "print semua, warna hanya yang perlu" gives [{from: 1, to: -1, mode: auto}]
        - bw_pages and color_pages: only for a number of pages without positions, such as "20 halaman hitam putih".
          Set both to 0 when page_ranges is not empty.
        - total_pages: the length of the whole document, only if the user states it, such as "dokumen saya 24 halaman".
          Otherwise null.
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
            'page_ranges' => $schema->array()->items($schema->object([
                'from' => $schema->integer()->required(),
                'to' => $schema->integer()->required(),
                'mode' => $schema->string()->enum(['bw', 'color', 'skip', 'auto'])->required(),
            ]))->required(),
            'bw_pages' => $schema->integer()->min(0)->required(),
            'color_pages' => $schema->integer()->min(0)->required(),
            'total_pages' => $schema->integer()->min(1)->nullable(),
            'copies' => $schema->integer()->min(1)->required(),
            'binding' => $schema->string()->enum(['spiral_binding', 'tape_binding', 'hardcover_binding'])->nullable(),
        ];
    }
}
