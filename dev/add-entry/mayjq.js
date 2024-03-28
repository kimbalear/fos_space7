$(document).ready(function() {
    console.log("DOM fully loaded and parsed");

    var arrows = $(".cta");
    console.log(arrows);

    arrows.each(function () {
        $(this).on('click', function () {
            var description = $(this).parent().find('.description');

            var isVisible = description.css('display') === 'block';
            if (isVisible) {
                description.css('display', 'none');
                $(this).removeClass('up').addClass('down');
            } else {
                description.css('display', 'block');
                $(this).removeClass('down').addClass('up');
            }
        });
    });

    // Assign a limit of 100 characters to the title field
    var titleInput = $("#titleDiv").find(".basefieldinput");
    titleInput.on('input', function() {
        console.log("Adding maxlength attribute to input text");
        $(this).attr('maxlength', '100');
    });

    // Assign a limit of 100 words to the description and comment field
    var textareaDivs = $(".value");

    if (textareaDivs.length >= 9) {
        var textareaDiv2 = $(textareaDivs[2]);
        var textareaDiv8 = $(textareaDivs[8]);

        textareaDiv2.on('input', limitWordCount);
        textareaDiv8.on('input', limitWordCount);
    }

    function limitWordCount() {
        var editorDiv = $(this).find('.editor_atto_content.form-control');
        var content = editorDiv.text();
        var words = content.split(" ");
        var wordCount = words.length;

        if (wordCount > 100) {
            var newContent = words.slice(0, 100).join(" ");
            editorDiv.text(newContent);

            var range = document.createRange();
            var sel = window.getSelection();
            range.setStart(editorDiv[0].childNodes[0], newContent.length);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }

});
