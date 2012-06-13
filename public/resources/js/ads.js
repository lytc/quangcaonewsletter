$(function() {
    $('textarea.tinymce').tinymce({
        script_url : '/resources/js/tinymce/tiny_mce.js'
        ,width: 400
        ,height: 300
        ,theme_advanced_resizing: true
        ,theme_advanced_resize_horizontal: false
    });
});