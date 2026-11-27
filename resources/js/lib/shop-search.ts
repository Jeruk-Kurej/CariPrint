/** Setiap kata harus ada di nama atau alamat tempat, urutannya bebas. Kata kunci kosong cocok dengan semua tempat. */
export function matchesShopQuery(
    shop: { name: string; address: string },
    query: string,
): boolean {
    const haystack = `${shop.name} ${shop.address}`.toLowerCase();

    return query
        .toLowerCase()
        .split(/\s+/)
        .every((word) => haystack.includes(word));
}
