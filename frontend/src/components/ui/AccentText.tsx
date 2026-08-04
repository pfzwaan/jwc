import { Fragment } from 'react';

/**
 * Renders a heading where words wrapped in *asterisks* take the brand colour —
 * e.g. "Wij zijn *Johan Willemsen* Computers" (Figma 31837:1042).
 *
 * A single CMS field rather than the lead/accent/tail triplet: the highlight can
 * sit anywhere in the sentence, and staff only fill in one box.
 */
export function AccentText({ text, accent }: { text: string; accent?: string }) {
  // Split on *…* keeping the delimiters' contents.
  const parts = text.split(/\*([^*]+)\*/g);

  return (
    <>
      {parts.map((part, i) =>
        // Odd indices are the captured groups, i.e. the highlighted runs.
        i % 2 === 1 ? (
          <span key={i} style={accent ? { color: accent } : undefined} className={accent ? undefined : 'text-[color:var(--color-brand)]'}>
            {part}
          </span>
        ) : (
          <Fragment key={i}>{part}</Fragment>
        ),
      )}
    </>
  );
}
