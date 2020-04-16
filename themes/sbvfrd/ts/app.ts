import * as $ from "jquery";

import AutoSuggest from "./autosuggest/AutoSuggest";
import Configuration from "./autosuggest/Configuration";
import Settings from "./autosuggest/Settings";
import RecordRenderer from "./RecordRenderer";
import CarouselManager from "./carousel/CarouselManager";
import MediaQueryObserver from "./common/MediaQueryObserver";
import BackToTopButton from "./components/BackToTopButton";
import ImageSequence from "./components/ImageSequence";
import TextOverflowExpander from "./common/TextOverflowExpander";
import Breakpoints from "./common/Breakpoints";

// must be available immediately
swissbib.imageSequence = ImageSequence;

$(document).ready(() => {
    const recordRenderer = new RecordRenderer(window.location.origin + VuFind.path + "/AJAX/Json");
    const recordIdEl: HTMLInputElement = $("input#record_id")[0] as HTMLInputElement;
    const contributorsList: HTMLElement = $(".sidebar .list-group.author")[0];
    const authorContributorsTemplate: any = (p: any) => {
        if (!p.name) {
            return "";
        }
        return `<li class="list-group-item"><a href="${VuFind.path}/Search/Results?lookfor=%22${p.name}%22&amp;type=Author" title="${p.name}">${p.name}</a><a href="${VuFind.path}/Card/Knowledge/Person/${p.id}" data-lightbox>
<span ${ p.hasSufficientData === "1" ? ' class="fa icon-info fa-lg"' : "" } style="display: inline;"
authorid="${p.id}"></span></a></li>`;
    };
    const organisationContributorsTemplate: any = (p: any) => {
        if (!p.name) {
            return "";
        }
        return `<li class="list-group-item"><a href="${VuFind.path}/Search/Results?lookfor=%22${p.name}%22&amp;type=Author" title="${p.name}">${p.name}</a><a href="${VuFind.path}/Card/Knowledge/Organisation/${p.id}" data-lightbox>
<span ${ p.hasSufficientData === "1" ? ' class="fa icon-info fa-lg"' : "" } style="display: inline;"
authorid="${p.id}"></span></a></li>`;
    };
    const subjects: JQuery<HTMLElement> = $(".subject [subjectid]");
    const subjectsTemplate: any = (s: any) => {
        return `<a href="${VuFind.path}/Card/Knowledge/Subject/${s.id}" data-lightbox>
<span ${ s.hasSufficientData === "1" ? ' class="fa icon-info fa-lg"' : "" } style="display: inline;"</span></a>`;
    };
    if (recordIdEl) {
        recordRenderer.renderContributors(recordIdEl.value, 'author', authorContributorsTemplate, contributorsList)
            .then(() => {
                $(contributorsList).parent("div").removeClass("hidden");
                // TODO Required to bind lightbox. Is this the correct way?
                VuFind.lightbox.init();
            });
        recordRenderer.renderContributors(recordIdEl.value, 'organisation', organisationContributorsTemplate, contributorsList)
            .then(() => {
                $(contributorsList).parent("div").removeClass("hidden");
                // TODO Required to bind lightbox. Is this the correct way?
                VuFind.lightbox.init();
            });
        recordRenderer.renderSubjects(subjects, subjectsTemplate)
            .then(() => {
                // TODO Required to bind lightbox. Is this the correct way?
                VuFind.lightbox.init();
            });
    }

    // setup auto-suggest
    const settings: Settings = swissbib.autoSuggestConfiguration();
    const autoSuggestConfiguration: Configuration = new Configuration(settings, VuFind);
    const autoSuggest = new AutoSuggest("#searchForm_lookfor", autoSuggestConfiguration);

    autoSuggest.initialize();

    /*
    // Remove the focus and close the autosuggest, if the search field is not empty on load
    if (! $.isEmptyObject(autoSuggest.getValue())) {
        $("#searchForm_lookfor").blur();
        // Blur has no effect on IE, so close it explicitly
        // The documentation says this should work, but it does not
        // $().autocomplete('hide');
        // Doing it old fashoined :-(
        $(".autocomplete-results").hide();
    }
    */

    const mediaQueryObserver: MediaQueryObserver = new MediaQueryObserver();

    // carousel
    const carouselManager: CarouselManager = new CarouselManager(swissbib.carousel, mediaQueryObserver);
    carouselManager.initialize();
    swissbib.carouselManager = carouselManager;

    // components
    const backToTopButtonDom: string = '<a id="back-to-top-btn" class="icon-arrow-up" href="#" class="hidden-md hidden-lg"></a>';
    const backToTopButton: BackToTopButton = new BackToTopButton(backToTopButtonDom);
    backToTopButton.initialize();

    const abstractContentExpander: TextOverflowExpander = new TextOverflowExpander(mediaQueryObserver,
        $(".abstract-text"), $(".abstract-overflow"), $(".abstract-overflow-more")
    );
    abstractContentExpander.initialize();

    // allow global access to the TextOverflowExpander component
    swissbib.components = swissbib.components || {};
    swissbib.components.TextOverflowExpander = TextOverflowExpander;


    // add 'collapse' class to page-anchors list on load when screen size is in the xs range
    const pageAnchorsMenuCollapseCallback = (query: string): void => {
        const className: string = Breakpoints.BOOTSTRAP.isOneOf(query, "xs", "sm") ? "collapse" : "collapse in";
        const target: JQuery<HTMLElement> = $('#detailpage-section-anchors, *[id^=detailpage-section-references]');
        target.removeClass("collapse in").addClass(className);
    };

    mediaQueryObserver.register(Breakpoints.BOOTSTRAP.xs, pageAnchorsMenuCollapseCallback);
    mediaQueryObserver.register(Breakpoints.BOOTSTRAP.sm, pageAnchorsMenuCollapseCallback);
    mediaQueryObserver.register(Breakpoints.BOOTSTRAP.md, pageAnchorsMenuCollapseCallback);
    mediaQueryObserver.register(Breakpoints.BOOTSTRAP.lg, pageAnchorsMenuCollapseCallback);

    mediaQueryObserver.on();
});
