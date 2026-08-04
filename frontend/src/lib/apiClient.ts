/**
 * Thin fetch wrapper around the Laravel API. JSON in/out, throws on non-2xx
 * with the server's message.
 *
 * Server-side (SSR) requests use INTERNAL_API_URL so they stay on the compose
 * network; browser requests use the published NEXT_PUBLIC_API_URL.
 */
const BROWSER_API_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8002/api';
const SERVER_API_URL = process.env.INTERNAL_API_URL ?? BROWSER_API_URL;

function baseUrl(): string {
  return typeof window === 'undefined' ? SERVER_API_URL : BROWSER_API_URL;
}

export async function apiFetch<T>(path: string, init?: RequestInit): Promise<T> {
  const res = await fetch(`${baseUrl()}${path}`, {
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...(init?.headers ?? {}),
    },
    ...init,
  });

  if (!res.ok) {
    const error = await res.json().catch(() => ({ message: res.statusText }));
    throw new Error(error.message ?? 'API error');
  }

  return res.json() as Promise<T>;
}

/** Same as apiFetch but returns null instead of throwing — for optional data. */
export async function apiFetchOrNull<T>(path: string, init?: RequestInit): Promise<T | null> {
  try {
    return await apiFetch<T>(path, init);
  } catch {
    return null;
  }
}
