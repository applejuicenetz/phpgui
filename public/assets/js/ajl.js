// Converts the contents of an .ajl file into ajfsp:// links.
// Format: free-text header, a line "100", then name / checksum / size triples.

const CHECKSUM = /^[a-f0-9]{32}$/i;

/** @returns {string[]} one ajfsp://file link per complete entry; [] if the text is not a valid .ajl */
export function ajlToLinks(text) {
    const lines = String(text).replace(/^\uFEFF/, '').split(/\r\n|\r|\n/).map((line) => line.trim());
    const start = lines.indexOf('100');
    if (start === -1) return [];
    const links = [];
    for (let i = start + 1; i + 2 < lines.length; i += 3) {
        const [name, checksum, size] = [lines[i], lines[i + 1], lines[i + 2]];
        if (name === '' || !CHECKSUM.test(checksum) || !/^\d+$/.test(size)) break;
        links.push('ajfsp://file|' + name + '|' + checksum + '|' + size + '/');
    }
    return links;
}
