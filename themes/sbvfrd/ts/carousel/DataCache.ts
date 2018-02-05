/**
 * Caching component for data loaded for a carousel.
 */
import SearchResult from "./SearchResult";
import DataEntry from "./DataEntry";

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


    public containsRange(from: number, to: number): boolean {
        return this.cache.length > to && this.checkRange(from, to);
    }

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

    public getRange(page: number, size: number): SearchResult {
        const start: number = page * size;
        const end: number = start + size;
        const entries: Array<DataEntry> = this.cache.slice(start, end);
        return new SearchResult(entries, page, size);
    }
}