// DEPRECATED
//
// This file is kept for compatibility reasons. The library has been refactored
// and rewritten in TypeScript. Consider switching to:
// https://github.com/johannschopplich/loadeer
//
// You can now just use for the same outcome, for example:
// `<script src="https://unpkg.com/loadeer" defer init></script>`

const isCrawler =
  !("onscroll" in window) ||
  /(gle|ing|ro)bot|crawl|spider/i.test(navigator.userAgent);

const debounceFn = (fn, delay = 250) => {
  let timeoutId;
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      timeoutId = null;
      fn(...args);
    }, delay);
  };
};

const load = (element) => {
  const newSrc = element.dataset.src;
  if (newSrc) element.src = newSrc;

  const newSrcset = element.dataset.srcset;
  if (newSrcset) {
    element.srcset = newSrcset;

    const newSizes = element.dataset.sizes;
    if (newSizes) {
      element.sizes =
        newSizes === "auto" ? `${element.offsetWidth}px` : newSizes;
    }
  }

  element.dataset.loaded = "true";
};

const isLoaded = (element) => element.dataset.loaded === "true";

const recalcSizes = (elements) => {
  for (const element of elements) {
    if (element.dataset.sizes === "auto") {
      element.sizes = `${element.offsetWidth}px`;
    }
  }
};

const onIntersection = (loaded) => (entries, observer) => {
  for (const entry of entries) {
    if (entry.intersectionRatio > 0 || entry.isIntersecting) {
      const { target } = entry;
      observer.unobserve(target);

      if (isLoaded(target)) continue;
      load(target);
      loaded(target);
    }
  }
};

const getElements = (selector, root = document) => {
  if (selector instanceof Element) return [selector];
  if (selector instanceof NodeList) return [...selector];

  return root.querySelectorAll(selector);
};

/**
 * SEO-friendly lazyload implementation derivatised from lozad.js
 *
 * @param {(string|Element|NodeList)} [selector] Optional custom selector, element or node list
 * @param {object} [options] Optional default options
 * @returns {object} Object containing `observe` & `triggerLoad` methods and initialized observers
 */
export function useLazyload(selector = "[data-lazyload]", options = {}) {
  const {
    root,
    rootMargin = "0px",
    threshold = 0,
    loaded = () => {},
  } = options;

  const observer = new IntersectionObserver(onIntersection(loaded), {
    root,
    rootMargin,
    threshold,
  });

  return {
    observe() {
      const elements = getElements(selector, root);

      for (const element of elements) {
        if (isLoaded(element)) continue;

        if (isCrawler) {
          load(element);
          loaded(element);
          continue;
        }

        observer.observe(element);
      }

      const debounced = debounceFn(() => recalcSizes(elements), 100);
      window.addEventListener("resize", debounced);
    },

    triggerLoad(element) {
      if (isLoaded(element)) return;

      load(element);
      loaded(element);
    },

    observer,
  };
}
