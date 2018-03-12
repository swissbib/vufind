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
     */
    private callback: ResultCallback;

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
        if (this.request) {
            this.request.abort();
            this.cleanup();
        }

        this.callback = callback;

        this.request = $.ajax({
            dataType: "json",
            success: this.successHandler,
            error: this.errorHandler,
            url: this.autoSuggest.configuration.getSectionAutoSuggestLink(this.section)
        });
    }

    private successHandler = (result: SearchResult, status: string, request:jqXHR) => {
        if (this.request === request) {
            const converter: SearchResultConverter = new SearchResultConverter();
            this.section.result = converter.convert(this.autoSuggest.configuration, result);
            this.autoSuggest.updateResultsContainer(this.callback);
        }
        this.cleanup();
    };

    private cleanup(): void {
        this.request = null;
        this.callback = null;
    }

    private errorHandler = (request: jqXHR, status: string, error: string) => {
        this.cleanup();
    }
}
