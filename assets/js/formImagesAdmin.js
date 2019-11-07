$(document).ready(function() {
    $('[data-action="delete"]').on('click', function() {
        const target = this.dataset.target;
        $(target).remove();
    });
});