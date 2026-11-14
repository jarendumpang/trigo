<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add More Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap");

    :root {
      --g: #084f7f;   /* main color (buttons, icons, progress) */
      --n: #01455a;   /* text */
      --m: #758787;   /* muted text */
      --l: #e2e8ea;   /* borders */
      --bg: #e1eef0;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background: var(--bg);
      color: var(--n);
      font: 14px "DM Sans", sans-serif;
    }

    .screen {
      max-width: 480px;
      min-height: 100vh;
      margin: 0 auto;
      background: #fff;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    /* ---------- Header ---------- */
    .topbar {
      display: grid;
      grid-template-columns: 40px 1fr 40px;
      align-items: center;
      padding: 22px 16px 0;
    }
    .back {
      border: 0;
      background: none;
      color: var(--n);
      font-size: 22px;
      padding: 6px;
      cursor: pointer;
      text-align: left;
    }
    .topbar h1 {
      margin: 0;
      text-align: center;
      font: 700 19px Manrope, sans-serif;
      letter-spacing: -0.3px;
    }
    .steps {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin: 12px 0 22px;
    }
    .steps span {
      width: 62px;
      height: 4px;
      border-radius: 4px;
      background: var(--g);
    }

    /* ---------- Fields ---------- */
    form { flex: 1; display: flex; flex-direction: column; }
    .fields { padding: 0 16px; flex: 1; }

    .field {
      display: flex;
      align-items: center;
      gap: 18px;
      padding: 16px 14px;
    }
    .field.boxed {
      border: 1px solid var(--l);
      border-radius: 12px;
      box-shadow: 0 1px 4px #0001;
      margin-bottom: 8px;
    }
    .field + .field:not(.boxed) { border-top: 1px solid var(--l); }

    .field > i {
      font-size: 24px;
      color: var(--g);
      width: 28px;
      text-align: center;
      flex-shrink: 0;
    }
    .field label {
      display: block;
      font-size: 12px;
      color: var(--m);
      font-weight: 500;
      margin-top: 2px;
    }
    .field .body { flex: 1; min-width: 0; }

    .field input,
    .field textarea {
      width: 100%;
      border: 0;
      outline: 0;
      padding: 0;
      margin: 0;
      background: transparent;
      font: 600 16px "DM Sans", sans-serif;
      color: var(--n);
      resize: none;
    }
    .field input::placeholder,
    .field textarea::placeholder {
      color: #8a9696;
      font-weight: 500;
    }
    .field:focus-within { background: #f4f9fa; border-radius: 12px; }

    /* ---------- Bottom sheet ---------- */
    .sheet {
      position: sticky;
      bottom: 0;
      background: #fff;
      border-radius: 22px 22px 0 0;
      padding: 20px 16px 26px;
      box-shadow: 0 -8px 24px #084f7f14;
    }
    .total {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 18px;
    }
    .total .label { font-size: 16px; padding-top: 6px; }
    .total .amount {
      font: 800 34px Manrope, sans-serif;
      letter-spacing: -1px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .total .amount i { font-size: 20px; color: var(--m); }

    .submit {
      width: 100%;
      border: 0;
      border-radius: 12px;
      padding: 17px;
      background: var(--g);
      color: #fff;
      font: 800 18px Manrope, sans-serif;
      cursor: pointer;
    }
    .submit:hover { filter: brightness(0.94); }
    .submit:focus-visible { outline: 3px solid #bae8f0; }
  </style>
</head>
<body>
  <div class="screen">

    <div class="topbar">
      <button type="button" class="back" onclick="history.back()" aria-label="Back">
        <i class="bi bi-chevron-left"></i>
      </button>
      <h1>Add More Details</h1>
      <span></span>
    </div>
    <div class="steps"><span></span><span></span></div>

    <form id="additionalInfoForm">
      <div class="fields">

        <div class="field boxed">
          <i class="bi bi-chat-left-text"></i>
          <div class="body">
            <textarea id="notes" name="notes" rows="1" placeholder="Add notes to your driver"></textarea>
          </div>
        </div>

        <div class="field">
          <i class="bi bi-telephone"></i>
          <div class="body">
            <input type="tel" id="contact" name="contact" placeholder="+63 9XX XXX XXXX" inputmode="tel" required>
            <label for="contact">Order Contact Number</label>
          </div>
        </div>

        <div class="field">
          <i class="bi bi-geo-alt"></i>
          <div class="body">
            <input type="text" id="location" name="location" placeholder="Enter your location" required>
            <label for="location">Location</label>
          </div>
        </div>

      </div>

      <div class="sheet">
        <div class="total">
          <span class="label">Total</span>
          <span class="amount"><span id="totalAmount">&#8369;0.00</span><i class="bi bi-info-circle"></i></span>
        </div>
        <button type="submit" class="submit">Review Order</button>
      </div>
    </form>

  </div>
</body>
</html>