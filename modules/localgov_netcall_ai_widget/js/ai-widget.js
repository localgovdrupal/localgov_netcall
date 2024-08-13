/**
 * @file JS file for the Netcall AI Widget instances.
 *
 * Placed before the closing </body> tag.
 */

(function netcallAiWidgetEmbedScript(Drupal, drupalSettings) {
  Drupal.behaviors.netcallAiWidgetEmbed = {
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

      Connect.init({
        workspaceId: `${workspaceId}`,
        definitionId: `${partitionId}`,
      });
    },
  };
})(Drupal, drupalSettings);
