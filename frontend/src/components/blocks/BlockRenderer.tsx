import type { PageBlockData, SiteSettings } from '@/lib/api';
import { ContactFormBlock } from './ContactFormBlock';
import { CtaBannerBlock } from './CtaBannerBlock';
import { DienstBoxesBlock } from './DienstBoxesBlock';
import { HeroBlock } from './HeroBlock';
import { LogosBlock } from './LogosBlock';
import { ProjectsBlock } from './ProjectsBlock';
import { ReviewsBlock } from './ReviewsBlock';
import { RichTextBlock } from './RichTextBlock';
import { SolutionCardsBlock } from './SolutionCardsBlock';
import { StatsBlock } from './StatsBlock';
import { TextImageBlock } from './TextImageBlock';

/**
 * Renders one CMS block. `block.type` matches a backend widget key (see
 * App\Widgets\*::type()); the data is handed to the matching React component.
 *
 * Every homepage section is a widget — register each new one here as it is
 * sliced, keeping the key identical to the backend widget type.
 *
 * `settings` and `source` are only needed by widgets that read site-wide data
 * or record where a submission came from.
 */
export function BlockRenderer({
  block,
  settings = {},
  source = '',
}: {
  block: PageBlockData;
  settings?: SiteSettings;
  source?: string;
}) {
  const data = block.data as Record<string, unknown>;

  switch (block.type) {
    case 'hero':
      return <HeroBlock data={data} />;
    case 'logos':
      return <LogosBlock data={data} />;
    case 'solution_cards':
      return <SolutionCardsBlock data={data} />;
    case 'dienst_boxes':
      return <DienstBoxesBlock data={data} />;
    case 'reviews':
      return <ReviewsBlock data={data} />;
    case 'projects':
      return <ProjectsBlock data={data} />;
    case 'cta_banner':
      return <CtaBannerBlock data={data} />;
    case 'text_image':
      return <TextImageBlock data={data} />;
    case 'stats':
      return <StatsBlock data={data} />;
    case 'contact_form':
      return <ContactFormBlock data={data} settings={settings} source={source} />;
    case 'rich_text':
      return <RichTextBlock data={data} />;
    default:
      return process.env.NODE_ENV !== 'production' ? (
        <div className="mx-auto max-w-[1800px] px-6 py-6 text-sm text-red-500">
          Onbekende widget: {block.type}
        </div>
      ) : null;
  }
}

