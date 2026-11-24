export type ColorNeed = 'color' | 'maybe' | 'bw';

export type ColorSavings = {
    shop: string;
    slug: string;
    chosenTotal: number;
    allColorTotal: number;
    allBlackWhiteTotal: number;
    saving: number;
};

/** Jawaban server atas cek warna: penilaian tiap halaman, halaman yang disarankan berwarna, dan hematnya. */
export type FileCheckResult = {
    needs: ColorNeed[];
    suggested: number[];
    savings: ColorSavings | null;
};
