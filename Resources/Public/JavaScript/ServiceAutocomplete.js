/**
 * FormEngine element "serviceAutocomplete" — client behaviour.
 *
 * Loaded as a native ES module by the FormEngine (see JavaScriptModules.php and
 * ServiceAutocompleteElement.php). It wires every service autocomplete input on
 * the current edit form: debounced search against the backend AJAX route, a
 * suggestion list rendered directly below the field, and — on selection — the
 * auto-fill of the sibling service_* fields plus creation/filling of the inline
 * cookie rows (native IRRE "create new record" flow, so nothing is persisted
 * until the whole record is saved).
 *
 * Compatible with TYPO3 v13 and v14: it only touches public DOM/markup
 * conventions (FormEngine field markers, the inline "create new" button and the
 * records container) and never imports version-specific internals.
 */

const AUTOCOMPLETE_SELECTOR = '.t3js-cb-service-autocomplete';
const INITIALISED_FLAG = 'cbServiceAutocompleteInitialised';

/**
 * Resolve a possibly localized JSON value ({de: …, en: …}) to a single string
 * for the currently edited record language, with an en→de→first fallback.
 *
 * @param {*} value
 * @param {string} lang
 * @return {string}
 */
function pickLanguage(value, lang) {
  if (value === null || value === undefined) {
    return '';
  }
  if (typeof value === 'string' || typeof value === 'number') {
    return String(value);
  }
  if (typeof value === 'object') {
    if (lang && value[lang] !== undefined) {
      return String(value[lang]);
    }
    if (value.en !== undefined) {
      return String(value.en);
    }
    if (value.de !== undefined) {
      return String(value.de);
    }
    const first = Object.values(value)[0];
    return first !== undefined ? String(first) : '';
  }
  return '';
}

function debounce(fn, wait) {
  let timer = null;
  return function debounced(...args) {
    if (timer) {
      window.clearTimeout(timer);
    }
    timer = window.setTimeout(() => fn.apply(this, args), wait);
  };
}

function fireInputEvents(el) {
  el.dispatchEvent(new Event('input', { bubbles: true }));
  el.dispatchEvent(new Event('change', { bubbles: true }));
}

/**
 * Set a FormEngine field value by its exact form element name. Handles the
 * standard "visible control + hidden mirror" pattern (input) as well as plain
 * textareas, and triggers validation so the value is persisted on save.
 */
