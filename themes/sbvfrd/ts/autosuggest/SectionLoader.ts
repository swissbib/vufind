import AutoSuggest from "./AutoSuggest";
import Section from "./Section";
import SearchResultConverter, {SearchResult} from "./SearchResultConverter";
import ResultCallback from "./ResultCallback";
import jqXHR = JQuery.jqXHR;


/**
 * Utility component to load a section's auto-suggest results.
 */
export default class SectionLoader {

    /**
     * @private
     */
    private autoSuggest: AutoSuggest;

    /**
     * @private
     */
    private section: Section;

    /**
     * @private
     */
    private request: jqXHR;

    /**
     * @private
     * Storage for the loading property.
     */
    private _loading: boolean;

    /**
     * Constructor.
     * @param {AutoSuggest} autoSuggest
     * @param {Section} section
     */
    constructor(autoSuggest: AutoSuggest, section: Section) {
        this.autoSuggest = autoSuggest;
        this.section = section;
    }

    /**
     * Indicates whether the loader is currently loading data.
     *
     * @returns {boolean}
     */
    public get loading(): boolean {
        return this._loading;
    }

    /**
     * Starts fetching results from a search backend for the section this loader is associated with. A load which is
     * currently in progress will be aborted immediately and the data is fetched again.
     *
     * @param {ResultCallback} callback
     * Invoked when the result has been received from the search backend.
     */
    public load(callback: ResultCallback) {
        if (this.loading) {
            this.request.abort();
        }

        let autoSuggest: AutoSuggest = this.autoSuggest;
        let section: Section = this.section;

        this.request = $.ajax({
            dataType: "json",
            success: (result: SearchResult) => {
                const converter: SearchResultConverter = new SearchResultConverter();
                section.result = converter.convert(autoSuggest.configuration, result);
                autoSuggest.updateResultsContainer(callback);
            },
            url: autoSuggest.configuration.getSectionAutoSuggestLink(section),
        });
    }
}
