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
    wrapper.classList.add("messages");
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
    const el = fragment.querySelector(".message");
    const content = fragment.querySelector(".message__content");
    const closeBtn = fragment.querySelector(".message__close");

    // Populate
    el.dataset.type = type;
    content.textContent = String(message ?? "");

    // Close behavior
    closeBtn?.addEventListener("click", () => el.remove(), {once: true});

    // Remove after 5 seconds
    setTimeout(() => el.remove(), 5000);

    return el;
  };
}

new Flash();