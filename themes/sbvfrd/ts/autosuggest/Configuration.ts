import Translator from "./Translator";
import Settings from "./Settings";
import Templates from "./Templates";
import Section from "./Section";
import Item from "./Item";


/**
 * A data structure component which represents the configuration for the AutoSuggest component.
 */
export default class Configuration implements Translator {

    /**
     * @private
     */
    private settings: Settings;

    /**
     * @private
     */
    private translator: Translator;

    /**
     * @private
     */
    private templates: Templates;


    /**
     * Constructor.
     * @param {Settings} settings
     * @param {Translator} translator
     */
    constructor(settings: Settings, translator: Translator) {
        this.settings = settings;
        this.translator = translator;
        this.templates = new Templates();
    }

    /**
     * Initializes the component.
     */
    public initialize() {
        for (let index = 0; index < this.settings.sections.length; ++index) {
            this.settings.sections[index].position = index;
        }
    }

    /**
     * Indicates whether the auto-suggest features is enabled or not.
     *
     * @returns {boolean}
     */
    public get enabled(): boolean {
        return this.settings.enabled;
    }

    /**
     * The number of sections configured for the auto-suggest.
     * @returns {number}
     */
    public get numSections(): number {
        return this.settings.sections.length;
    }

    /**
     * Provides the section that belongs to the given position.
     *
     * @param {number} position
     *
     * @returns {Section}
     */
    public getSectionAt(position: number): Section {
        return this.settings.sections[position];
    }

    /**
     * Generates a search URL to request all auto-suggest results for the given section.
     *
     * @param {Section} section
     * @returns {string}
     */
    public getSectionAutoSuggestLink(section: Section): string {
        let template: string = VuFind.path + this.settings.templates.search.autosuggest;
        return this.templates.resolve(template, section);
    }

    /**
     * Generates a URL to navigate directly to a single search result.
     *
     * @param {VuFindAutoCompleteItem} item
     * @returns {string}
     */
    public getRecordLink(item: Item, section: Section): string {
        let template: string = VuFind.path + this.settings.templates.search.record;
        template = this.templates.resolve(template, {query: item[section.field], type: section.type});

        if (this.getRetainFilterState()) {
            let filter = this.getFilterOfCurrentUrl();
            let searchDelimiter = '?';
            if ( template.indexOf('?') > -1 ) {
                searchDelimiter = '&';
            }
            template += searchDelimiter + 'filter=' + filter
        }

        return template;
    }

    /**
     * @inheritDoc
     */
    public translate(key: string, replacements?: Array<any>): string {
        return this.translator.translate(key, replacements);
    }

    /**
     * Get filter search parameter of current url
     */
    private getFilterOfCurrentUrl(): string {
        var filter = window.location.search;
        var filterPos = filter.indexOf('filter[]=');
        if (filterPos > -1 ) {
            filterPos += 9;
            filter = filter.substring( filterPos );
        } else {
            filterPos = filter.indexOf('filter%5B%5D=');
            if (filterPos > -1 ) {
                filterPos += 13;
                filter = filter.substring( filterPos );
            } else {
                filter = '';
            }
        }
        return filter;
    }

    /**
     * Get "retain filter" checbox status
     */
    private getRetainFilterState(): boolean {
        let isChecked = false;
        let retainFilterCheckbox = $('#searchFormKeepFilters');
        if (retainFilterCheckbox && retainFilterCheckbox.is(':checked')) {
            isChecked = true;
        }
        return isChecked;
    }
}
