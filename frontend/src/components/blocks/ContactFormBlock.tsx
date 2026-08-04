import { AnimateIn } from '@/components/ui/AnimateIn';
import { ContactForm } from '@/components/contact/ContactForm';
import type { SiteSettings } from '@/lib/api';
import { arr, str } from '@/lib/blockData';

/**
 * Contact / offerte section (Figma 31853:779 — the "Form" block on the project
 * detail page).
 *
 * Heading top-left, then the form (938fr) beside two aside cards (602fr): a
 * dark "Liever direct bellen?" card with the phone number, and a light
 * "Afspraak plannen" card. Both read their content from Instellingen.
 */
export function ContactFormBlock({
  data,
  settings,
  source,
}: {
  data: Record<string, unknown>;
  settings: SiteSettings;
  source: string;
}) {
  const heading = str(data.heading);
  const intro = str(data.intro);
  const showAside = data.show_details !== false;
  const submitLabel = str(data.submit_label) || 'Bericht versturen';
  const subjects = arr(data.subjects)
    .map((s) => str(s.label))
    .filter(Boolean);

  return (
    <section className="w-full bg-white">
      <div className="mx-auto max-w-[1800px] px-6 py-[100px] md:px-12 xl:px-[100px]">
        {(heading || intro) && (
          <AnimateIn>
            <div className="mb-[52px]">
              {heading && (
                <h2 className="max-w-[876px] font-heading text-[clamp(26px,2.8vw,40px)] font-extrabold leading-[1.2] text-[color:var(--color-ink)]">
                  {heading}
                </h2>
              )}
              {intro && (
                <p className="mt-[16px] max-w-[915px] font-body text-[clamp(17px,1.3vw,20px)] leading-[1.7] text-[color:var(--color-ink)] 2xl:leading-[36.563px]">
                  {intro}
                </p>
              )}
            </div>
          </AnimateIn>
        )}

        <div className={`grid gap-[58px] ${showAside ? 'xl:grid-cols-[938fr_602fr]' : ''}`}>
          <AnimateIn>
            <ContactForm subjects={subjects} source={source} submitLabel={submitLabel} />
          </AnimateIn>

          {showAside && (
            <AnimateIn delay={0.08}>
              <div className="flex flex-col gap-[28px]">
                <CallCard settings={settings} heading={str(data.details_heading) || 'Liever direct bellen?'} />
                <AppointmentCard />
              </div>
            </AnimateIn>
          )}
        </div>
      </div>
    </section>
  );
}

/** Dark card with the phone number (Figma 31853:819). */
function CallCard({ settings, heading }: { settings: SiteSettings; heading: string }) {
  const phone = settings.contact_phone?.trim();
  if (!phone) return null;

  return (
    <div className="rounded-[20.758px] bg-[color:var(--color-ink-deep)] p-[36px]">
      <h3 className="font-heading text-[24px] font-bold leading-[36.3px] text-white">{heading}</h3>

      <a
        href={`tel:${phone.replace(/[^0-9+]/g, '')}`}
        className="mt-[26px] flex items-center gap-[15px] transition hover:opacity-80"
      >
        <span className="grid size-[52px] shrink-0 place-items-center rounded-[18.163px] bg-white/15">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src="/images/design/icon-phone.svg" alt="" aria-hidden="true" className="size-[21px]" />
        </span>
        <span className="font-body text-[clamp(18px,1.5vw,20.758px)] font-bold leading-[31px] text-white">
          {phone}
        </span>
      </a>
    </div>
  );
}

/** Light card inviting an appointment (Figma 31853:851). */
function AppointmentCard() {
  return (
    <div className="rounded-[20.758px] border-[1.297px] border-[color:var(--color-card-border)] bg-[color:var(--color-surface-alt)] p-[31px]">
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img src="/images/design/icon-calendar.svg" alt="" aria-hidden="true" className="size-[34px]" />

      <h3 className="mt-[8px] font-heading text-[24px] font-bold leading-[36.3px] text-[color:var(--color-card-title)]">
        Afspraak plannen
      </h3>

      <p className="mt-[10px] max-w-[537px] font-body text-[18px] leading-[29.5px] text-[color:var(--color-ink)]">
        Liever een vaste tijd afspreken? Bel ons of stuur een bericht en wij plannen een afspraak die u uitkomt.
      </p>

      <p className="mt-[24px] flex items-center gap-[10px] font-body text-[18px] font-semibold leading-[26px] text-[color:var(--color-brand)]">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img src="/images/design/icon-clock.svg" alt="" aria-hidden="true" className="size-[18px]" />
        Altijd binnen 1 werkdag reactie
      </p>
    </div>
  );
}
