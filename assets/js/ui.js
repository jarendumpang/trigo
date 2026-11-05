// /* Shared browser interactions only.
//    HTML holds each page's design. PHP will handle accounts, roles, and saved data.
//    No page rendering, role switching, authentication, or browser storage here. */

// const dialog = document.querySelector('#ui-dialog');

// // textContent keeps text typed by a visitor from becoming HTML.
// function showDialog(title, message) {
//   document.querySelector('#ui-dialog-title').textContent = title;
//   document.querySelector('#ui-dialog-content').textContent = message;
//   if (!dialog.open) dialog.showModal();
// }

// function money(value) {
//   return '₱' + value.toLocaleString('en-PH', { maximumFractionDigits: 2 });
// }

// function validInputs(container) {
//   for (const input of container.querySelectorAll('input, select, textarea')) {
//     if (!input.reportValidity()) return false;
//   }
//   return true;
// }

// // Filters only the rows already printed in HTML. Nothing is fetched or changed.
// document.querySelectorAll('[data-filter]').forEach(function (input) {
//   const container = input.closest('.card') || document;
//   const rows = container.querySelectorAll('tr[data-person]');
//   input.addEventListener('input', function () {
//     const query = input.value.trim().toLowerCase();
//     rows.forEach(function (row) {
//       row.hidden = !row.textContent.toLowerCase().includes(query);
//     });
//   });
// });

// // Keep sample chart data separate from the code that draws it.
// const charts = {
//   Daily: { values: [22, 35, 60, 40, 105, 125, 78], labels: ['6 AM', '8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM'], days: 1 },
//   Weekly: { values: [54, 80, 68, 98, 87, 139, 108], labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], days: 7 },
//   Monthly: { values: [50, 68, 92, 80, 120, 113, 147], labels: ['1–4', '5–8', '9–12', '13–16', '17–20', '21–24', '25–31'], days: 30 },
//   Yearly: { values: [35, 55, 70, 105, 95, 139, 150], labels: ['Jan', 'Mar', 'May', 'Jul', 'Sep', 'Nov', 'Dec'], days: 365 }
// };

// function selectPeriod(button) {
//   const period = button.dataset.period;
//   const sample = charts[period];
//   const tabs = button.closest('.tabs');
//   const card = tabs.closest('.card');
//   tabs.querySelectorAll('button').forEach(function (tab) {
//     const selected = tab === button;
//     tab.classList.toggle('active', selected);
//     tab.setAttribute('aria-pressed', selected);
//   });
//   card.querySelectorAll('.bar').forEach(function (bar, i) {
//     bar.style.height = sample.values[i] + 'px';
//     bar.title = sample.values[i] + ' sample units';
//   });
//   card.querySelectorAll('.chartlabels span').forEach(function (label, i) {
//     label.textContent = sample.labels[i];
//   });
//   const subtitle = card.querySelector('.row .small.muted');
//   if (subtitle) subtitle.textContent = period + ' activity · sample data';

//   // Update the nearby example summary, never database records.
//   const grid = card.closest('.grid');
//   if (!grid) return;
//   grid.querySelectorAll('.card').forEach(function (summary) {
//     const totalLabel = Array.from(summary.querySelectorAll('span')).find(function (span) {
//       return span.textContent.startsWith('Total customer fares');
//     });
//     if (!totalLabel) return;
//     const total = 3420 * sample.days;
//     summary.querySelector('.stat').textContent = money(total);
//     totalLabel.textContent = 'Total customer fares · ' + period.toLowerCase();
//     const amounts = summary.querySelectorAll('.line strong');
//     if (amounts.length >= 2) {
//       amounts[0].textContent = money(total * 0.85);
//       amounts[1].textContent = money(total * 0.15);
//     }
//   });
// }

// function previewFare(button) {
//   const form = button.closest('form');
//   if (!validInputs(form)) return;
//   const base = Number(form.querySelector('#base').value);
//   const included = Number(form.querySelector('#included').value);
//   const rate = Number(form.querySelector('#rate').value);
//   const commission = Number(form.querySelector('#commission').value);
//   const total = base + Math.max(0, 3.8 - included) * rate;
//   showDialog('Fare preview', '3.8 km example: ' + money(total) +
//     '. Example earnings: ' + money(total * (1 - commission / 100)) +
//     '. Rates have not been saved. PHP must validate and save them.');
// }

