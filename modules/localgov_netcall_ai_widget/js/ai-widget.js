/**
 * @file JS file for the Netcall AI Widget instances.
 *
 * Placed before the closing </body> tag.
 */

(function netcallAiWidgetEmbedScript(Drupal, drupalSettings) {
  Drupal.behaviors.netcallAiWidgetEmbed = {
    attach: function (context) {
      let workspace_id = drupalSettings.localgovNetcall.aiWigdetWorkspaceId;
      let partition_id = drupalSettings.localgovNetcall.aiWigdetPartitionId;

      // @todo: check the path of the page, and re-assign the partition_id if needed.
      // partition overrides are available in aiWigdetPartitionOverrides

      Connect.init({
        workspaceId: `${workspace_id}`,
        definitionId: `${partition_id}`,
      });
    },
  };
})(Drupal);
