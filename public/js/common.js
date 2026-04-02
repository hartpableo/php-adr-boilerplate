// -- Select a single element --
export const $ = (selector, scope = document.body) => {
  return scope.querySelector(selector);
};

// -- Select multiple elements --
export const $$ = (selector, scope = document.body) => {
  return scope.querySelectorAll(selector);
};

// -- Select a template --
export const $t = (name, scope = document.body) => {
  return $(`template[data-part="${name}"]`, scope).content.cloneNode(true);
};

// -- Render stylesheet --
export const renderStylesheet = (details = {}) => {
  return new Promise((resolve, reject) => {
    const link = createElement("link", {
      rel: "stylesheet",
      href: details.file,
      media: details?.media || "screen"
    });
    link.addEventListener("load", resolve, {once: true});
    link.addEventListener("error", reject, {once: true});
    document.head.appendChild(link);
  });
};

// -- Render a callback function --
// -- Usually used for custom vanilla scripts --
export const onDOMReady = (callback) => {
  if (document.readyState !== "loading") {
    callback();
    return;
  }

  document.addEventListener("DOMContentLoaded", callback, {once: true});
};

// -- Create an element with provided attributes --
export const createElement = (tag, attributes = {}) => {
  const el = document.createElement(tag);
  return updateElement(el, attributes);
};

// -- Update an element's attributes --
export const updateElement = (el, attributes = {}) => {
  for (const [attr, value] of Object.entries(attributes)) {
    if (attr === "dataset" && value && typeof value === "object") {
      for (const [key, val] of Object.entries(value)) {
        el.dataset[key] = String(val);
      }
      continue;
    }

    el[attr] = value;
  }

  return el;
};

const handleResponse = async (response) => {
  const contentType = response.headers.get("Content-Type") || "";

  return contentType.includes("application/json")
    ? await response.json()
    : await response.text();
};

const handleOptions = (data, options, method) => {
  const csrfToken = window.CSRF_TOKEN || "";

  return {
    ...options,
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
      ...(csrfToken ? {"X-CSRF-Token": csrfToken} : {}),
      ...options.headers
    },
    method: method,
    body: JSON.stringify(data),
  };
};

export const api = {
  dispatch: (url, options = {}) => fetch(url, options).then(handleResponse),
  get: (url, options = {}) => api.dispatch(url, {...options, credentials: "same-origin", method: "GET"}),
  post: (url, data = {}, options = {}) => api.dispatch(url, handleOptions(data, options, "POST")),
  put: (url, data = {}, options = {}) => api.dispatch(url, handleOptions(data, options, "PUT")),
  patch: (url, data = {}, options = {}) => api.dispatch(url, handleOptions(data, options, "PATCH")),
};