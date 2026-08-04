/**
 * Guards link output. Menu/button hrefs come from the CMS unrestricted (anchors,
 * relative paths and full URLs are all allowed by design), so we sanitise on
 * render: allow #, relative paths and http(s)/mailto/tel, block javascript: etc.
 */
export function safeUrl(href?: string | null): string {
  const value = (href ?? '').trim();

  if (value === '') return '#';

  // Anchors, query-only and relative/protocol-relative paths are fine.
  if (value.startsWith('#') || value.startsWith('?') || value.startsWith('/')) {
    return value;
  }

  // Known-safe schemes.
  if (/^(https?:|mailto:|tel:)/i.test(value)) return value;

  // Any other explicit scheme (javascript:, data:, …) is rejected.
  if (/^[a-z][a-z0-9+.-]*:/i.test(value)) return '#';

  // No scheme, no leading slash → treat as a relative path.
  return value;
}