function setFieldValueByName(root, name, value) {
  const escaped = (window.CSS && CSS.escape) ? CSS.escape(name) : name.replace(/(["\\\]])/g, '\\$1');
  const visible = root.querySelector('[data-formengine-input-name="' + escaped + '"]');
  const hidden = root.querySelector('input[type="hidden"][name="' + escaped + '"]');

  if (visible) {
    visible.value = value;
    fireInputEvents(visible);
  }
  if (hidden) {
    hidden.value = value;
    hidden.dispatchEvent(new Event('change', { bubbles: true }));
  }
  if (!visible && !hidden) {
    const direct = root.querySelector('[name="' + escaped + '"]');
    if (direct) {
      direct.value = value;
      fireInputEvents(direct);
    }
  }
}

/**
 * Set a value inside an inline record row by matching the trailing field
 * segment, e.g. suffix "[cookie_name]".
 */
function setFieldValueBySuffix(row, suffix, value) {
  const esc = (s) => (window.CSS && CSS.escape) ? CSS.escape(s) : s.replace(/(["\\\]])/g, '\\$1');
  const visible = row.querySelector('[data-formengine-input-name$="' + esc(suffix) + '"]');
  if (visible) {
    const name = visible.getAttribute('data-formengine-input-name');
    setFieldValueByName(row, name, value);
    return;
  }
  const direct = row.querySelector('[name$="' + esc(suffix) + '"]');
  if (direct) {
    direct.value = value;
    fireInputEvents(direct);
  }
}

/**
 * Locate the field-item container of the inline cookies field within the form.
 */
function findInlineFieldItem(form, inlineField) {
  // The inline container id/data references end with the field name.
  const marker = form.querySelector(
    '[id*="-' + inlineField + '"] , [data-object-group*="-' + inlineField + '"]'
  );
  if (marker) {
    return marker.closest('.t3js-formengine-field-item') || marker.parentElement;
  }
  return null;
}

/**
 * Find the native "create new record" button of the inline cookies field.
 */
function findInlineCreateButton(form, inlineField) {
  const item = findInlineFieldItem(form, inlineField);
  if (item) {
    const btn = item.querySelector('.t3js-create-new-button');
    if (btn) {
      return btn;
    }
  }
  // Fallback: match by the button's own object reference.
  const buttons = form.querySelectorAll('.t3js-create-new-button');
  for (const btn of buttons) {
    const ref = btn.getAttribute('data-object-id')
      || btn.getAttribute('data-objectid')
      || btn.getAttribute('data-record-uid')
      || '';
    if (ref.includes(inlineField)) {
      return btn;
    }
    if (btn.closest('[id*="-' + inlineField + '"]')) {
      return btn;
    }
  }
  return null;
}

/**
 * Click the inline "create new" button and resolve with the freshly rendered
 * record row once the FormEngine appended it (async AJAX render).
 *
 * @return {Promise<HTMLElement|null>}
 */
function createInlineRow(watchRoot, createButton) {
  return new Promise((resolve) => {
    let settled = false;
    const finish = (row) => {
      if (settled) {
        return;
      }
      settled = true;
      observer.disconnect();
      window.clearTimeout(timeout);
      resolve(row);
    };

    const isRecordRow = (node) => node.nodeType === 1 && (
      node.matches?.('[data-object-uid]')
      || node.matches?.('.inlineRow')
      || (node.id && /-NEW[0-9a-f]+/i.test(node.id))
    );

    const observer = new MutationObserver((mutations) => {
      for (const mutation of mutations) {
        for (const node of mutation.addedNodes) {
          if (isRecordRow(node)) {
            finish(node);
            return;
          }
          if (node.nodeType === 1) {
            const nested = node.querySelector?.('[data-object-uid], .inlineRow');
            if (nested) {
              finish(nested);
              return;
            }
          }
        }
      }
    });

    observer.observe(watchRoot, { childList: true, subtree: true });
    const timeout = window.setTimeout(() => finish(null), 5000);

    createButton.click();
  });
}

/**
 * Apply a selected service block to the form: main field, sibling fields and
 * inline cookie rows. Sibling/inline creation is client-side only.
 */
async function applyService(input, config, service) {
  const form = input.closest('form') || document;

  // 1) Persist the service name in this very field.
  setFieldValueByName(form, config.fieldName, service.service_name || '');

  // 2) Fill the sibling service_* fields (derive their names from ours).
  const baseName = config.fieldName.replace(/\[service_name\]$/, '');
  Object.entries(config.serviceFields || {}).forEach(([tcaField, jsonKey]) => {
    const name = baseName + '[' + tcaField + ']';
    const value = pickLanguage(service[jsonKey], config.language);
    setFieldValueByName(form, name, value);
  });

  // 3) Create + fill the inline cookie rows (native IRRE flow).
  // Scope to THIS component's inline record wrapper: with several components
  // expanded at once, the form contains multiple service_cookies inlines, so a
  // form-wide lookup would target the wrong component. The autocomplete input
  // sits inside its component's inline record ([data-object-uid]); when editing
  // a component as the top-level record there is no such wrapper and the whole
  // form (with its single inline) is the correct scope.
  const scope = input.closest('[data-object-uid]') || form;
  const cookies = Array.isArray(service.cookies) ? service.cookies : [];
  if (cookies.length === 0) {
    return;
  }
  const createButton = findInlineCreateButton(scope, config.inlineField);
  const watchRoot = findInlineFieldItem(scope, config.inlineField) || scope;
  if (!createButton) {
    return;
  }

  for (const cookie of cookies) {
    // eslint-disable-next-line no-await-in-loop
    const row = await createInlineRow(watchRoot, createButton);
    if (!row) {
      continue;
    }
    Object.entries(config.cookieFields || {}).forEach(([tcaField, jsonKey]) => {
      const value = pickLanguage(cookie[jsonKey], config.language);
      setFieldValueBySuffix(row, '[' + tcaField + ']', value);
    });
  }
}

function closeSuggestions(box) {
  box.hidden = true;
  box.innerHTML = '';
  box.dataset.activeIndex = '-1';
}

function renderSuggestions(box, input, config, services) {
  box.innerHTML = '';
  if (!services.length) {
    closeSuggestions(box);
    return;
  }
  const list = document.createElement('div');
  list.className = 'cb-service-suggest-list';
  list.setAttribute('role', 'listbox');

  services.forEach((service, index) => {
    const item = document.createElement('button');
    item.type = 'button';
    item.className = 'cb-service-suggest-item';
    item.setAttribute('role', 'option');
    item.dataset.index = String(index);

    const title = document.createElement('span');
    title.className = 'cb-service-suggest-title';
    title.textContent = service.service_name || '';

    const meta = document.createElement('span');
    meta.className = 'cb-service-suggest-meta';
    const cookieCount = Array.isArray(service.cookies) ? service.cookies.length : 0;
    const parts = [];
    if (service.service_publisher) {
      parts.push(service.service_publisher);
    }
    parts.push(cookieCount === 1 ? '1 Cookie' : cookieCount + ' Cookies');
    meta.textContent = parts.join(' · ');

    item.appendChild(title);
    item.appendChild(meta);
    item.addEventListener('mousedown', (e) => {
      // mousedown (not click) so it fires before the input blur closes the box.
      e.preventDefault();
      closeSuggestions(box);
      applyService(input, config, service);
    });
    list.appendChild(item);
  });

  box.appendChild(list);
  box.hidden = false;
  box.dataset.activeIndex = '-1';
}

async function search(input, config, box) {
  const query = input.value.trim();
  if (query.length < 1) {
    closeSuggestions(box);
    return;
  }
  const url = config.ajaxUrl + (config.ajaxUrl.includes('?') ? '&' : '?') + 'q=' + encodeURIComponent(query);
  try {
    const response = await fetch(url, {
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
    });
    if (!response.ok) {
      closeSuggestions(box);
      return;
    }
    const data = await response.json();
    renderSuggestions(box, input, config, Array.isArray(data.results) ? data.results : []);
  } catch (e) {
    closeSuggestions(box);
  }
}

function moveActive(box, delta) {
  const items = Array.from(box.querySelectorAll('.cb-service-suggest-item'));
  if (!items.length) {
    return;
  }
  let index = parseInt(box.dataset.activeIndex || '-1', 10);
  index = (index + delta + items.length) % items.length;
  box.dataset.activeIndex = String(index);
  items.forEach((el, i) => el.classList.toggle('is-active', i === index));
  items[index].scrollIntoView({ block: 'nearest' });
}

function initElement(input) {
  if (input.dataset[INITIALISED_FLAG]) {
    return;
  }
  input.dataset[INITIALISED_FLAG] = '1';

  let config;
  try {
    config = JSON.parse(input.getAttribute('data-cb-service-config') || '{}');
  } catch (e) {
    return;
  }

  const box = input.parentElement.querySelector('.t3js-cb-service-suggest');
  if (!box) {
    return;
  }
  box.dataset.activeIndex = '-1';

  const runSearch = debounce(() => search(input, config, box), 250);
  input.addEventListener('input', runSearch);
  input.addEventListener('focus', () => {
    if (input.value.trim().length >= 1) {
      runSearch();
    }
  });

  input.addEventListener('keydown', (e) => {
    if (box.hidden) {
      return;
    }
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      moveActive(box, 1);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      moveActive(box, -1);
    } else if (e.key === 'Enter') {
      const index = parseInt(box.dataset.activeIndex || '-1', 10);
      const items = box.querySelectorAll('.cb-service-suggest-item');
      if (index >= 0 && items[index]) {
        e.preventDefault();
        items[index].dispatchEvent(new MouseEvent('mousedown'));
      }
    } else if (e.key === 'Escape') {
      closeSuggestions(box);
    }
  });

  // Close on outside click / blur.
  input.addEventListener('blur', () => window.setTimeout(() => closeSuggestions(box), 150));
}

function initAll(root) {
  (root || document).querySelectorAll(AUTOCOMPLETE_SELECTOR).forEach(initElement);
}

// Initialise fields present now …
initAll(document);

// … and any that appear later (translation/inline/ajax-rendered forms).
const domObserver = new MutationObserver((mutations) => {
  for (const mutation of mutations) {
    for (const node of mutation.addedNodes) {
      if (node.nodeType !== 1) {
        continue;
      }
      if (node.matches?.(AUTOCOMPLETE_SELECTOR)) {
        initElement(node);
      }
      node.querySelectorAll?.(AUTOCOMPLETE_SELECTOR).forEach(initElement);
    }
  }
});
domObserver.observe(document.body, { childList: true, subtree: true });

export default { initAll };
