<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

/**
 * Transcribes the prices written on a photo of a print shop's price list. It only copies what is visible:
 * it never estimates a missing price (core rule 1). A person checks every row before it is imported.
 */
class PriceListReaderAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
        You transcribe the price list in a photo of a print shop. Return one item per price you can clearly read.
        - Copy only what is visible. Never guess, estimate, calculate or fill in a price. If a number is not clearly readable, leave that line out.
        - price: whole Rupiah as an integer. "1.500" and "1,500" mean 1500. "500" means 500. "1k" or "1rb" means 1000.
        - service: print (cetak, print), photocopy (fotokopi, fc, copy), scan, laminating, spiral_binding (jilid spiral or ring), tape_binding (jilid lakban), hardcover_binding (jilid hardcover). Skip services that are not in this list.
        - paper_size: a4, f4 (folio) or a3. Use null when the line has no paper size, for example binding and laminating.
        - color_mode: bw (hitam putih, B/W) or color (warna). Use null when it does not apply or is not stated.
        - If the price depends on quantity (for example 1-10 sheets and 100+ sheets), return only the price for the smallest quantity and set confident to false.
        - text: the line as written in the photo, short.
        - confident: true only if you are sure about both the service and the number.
        PROMPT;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'items' => $schema->array()->items($schema->object([
                'service' => $schema->string()->enum(['print', 'photocopy', 'scan', 'laminating', 'spiral_binding', 'tape_binding', 'hardcover_binding'])->required(),
                'paper_size' => $schema->string()->enum(['a4', 'f4', 'a3'])->nullable(),
                'color_mode' => $schema->string()->enum(['bw', 'color'])->nullable(),
                'price' => $schema->integer()->min(0)->required(),
                'text' => $schema->string()->required(),
                'confident' => $schema->boolean()->required(),
            ]))->required(),
        ];
    }
}
