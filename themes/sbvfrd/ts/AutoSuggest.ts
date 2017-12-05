/// <reference path="types/jquery.vufind.autocomplete.d.ts"/>

import * as $ from "jquery"


/**
 * Component to handle auto-completion based on the autocomplete.js integrated within VuFind.
 */
export class AutoSuggest {

    private sourceSelector: string;
    private sourceInputElement: JQuery<HTMLElement>;

    private resultListContainerElement: JQuery<HTMLElement>;
    private static RESULT_LIST_CONTAINER_SELECTOR:string = "div.autocomplete-results";

    private configuration: AutoSuggestConfiguration;


    constructor(sourceSelector: string, configuration: AutoSuggestConfiguration) {
        this.sourceSelector = sourceSelector;
        this.configuration = configuration;
    }


    /**
     * Default search result section item limit. In case a section's limit evaluates to be not a number or is less than
     * zero, then the default limit is applied.
     * @type {number}
     */
    public defaultSectionLimit: number = 10;


    /**
     * Initializes the auto-suggest component based on the source input element selector passed in to the constructor.
     * This method must be invoked once after instantiation to bind to the search input field and to listen for user
     * input.
     */
    public initialize() {
        this.setupSourceInputElement();
        this.setupResultListContainer();
    }


    private setupSourceInputElement() {
        this.sourceInputElement = $(this.sourceSelector);

        this.sourceInputElement.autocomplete({
            handler: this.autoCompleteHandler
        });
    };

    // keep 'this' context on AutoSuggest instance by using arrow function
    private autoCompleteHandler = (inputElement: JQuery<HTMLElement>, callback: Function) => {

        for (let position = 0; position < this.configuration.numSections(); ++position) {
            this.requestSectionResultsIfNeeded(
                this.configuration.getSectionAt(position),
                inputElement.val() as string,
                callback
            );
        }
    };

    private requestSectionResultsIfNeeded(section: AutoSuggestSection, searchString: string, callback: Function) {
        let limit = isNaN(section.limit) || section.limit < 0 ? this.defaultSectionLimit : section.limit;

        if (limit > 0) {
            section.results = this.requestSectionResults(section, searchString, limit);
            this.updateResultsContainer(callback);
        }
    }

    private requestSectionResults(section: AutoSuggestSection, searchString: string, limit: number): Array<string | VuFindAutoCompleteItem> {
        // TODO: Implement actual search request and remove dummy after then
        let results: Array<string | VuFindAutoCompleteItem> = [];

        for (let index: number = 0; index < limit; ++index) {
            let random = Math.floor(Math.random() * 1000);
            results[index] = { label: `${searchString} ${random}` };
        }

        return results;
    }

    private updateResultsContainer(callback: Function) {
        let collection: VuFindAutoCompleteItemCollection = {
            groups: []
        };

        for (let position = 0; position < this.configuration.numSections(); ++position) {
            this.buildSectionResult(this.configuration.getSectionAt(position), collection);
        }

        callback(collection);
    }

    private buildSectionResult(section: AutoSuggestSection, collection: VuFindAutoCompleteItemCollection) {
        if (section.results && section.results.length > 0) {
            // ignore sections with no results
            collection.groups.push({
                items: section.results || [],
                label: AutoSuggestTemplates.sectionHeader({
                    label: section.label,
                    targetLabel: 'Show all in this group &gt;&gt;',
                    target: '#'
                })
            });
        }
    }

    private setupResultListContainer() {
        this.resultListContainerElement = $(AutoSuggest.RESULT_LIST_CONTAINER_SELECTOR);
        // TODO: Check whether we need updating the result list container, because this implementation doesn't work
        // as we are unable to react on attribute
        this.resultListContainerElement.on('show', this.resultListContainerElement_displayHandler);
    }

    private resultListContainerElement_displayHandler = (eventObject: JQuery.Event) => {
        let input: JQuery<HTMLElement> = this.sourceInputElement;
        let position: JQuery.Coordinates = input.offset();

        this.resultListContainerElement.css({
            top: position.top + input.outerHeight() - Number(input.css('margin-bottom')),
            left: position.left,
            minWidth: input.width() + input.css('padding-left') + input.css('padding-right')
        });
    }
}

/**
 * Intermediate type to get translation API type-checked
 */
interface AutoSuggestTranslator {
    /**
     * @param {string} key
     * @returns {string}
     */
    translate: (key: string, replacements?: Array<any>) => string;
}

/**
 * Data structure component represents the configuration for the AutoSuggest class.
 */
export class AutoSuggestConfiguration {

    private sections: Array<AutoSuggestSection>;
    private translator: AutoSuggestTranslator;

    constructor(sections: Array<AutoSuggestSection>, translator: AutoSuggestTranslator) {
        this.sections = sections;
        this.translator = translator;
    }

    public initialize() {
        for (let index = 0; index < this.sections.length; ++index) {
            this.sections[index].position = index;
        }
    }

    public numSections(): number {
        return this.sections.length;
    }

    public getSectionAt(position: number): AutoSuggestSection {
        return this.sections[position];
    }

    public getTranslation(key: string): string {
        return this.translator.translate(key);
    }
}

/**
 * Data structure that represents a section within the auto-suggest results.
 */
export class AutoSuggestSection {
    /**
     * Label to display in the section header
     */
    label: string;
    /**
     * Determines how many search results have to be requested for this section. If not a number or a negative number
     * the default section limit is applied. When the limit is set to zero, then section will be ignored and no search
     * request will be performed.
     */
    limit?: number;
    /**
     * The position of this section in the search result list container
     */
    position?: number;
    /**
     * The last search results
     */
    results?: Array<string | VuFindAutoCompleteItem>;

    /**
     * The searcher to use for requesting results in this section
     */
    searcher?: string;
}


/**
 * Internal class to keep template snippets in one place.
 */
class AutoSuggestTemplates {

    public static sectionHeader(args: { label: string, target: string, targetLabel: string }) {
        return `<span class="section-label">${args.label}</span><a href="${args.target}">${args.targetLabel}</a>`;
    }
}