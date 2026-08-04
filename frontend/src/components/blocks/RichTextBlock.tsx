import { AnimateIn } from '@/components/ui/AnimateIn';
import { str } from '@/lib/blockData';

/** Generic heading + WYSIWYG body section. */
export function RichTextBlock({ data }: { data: Record<string, unknown> }) {
  const heading = str(data.heading);
  const body = str(data.body);

  return (
    <section className="w-full py-16 md:py-24">
      <div className="mx-auto max-w-[900px] px-6 md:px-12">
        {heading && (
          <AnimateIn>
            <h2 className="font-heading text-[clamp(28px,3vw,44px)] font-extrabold leading-tight text-[color:var(--color-ink)]">
              {heading}
            </h2>
          </AnimateIn>
        )}
        {body && (
          <AnimateIn>
            {/* Staff-authored rich text (Filament RichEditor). */}
            <div
              className="mt-6 text-[18px] leading-relaxed text-[color:var(--color-ink)] [&_a]:text-[color:var(--color-brand)] [&_a]:underline [&_h3]:mt-8 [&_h3]:font-heading [&_h3]:text-[24px] [&_h3]:font-bold [&_li]:ml-5 [&_li]:list-disc [&_p]:mb-4 [&_ul]:my-4"
              dangerouslySetInnerHTML={{ __html: body }}
            />
          </AnimateIn>
        )}
      </div>
    </section>
  );
}
