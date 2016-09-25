require(['jquery', 'jqueryui'], function ($, jqui) {

    $(document).ready(function () {
        var callback = function (actualstep, nextstep) {
            if (actualstep != null && nextstep != null) {
                var operation = $('#myModal').find('[data-step=' + actualstep + '] div .local-gradebook-droppable').val();
                $('#myModal').find('[data-step=' + nextstep + '] div .local-gradebook-droppable').val(operation);
            }

        };

        var completeCallback = function (actualstep, nextstep) {
            callback(actualstep, nextstep);
            var inputToWrite = $('#myModal').attr('data-input');
            var div = $('#myModal').find("[data-step='" + 2 + "'] input");
            var operation = div.val();
            
            var inputSelected = $('.advanced-operation').find("[data-input='" + inputToWrite + "'] input");
            inputSelected.val(operation)
        };

        $('#myModal').modalSteps({
            callbacks: {
                '*': callback
            },
            completeCallback: completeCallback
        });
        $(init);

        $('.local-gradebook-openmodal').click(function () {
            $('#myModal').attr('data-input', $(this).parent().attr('data-input'));
            $('#myModal').find('[data-step=' + 1 + '] input').val($(this).parent().find('input').val())
        });
    });

    /**
     * Change operation type
     */
    $(".dropdown-menu li a").click(function () {
        $(".local-gradebook-condition-button").html($(this).text() + ' <span class="caret"></span>');
    });

    /**
     * Drag and drop part
     */
    function init() {
        $('.local-gradebook-draggable').draggable({
            cancel: false,
        });
    }

    $(".local-gradebook-droppable").droppable({
        drop: function (event, ui) {
            var value = ui.draggable.attr("value");
            var elementCloned = ui.draggable.clone();
            $(this).val($(this).val() + elementCloned.attr("value"));
        }
    });

    $('.local-gradebook-draggable').on('mousedown', function (e) {
        $(this).draggable({
            helper: "clone"
        }).css({
            opacity: '.7'
        });

    });
    /**
     * End of drag and drop
     */
});
