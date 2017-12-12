/// <reference path="../types/jquery.vufind.autocomplete.d.ts"/>

import SectionLimitValidator from "./SectionLimitValidator";
import Templates from "./Templates";
import Configuration from "./Configuration";
import Section from "./Section";
import SectionLoader from "./SectionLoader";
import ResultCallback from "./ResultCallback";
import ItemCollection from "./ItemCollection";
import ItemSection from "./ItemSection";


/**
 * Main auto-suggest component.
 */
export default class AutoSuggest {

    /**
     * @private
     * @type {string}
     */
    private static RESULT_LIST_CONTAINER_SELECTOR: string = "body > div.autocomplete-results";

    /**
     * @private
     * @type {string}
     */
    private static SECTION_HEADER_LINK_SELECTOR: string = ".ac-section-header > a";

    /**
     * @private
     * Sizzle selector for the search input element
     */
    private searchInputSelector: string;

    /**
     * @private
     * The resolved search input element.
     */
    private searchInputElement: JQuery<HTMLElement>;

    /**
     * @private
     * The element that renders the results of the auto-suggest search.
     */
    private resultListContainerElement: JQuery<HTMLElement>;

    /**
     * @private
     */
    private limitValidator: SectionLimitValidator;

    /**
     * @private
     * Used to resolve template strings from a given set of replacements.
     */
    private templates: Templates;


    /**
     * Constructor.
     * @param {string} searchInputSelector
     * The Sizzle selector used to get a reference on the input element that is designated for a search.
     *
     * @param {Configuration} configuration
     * The auto-suggest configuration for the auto-suggest.
     */
    constructor(searchInputSelector: string, configuration: Configuration) {
        this.searchInputSelector = searchInputSelector;
        this._configuration = configuration;

        this.limitValidator = new SectionLimitValidator();
        this.templates = new Templates();
    }


    /**
     * @private
     * Storage for the configuration property.
     */
    private _configuration: Configuration;

    /**
     * The configuration provides auto-suggest sections and an interface for translations and for generating links for
     * further searches.
     *
     * @returns {Configuration}
     */
    public get configuration(): Configuration {
        return this._configuration;
    }


    /**
     * @private
     * Storage for the defaultSectionLimit property.
     */
    private _defaultSectionLimit: number = 10;

    /**
     * Default search result section item limit. The default limit itself must be a positive integer greater than or
     * equal to 1. A specific section's limit has to meet this criteria but can be zero to switch of the section. When
     * it doesn't fit into these requirements the default limit is applied.
     *
     * @throws RangeError
     * On the attempt to set the default limit to a non-positive, non-integer or infinite value (including NaN and
     * floating point numbers).
     */
    public get defaultSectionLimit(): number
    {
        return this._defaultSectionLimit;
    }

    /**
     * @private
     */
    public set defaultSectionLimit(value: number)
    {
        if (!this.limitValidator.isValid(value)) {
            throw new RangeError(`Default section limit is out of range: ${value}`);
        }

        this._defaultSectionLimit = value;
    }


    /**
     * Initializes the auto-suggest component by setting up the search input using the VuFind autocomplete.js jQuery
     * plugin. Additionally connections to the result list container are established during initialization to handle
     * auto-suggest section header links.
     */
    public initialize() {
        this.setupSourceInputElement();
        this.setupResultListContainerElement();
    };


    /**
     * Updates the result list using the given callback.
     *
     * @param {Function} callback
     * Must be the function
     */
    public updateResultsContainer(callback: ResultCallback) {
        const collection: ItemCollection = { groups: [] };

        for (let position = 0; position < this.configuration.numSections; ++position) {
            this.buildSectionResult(this.configuration.getSectionAt(position), collection);
        }

        this.applyResults(collection, callback);
    };


    /**
     * @private
     */
    private setupSourceInputElement() {
        this.searchInputElement = $(this.searchInputSelector);

        if (this.configuration.enabled) {
            this.searchInputElement.autocomplete({
                handler: this.autoCompleteHandler
            });
        }
    };

    /**
     * @private
     * Keep 'this' context on AutoSuggest instance by using arrow function, so it can be used as event listener without
     * using the bind() method
     */
    private autoCompleteHandler = (inputElement: JQuery<HTMLElement>, callback: ResultCallback) => {
        for (let position = 0; position < this.configuration.numSections; ++position) {
            const section: Section = this.configuration.getSectionAt(position);
            section.query = inputElement.val() as string;

            this.requestSectionResultsIfNeeded(section, callback);
        }
    };

    /**
     * @private
     */
    private requestSectionResultsIfNeeded(section: Section, callback: ResultCallback) {
        const limit = this.limitValidator.isValidOrZero(section.limit)
            ? section.limit
            : this.defaultSectionLimit;

        if (limit > 0) {
            this.requestSectionResults(section, limit, callback);
            this.updateResultsContainer(callback);
        }
    };

    /**
     * @private
     */
    private requestSectionResults(section: Section, limit: number, callback: ResultCallback): void {
        if (!section.loader) {
            section.loader = new SectionLoader(this, section);
        }

        section.loader.load(callback);
    };

    /**
     * @private
     */
    private buildSectionResult(section: Section, collection: ItemCollection) {
        if (section.result && section.result.total > 0) {
            collection.groups.push(this.createItemSection(section));
        }
    };

    /**
     * @private
     */
    private createItemSection(section:Section) : ItemSection {
        const config: Configuration = this.configuration;

        return {
            items: section.result ? section.result.items.slice(0, section.limit) : [],
            label: this.templates.sectionHeader({
                label: config.translate(section.label),
                targetLabel: config.translate('autosuggest.show.all', [section.result.total]),
                target: this.configuration.getLookForLink(section)
            })
        }
    };

    /**
     * @private
     */
    private setupResultListContainerElement() {
        this.resultListContainerElement = $(AutoSuggest.RESULT_LIST_CONTAINER_SELECTOR);
        this.sectionHeaders.on('mousedown', this.sectionHeaderLinkMouseDownHandler);
    };

    /**
     * @private
     */
    private applyResults(collection: ItemCollection, callback: ResultCallback) {
        // callback may regenerate result list completely, so we disconnect before
        // and reconnect to section headers again after then
        this.disconnectSectionHeaders();
        callback(collection);
        this.connectSectionHeaders();
    }

    /**
     * @private
     */
    private sectionHeaderLinkMouseDownHandler = (event: JQuery.Event) => {
        // simply navigate directly to the link to circumvent browser's blur behavior
        // which causes click event not to be fired
        window.location.href = $(event.target).attr('href');
    };

    /**
     * @private
     */
    private get sectionHeaders(): JQuery<HTMLElement> {
        return this.resultListContainerElement.find(AutoSuggest.SECTION_HEADER_LINK_SELECTOR);
    };

    /**
     * @private
     */
    private disconnectSectionHeaders() {
        this.sectionHeaders.off('mousedown', this.sectionHeaderLinkMouseDownHandler);
    };

    /**
     * @private
     */
    private connectSectionHeaders() {
        this.sectionHeaders.on('mousedown', this.sectionHeaderLinkMouseDownHandler);
    };
}