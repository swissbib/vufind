/// <reference path="types/jquery.vufind.autocomplete.d.ts"/>

import * as $ from "jquery"
import jqXHR = JQuery.jqXHR;


/**
 * Component to handle auto-completion based on the autocomplete.js integrated within VuFind.
 */
export class AutoSuggest {

    private static RESULT_LIST_CONTAINER_SELECTOR: string = "body > div.autocomplete-results";
    private static SECTION_HEADER_LINK_SELECTOR: string = ".ac-section-header > a";

    private sourceSelector: string;
    private sourceInputElement: JQuery<HTMLElement>;

    private resultListContainerElement: JQuery<HTMLElement>;

    private autoCompleteCallback: Function;


    /**
     * Constructor.
     * @param {string} sourceSelector
     * @param {AutoSuggestConfiguration} configuration
     */
    constructor(sourceSelector: string, configuration: AutoSuggestConfiguration) {
        this.sourceSelector = sourceSelector;
        this._configuration = configuration;
    }


    private _configuration: AutoSuggestConfiguration;

    public get configuration(): AutoSuggestConfiguration {
        return this._configuration;
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


    public initialize() {
        this.setupSourceInputElement();
        this.setupResultListContainerElement();
    }



    private setupSourceInputElement() {
        this.sourceInputElement = $(this.sourceSelector);

        if (this.configuration.enabled) {
            this.sourceInputElement.autocomplete({
                handler: this.autoCompleteHandler
            });
        }

        //this.sourceInputElement.blur(function (e) {
        //    console.log(e);
        //});
    };

    // keep 'this' context on AutoSuggest instance by using arrow function
    private autoCompleteHandler = (inputElement: JQuery<HTMLElement>, callback: Function) => {

        // keep handler to not passing it around all the time
        this.autoCompleteCallback = callback;

        for (let position = 0; position < this.configuration.numSections; ++position) {
            const section: AutoSuggestSection = this.configuration.getSectionAt(position);
            section.query = inputElement.val() as string;

            this.requestSectionResultsIfNeeded(section, callback);
        }
    };

    private requestSectionResultsIfNeeded(section: AutoSuggestSection, callback: Function) {
        const limit = SectionLimitValidator.isValidOrZero(section.limit)
            ? section.limit
            : this.defaultSectionLimit;

        if (limit > 0) {
            this.requestSectionResults(section, limit, callback);
            this.updateResultsContainer(callback);
        }
    }

    private requestSectionResults(section: AutoSuggestSection, limit: number, callback: Function): void {
        if (!section.loader) {
            section.loader = new AutoSuggestSectionLoader(this, section);
        }

        section.loader.load(callback);
    }

    public updateResultsContainer(callback: Function) {
        const collection: VuFindAutoCompleteItemCollection = { groups: [] };

        for (let position = 0; position < this.configuration.numSections; ++position) {
            this.buildSectionResult(this.configuration.getSectionAt(position), collection);
        }

        // callback may regenerate result list completely, so we disconnect before
        // and reconnect to section headers again after then
        this.sectionHeaders.off('mousedown', this.sectionHeaderLinkMouseDownHandler);
        callback(collection);
        this.sectionHeaders.on('mousedown', this.sectionHeaderLinkMouseDownHandler);
    }

    private buildSectionResult(section: AutoSuggestSection, collection: VuFindAutoCompleteItemCollection) {
        if (section.results && section.results.length > 0) {
            collection.groups.push(this.createItemSection(section));
        }
    }

    private createItemSection(section:AutoSuggestSection) : VuFindAutoCompleteItemSection {
        const config: AutoSuggestConfiguration = this.configuration;

        return {
            items: section.results ? section.results.slice(0, section.limit) : [],
            label: AutoSuggestTemplates.sectionHeader({
                label: config.getTranslation(section.label),
                targetLabel: config.getTranslation('autosuggest.show.all', [section.results.length]),
                target: this.configuration.getLookForLink(section)
            })
        }
    }

    private setupResultListContainerElement() {
        this.resultListContainerElement = $(AutoSuggest.RESULT_LIST_CONTAINER_SELECTOR);
        this.sectionHeaders.on('mousedown', this.sectionHeaderLinkMouseDownHandler);
    }

    private get sectionHeaders(): JQuery<HTMLElement> {
        return this.resultListContainerElement.find(AutoSuggest.SECTION_HEADER_LINK_SELECTOR);
    }

    private sectionHeaderLinkMouseDownHandler = (event: JQuery.Event) => {
        // simply navigate directly to the link to circumvent browser's blur behavior
        // which causes click event not to be fired
        window.location.href = $(event.target).attr('href');
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
     * Constructor.
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

    public getSectionAutoSuggestLink(section: AutoSuggestSection): string {
        let template: string = this.settings.templates.search.autosuggest;
        return AutoSuggestTemplates.resolve(template, section);
    }

    public getLookForLink(section: AutoSuggestSection): string {
        let template: string = this.settings.templates.search.lookfor;
        return AutoSuggestTemplates.resolve(template, section);
    }

    public getRecordLink(item: VuFindAutoCompleteItem): string {
        let template: string = this.settings.templates.search.record;
        return AutoSuggestTemplates.resolve(template, item);
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

    /**
     * A set of template strings to be filled with values on runtime.
     */
    readonly templates: {
        search: {

            /**
             * Search URL template for the auto-suggest result list
             */
            autosuggest: string,

            /**
             * Search URL template for all results in a section of the auto-suggest result list
             */
            lookfor: string,

            /**
             * Search URL template for a single record in the azto-suggest result list
             */
            record: string
        }
    };
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
    readonly searcher: string;

    /**
     * The search type to filter results on request for this section
     */
    readonly type: string;

    /**
     * The last search string queried.
     */
    query?: string;

    /**
     * The section loader used for this section.
     */
    loader?: AutoSuggestSectionLoader;
}

class AutoSuggestSectionLoader {

    private autoSuggest: AutoSuggest;
    private section: AutoSuggestSection;
    private callback: Function;

    private request: jqXHR;

    constructor(autoSuggest: AutoSuggest, section: AutoSuggestSection) {
        this.autoSuggest = autoSuggest;
        this.section = section;
    }


    private _loading: boolean;

    public get loading(): boolean {
        return this._loading;
    }

    public load(callback: Function) {
        if (this.loading) {
            this.request.abort();
        }

        let autoSuggest: AutoSuggest = this.autoSuggest;
        let section: AutoSuggestSection = this.section;

        this.request = $.ajax({
            url: autoSuggest.configuration.getSectionAutoSuggestLink(section),
            dataType: 'json',
            success: function (result: { data: Array<string> }) {
                section.results = SearchResultConverter.convert(autoSuggest.configuration, result.data);
                autoSuggest.updateResultsContainer(callback);
            }
        });
    }
}


/**
 * Internal class to keep template snippets in one place.
 */
class AutoSuggestTemplates {

    public static sectionHeader(args: { label: string, target: string, targetLabel: string }): string {
        return `<span class="section-label">${args.label}</span><a href="${args.target}">${args.targetLabel}</a>`;
    }

    public static resolve(template: string, replacements: {[key: string] : any}): string {
        let result: string = template;

        for (let key in replacements) {
            let placeholder = '{' + key + '}';
            result = result.replace(placeholder, replacements[key]);
        }

        return result;
    }
}

/**
 * Utility class that converts the results received from some search backend into VuFindAutoCompleteItem obejcts.
 */
class SearchResultConverter {

    public static convert(configuration: AutoSuggestConfiguration, results: Array<string>): Array<VuFindAutoCompleteItem> {
        const valueIndex: number = 0;
        const typeIndex: number = 1;
        const labelIndex: number = 2;

        let conversions: Array<VuFindAutoCompleteItem> = [];

        for (let index: number = 0; index < results.length; ++index) {
            let item: VuFindAutoCompleteItem = {
                label: results[index],
                value: results[index]
            };

            item.href = configuration.getRecordLink(item);

            conversions[index] = item;
        }

        return conversions;
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