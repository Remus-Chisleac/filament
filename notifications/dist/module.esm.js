// packages/notifications/resources/js/components/notification.js
var notification_default = (Alpine) => {
  Alpine.data("notificationComponent", ({$wire, notification}) => ({
    isVisible: false,
    top: null,
    isClosing: false,
    init: function() {
      this.top = this.$el.getBoundingClientRect().top;
      this.$nextTick(() => this.isVisible = true);
      if (notification.duration !== null) {
        setTimeout(() => this.close(), notification.duration);
      }
      Livewire.hook("message.received", (message) => {
        if (message.component.fingerprint.name !== "notifications") {
          return;
        }
        this.top = this.$el.getBoundingClientRect().top;
      });
      Livewire.hook("message.processed", (message) => {
        if (message.component.fingerprint.name !== "notifications") {
          return;
        }
        if (this.isClosing) {
          return;
        }
        this.animate();
      });
    },
    animate: function() {
      const top = this.$el.getBoundingClientRect().top;
      this.$el.getAnimations().forEach((animation) => animation.finish());
      this.$el.animate([
        {transform: `translateY(${this.top - top}px)`},
        {transform: "translateY(0px)"}
      ], {
        duration: parseFloat(window.getComputedStyle(this.$el).transitionDuration) * 1e3,
        easing: window.getComputedStyle(this.$el).transitionTimingFunction
      });
      this.top = top;
    },
    close: function() {
      this.isClosing = true;
      $wire.close(notification.id);
    }
  }));
};

// packages/notifications/resources/js/index.js
var js_default = (Alpine) => {
  Alpine.plugin(notification_default);
};
export {
  notification_default as NotificationComponentAlpinePlugin,
  js_default as default
};
