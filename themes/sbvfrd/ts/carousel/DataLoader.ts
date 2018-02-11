import Templates from "./Templates";
import Carousel from "./Carousel";
import DataCache from "./DataCache";
import DataEntry from "./DataEntry";
import Paginator from "./Paginator";
import SearchResult, {SearchResultProvider} from "./SearchResult";

/**
 * Loads data for a carousel.
 */
export default class DataLoader implements SearchResultProvider {

    /**
     * @private
     * @type {DataCache}
     */
    private cache: DataCache = new DataCache();

    /**
     * Constructor.
     *
     * @param {Carousel} carousel
     * The carousel to load data into.
     */
    constructor(readonly carousel: Carousel) { }

    /**
     * Loads data based on the current state of the paginator. In case the paginator state points to a range which is
     * already cached completely no request is performed and the callback is invoked immediately.
     *
     * @param {Paginator} paginator
     * The paginator from which to use the state to fetch data.
     *
     * @param {Callback} callback
     * The function to invoke when data has been loaded.
     */
    public load(paginator: Paginator, callback: Callback): void {
        const page: number = paginator.page;
        const size: number = paginator.size;

        if (this.cache.contains(page, size)) {
            callback(page, size);
        } else {
            this.requestData(page, size, callback);
        }
    }

    /**
     * @inheritDoc
     */
    public getData(page?: number, size?: number): SearchResult {
        return (page > 0 && size > 0) ? this.cache.getRange(page, size) : this.cache.all();
    }

    /**
     * Performs an AJAX request based on the underlying Carousel's configured template.
     *
     * @param {number} page
     * The page request parameter value.
     *
     * @param {number} size
     * The page size request parameter value.
     *
     * @param {Callback} callback
     * The function to invoke on success.
     */
    private requestData(page: number, size: number, callback: Callback) {
        const loader: DataLoader = this;
        $.ajax({
            dataType: "json",
            success: (result: Array<DataEntry>) => {
                loader.processResult(result, page, size);
                callback(page, size);
            },
            url: this.getSearchUrl(page, size)
        });
    }

    /**
     * Resolves the AJAX URL template with the given page and size.
     *
     * @param {number} page
     * The page request parameter value.
     *
     * @param {number} size
     * The page size request parameter value.
     *
     * @return {string}
     * The resulting request URL.
     */
    private getSearchUrl(page: number, size: number): string {
        // TODO: Check whether the following is actually true
        // page is zero-based internally but one-based interpreted by server side
        return (new Templates(this.carousel.configuration)).ajax(page + 1, size);
    }

    /**
     * Processes the received data entries by storing them in the internal cache for reuse.
     *
     * @param {Array<DataEntry>} entries
     * The collection of received data entries.
     *
     * @param {number} page
     * The page the entries belong to.
     *
     * @param {number} size
     * The size of the page.
     */
    private processResult = (entries: Array<DataEntry>, page: number, size: number): void => {
        this.cache.store(new SearchResult(entries, page, size));
    }
}

/**
 * Type declaration for the callback to be passed in to the loader's load method.
 */
declare type Callback = (page: number, size: number) => void;