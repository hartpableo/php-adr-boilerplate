import {$t} from "./common.js";

/**
 * Flash class
 */
class Flash {
  constructor() {
    if (document.readyState !== "loading") {
      this.init();
      return;
    }

    document.addEventListener("DOMContentLoaded", this.init, {once: true});
  }

  /**
   * Init
   */
  init = () => {
    // Check for the data source
    // Which is a global variable "FLASHES"
    const flashes = typeof FLASHES === "object" ? Object.entries(FLASHES) : null;
    if (!flashes) return;

    // Create a messages wrapper
    const wrapper = document.createElement("div");
    wrapper.classList.add("messages", "position-fixed", "top-0", "end-0", "p-3");
    wrapper.style.zIndex = "1050";
    wrapper.role = "alert";

    // Render each flash message
    Object.values(flashes).forEach(([type, messages]) => {
      messages.forEach(message => {
        const messageEl = this.messageElement({type, message});
        wrapper.appendChild(messageEl);
      });
    });

    // Append to the DOM
    document.body.appendChild(wrapper);
  };

  /**
   * Message element
   */
  messageElement = (flash) => {
    const {type, message} = flash;

    const fragment = $t("flash");
    const el = fragment.querySelector(".alert");
    const content = fragment.querySelector(".alert__content");
    const closeBtn = fragment.querySelector(".btn-close");

    // Map common flash types to bootstrap alert types
    const typeMapping = {
      "success": "alert-success",
      "error": "alert-danger",
      "warning": "alert-warning",
      "info": "alert-info"
    };

    // Populate
    el.classList.add(typeMapping[type] || "alert-info");
    content.textContent = String(message ?? "");

    // Close behavior
    closeBtn?.addEventListener("click", () => el.remove(), {once: true});

    // Remove after 5 seconds
    setTimeout(() => {
      // Use bootstrap's transition if possible, otherwise just remove
      if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
        const alert = bootstrap.Alert.getOrCreateInstance(el);
        alert.close();
      } else {
        el.remove();
      }
    }, 5000);

    return el;
  };
}

new Flash();