// function previewBooking(button) {
//   const form = button.closest('form');
//   if (!validInputs(form)) return;
//   const pickup = form.querySelector('#pickup').value.trim();
//   const dropoff = form.querySelector('#dropoff').value.trim();
//   if (!pickup || !dropoff) {
//     showDialog('Check your locations', 'Enter both a pickup and destination.');
//     return;
//   }
//   showDialog('Your ride, at a glance.', pickup + ' → ' + dropoff +
//     '. Design example only: 3.8 km, about 12 minutes, ₱53 (₱25 base + 2.8 × ₱10). ' +
//     'The sample distance is fixed and is not calculated from your locations. PHP and a routing service must provide the actual fare and confirm the booking.');
// }

// // Request location only after the visitor presses the location button.
// function locate() {
//   if (!navigator.geolocation) {
//     showDialog('Location unavailable', 'Enter the pickup location manually.');
//     return;
//   }
//   navigator.geolocation.getCurrentPosition(function (position) {
//     showDialog('Current location', 'Latitude: ' + position.coords.latitude.toFixed(5) +
//       ', longitude: ' + position.coords.longitude.toFixed(5) +
//       '. Enter your pickup address manually; address lookup is not connected.');
//   }, function () {
//     showDialog('Location unavailable', 'Location permission was denied or the location could not be found. Enter your pickup manually.');
//   }, { timeout: 10000 });
// }

// // These forms are design previews. No credentials or files are sent anywhere.
// document.querySelectorAll('[data-frontend-form]').forEach(function (form) {
//   form.addEventListener('submit', function (event) {
//     event.preventDefault();
//     const button = form.querySelector('[data-booking-preview], [data-fare-preview], [data-backend-action]');
//     if (button) button.click();
//   });
// });

// document.querySelectorAll('input[type="file"]').forEach(function (input) {
//   input.addEventListener('change', function () {
//     const names = Array.from(input.files).map(function (file) { return file.name; });
//     showDialog('Selected files', names.length ? names.join(', ') + '. Files are selected locally and have not been uploaded.' : 'No files selected.');
//   });
// });

// document.querySelectorAll('[data-period]').forEach(function (button) {
//   button.setAttribute('aria-pressed', button.classList.contains('active'));
// });

// // Each data attribute names a small UI action, regardless of which page uses it.
// document.addEventListener('click', function (event) {
//   const button = event.target.closest('button, a[data-message]');
//   if (!button) return;
//   if (button.hasAttribute('data-close-dialog')) dialog.close();
//   else if (button.hasAttribute('data-message')) {
//     event.preventDefault();
//     showDialog(button.dataset.title || 'Information', button.dataset.message);
//   } else if (button.hasAttribute('data-period')) selectPeriod(button);
//   else if (button.hasAttribute('data-fare-preview')) previewFare(button);
//   else if (button.hasAttribute('data-booking-preview')) previewBooking(button);
//   else if (button.hasAttribute('data-location')) locate();
//   else if (button.hasAttribute('data-card-preview')) {
//     const card = button.closest('.card');
//     showDialog(card.querySelector('h3').textContent, card.querySelector('p').textContent);
//   } else if (button.hasAttribute('data-availability')) {
//     showDialog('Availability preview', 'The online/offline control is ready for PHP integration. Your server must check availability and provide ride requests. No availability has been changed.');
//   } else if (button.hasAttribute('data-states')) {
//     showDialog('Explore interface states', 'Loading: Finding a route… Empty: No rides found. Error: Connection interrupted; try again. Success: Your form passed browser validation. These are UI examples only.');
//   } else if (button.hasAttribute('data-backend-action')) {
//     const container = button.closest('form, .formbox, .card') || document;
//     if (validInputs(container)) {
//       showDialog('Ready for PHP', 'The visible fields passed browser validation. This frontend does not authenticate, save changes, upload documents, send emails, or process payments. Connect this action to your PHP endpoint.');
//     }
//   }
// });
