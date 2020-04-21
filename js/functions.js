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
        var id = $(this).attr("id");
        var fhash = $(this).attr("fhash");
        console.log("Tracking download - id:"+id+", fhash:"+fhash);
        $.post("dl_click.php", {id: id, fhash: fhash});
    });

    $('.zip-dl').click(function() {
        if (UAParser().browser.name == "Firefox") {
            $('#sw-tab-warning').removeClass('hidden');
        }
        $(this).children('.zip-loading').removeClass('hidden');
        var name = $(this).children('.zip-dl-files').attr('name');
        var files_parsed = JSON.parse($(this).children('.zip-dl-files').html());
        var files = [];
        files_parsed.forEach(function(f){
            files.push({url: location.origin + "/" + f[0], path: f[1]});
        });
        createDownload(name, files)
            .then(url => {
                window.location.href = url;
                $(this).children('.zip-loading').addClass('hidden');
            })
            .catch(error => {
                alert((error && error.message) || 'Unknown error');
            });
    });
};

$(document).ready(click_functions);
