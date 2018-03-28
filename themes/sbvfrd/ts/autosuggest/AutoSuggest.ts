/// <reference path="../types/jquery.vufind.autocomplete.d.ts"/>

import Configuration from "./Configuration";
import ItemCollection from "./ItemCollection";
import ItemSection from "./ItemSection";
import ResultCallback from "./ResultCallback";
import Section from "./Section";
import SectionLimitValidator from "./SectionLimitValidator";
import SectionLoader from "./SectionLoader";
import Templates from "./Templates";

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
    private static SECTION_HEADER_LINK_SELECTOR: string = ".ac-group-header > a";

    /**
     * @private
     * The resolved search input element.
     */
    private searchInputElement: JQuery<HTMLElement>;

    /**
     * @private
     */
    private autocompleteInstance: any;

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
    constructor(private searchInputSelector: string, readonly configuration: Configuration) {
        this.limitValidator = new SectionLimitValidator();
        this.templates = new Templates();
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
    public get defaultSectionLimit(): number {
        return this._defaultSectionLimit;
    }

    /**
     * Returns the value of the input field
     *
     * @returns {any} The value of the input field
     */
    public getValue(): any {
        return this.searchInputElement.val();
    }

    /**
     * @private
     */
    public set defaultSectionLimit(value: number) {
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
    }

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

        if (collection.groups.length === 1) {
            // in case only one section is available, then simply use its items to exclude section header
            collection.groups[0] = {
                label: undefined,
                items: (collection.groups[0] as ItemSection).items
            };
        }

        this.applyResults(collection, callback);
        this.searchInputElement.removeClass('hidden');//autocomplete('show');
        this.autocompleteInstance.show();
    }

    /**
     * @private
     */
    private setupSourceInputElement() {
        this.searchInputElement = $(this.searchInputSelector);

        if (this.configuration.enabled) {
            this.autocompleteInstance = this.searchInputElement.autocomplete({
                handler: this.autoCompleteHandler,
                loadingString: this.configuration.translate('autosuggest.loading')
            });
        }
    }

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
    }

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
    }

    /**
     * @private
     */
    private requestSectionResults(section: Section, limit: number, callback: ResultCallback): void {
        if (!section.loader) {
            section.loader = new SectionLoader(this, section);
        }

        section.loader.load(callback);
    }

    /**
     * @private
     */
    private buildSectionResult(section: Section, collection: ItemCollection) {
        if (section.result && section.result.total > 0) {
            collection.groups.push(this.createItemSection(section));
        }
    }

    /**
     * @private
     */
    private createItemSection(section: Section): ItemSection {
        const config: Configuration = this.configuration;

        return {
            items: section.result ? section.result.items.slice(0, section.limit) : [],
            label: this.templates.sectionHeader({
                label: config.translate(section.label),
                target: this.configuration.getLookForLink(section),
                targetLabel: config.translate("autosuggest.show.all", [section.result.total]),
            }),
        };
    }

    /**
     * @private
     */
    private setupResultListContainerElement() {
        this.resultListContainerElement = $(AutoSuggest.RESULT_LIST_CONTAINER_SELECTOR);
        this.sectionHeaders.on("mousedown", this.sectionHeaderLinkMouseDownHandler);
    }

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
        window.location.href = $(event.target).attr("href");
    }

    /**
     * @private
     */
    private get sectionHeaders(): JQuery<HTMLElement> {
        return this.resultListContainerElement.find(AutoSuggest.SECTION_HEADER_LINK_SELECTOR);
    }

    /**
     * @private
     */
    private disconnectSectionHeaders() {
        this.sectionHeaders.off("mousedown", this.sectionHeaderLinkMouseDownHandler);
    }

    /**
     * @private
     */
    private connectSectionHeaders() {
        this.sectionHeaders.on("mousedown", this.sectionHeaderLinkMouseDownHandler);
    }
}
