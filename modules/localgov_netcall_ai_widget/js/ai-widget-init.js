/**
 * @file JS file for the Netcall AI Widget.
 *
 * This script is responsible for loading the Netcall AI widget onto the page.
 * It gets placed in the <head> tag.
 */

(function netcallAiWidgetInitScript(Drupal, drupalSettings) {
  Drupal.behaviors.netcallAiWidgetInit = {
    attach: function (context) {

      let workspace_id = drupalSettings.localgovNetcall.aiWigdetWorkspaceId;
      let partition_id = drupalSettings.localgovNetcall.aiWigdetPartitionId;

      // @todo: check the path of the page, and re-assign the partition_id if needed.
      // partition overrides are available in aiWigdetPartitionOverrides

      "use strict";
      (function () {
        const t = [],
          i = [];
        let c = null;
        (window.Connect = {
          init: (...n) => (
            (c = new Promise((e, s) => {
              t.push({ resolve: e, reject: s, args: n });
            })),
            c
          ),
          on: (...n) => {
            i.push(n);
          },
        }),
          (window.__onConnectHostReady__ = function (n) {
            if (
              (delete window.__onConnectHostReady__,
              (window.Connect = n),
              t && t.length)
            )
              for (const e of t)
                n.init(...e.args)
                  .then(e.resolve)
                  .catch(e.reject);
            for (const e of i) n.on(e);
          });
        function a() {
          var n;
          try {
            let e = this.response;
            if ((typeof e == "string" && (e = JSON.parse(e)), e.url)) {
              const s = document.querySelectorAll("script")[0],
                r = document.createElement("script");
              (r.async = !0),
                (r.src = e.url),
                (n = s.parentNode) == null || n.insertBefore(r, s);
            }
          } catch {}
        }
        const o = new XMLHttpRequest();
        o.addEventListener("load", a),
          o.open(
            "GET",
            `https://webassist.netcall-apollo-dev.co.uk/api/v1/loader/workspaces/${workspace_id}/definitions/${partition_id}`,
            !0
          ),
          (o.responseType = "json"),
          o.send();
      })();
    },
  };
})(Drupal, drupalSettings);
