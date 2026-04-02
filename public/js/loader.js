import {renderStylesheet} from "./common.js";

const observedElements = new WeakSet();
const loadedCssModules = new Set();
const requestedJsModules = new Set();
const appVersion = window.ENV?.APP_VERSION ?? "";

function shouldObserve(el) {
  return el.tagName.startsWith("X-") || el.dataset.jsModule || el.dataset.cssModule;
}

function getComponentModulePath(el) {
  const componentName = el.tagName.toLowerCase().replace("x-", "");
  const versionSuffix = appVersion ? `?v=${appVersion}` : "";
  return `/js/components/${componentName}.js${versionSuffix}`;
}

function getJsModulePath(el) {
  if (el.dataset.jsModule) {
    return el.dataset.jsModule;
  }

  if (el.tagName.startsWith("X-")) {
    return getComponentModulePath(el);
  }

  return null;
}

function loadJsModule(modulePath) {
  if (!modulePath || requestedJsModules.has(modulePath)) {
    return Promise.resolve();
  }

  requestedJsModules.add(modulePath);

  return import(modulePath).catch(error => {
    requestedJsModules.delete(modulePath);
    console.error(`Failed to load JS module: ${modulePath}`, error);
  });
}

function loadCssModule(cssModule) {
  if (!cssModule || loadedCssModules.has(cssModule)) {
    return Promise.resolve(); // already loaded, nothing to wait for
  }

  loadedCssModules.add(cssModule);

  return renderStylesheet({file: cssModule});
}

function observeElement(el, io) {
  if (!(el instanceof Element)) {
    return;
  }

  if (!shouldObserve(el)) {
    return;
  }

  if (observedElements.has(el) || el.hasAttribute("data-mounted")) {
    return;
  }

  observedElements.add(el);
  io.observe(el);
}

function observeTree(root, io) {
  if (!(root instanceof Element)) {
    return;
  }

  observeElement(root, io);

  for (const el of root.querySelectorAll("*")) {
    observeElement(el, io);
  }
}

const io = new IntersectionObserver(entries => {
  for (const entry of entries) {
    if (!entry.isIntersecting) continue;

    const el = entry.target;
    io.unobserve(el);

    const cssPromises = el.dataset.cssModule
      ? el.dataset.cssModule.split(",").map(css => loadCssModule(css))
      : [];

    const jsPromise = loadJsModule(getJsModulePath(el));

    Promise.all([...cssPromises, jsPromise]).then(() => {
      el.setAttribute("data-mounted", "");
    });
  }
});

observeTree(document.body, io);

const mo = new MutationObserver(mutations => {
  for (const mutation of mutations) {
    for (const node of mutation.addedNodes) {
      observeTree(node, io);
    }
  }
});

mo.observe(document.body, {
  childList: true,
  subtree: true
});