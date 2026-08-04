'use client';

import { useState } from 'react';
import { apiFetch } from '@/lib/apiClient';
import { ArrowChevron } from '@/components/ui/ArrowChevron';
import { ConsentCheckbox } from '@/components/ui/ConsentCheckbox';
import { Honeypot } from '@/components/ui/Honeypot';

type Status = { state: 'idle' | 'sending' | 'sent' | 'error'; message?: string };

/**
 * Contact / offerte form (Figma 31854:960).
 *
 * Naam + E-mailadres side by side, then telefoon, onderwerp and bericht full
 * width, with a full-width button. Fields are #f9fafb on a 1.248px #e5e7eb
 * border, rounded-[17.469px]; labels are uppercase Inter SemiBold 15/0.374.
 *
 * Posts to /api/v1/contact, which is rate-limited, honeypot-guarded and
 * validates every field again server-side.
 */
export function ContactForm({
  subjects,
  source,
  submitLabel,
}: {
  subjects: string[];
  source: string;
  submitLabel: string;
}) {
  const [status, setStatus] = useState<Status>({ state: 'idle' });

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form).entries());

    setStatus({ state: 'sending' });

    try {
      const res = await apiFetch<{ message: string }>('/v1/contact', {
        method: 'POST',
        body: JSON.stringify({ ...data, consent: data.consent === 'on', source }),
      });
      setStatus({ state: 'sent', message: res.message });
      form.reset();
    } catch (error) {
      setStatus({
        state: 'error',
        message: error instanceof Error ? error.message : 'Er ging iets mis. Probeer het later opnieuw.',
      });
    }
  }

  if (status.state === 'sent') {
    return (
      <div
        role="status"
        className="rounded-[17.469px] border border-[color:var(--color-success)] bg-[color:var(--color-success)]/10 p-10 text-center"
      >
        <p className="font-heading text-[24px] font-bold text-[color:var(--color-ink)]">Bedankt!</p>
        <p className="mt-2 font-body text-[18px] leading-[29.5px] text-[color:var(--color-ink)]">{status.message}</p>
      </div>
    );
  }

  return (
    <form onSubmit={onSubmit} className="flex flex-col gap-[21px]">
      <Honeypot />

      <div className="grid gap-[20px] md:grid-cols-2">
        <Field name="name" label="Naam" placeholder="Jan de Vries" required />
        <Field name="email" label="E-mailadres" type="email" placeholder="jan@uw-bedrijf.nl" required />
      </div>

      <Field name="phone" label="Telefoonnummer" type="tel" placeholder="0488-123456" />

      {subjects.length > 0 ? (
        <label className="flex flex-col gap-[8px]">
          <FieldLabel>Onderwerp</FieldLabel>
          <select name="subject" className={`${INPUT} appearance-none`}>
            <option value="">— Kies een onderwerp —</option>
            {subjects.map((s) => (
              <option key={s} value={s}>
                {s}
              </option>
            ))}
          </select>
        </label>
      ) : (
        <Field name="subject" label="Onderwerp" placeholder="Bijv. offerte aanvragen, computerstoring, …" />
      )}

      <label className="flex flex-col gap-[8px]">
        <FieldLabel>Uw bericht</FieldLabel>
        <textarea
          name="message"
          rows={5}
          placeholder="Omschrijf uw vraag of probleem zo specifiek mogelijk."
          className={`${INPUT} h-[160px] resize-y py-[16px] leading-[26px]`}
        />
      </label>

      <ConsentCheckbox />

      {status.state === 'error' && (
        <p role="alert" className="font-body text-[16px] text-red-600">
          {status.message}
        </p>
      )}

      <button
        type="submit"
        disabled={status.state === 'sending'}
        className="inline-flex h-[65px] w-full items-center justify-center gap-2 rounded-[10px] bg-[color:var(--color-brand)] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {status.state === 'sending' ? 'Versturen…' : submitLabel}
        <ArrowChevron />
      </button>
    </form>
  );
}

/** Figma 31853:927 — #f9fafb on #e5e7eb, 57.4px tall, 20px inset. */
const INPUT =
  'w-full rounded-[17.469px] border-[1.248px] border-[#e5e7eb] bg-[#f9fafb] px-[20px] font-body text-[17.469px] text-[color:var(--color-ink)] outline-none transition placeholder:text-[#bababa] focus:border-[color:var(--color-brand)]';

function FieldLabel({ children }: { children: React.ReactNode }) {
  return (
    <span className="font-body text-[15px] font-semibold uppercase leading-5 tracking-[0.374px] text-[color:var(--color-ink)]">
      {children}
    </span>
  );
}

function Field({
  name,
  label,
  type = 'text',
  placeholder,
  required = false,
}: {
  name: string;
  label: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
}) {
  return (
    <label className="flex flex-col gap-[8px]">
      <FieldLabel>
        {label}
        {required && <span aria-hidden="true"> *</span>}
      </FieldLabel>
      <input
        name={name}
        type={type}
        required={required}
        placeholder={placeholder}
        autoComplete={AUTOCOMPLETE[name] ?? 'on'}
        className={`${INPUT} h-[57.4px]`}
      />
    </label>
  );
}

const AUTOCOMPLETE: Record<string, string> = {
  name: 'name',
  email: 'email',
  phone: 'tel',
  company: 'organization',
};
