/**
 * Created by dtomefer on 17/10/16.
 */

function showConfirmation() {
    var content = document.getElementById("id_clearbutton").getAttribute("data-question");
    return confirm(content);
}
