function allowedCharsOnly(str, allowed_chars){
    var return_str = "";
    for (var i = 0; i<str.length; i++){
        var c = str.charAt(i);
        var x = allowed_chars.indexOf(c);
        if (x != -1){
            return_str += c;
        }
    }
    return return_str;
}

function removeAllButLast(string, token) {
    var parts = string.split(token);
    if (parts[1]===undefined)
        return string;
    else
        return parts.slice(0,-1).join('') + token + parts.slice(-1)
}

function nextInDOM(_selector, _subject) {
    // From http://stackoverflow.com/a/12873187/2488994 by techfoobar
    var next = getNext(_subject);
    while(next.length != 0) {
        var found = searchFor(_selector, next);
        if(found != null) return found;
        next = getNext(next);
    }
    return null;
}
function getNext(_subject) {
    if(_subject.next().length > 0) return _subject.next();
    return getNext(_subject.parent());
}
function searchFor(_selector, _subject) {
    if(_subject.is(_selector)) return _subject;
    else {
        var found = null;
        _subject.children().each(function() {
            found = searchFor(_selector, $(this));
            if(found != null) return false;
        });
        return found;
    }
    return null; // will/should never get here
}

function strContains(needle, haystack) {
    return haystack.toLowerCase().indexOf(needle) >= 0;
}


var go = function(){

    // Disable enter key
    $("form").bind("keypress", function(e) {
        if (e.keyCode == 13) {
            return false;
        }
    });

    $("#model-maps").change(function() {
        var fileList = document.getElementById("model-maps").files;
        var html = "";
        $.each(fileList, function(i, f){
            var fname = f['name'];
            var warn = "";
            if (!strContains('.png', fname)){
                warn += "Not a PNG file; "
            }
            var invalidate = new RegExp('_[0-9]k.[a-zA-Z]');
            if (invalidate.test(fname)){
                warn += "Filename must not include the resolution; "
            }
            // fname = fname.replace(/\.png/, "");
            html += "<li>"+fname;
            if (warn){
                html += ' <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>';
                html += warn;
                html += "</b>";
            }
            html += "</li>";
        });
        $("#map-list").html(html)
        $("#map-list").removeClass("hidden");
    });

    function previewUploaded(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#main-render-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#main-render").change(function() {
        previewUploaded(this);
        $("#main-render-preview-wrapper").removeClass("hidden");
    });

    function previewUploadedImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#main-render-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#main-render").change(function() {
        previewUploadedImg(this);
        $("#main-render-preview-wrapper").removeClass("hidden");
    });

    // Click functions
    $(".show-tooltip").click(function() {
        var tooltip = nextInDOM(".tooltip", $(this));
        // var tooltip = $(this).nextAll(".tooltip")[0];
        console.log(tooltip);
        if (tooltip.hasClass("hidden")){
            tooltip.removeClass("hidden");
        }else{
            tooltip.addClass("hidden");
        }
    });

    $('#auto-name').click(function() {
        if($("#form-name").is(":disabled")){
            $("#form-name").prop("disabled", false);
        }else{
            $("#form-name").prop("disabled", true);
            autoName();
        }
    });

    $('.cat-option').click(function() {
        var newCat = $(this).html();
        var currentCats = $("#form-cats").val().replace(/;/, ",");
        if (currentCats == ""){
            $("#form-cats").val(newCat);
        }else{
            var currentCatsArr = currentCats.split(",");
            var newCats = [];
            for (var i=0; i<currentCatsArr.length; i++){
                var cat = currentCatsArr[i].trim();
                if (cat != newCat){
                    newCats.push(cat);
                }
            }
            newCats.push(newCat);
            $("#form-cats").val(newCats.join(", "));
        }
    });

    $('.tag-option').click(function() {
        var newTag = $(this).html();
        var currentTags = $("#form-tags").val().replace(/;/, ",");
        if (currentTags == ""){
            $("#form-tags").val(newTag);
        }else{
            var currentTagsArr = currentTags.split(",");
            var newTags = [];
            for (var i=0; i<currentTagsArr.length; i++){
                var tag = currentTagsArr[i].trim();
                if (tag != newTag){
                    newTags.push(tag);
                }
            }
            newTags.push(newTag);
            $("#form-tags").val(newTags.join(", "));
        }
    });


    // Form changes
    var validateSlug = function(str){
            str = str.replace(/ /g, "_");
            return allowedCharsOnly(str, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM_-0123456789");
    }
    var slugToName = function(str){
        str = str.replace(/_/g, " ");
        str = str.replace(/([A-Z])/g, ' $1');  // Space before caps
        str = str.replace(/^./, function(str){ return str.toUpperCase(); });  // First letter caps
        str = str.replace(/ +(?= )/g,'');  // Double spaces
        str = str.trim();
        return str;
    }
    var autoName = function(){
        if ($("#auto-name").is(":checked")){
            var slug = $('#form-slug').val();
            $("#form-name").val(slugToName(slug));
            $('#form-name-actual').val($('#form-name').val());
        }
    }
    $('#form-slug').keyup(autoName);
    $('#form-slug').change(function(){
        var slug = $('#form-slug').val();
        $('#form-slug').val(validateSlug(slug));
        $('#form-name-actual').val($("#form-name").val());
        $.post("get_mod_files.php", {slug: slug}, function(result){
            if (Array.isArray(result) && result.length > 0){
                var html = "Files found:<br><ul><li>" + result.join("</li><li>") + "</li></ul>";
            }else{
                var html = "<span class='red-text'>No files found for that slug!</span>";
            }
            $("#file-list").html(html);
        });
    });

    var validateTagsCats = function(str){
        str = str.replace(/;/g, ",");
        str = str.replace(/, /g, ",");
        str = str.replace(/,/g, ", ");
        return str;
    }
    $('#form-cats').change(function(){
        $('#form-cats').val(validateTagsCats($('#form-cats').val()));
    });
    $('#form-tags').change(function(){
        $('#form-tags').val(validateTagsCats($('#form-tags').val()));
    });
};

$(document).ready(go);
