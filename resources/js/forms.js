import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";
import tinymce from "tinymce/tinymce";
import "tinymce/icons/default/icons";
import "tinymce/themes/silver";
import "tinymce/models/dom";
import "tinymce/plugins/lists";
import "tinymce/plugins/link";
import "tinymce/plugins/image";
import "tinymce/plugins/media";
import "tinymce/plugins/table";
import "tinymce/plugins/code";
import "tinymce/plugins/autolink";
import "tinymce/plugins/fullscreen";

window.tinymce = tinymce;

function initTomSelect() {
    document.querySelectorAll("select.search").forEach((el) => {
        if (el.tomselect) return;

        const placeholderOption = el.querySelector('option[value=""]');

        const ts = new TomSelect(el, {
            create: el.multiple,
            plugins: el.multiple ? ["remove_button"] : [],
            ...(placeholderOption ? { placeholder: placeholderOption.textContent } : {}),
            allowEmptyOption: true,
        });

        if (el.hasAttribute("data-has-error")) {
            ts.wrapper.classList.add("has-error");
        }
    });
}

function initWysiwyg() {
    if (typeof window.initAllWysiwyg === "function") {
        window.initAllWysiwyg();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    initTomSelect();
    initWysiwyg();
});
document.addEventListener("livewire:navigated", () => {
    initTomSelect();
    initWysiwyg();
});
