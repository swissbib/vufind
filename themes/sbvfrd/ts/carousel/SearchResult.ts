import DataEntry from "./DataEntry";

/**
 * A result object containing data fetched from the search backend for the carousel.
 */
export default class SearchResult implements SearchResultProvider {

    /**
     * Constructor.
     *
     * @param {Array<DataEntry>} entries
     * The list of data entries received as result.
     *
     * @param {number} page
     * The page the entries belong to. A negative value indicates the absence of page and size information and the
     * result contains all available entries.
     *
     * @param {number} size
     * The size of the page.  Values less than 1 indicate the absence of page and size information and the result
     * contains all available entries.
     */
    constructor(
        readonly entries:Array<DataEntry> = [],
        readonly page: number = -1,
        readonly size: number = -1
    ) { }

    /**
     * The offset into the un-paged collection of possible entries.
     *
     * @return {number}
     */
    public get offset(): number {
        return this.page * this.size;
    }

    /**
     * Indicates whether the result contains no entries.
     *
     * @return {boolean}
     */
    public get empty(): boolean {
        return this.entries.length === 0;
    }

    /**
     * Indicates that the result contains all available data entries.
     *
     * @return {Boolean}
     */
    public get containsAll():Boolean {
        return !this.paginationValid(this.page, this.size);
    }

    public getData(page?: number, size?: number): SearchResult {
        let result: SearchResult;

        if (this.paginationValid(page, size)) {
            const from: number = page * size;
            const to: number = from * size;
            result = new SearchResult(this.entries.slice(from, to), page, size);
        } else {
            result = new SearchResult(this.entries.slice());
        }

        return result;
    }

    private paginationValid(page?: number, size?: number): boolean {
        return page >= 0 && size > 0;
    }
}


export interface SearchResultProvider {

    /**
     * Retrieves data from the provider. This method does not make any checks for the presence if entries in the
     * specified range, so the result might be either partially or completely empty.
     *
     * @param {number} page
     * The page to retrieve data for. If left or negative all available data entries will be returned.
     *
     * @param {number} size
     * The size of the page indicating the number of entries to retrieve.  If left or negative all available data
     * entries will be returned.
     *
     * @return {SearchResult}
     */
    getData(page?: number, size?: number): SearchResult;
}