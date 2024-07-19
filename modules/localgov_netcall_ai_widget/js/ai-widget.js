/**
 * @file JS file for the Netcall AI Widget instances.
 *
 * Placed before the closing </body> tag.
 */

(function netcallAiWidgetEmbedScript(Drupal) {
  Drupal.behaviors.netcallAiWidgetEmbed = {
    attach: function (context) {
      Connect.init({
        workspaceId: "WORKSPACEIDHERE",
        definitionId: "PARTITIONIDHERE ",
      });
    },
  };
})(Drupal);
