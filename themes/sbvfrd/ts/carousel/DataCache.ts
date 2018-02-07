import DataEntry from "./DataEntry";
import SearchResult from "./SearchResult";

/**
 * Caching component for data entries loaded for a carousel.
 */
export default class DataCache {

    /**
     * @private
     * @type {Array<any>}
     */
    private cache:Array<DataEntry> = [];

    /**
     * Constructor.
     */
    constructor() { }

    /**
     * Checks whether there are entries available for the given page and size.
     *
     * @param {number} page
     * The page to perform the check on.
     *
     * @param {number} size
     * The size of the page.
     *
     * @return {boolean}
     * True when for all indices in the range entries are available, false otherwise.
     */
    public contains(page: number, size: number): boolean {
        const from: number = page * size;
        const to: number = from + size;
        return this.cache.length > to && this.checkRange(from, to);
    }

    /**
     * Internal method to check the given range.
     *
     * @param {number} from
     * Index at which to start.
     *
     * @param {number} to
     * Index at which to stop
     *
     * @return {boolean}
     * In case there is at least one index without a data entry, then false is returned and true otherwise.
     */
    private checkRange(from: number, to: number): boolean {
        let result: boolean = true;

        for (let index = from; index < to; ++index) {
            if (!this.cache[index]) {
                result = false;
                break;
            }
        }

        return result;
    }

    /**
     * Stores the given collection of search result data in the cache. Insertion starts at the offset
     * specified by the result object.
     *
     * @param {SearchResult} data
     * The data collection to store.
     */
    public store(data: SearchResult): void {
        data.entries.forEach((item, index) => this.cache[data.offset + index] = item);
    }

    /**
     * Provides the cache entries for the given range.
     *
     * @param {number} page
     * The page to get the entries for.
     *
     * @param {number} size
     * The page size which determines the number of entries to retrieve from the cache.
     *
     * @return {SearchResult}
     */
    public getRange(page: number, size: number): SearchResult {
        const from: number = page * size;
        const to: number = from + size;
        const entries: Array<DataEntry> = this.cache.slice(from, to);
        return new SearchResult(entries, page, size);
    }

    public all(): SearchResult {
        return new SearchResult(this.cache.slice(), -1, -1);
    }
}