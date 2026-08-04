import Link from 'next/link';
import { safeUrl } from '@/lib/safeUrl';
import type { FooterColumn, SiteSettings } from '@/lib/api';

/**
 * Section 10 — the footer (Figma 31789:1872). #F7F7F7, four columns, a #DDDDDD
 * divider and a centred copyright line.
 *
 * Fully CMS-driven: the columns come from the Filament "Footer" page
 * (BlockLayout → columns → widget blocks). Each block type maps to a widget
 * component below; contact details and socials fall back to Instellingen.
 */
export function Footer({ columns, settings }: { columns: FooterColumn[]; settings: SiteSettings }) {
  const year = new Date().getFullYear();
  const company = settings.company_name?.trim() || 'Johan Willemsen Computers';

  return (
    <footer className="w-full bg-[color:var(--color-surface-muted)] text-[color:var(--color-ink)]">
      <div className="mx-auto max-w-[1800px] px-6 pt-[79px] pb-10 md:px-12 xl:px-[100px]">
        <div className="grid gap-x-[102px] gap-y-12 sm:grid-cols-2 xl:grid-cols-[minmax(0,415px)_minmax(0,246px)_minmax(0,260px)_minmax(0,358px)] xl:justify-between">
          {columns.map((column) => (
            <div key={column.id}>
              {column.blocks.map((block, i) => (
                <FooterBlock key={i} type={block.type} data={block.data} settings={settings} />
              ))}
            </div>
          ))}
        </div>
      </div>

      <div className="border-t border-[color:var(--color-border)]">
        <p className="px-6 py-4 text-center font-body text-[16px] leading-[30px] text-[color:var(--color-ink)]">
          Copyright © {year} {company}
        </p>
      </div>
    </footer>
  );
}

function FooterBlock({
  type,
  data,
  settings,
}: {
  type: string;
  data: Record<string, unknown>;
  settings: SiteSettings;
}) {
  switch (type) {
    case 'about':
      return <AboutColumn data={data} settings={settings} />;
    case 'link_list':
      return <LinkListColumn data={data} />;
    case 'contact_info':
      return <ContactColumn data={data} settings={settings} />;
    default:
      return null;
  }
}

/** Column 1 — logo, intro paragraph, company numbers and social icons. */
function AboutColumn({ data, settings }: { data: Record<string, unknown>; settings: SiteSettings }) {
  const showLogo = data.show_logo !== false;
  const showSocials = data.show_socials !== false;
  const text = typeof data.text === 'string' && data.text.trim() !== '' ? data.text : settings.tagline || '';
  const vat = settings.company_vat?.trim();
  const coc = settings.company_coc?.trim();

  // Sizes are each icon's intrinsic aspect ratio (the exported SVGs use
  // preserveAspectRatio="none", so they must not be squeezed into one box).
  const socials = [
    { key: 'social_twitter', src: '/images/design/social-twitter.svg', label: 'X / Twitter', cls: 'h-[22px] w-[27px]' },
    { key: 'social_mail', src: '/images/design/social-mail.svg', label: 'E-mail', cls: 'h-[24px] w-[30px]' },
    { key: 'social_facebook', src: '/images/design/social-facebook.svg', label: 'Facebook', cls: 'h-[26px] w-[13px]' },
  ];

  const socialHref = (key: string): string =>
    key === 'social_mail'
      ? settings.contact_email
        ? `mailto:${settings.contact_email}`
        : ''
      : settings[key] || '';

  return (
    <div>
      {showLogo && (
        <Link href="/" className="inline-block" aria-label="Naar de homepage">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src={settings.logo?.trim() || '/images/design/logo.png'}
            alt={settings.company_name || 'Johan Willemsen Computers'}
            className="h-[158px] w-auto"
          />
        </Link>
      )}

      {text && (
        <p className="mt-4 max-w-[415px] font-body text-[18px] font-medium leading-[30px] whitespace-pre-line">
          {text}
        </p>
      )}

      {(vat || coc) && (
        <p className="mt-[30px] font-body text-[18px] font-bold leading-[30px]">
          {vat && <>BTW: {vat}</>}
          {vat && coc && <br />}
          {coc && <>KVK: {coc}</>}
        </p>
      )}

      {showSocials && (
        <div className="mt-8 flex h-[26px] items-center gap-[15px]">
          {socials.map((s) => {
            const href = socialHref(s.key);
            if (!href) return null;
            return (
              <a
                key={s.key}
                href={safeUrl(href)}
                aria-label={s.label}
                target={href.startsWith('http') ? '_blank' : undefined}
                rel={href.startsWith('http') ? 'noopener noreferrer' : undefined}
                className="flex items-center transition hover:opacity-70"
              >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={s.src} alt="" className={s.cls} />
              </a>
            );
          })}
        </div>
      )}
    </div>
  );
}

