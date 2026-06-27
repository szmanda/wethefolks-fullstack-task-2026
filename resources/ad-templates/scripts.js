const clickUrl = '{{TRACKING_CLICK_URL}}';
const impressionUrl = '{{TRACKING_IMPRESSION_URL}}';

function sendTracking(url) {
  if (navigator.sendBeacon) {
    navigator.sendBeacon(url);
    return;
  }

  fetch(url, {
    method: 'POST',
    mode: 'cors',
  }).catch(() => {
    // Silently ignore tracking failures.
  });
}

function registerTracking() {
  sendTracking(impressionUrl);

  const link = document.getElementById('ad-click-link');
  if (link) {
    link.addEventListener('click', () => {
      sendTracking(clickUrl);
    });
  }
}

document.addEventListener('DOMContentLoaded', registerTracking);
