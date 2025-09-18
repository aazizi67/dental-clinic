// Persian datepicker imports (Vite)
// Persian datepicker handled via CDN to avoid multiple jQuery instances
// Removed flatpickr fallback to force Persian calendar
// Utilities to convert digits
const persianDigitMap = {
  '0': '۰', '1': '۱', '2': '۲', '3': '۳', '4': '۴',
  '5': '۵', '6': '۶', '7': '۷', '8': '۸', '9': '۹'
};

const englishDigitMap = Object.fromEntries(Object.entries(persianDigitMap).map(([en, fa]) => [fa, en]));

export function toPersianDigits(input) {
  if (input == null) return '';
  return String(input).replace(/[0-9]/g, d => persianDigitMap[d]);
}

export function toEnglishDigits(input) {
  if (input == null) return '';
  return String(input).replace(/[۰-۹]/g, d => englishDigitMap[d]);
}

function convertTextNodesToPersianDigits(root) {
  const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
  const textNodes = [];
  let node;
  while ((node = walker.nextNode())) {
    // Skip script/style tags
    const parent = node.parentElement;
    if (!parent) continue;
    const tag = parent.tagName.toLowerCase();
    if (tag === 'script' || tag === 'style') continue;
    textNodes.push(node);
  }
  for (const tn of textNodes) {
    tn.nodeValue = toPersianDigits(tn.nodeValue);
  }
}

function setupInputsWithPersianDisplay() {
  // Inputs with data-persian-digits show Persian while typing, but store English
  const inputs = document.querySelectorAll('[data-persian-digits="true"]');
  inputs.forEach((el) => {
    el.addEventListener('input', () => {
      const caret = el.selectionStart;
      const english = toEnglishDigits(el.value);
      el.value = toPersianDigits(english);
      // Restore caret best-effort
      try { el.setSelectionRange(caret, caret); } catch (e) {}
    });
  });

  // On any form submit, normalize inputs to English so DB gets ASCII digits
  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', () => {
      form.querySelectorAll('input, textarea').forEach((el) => {
        if (el.hasAttribute('data-persian-digits')) {
          el.value = toEnglishDigits(el.value);
        }
      });
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // Convert all visible digits to Persian for display
  convertTextNodesToPersianDigits(document.body);
  // Setup inputs behavior
  setupInputsWithPersianDisplay();

  // JalaliDatePicker handles all date functionality now
  // Old Persian datepicker code removed - using reliable JalaliDatePicker instead
  console.log('JalaliDatePicker ready for use');
});



