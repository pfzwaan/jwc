import { SmartImage } from '@/components/ui/SmartImage';

/**
 * Laptop + phone mockup from the project detail hero (Figma 31852:2868).
 *
 * The bezels are the exported Figma chrome; the screenshots come from the CPT
 * and sit in the exact screen insets the design uses — laptop main screen
 * inset 6.8%/12.32%, phone screen inset 1.94%/4.44%/1.94%/4.67%.
 *
 * The phone shows a dedicated portrait screenshot when the project has one.
 * Without one it falls back to the desktop shot scaled to the phone's *width*
 * (top-anchored, overflow clipped) — that reads as a preview of the site's
 * header. Scaling it to fill the height instead, as object-cover does, crops
 * the sides away and leaves a meaningless vertical strip.
 */
export function DeviceMockup({
  image,
  imageMobile,
  alt,
}: {
  image: string;
  imageMobile?: string;
  alt: string;
}) {
  const phoneShot = imageMobile?.trim() || image;
  const hasDedicatedShot = Boolean(imageMobile && imageMobile.trim() !== '');

  return (
    <div className="relative mx-auto aspect-[1237/529] w-full max-w-[1237px]">
      {/* Laptop — 949×529 of the 1237 frame, anchored left. */}
      <div className="absolute left-0 top-0 h-full w-[76.72%]">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src="/images/design/device-laptop-screen.svg"
          alt=""
          aria-hidden="true"
          className="absolute inset-[0_9.49%_1.82%_9.5%] h-auto w-[81.01%]"
        />
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src="/images/design/device-laptop-inner.svg"
          alt=""
          aria-hidden="true"
          className="absolute inset-[0.92%_10.09%_5.73%_10.13%]"
        />

        {/* Screen — the project screenshot, cropped from the top. */}
        <div className="absolute inset-[6.8%_12.32%_10.3%_12.32%] overflow-hidden bg-[#0f0f10]">
          <SmartImage src={image} alt={alt} className="h-full w-full object-cover object-top" />
        </div>

        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src="/images/design/device-laptop-base.svg"
          alt=""
          aria-hidden="true"
          className="absolute inset-[97.05%_0_0_0] w-full"
        />
      </div>

      {/* Phone — 256×524 at x1263/y478 inside the 1237×529 group, i.e.
          left 79.3%, top 0.57%, 20.7% × 99.05%. Derived from the coordinates,
          not Figma's exported `inset` percentages: those are relative to a
          different ancestor and produced a 256×394 box, whose wrong aspect
          letterboxed the frame and let the screen bleed past its edge. */}
      <div className="absolute left-[79.3%] top-[0.57%] hidden h-[99.05%] w-[20.7%] md:block">
        <div className="absolute inset-[0_1%] rounded-[50px] bg-black shadow-[0px_0px_50px_0px_rgba(0,0,0,0.4)]" />

        <div className="absolute inset-[1.94%_4.44%_1.94%_4.67%] overflow-hidden rounded-[15px] bg-white">
          <SmartImage
            src={phoneShot}
            alt=""
            className={
              hasDedicatedShot
                ? // A real portrait shot fills the screen properly.
                  'h-full w-full object-cover object-top'
                : // A desktop shot: match the width and let the height overflow.
                  'w-full'
            }
          />
        </div>

        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src="/images/design/device-phone.png"
          alt=""
          aria-hidden="true"
          // contain, now that the box matches the frame's aspect (0.489) — cover
          // would crop the bezel and notch away.
          className="pointer-events-none absolute inset-0 h-full w-full object-contain"
        />
      </div>
    </div>
  );
}
