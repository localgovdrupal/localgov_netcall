/**
 * @file JS file for the Netcall AI Widget.
 *
 * This script is responsible for loading the Netcall AI widget onto the page.
 * It gets placed in the <head> tag.
 */

(function netcallAiWidgetInitScript(Drupal, drupalSettings) {
  Drupal.behaviors.netcallAiWidgetInit = {
    attach(context) {
      const workspaceId = drupalSettings.localgovNetcall.aiWigdetWorkspaceId;
      let partitionId = drupalSettings.localgovNetcall.aiWigdetPartitionId;
      const currentPath = window.location.pathname;
      const partitionOverrides = [];
      const partitionOverridesFromConfig =
        drupalSettings.localgovNetcall.aiWigdetPartitionOverrides;

      // Check if there's any overrides in the config.
      // If so, add each to the partitionOverrides array.
      if (partitionOverridesFromConfig) {
        partitionOverridesFromConfig
          .split('\r\n')
          .forEach((partitionOverride) => {
            partitionOverrides.push(partitionOverride);
          });
      }

      partitionOverrides.forEach((partitionOverride) => {
        // Partition overrides are set as key|value pairs.
        // We'll split them here to get the partition and path.
        const split = partitionOverride.split('|');
        const partition = split[0];
        let path = split[1];

        // currentPath always starts with "/", so let's make sure we have the
        // same format for the paths for any overrides.
        if (!path.startsWith('/')) {
          path = `/${path}`;
        }
        if (currentPath === path) {
          partitionId = partition;
        }
        // If last character of path is *,
        // check if current path starts with path.
        // * is our wildcard character, so anything within that path will use
        // the partition.
        if (
          path.slice(-1) === '*' &&
          currentPath.startsWith(path.slice(0, -1))
        ) {
          partitionId = partition;
        }
      });

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
            if ((typeof e == 'string' && (e = JSON.parse(e)), e.url)) {
              const s = document.querySelectorAll('script')[0],
                r = document.createElement('script');
              (r.async = !0),
                (r.src = e.url),
                (n = s.parentNode) == null || n.insertBefore(r, s);
            }
          } catch {}
        }
        const o = new XMLHttpRequest();
        o.addEventListener('load', a),
          o.open(
            'GET',
            `https://webassist.netcall-apollo-dev.co.uk/api/v1/loader/workspaces/${workspaceId}/definitions/${partitionId}`,
            !0,
          ),
          (o.responseType = 'json'),
          o.send();
      })();
    },
  };
})(Drupal, drupalSettings);
