jQuery(document).ready(function($) {
    $("#e2w_doc_submitpost").attr("id", "submitpost");
    var radioSelector = "#cmb2-metabox-e2w_doc_submitpost input[type=radio][name=e2w_doc_save_export]";
    function init() {
        var label = $(radioSelector + ":checked");
        changeLabel(label);
    }
    init();
    $(radioSelector).change(function() {
        var label = $("label[for=" + this.id + "]").first().html();
        changeLabel(label);
    });
    function changeLabel(label) {
        $("#cmb2-metabox-e2w_doc_submitpost #publishing-action input").val(label);
    }
});