import Link from 'next/link';

/**
 * Explicit consent before a submission is stored. The backend validates this
 * as `accepted`, so an unchecked box is rejected server-side too — the client
 * is never trusted.
 */
export function ConsentCheckbox({ privacyHref = '/privacy-policy' }: { privacyHref?: string }) {
  return (
    <label className="flex cursor-pointer items-start gap-3 font-body text-[16px] leading-[26px] text-[color:var(--color-ink)]">
      <input
        type="checkbox"
        name="consent"
        required
        className="mt-[5px] size-[18px] shrink-0 cursor-pointer accent-[color:var(--color-brand)]"
      />
      <span>
        Ik ga akkoord met de verwerking van mijn gegevens zoals beschreven in de{' '}
        <Link href={privacyHref} className="font-semibold underline hover:text-[color:var(--color-brand)]">
          privacyverklaring
        </Link>
        .
      </span>
    </label>
  );
}
