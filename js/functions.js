var click_functions = function(){

    // Download multiselection
    $('.download-buttons .sel').click(function() {
        if ($(this).parent().hasClass("selected")){
            $(this).parent().removeClass("selected");
            $(this).html("<i class=\"material-icons\">check_box_outline_blank</i>");
        }else{
            $(this).parent().addClass("selected");
            $(this).html("<i class=\"material-icons\">check_box</i>");
        }
    });
    // Download track
    $('.dl-btn').click(function() {
        $.post("dl_click.php", {id: $(this).attr("id"), fhash: $(this).attr("fhash")});
    });
};

$(document).ready(click_functions);
