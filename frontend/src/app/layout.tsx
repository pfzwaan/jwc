import type { Metadata } from 'next';
import { DM_Sans, Inter, Plus_Jakarta_Sans } from 'next/font/google';
import './globals.css';
import { fetchFooter, fetchMenu, fetchSettings } from '@/lib/api';
import { Header } from '@/components/layout/Header';
import { TopBar } from '@/components/layout/TopBar';
import { Footer } from '@/components/layout/Footer';

// Sliced from Figma: Plus Jakarta Sans for headings/buttons, Inter for body.
const jakarta = Plus_Jakarta_Sans({
  variable: '--font-jakarta',
  subsets: ['latin'],
  weight: ['400', '500', '600', '700', '800'],
  display: 'swap',
});

const inter = Inter({
  variable: '--font-inter',
  subsets: ['latin'],
  weight: ['400', '500', '600', '700'],
  display: 'swap',
});

// Used for the card bodies and "Meer info" links (Figma 31783:6350).
const dmSans = DM_Sans({
  variable: '--font-dm-sans',
  subsets: ['latin'],
  weight: ['400', '500', '700'],
  display: 'swap',
});

/**
 * Regenerate every route at most once a minute. The lowest `revalidate` across
 * a route's layouts and pages governs the whole route, so setting it here means
 * the CMS-driven header and footer below can never be stale by more than this —
 * the menu and footer are fetched in this layout, and a build-time failure there
 * used to ship an empty <nav> that only a rebuild could fix.
 */
export const revalidate = 60;

export const metadata: Metadata = {
  title: {
    default: 'Johan Willemsen Computers — uw betrouwbare ICT-partner',
    // Routes that fall back to a plain page title get the brand appended here,
    // so no route has to repeat it. A staff-written SEO title bypasses this via
    // `absolute` (see lib/seo.ts).
    template: '%s — Johan Willemsen Computers',
  },
  description:
    'Johan Willemsen Computers is uw betrouwbare ICT-specialist in Opheusden. Van computerreparaties en netwerkbeheer tot cloudoplossingen, websites en complete systeemondersteuning.',
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  // Header/footer are CMS-driven; fetched once here so every route gets them.
  const [menu, settings, footerColumns] = await Promise.all([
    fetchMenu(),
    fetchSettings(),
    fetchFooter(),
  ]);

  return (
    <html lang="nl" className={`${jakarta.variable} ${inter.variable} ${dmSans.variable} h-full antialiased`}>
      <body className="flex min-h-full flex-col">
        <TopBar settings={settings} />
        {/* The menu bar floats over the hero (Figma: bar at y=104, hero at y=60),
            so it is absolutely positioned inside this wrapper rather than taking
            up flow height. Pages that don't open with a full-bleed hero add
            their own top padding. */}
        <div className="relative flex flex-1 flex-col">
          <Header menu={menu} settings={settings} />
          <main className="flex-1">{children}</main>
        </div>
        <Footer columns={footerColumns} settings={settings} />
      </body>
    </html>
  );
}
