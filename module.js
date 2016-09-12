jQuery(document).ready(function($){
    $(".dropdown-menu li a").click(function () {
        alert('asdasd');
        $(".local-gradebook-condition-button").html($(this).text() + ' <span class="caret"></span>');
    });
});

