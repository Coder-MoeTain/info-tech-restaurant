const KEY = 'rmsSettings';

const defaults = {
  taxRate: 0.07,
  serviceRate: 0.10,
};

export function loadSettings() {
  try {
    const raw = localStorage.getItem(KEY);
    if (!raw) return { ...defaults };
    return { ...defaults, ...JSON.parse(raw) };
  } catch {
    return { ...defaults };
  }
}

export function saveSettings(data) {
  localStorage.setItem(KEY, JSON.stringify({ ...defaults, ...data }));
}
