$(document).on({
    "contextmenu": function(e) {
        console.log("ctx menu button:", e.which);

        // Stop the context menu
        e.preventDefault();
    },
    "mousedown": function(e) {
        console.log("normal mouse down:", e.which);
    },
    "mouseup": function(e) {
        console.log("normal mouse up:", e.which);
    }
});
$('body').bind('copy paste', function(e) {
    e.preventDefault();
    return false;
});