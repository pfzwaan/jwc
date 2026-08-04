import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { SmartImage } from '@/components/ui/SmartImage';
import { safeUrl } from '@/lib/safeUrl';
import { arr, str } from '@/lib/blockData';

/**
 * Section 4 — the client logo strip (Figma 31774:6255). 147px band, logos
 * evenly spread and vertically centred, capped at 60px tall so mixed logo
 * shapes stay on one optical line.
 */
export function LogosBlock({ data }: { data: Record<string, unknown> }) {
  const heading = str(data.heading);
  const grayscale = data.grayscale === true;
  const logos = arr(data.logos).filter((l) => str(l.image) !== '' || str(l.name) !== '');

  if (logos.length === 0) return null;

  return (
    <section className="w-full bg-white">
      <div className="mx-auto max-w-[1800px] px-6 py-10 md:px-12 xl:px-[100px]">
        {heading && (
          <AnimateIn>
            <h2 className="mb-8 text-center font-heading text-[22px] font-extrabold text-[color:var(--color-ink)]">
              {heading}
            </h2>
          </AnimateIn>
        )}

        <AnimateIn>
          <div className="flex min-h-[147px] flex-wrap items-center justify-between gap-x-10 gap-y-8">
            {logos.map((logo, i) => (
              <LogoItem
                key={i}
                image={str(logo.image)}
                name={str(logo.name)}
                href={str(logo.href)}
                grayscale={grayscale}
              />
            ))}
          </div>
        </AnimateIn>
      </div>
    </section>
  );
}

function LogoItem({
  image,
  name,
  href,
  grayscale,
}: {
  image: string;
  name: string;
  href: string;
  grayscale: boolean;
}) {
  const img = (
    <SmartImage
      src={image}
      alt={name}
      className={`h-[60px] w-auto max-w-[300px] object-contain ${
        grayscale ? 'grayscale transition duration-300 hover:grayscale-0' : ''
      }`}
    />
  );

  if (href.trim() === '') return img;

  return (
    <Link
      href={safeUrl(href)}
      target={href.startsWith('http') ? '_blank' : undefined}
      rel={href.startsWith('http') ? 'noopener noreferrer' : undefined}
      aria-label={name || undefined}
    >
      {img}
    </Link>
  );
}
