/** Label Indonesia untuk layanan, baik dari nama enum maupun nilainya. */
const serviceLabels: Record<string, string> = {
    Print: 'Print',
    print: 'Print',
    Photocopy: 'Fotokopi',
    photocopy: 'Fotokopi',
    Scan: 'Scan',
    scan: 'Scan',
    Laminating: 'Laminating',
    laminating: 'Laminating',
    SpiralBinding: 'Jilid spiral',
    spiral_binding: 'Jilid spiral',
    TapeBinding: 'Jilid lakban',
    tape_binding: 'Jilid lakban',
    HardcoverBinding: 'Jilid hardcover',
    hardcover_binding: 'Jilid hardcover',
};

export function serviceLabel(service: string): string {
    return serviceLabels[service] ?? service;
}
