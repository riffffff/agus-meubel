export function formatRupiah(val: number | string | null | undefined): string {
    if (val === null || val === undefined) return 'Rp 0';
    const num = typeof val === 'number' ? val : parseFloat(String(val));
    const validNum = isNaN(num) ? 0 : num;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(validNum);
}
