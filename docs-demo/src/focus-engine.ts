export function focusToObjectPosition(
    focusX: number,
    focusY: number,
): string {
    const x = Math.max(0, Math.min(100, focusX * 100));
    const y = Math.max(0, Math.min(100, focusY * 100));
    return `${x.toFixed(1)}% ${y.toFixed(1)}%`;
}

export function cssForFocus(focusX: number, focusY: number): string {
    return `object-fit: cover; object-position: ${focusToObjectPosition(focusX, focusY)};`;
}
