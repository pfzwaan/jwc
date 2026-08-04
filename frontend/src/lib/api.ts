import { apiFetchOrNull } from './apiClient';

// --- Shared shapes -----------------------------------------------------------

export interface PageBlockData {
  id: number;
  type: string;
  data: Record<string, unknown>;
}

export interface PageData {
  id: number;
  title: string;
  slug: string;
  seo?: { title?: string; description?: string } | null;
  blocks: PageBlockData[];
}

export interface MenuItem {
  id: number;
  label: string;
  href: string;
  type: 'none' | 'dropdown' | 'mega';
  cta: { label: string; href: string } | null;
  featured: {
    badge?: string | null;
    title: string;
    text?: string | null;
    image?: string;
    ctaLabel?: string | null;
    ctaHref?: string | null;
  } | null;
  children: { id: number | string; label: string; href: string }[];
}

export interface FooterColumn {
  id: number;
  blocks: { type: string; data: Record<string, unknown> }[];
}

export type SiteSettings = Record<string, string>;

export interface DienstCard {
  id: number;
  title: string;
  slug: string;
  href: string;
  label?: string | null;
  excerpt?: string | null;
  image: string;
  icon?: string | null;
  /** Per-dienst accent (button, ticks, pill background at 20%). */
  accent?: string | null;
  /** Pill label colour — defaults to `accent`. */
  accentLabel?: string | null;
  features: string[];
  ctaLabel?: string | null;
  ctaHref?: string | null;
}

export interface ProjectCardData {
  id: number;
  title: string;
  slug: string;
  /** The project's own page. */
  href: string;
  image: string;
  /** Portrait shot for the phone in the detail hero; falls back to `image`. */
  imageMobile?: string;
  /** The live client site. */
  url?: string | null;
  accent?: string | null;
  featured?: boolean;
  excerpt?: string | null;
  categories?: string[];
  skills?: string[];
}

export interface ProjectDetail extends ProjectCardData {
  body?: string | null;
  gallery?: string[] | null;
  seo?: { title?: string; description?: string } | null;
}

export interface BlogCard {
  id: number;
  title: string;
  slug: string;
  href: string;
  excerpt?: string | null;
  image: string;
  author?: string | null;
  category?: string | null;
  publishedAt?: string | null;
  /** Derived server-side from the body at 200 wpm. */
  readMinutes?: number;
}

export interface BlogDetail extends BlogCard {
  body?: string | null;
  seo?: { title?: string; description?: string } | null;
}

// --- Fetchers ----------------------------------------------------------------
// All return null / [] on failure so a page never crashes on a cold API.

interface Envelope<T> {
  data: T;
}

export async function fetchPage(slug: string): Promise<PageData | null> {
  const key = slug.trim() === '' ? 'home' : slug;
  const res = await apiFetchOrNull<Envelope<PageData>>(`/v1/pages/${encodeURIComponent(key)}`);

  return res?.data ?? null;
}

export async function fetchMenu(): Promise<MenuItem[]> {
  const res = await apiFetchOrNull<Envelope<MenuItem[]>>('/v1/menu');

  return res?.data ?? [];
}

export async function fetchSettings(): Promise<SiteSettings> {
  const res = await apiFetchOrNull<Envelope<SiteSettings>>('/v1/settings');

  return res?.data ?? {};
}

export async function fetchFooter(): Promise<FooterColumn[]> {
  const res = await apiFetchOrNull<Envelope<{ columns: FooterColumn[] }>>('/v1/footer');

  return res?.data?.columns ?? [];
}

export async function fetchDiensten(): Promise<DienstCard[]> {
  const res = await apiFetchOrNull<Envelope<DienstCard[]>>('/v1/diensten');

  return res?.data ?? [];
}

export async function fetchDienst(slug: string): Promise<(DienstCard & { body?: string | null }) | null> {
  const res = await apiFetchOrNull<Envelope<DienstCard & { body?: string | null }>>(
    `/v1/diensten/${encodeURIComponent(slug)}`,
  );

  return res?.data ?? null;
}

export async function fetchProjects(): Promise<ProjectCardData[]> {
  const res = await apiFetchOrNull<Envelope<ProjectCardData[]>>('/v1/projecten');

  return res?.data ?? [];
}

export async function fetchProject(slug: string): Promise<ProjectDetail | null> {
  const res = await apiFetchOrNull<Envelope<ProjectDetail>>(`/v1/projecten/${encodeURIComponent(slug)}`);

  return res?.data ?? null;
}

export async function fetchBlogPosts(): Promise<BlogCard[]> {
  const res = await apiFetchOrNull<Envelope<BlogCard[]>>('/v1/blog');

  return res?.data ?? [];
}

export async function fetchBlogPost(slug: string): Promise<BlogDetail | null> {
  const res = await apiFetchOrNull<Envelope<BlogDetail>>(`/v1/blog/${encodeURIComponent(slug)}`);

  return res?.data ?? null;
}
