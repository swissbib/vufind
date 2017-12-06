/// <reference path="types/jquery.vufind.autocomplete.d.ts"/>

import * as $ from "jquery"


/**
 * Component to handle auto-completion based on the autocomplete.js integrated within VuFind.
 */
export class AutoSuggest {

    private static RESULT_LIST_CONTAINER_SELECTOR: string = "body > div.autocomplete-results";

    private sourceSelector: string;
    private sourceInputElement: JQuery<HTMLElement>;

    private resultListContainerElement: JQuery<HTMLElement>;

    private configuration: AutoSuggestConfiguration;


    /**
     * Constructor.
     * @param {string} sourceSelector
     * @param {AutoSuggestConfiguration} configuration
     */
    constructor(sourceSelector: string, configuration: AutoSuggestConfiguration) {
        this.sourceSelector = sourceSelector;
        this.configuration = configuration;
    }



    private _defaultSectionLimit: number = 10;

    /**
     * Default search result section item limit. The default limit limit itself must be an positive integer greater than
     * or equal to 1. A specific section's limit has to meet this criteria but can be zero to switch of the section.
     * When it doesn't fit into these requirements the default limit is applied.
     *
     * @type {number}
     *
     * @throws RangeError
     * On the attempt to set the default limit to a non-positive, non-integer or infinite value (including NaN and
     * floating point numbers).
     */
    public get defaultSectionLimit(): number
    {
        return this._defaultSectionLimit;
    }

    public set defaultSectionLimit(value: number)
    {
        if (!SectionLimitValidator.isValid(value)) {
            throw new RangeError(`Default section limit is out of range: ${value}`);
        }

        this._defaultSectionLimit = value;
    }


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

        if (this.configuration.enabled) {
            this.sourceInputElement.autocomplete({
                handler: this.autoCompleteHandler
            });
        }
    };

    // keep 'this' context on AutoSuggest instance by using arrow function
    private autoCompleteHandler = (inputElement: JQuery<HTMLElement>, callback: Function) => {

        for (let position = 0; position < this.configuration.numSections; ++position) {
            this.requestSectionResultsIfNeeded(
                this.configuration.getSectionAt(position),
                inputElement.val() as string,
                callback
            );
        }
    };

    private requestSectionResultsIfNeeded(section: AutoSuggestSection, searchString: string, callback: Function) {
        let limit = SectionLimitValidator.isValidOrZero(section.limit) ? section.limit : this.defaultSectionLimit;

        if (limit > 0) {
            section.results = this.requestSectionResults(section, searchString, limit);
            this.updateResultsContainer(callback);
        }
    }

    private requestSectionResults(section: AutoSuggestSection, searchString: string, limit: number): Array<string | VuFindAutoCompleteItem> {
        // TODO: Implement actual search request and remove dummy after then
        let results: Array<string | VuFindAutoCompleteItem> = [];
        let numResults = Math.floor(Math.random() * limit * 10);

        for (let index: number = 0; index < numResults; ++index) {
            let random = Math.floor(Math.random() * 1000);
            results[index] = { label: `${searchString} ${random}` };
        }

        return results;
    }

    private updateResultsContainer(callback: Function) {
        let collection: VuFindAutoCompleteItemCollection = {
            groups: []
        };

        for (let position = 0; position < this.configuration.numSections; ++position) {
            this.buildSectionResult(this.configuration.getSectionAt(position), collection);
        }

        callback(collection);
    }

    private buildSectionResult(section: AutoSuggestSection, collection: VuFindAutoCompleteItemCollection) {
        if (section.results && section.results.length > 0) {
            // ignore sections with no results
            let config: AutoSuggestConfiguration = this.configuration;
            let targetLabel: string = config.getTranslation('autosuggest.show.all', [section.results.length])
            let sectionLabel: string = config.getTranslation(section.label);
            let limitedResults: Array<string | VuFindAutoCompleteItem> | VuFindAutoCompleteItemSection;

            limitedResults = section.results ? section.results.slice(0, section.limit) : [];

            collection.groups.push({
                items: limitedResults,
                label: AutoSuggestTemplates.sectionHeader({
                    label: sectionLabel,
                    targetLabel: targetLabel,
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

    private settings: AutoSuggestSettings;
    private translator: AutoSuggestTranslator;

    /**
     *
     * @param {AutoSuggestSettings} settings
     * @param {AutoSuggestTranslator} translator
     */
    constructor(settings: AutoSuggestSettings, translator: AutoSuggestTranslator) {
        this.settings = settings;
        this.translator = translator;
    }

    public initialize() {
        for (let index = 0; index < this.settings.sections.length; ++index) {
            this.settings.sections[index].position = index;
        }
    }

    public get enabled(): boolean {
        return this.settings.enabled;
    }

    public get numSections(): number {
        return this.settings.sections.length;
    }

    public getSectionAt(position: number): AutoSuggestSection {
        return this.settings.sections[position];
    }

    public getTranslation(key: string, replacements?: Array<any>): string {
        return this.translator.translate(key, replacements);
    }
}

export interface AutoSuggestSettings {
    /**
     * Configured sections to show in the auto-suggest result list.
     */
    readonly sections: Array<AutoSuggestSection>;
    /**
     * Indicates whether auto-suggest is enabled or not. If false, then no searches are performed at all.
     */
    readonly enabled: boolean;
}

/**
 * Data structure that represents a section within the auto-suggest results.
 */
export class AutoSuggestSection {
    /**
     * Label to display in the section header
     */
    readonly label: string;
    /**
     * Determines how many search results have to be requested for this section. If not a number or a negative number
     * the default section limit is applied. When the limit is set to zero, then section will be ignored and no search
     * request will be performed.
     */
    readonly limit?: number;
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
    readonly searcher?: string;
}


/**
 * Internal class to keep template snippets in one place.
 */
class AutoSuggestTemplates {

    public static sectionHeader(args: { label: string, target: string, targetLabel: string }) {
        return `<span class="section-label">${args.label}</span><a href="${args.target}">${args.targetLabel}</a>`;
    }
}

/**
 * Internal class to validate section limit values
 */
class SectionLimitValidator {

    public static isValid(limit: number): boolean {
        return !isNaN(limit) && isFinite(limit) && Math.floor(limit) === limit && limit > 1;
    }

    public static isValidOrZero(limit: number): boolean {
        return SectionLimitValidator.isValid(limit) || limit === 0;
    }
}