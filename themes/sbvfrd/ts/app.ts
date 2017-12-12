///<reference path="autosuggest/AutoSuggest.ts" />

import * as $ from "jquery";
import {Hydra} from "./Hydra";

import AutoSuggest from "./autosuggest/AutoSuggest";
import Configuration from "./autosuggest/Configuration";
import Settings from "./autosuggest/Settings";


$(document).ready(() => {
    const client = new Hydra(window.location.origin + VuFind.path + "/AJAX/Json");
    const recordIdEl: HTMLInputElement = $("input#record_id")[0] as HTMLInputElement;
    const list: HTMLElement = $(".sidebar .list-group")[0];
    const template: any = (p: any) => {
        if (!p.lastName || !p.firstName) {
            return "";
        }
        return `<li class="list-group-item"><a href="${VuFind.path}/Search/Results?lookfor=${p.lastName},
${p.firstName}&amp;type=Author" title=" ${p.lastName}, ${p.firstName}">${p.lastName}, ${p.firstName}</a>
<span ${ Hydra.personHasSufficientData(p) ? ' class="fa fa-info-circle fa-lg"' : "" } style="display: inline;"
authorid="${p["@id"]}"></span></li>`;
    };

    if (recordIdEl) {
        client.renderContributors(recordIdEl.value, list, template);
    }

    // setup auto-suggest
    const settings: Settings = swissbib.autoSuggestConfiguration();
    const autoSuggestConfiguration: Configuration = new Configuration(settings, VuFind);
    const autoSuggest = new AutoSuggest("#searchForm_lookfor", autoSuggestConfiguration);

    autoSuggest.initialize();
});
