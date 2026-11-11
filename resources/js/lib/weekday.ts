const dayKeys = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

/** Kunci hari ini (sun sampai sat), sama dengan kunci di jam buka dari server. */
export function todayKey(date: Date = new Date()): string {
    return dayKeys[date.getDay()];
}
