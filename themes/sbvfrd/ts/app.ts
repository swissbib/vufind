///<reference path="autosuggest/AutoSuggest.ts" />

import * as $ from "jquery";

import AutoSuggest from "./autosuggest/AutoSuggest";
import Configuration from "./autosuggest/Configuration";
import Settings from "./autosuggest/Settings";
import {RecordRenderer} from "./RecordRenderer";

$(document).ready(() => {
    const recordRenderer = new RecordRenderer(window.location.origin + VuFind.path + "/AJAX/Json");
    const recordIdEl: HTMLInputElement = $("input#record_id")[0] as HTMLInputElement;
    const contributorsList: HTMLElement = $(".sidebar .list-group.author")[0];
    const contributorsTemplate: any = (p: any) => {
        if (!p.lastName || !p.firstName) {
            return "";
        }
        return `<li class="list-group-item"><a href="${VuFind.path}/Search/Results?lookfor=${p.lastName},
${p.firstName}&amp;type=Author" title=" ${p.lastName}, ${p.firstName}">${p.lastName}, ${p.firstName}</a><a href="${VuFind.path}/Card/Knowledge/Person/${p.id}" data-lightbox>
<span ${ p.hasSufficientData === "1" ? ' class="fa fa-info-circle fa-lg"' : "" } style="display: inline;"
authorid="${p.id}"></span></a></li>`;
    };

    const subjects: JQuery<HTMLElement> = $(".subject [subjectid]");
    const subjectsTemplate: any = (s: any) => {
        return `<a href="${VuFind.path}/Card/Knowledge/Subject/${s.id}" data-lightbox>
<span ${ s.hasSufficientData === "1" ? ' class="fa fa-info-circle fa-lg"' : "" } style="display: inline;"</span></a>`;
    };
    if (recordIdEl) {
        recordRenderer.render(recordIdEl.value, contributorsTemplate, contributorsList)
            .then(() => {
                // TODO Required to bind lightbox. Is this the correct way?
                VuFind.lightbox.init();
            });
        recordRenderer.renderSubjects(subjects, subjectsTemplate);
            // TODO Required to bind lightbox. Is this the correct way?
        VuFind.lightbox.init();
    }

    // setup auto-suggest
    const settings: Settings = swissbib.autoSuggestConfiguration();
    const autoSuggestConfiguration: Configuration = new Configuration(settings, VuFind);
    const autoSuggest = new AutoSuggest("#searchForm_lookfor", autoSuggestConfiguration);

    autoSuggest.initialize();
});
