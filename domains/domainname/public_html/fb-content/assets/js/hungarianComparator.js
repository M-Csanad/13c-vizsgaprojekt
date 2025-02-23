const hungarianAlphabet = [
  "a", "á", "b", "c", "cs", "d", "e", "é", "f", "g", "gy", "h",
  "i", "í", "j", "k", "l", "ly", "m", "n", "ny", "o", "ó", "ö", "ő",
  "p", "q", "r", "s", "sz", "t", "ty", "u", "ú", "ü", "ű",
  "v", "w", "x", "y", "z", "zs"
];

export default class HungarianComparator {
  compareStrings(a, b) {
    const aLower = a.toLowerCase();
    const bLower = b.toLowerCase();

    let i = 0;
    while (i < aLower.length && i < bLower.length) {
      let aChar = aLower[i];
      let bChar = bLower[i];
      const nextA = aLower.slice(i, i + 2);
      const nextB = bLower.slice(i, i + 2);

      if (hungarianAlphabet.includes(nextA)) aChar = nextA;
      if (hungarianAlphabet.includes(nextB)) bChar = nextB;

      const aIndex = hungarianAlphabet.indexOf(aChar);
      const bIndex = hungarianAlphabet.indexOf(bChar);
      if (aIndex !== bIndex) return aIndex - bIndex;

      i += aChar.length; 
    }
    return aLower.length - bLower.length;
  }
}
