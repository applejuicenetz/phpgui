// Shared range selection. Resolve rows on each click so live-added results participate.
export function rangeSelection(root, selector, onChange) {
    let anchor = null;
    const visible = () => Array.from(root.querySelectorAll(selector)).filter((check) => {
        const row = check.closest('tr');
        return !check.disabled && row && !row.hidden;
    });
    root.addEventListener('click', (event) => {
        const check = event.target.closest(selector);
        if (!check || !root.contains(check)) return;
        const checks = visible();
        const from = checks.indexOf(anchor);
        const to = checks.indexOf(check);
        if (event.shiftKey && from >= 0 && to >= 0) {
            for (let i = Math.min(from, to); i <= Math.max(from, to); i++) {
                checks[i].checked = check.checked;
            }
        }
        anchor = check;
        onChange();
    });
    return { reset: () => { anchor = null; }, visible };
}