/** Columns 2 & 3 — a heading plus "> label" links. */
function LinkListColumn({ data }: { data: Record<string, unknown> }) {
  const heading = typeof data.heading === 'string' ? data.heading : '';
  const items = Array.isArray(data.items) ? (data.items as Record<string, unknown>[]) : [];

  return (
    <div>
      {heading && <h2 className="font-body text-[18px] font-bold leading-[35px]">{heading}</h2>}
      <ul className="mt-[12px] xl:mt-[35px]">
        {items.map((item, i) => (
          <li key={i}>
            <Link
              href={safeUrl(typeof item.href === 'string' ? item.href : '')}
              className="block font-body text-[18px] font-medium leading-[35px] transition-colors hover:text-[color:var(--color-brand)]"
            >
              <span aria-hidden="true">&gt; </span>
              {typeof item.label === 'string' ? item.label : ''}
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}

/** Column 4 — address (medium) and the contactable details (bold). */
function ContactColumn({ data, settings }: { data: Record<string, unknown>; settings: SiteSettings }) {
  // "Uit Instellingen halen" keeps the contact details in exactly one place.
  const fromSettings = data.from_settings !== false;
  const pick = (key: string, settingKey: string): string =>
    (fromSettings ? settings[settingKey] : (data[key] as string)) || '';

  const heading = (typeof data.heading === 'string' && data.heading) || settings.footer_contact_heading || 'Contact';
  const company = pick('company', 'company_name');
  const line1 = pick('address_line1', 'contact_address_line1');
  const line2 = pick('address_line2', 'contact_address_line2');
  const vat = pick('vat', 'company_vat');
  const coc = pick('coc', 'company_coc');
  const email = pick('email', 'contact_email');
  const phone = pick('phone', 'contact_phone');
  const hours = settings.contact_phone_hours?.trim();
  const fax = pick('fax', 'contact_fax');

  const plain = [company, line1, line2, vat && `BTW nr: ${vat}`, coc && `KVK: ${coc}`].filter(Boolean) as string[];

  return (
    <div>
      {heading && <h2 className="font-body text-[18px] font-bold leading-[35px]">{heading}</h2>}
      <div className="mt-[12px] xl:mt-[35px]">
        {plain.map((line, i) => (
          <p key={i} className="font-body text-[18px] font-medium leading-[35px]">
            {line}
          </p>
        ))}
        {email && (
          <p className="font-body text-[18px] font-bold leading-[35px]">
            <a href={`mailto:${email}`} className="transition-colors hover:text-[color:var(--color-brand)]">
              {email}
            </a>
          </p>
        )}
        {phone && (
          <p className="font-body text-[18px] font-bold leading-[35px]">
            <a
              href={`tel:${phone.replace(/[^0-9+]/g, '')}`}
              className="transition-colors hover:text-[color:var(--color-brand)]"
            >
              T: {phone}
            </a>
            {hours && ` (${hours})`}
          </p>
        )}
        {fax && <p className="font-body text-[18px] font-bold leading-[35px]">Fax: {fax}</p>}
      </div>
    </div>
  );
}
