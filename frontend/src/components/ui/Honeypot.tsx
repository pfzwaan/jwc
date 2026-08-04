/**
 * Spam honeypot. Humans never see or fill this; bots tend to. The backend
 * rejects any submission where it arrives non-empty
 * (App\Http\Middleware\Honeypot::FIELD).
 *
 * Hidden with an off-screen wrapper rather than `type="hidden"` so that
 * form-filling bots still treat it as a real field.
 */
export function Honeypot() {
  return (
    <div aria-hidden="true" className="absolute left-[-9999px] top-auto h-px w-px overflow-hidden">
      <label htmlFor="website">Laat dit veld leeg</label>
      <input id="website" name="website" type="text" tabIndex={-1} autoComplete="off" defaultValue="" />
    </div>
  );
}
