require(['jquery'], function ($) {

    $(document).ready(function () {
        $('#myModal').modalSteps();
    });

    /**
     * Change operation type
     */
    $(".dropdown-menu li a").click(function () {
        $(".local-gradebook-condition-button").html($(this).text() + ' <span class="caret"></span>');
    });
});


